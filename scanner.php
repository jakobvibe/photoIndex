<?php
set_time_limit(0);
require_once('libs/encoding.php'); 
require_once('config.php'); 
use \ForceUTF8\Encoding;  // It's namespaced now.

function scan($dir, $thumbsDir){

	$files = array();
	
	if(file_exists($dir)){
		foreach(scandir($dir) as $f) {
			if(!$f || $f[0] == '.' || $f == 'Thumbs.db') {
				continue; // Ignore hidden files and Thumbs.db
			}
			
			$fullpathFileName = $dir . '/' . $f;
			if(is_dir($fullpathFileName) && $folderview) {
				// The path is a folder
				$files[] = array(
					"name" => utf8_encode($f),
					"type" => "folder",
					"path" => utf8_encode($fullpathFileName),
					"items" => scan($fullpathFileName,$thumbsDir) // Recursively get the contents of the folder
				);
			}
			else {
				if(!strpos(strtolower($fullpathFileName), 'jpg')) {
					continue;
				}
				$files[] = parseImage($fullpathFileName, $filename, $dir, $thumbsDir);
			}
		}
	}

	return $files;
}

function scanFull($dir, $thumbsDir) {
	echo "Scanning";
	$files = array();
	$results = array();
	if (is_dir($dir)) {
		$iterator = new RecursiveDirectoryIterator($dir);
		foreach ( new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file ) {
			if ($file->isFile() && $file->getBaseName() != 'Thumbs.db' && $file->getBaseName()[0] != '.') {
				
				$thisfile = $file->getFilename();
				$thispath = $file->getPath()."/".$file->getFilename();
				$files[] = parseImage($thispath, $thisfile, $file->getPath(), $thumbsDir);
			} else if($file->isDir() && $file->getBaseName() != '.' && $file->getBaseName() != '..') {
				
				if (strpos($file->getPath(),'/')) $path = substr($file->getPath(),strpos($file->getPath(),'/')+1);
				else $path = '';

				$path = $file->getPath();
				
				$folder = array(
					'filename' => utf8_decode($file->getBaseName()),
					'type' => 'folder',
					'path' => utf8_decode($path)
				);
				
				$db = getDBHandler();
				$sql = $db->prepare("replace into photos (name, type, path, lastscan) values (:filename, :type, :path, NOW())");
				$sql->execute($folder);
			}
			echo ".";
		}
	}
	
	echo "\n";    
	
}

function pathToArray($path , $separator = '/') {
    if (($pos = strpos($path, $separator)) === false) {
        return array($path);
    }
    return array(substr($path, 0, $pos) => pathToArray(substr($path, $pos + 1)));
}

function generateDirectoryListing($jsonFile, $photoDir, $thumbsDir) {
	$dirScan = scan($photoDir,$thumbsDir);
	$content = json_encode(array(
		"name" => "photos",
		"type" => "folder",
		"path" => utf8_encode($photoDir),
		"items" => $dirScan
	));
	file_put_contents($jsonFile, $content);
}

function generateFullListing($jsonFile, $photoDir, $thumbsDir) {
	$dirScan = scanFull($photoDir,$thumbsDir);
	$content = json_encode(array(
		"name" => "photos",
		"type" => "folder",
		"path" => utf8_encode($photoDir),
		"items" => $dirScan
	));
	file_put_contents($jsonFile, $content);
}


function generateThumb($pathToImages, $pathToThumbs, $fname, $thumbWidth) {
	//$fname = utf8_decode($fname);
   // parse path for the extension
    $info = pathinfo($pathToImages . $fname);
    // continue only if this is a JPEG image
	var_Dump($info);
    if ( strtolower($info['extension']) == 'jpg' ) 
    {
      echo "Creating thumbnail for {$fname} <br />";
	  
		if($fname) $img = imagecreatefromjpeg( "{$pathToImages}/{$fname}" );
     
		if($img) {
		  // load image and get image size
		  $width = imagesx( $img );
		  $height = imagesy( $img );

		  // calculate thumbnail size
		  $new_width = $thumbWidth;
		  $new_height = floor( $height * ( $thumbWidth / $width ) );

		  // create a new temporary image
		  $tmp_img = imagecreatetruecolor( $new_width, $new_height );
		  
		  // Switch antialiasing on for one image
		  //imageantialias($tmp_img, true);

		  // copy and resize old image into new image 
		  imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

		  // save thumbnail into a file
		  imagejpeg( $tmp_img, "{$pathToThumbs}/{$fname}" );
		}
    }
}

function parseImage($fullpathFileName, $filename, $dir, $thumbsDir) {
	
	$keywords = '';
	$img = getimagesize($fullpathFileName, $data);
	if(!$img) {
		var_dump($fullpathFileName);
	}
	$width = $img[0];
	$height = $img[1];
	if (array_key_exists('APP13', $data)) {
		$iptc = iptcparse($data['APP13']); 
		if (is_array($iptc) && array_key_exists('2#025', $iptc) && is_array($iptc['2#025'])) {
			$keywords = implode(',',$iptc['2#025']);
		} 
	}
	
	try {
		$exif_ifd0 = @exif_read_data ($fullpathFileName ,'IFD0');       
		$exif_exif = @exif_read_data ($fullpathFileName ,'EXIF');
	}
	catch (Exception $exp) {
		$exif_ifd0 = false;
		$exif_exif = false;
	}
	
	
	if (!file_exists("{$thumbsDir}/small/{$filename}")) generateThumb($dir, $thumbsDir.'/small', $filename, 574);
	if (!file_exists("{$thumbsDir}/large/{$filename}")) generateThumb($dir, $thumbsDir.'/large', $filename, 1600);
	
	$notFound = "N/A";

	 // Make 
	  if (@array_key_exists('Make', $exif_ifd0)) {
		$camMake = $exif_ifd0['Make'];
	  } else { $camMake = $notFound; }
	
	 // Model
	  if (@array_key_exists('Model', $exif_ifd0)) {
		$camModel = $exif_ifd0['Model'];
	  } else { $camModel = $notFound; }
	  
	  // Exposure
	  if (@array_key_exists('ExposureTime', $exif_ifd0)) {
		$camExposure = $exif_ifd0['ExposureTime'];
	  } else { $camExposure = $notFound; }

	  // Aperture
	  if (@array_key_exists('ApertureFNumber', $exif_ifd0['COMPUTED'])) {
		$camAperture = $exif_ifd0['COMPUTED']['ApertureFNumber'];
	  } else { $camAperture = $notFound; }
	  
	  // Date
	  if (@array_key_exists('DateTime', $exif_ifd0)) {
		$camDate = $exif_ifd0['DateTime'];
	  } else { $camDate = $notFound; }
	  
	  // ISO
	  if (@array_key_exists('ISOSpeedRatings',$exif_exif)) {
		$camIso = $exif_exif['ISOSpeedRatings'];
	  } else { $camIso = $notFound; }

	   // Copyright
	  if (@array_key_exists('Copyright',$exif_exif)) {
		$camCopyright = $exif_exif['Copyright'];
	  } else { $camCopyright = $notFound; }

	  // Artist
	  if (@array_key_exists('Artist',$exif_exif)) {
		$camArtist = $exif_exif['Artist'];
		if(preg_match('/^\d/', $camArtist)) {
			$camArtist = $camCopyright;
		}
	  } else { $camArtist = $notFound; }
	  
	  
	   // ImageDescription
	  if (@array_key_exists('ImageDescription',$exif_exif)) {
		$camImageDescription = $exif_exif['ImageDescription'];
	  } else { $camImageDescription = $notFound; }
	  
	// It is a file
	$file = array(
		"filename" => utf8_decode($filename),
		"type" => "file",
		"path" => utf8_decode($dir),
		"copyright" => utf8_decode($camCopyright),
		"artist" => utf8_decode($camArtist),
		"imageDescription" => $camImageDescription,
		"keywords" => $keywords,
		"thumbnail" => "{$thumbsDir}/small/".$filename,
		"thumbnail_large" => "{$thumbsDir}/large/".$filename,
		"width" => $width,
		"height" => $height,
		"camera_maker" => $camMake,
		"camera_model" => $camModel,
		"exposure" => $camExposure,
		"aperture" => $camAperture,
		"date" => $camDate,
		"iso" => $camIso,
		"size" => filesize($fullpathFileName) // Gets the size of this file
	);
	
	// Anders test
	//file_put_contents('dataArray.txt',print_r($file, true),FILE_APPEND);
	
	$db = getDBHandler();
	$sql = $db->prepare("replace into photos(name, type, path, copyright, artist, imageDescription, keywords, thumbnail, thumbnail_large, width, height, camera_maker, camera_model, exposure, aperture, date, iso, size, lastscan) values (:filename, :type, :path, :copyright, :artist, :imageDescription, :keywords, :thumbnail, :thumbnail_large, :width, :height, :camera_maker, :camera_model, :exposure, :aperture, :date, :iso, :size, NOW())");
	$sql->execute($file);
	return $file;
}


$dirScan = scanFull($photoDir,$thumbsDir);

Echo "Done\n";
