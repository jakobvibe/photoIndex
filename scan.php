<?php



function scan($dir){

	$files = array();
	
	if(file_exists($dir)){
		foreach(scandir($dir) as $f) {
	
			if(!$f || $f[0] == '.') {
				continue; // Ignore hidden files
			}
			
			$filename = $dir . '/' . $f;
			if(is_dir($filename)) {
				// The path is a folder
				$files[] = array(
					"name" => utf8_encode($f),
					"type" => "folder",
					"path" => utf8_encode($filename),
					"items" => scan($filename) // Recursively get the contents of the folder
				);
			}
			else {
				$exif = exif_read_data($filename);
				$img = getimagesize($filename, $data);
				//var_dump($data['APP13']);
				$iptc = iptcparse($data['APP13']); 
				if (is_array($iptc['2#025'])) $keywords = implode(',',$iptc['2#025']);
				else unset($keywords);
				
				if (!file_exists("thumb/{$f}")) generateThumb($dir, "thumb", $f, 100);
				
				// It is a file
				$files[] = array(
					"name" => utf8_encode($f),
					"type" => "file",
					"path" => utf8_encode($filename),
					"Copyright" => utf8_encode($exif['Copyright']),
					"Artist" => utf8_encode($exif['Artist']),
					"ImageDescription" => utf8_encode($exif['ImageDescription']),
					"Keywords" => $keywords,
					"thumbnail" => "thumb/{$f}",
					"size" => filesize($filename) // Gets the size of this file
				);
			}
		}
	}

	return $files;
}

function getDirectoryListing($jsonFile, $rootDir) {
	generateDirectoryListing($jsonFile, $rootDir);
	serveDirectoryListing($jsonFile);
}

function generateDirectoryListing($jsonFile, $rootDir) {
	$lastModified = file_exists($jsonFile) ? filemtime($jsonFile) : false; 
	
	//Only generate a new file if no file exists or 5 seconds has passed since last generate
	if ($lastModified === false || (time() - 5) > $lastModified) {
		$dirScan = scan($rootDir);
		$content = json_encode(array(
			"name" => "Photos",
			"type" => "folder",
			"path" => utf8_encode($rootDir),
			"items" => $dirScan
		));
		file_put_contents($jsonFile, $content);
	}
}

function serveDirectoryListing($jsonFile) {
	header('Content-type: application/json');

	echo file_get_contents($jsonFile);
}

function generateThumb($pathToImages, $pathToThumbs, $fname, $thumbWidth) {
   // parse path for the extension
    $info = pathinfo($pathToImages . $fname);
    // continue only if this is a JPEG image
    if ( strtolower($info['extension']) == 'jpg' ) 
    {
      //echo "Creating thumbnail for {$fname} <br />";

      // load image and get image size
      $img = imagecreatefromjpeg( "{$pathToImages}/{$fname}" );
      $width = imagesx( $img );
      $height = imagesy( $img );

      // calculate thumbnail size
      $new_width = $thumbWidth;
      $new_height = floor( $height * ( $thumbWidth / $width ) );

      // create a new temporary image
      $tmp_img = imagecreatetruecolor( $new_width, $new_height );

      // copy and resize old image into new image 
      imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

      // save thumbnail into a file
      imagejpeg( $tmp_img, "{$pathToThumbs}/{$fname}" );
    }
}


$jsonFile = 'directorylist.json';
$masterFolder = 'photos';

// Output the directory listing as JSON
getDirectoryListing($jsonFile, $masterFolder);
