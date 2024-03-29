<?php

set_time_limit(0);
require_once 'libs/encoding.php';
use ForceUTF8\Encoding;  // It's namespaced now.

function scan($dir, $thumbsDir) {
    $files = [];

    if (file_exists($dir)) {
        foreach (scandir($dir) as $f) {
            if (!$f || $f[0] == '.') {
                continue; // Ignore hidden files
            }

            $filename = $dir . '/' . $f;
            if (is_dir($filename)) {
                // The path is a folder
                $files[] = [
                    "name" => Encoding::toUTF8($f),
                    "type" => "folder",
                    "path" => Encoding::toUTF8($filename),
                    "items" => scan($filename, $thumbsDir) // Recursively get the contents of the folder
                ];
            } else {
                if (!strpos($filename, 'jpg')) {
                    continue;
                }

                $img = getimagesize($filename, $data);
                $width = $img[0];
                $height = $img[1];
                if (array_key_exists('APP13', $data)) {
                    $iptc = iptcparse($data['APP13']);
                }
                if (is_array($iptc) && array_key_exists('2#025', $iptc) && is_array($iptc['2#025'])) {
                    $keywords = implode(',', $iptc['2#025']);
                } else {
                    $keywords = '';
                }

                try {
                    $exif_ifd0 = @exif_read_data($filename, 'IFD0');
                    $exif_exif = @exif_read_data($filename, 'EXIF');
                } catch (Exception $exp) {
                    $exif_ifd0 = false;
                    $exif_exif = false;
                }


                if (!file_exists("{$thumbsDir}/small/{$f}")) {
                    generateThumb($dir, $thumbsDir . '/small', $f, 574);
                }
                if (!file_exists("{$thumbsDir}/large/{$f}")) {
                    generateThumb($dir, $thumbsDir . '/large', $f, 1600);
                }

                $notFound = "N/A";

                // Make
                if (@array_key_exists('Make', $exif_ifd0)) {
                    $camMake = $exif_ifd0['Make'];
                } else {
                    $camMake = $notFound;
                }

                // Model
                if (@array_key_exists('Model', $exif_ifd0)) {
                    $camModel = $exif_ifd0['Model'];
                } else {
                    $camModel = $notFound;
                }

                // Exposure
                if (@array_key_exists('ExposureTime', $exif_ifd0)) {
                    $camExposure = $exif_ifd0['ExposureTime'];
                } else {
                    $camExposure = $notFound;
                }

                // Aperture
                if (@array_key_exists('ApertureFNumber', $exif_ifd0['COMPUTED'])) {
                    $camAperture = $exif_ifd0['COMPUTED']['ApertureFNumber'];
                } else {
                    $camAperture = $notFound;
                }

                // Date
                if (@array_key_exists('DateTime', $exif_ifd0)) {
                    $camDate = $exif_ifd0['DateTime'];
                } else {
                    $camDate = $notFound;
                }

                // ISO
                if (@array_key_exists('ISOSpeedRatings', $exif_exif)) {
                    $camIso = $exif_exif['ISOSpeedRatings'];
                } else {
                    $camIso = $notFound;
                }

                // Copyright
                if (@array_key_exists('Copyright', $exif_exif)) {
                    $camCopyright = $exif_exif['Copyright'];
                } else {
                    $camCopyright = $notFound;
                }

                // Artist
                if (@array_key_exists('Artist', $exif_exif)) {
                    $camArtist = $exif_exif['Artist'];
                    if (preg_match('/^\d/', $camArtist)) {
                        $camArtist = $camCopyright;
                    }
                } else {
                    $camArtist = $notFound;
                }


                // ImageDescription
                if (@array_key_exists('ImageDescription', $exif_exif)) {
                    $camImageDescription = $exif_exif['ImageDescription'];
                } else {
                    $camImageDescription = $notFound;
                }

                // It is a file
                $files[] = [
                    "name" => Encoding::toUTF8($f),
                    "type" => "file",
                    "path" => Encoding::toUTF8($filename),
                    "copyright" => Encoding::toUTF8($camCopyright),
                    "artist" => Encoding::toUTF8($camArtist),
                    "imageDescription" => Encoding::toUTF8($camImageDescription),
                    "keywords" => Encoding::toUTF8($keywords),
                    "thumbnail" => Encoding::toUTF8("{$thumbsDir}/small/{$f}"),
                    "thumbnail_large" => Encoding::toUTF8("{$thumbsDir}/large/{$f}"),
                    "width" => $width,
                    "height" => $height,
                    "camera_maker" => Encoding::toUTF8($camMake),
                    "camera_model" => Encoding::toUTF8($camModel),
                    "exposure" => $camExposure,
                    "aperture" => $camAperture,
                    "date" => $camDate,
                    "iso" => $camIso,
                    "size" => filesize($filename) // Gets the size of this file
                ];
            }
        }
    }

    return $files;
}

function getDirectoryListing($jsonFile, $photoDir, $thumbsDir) {
    createThumbnailDirectory($thumbsDir);
    generateDirectoryListing($jsonFile, $photoDir, $thumbsDir);
    serveDirectoryListing($jsonFile);
}

function createThumbnailDirectory($thumbsDir) {
    if (!file_exists($thumbsDir)) {
        mkdir($thumbsDir);
    }
    if (!file_exists($thumbsDir . '/small')) {
        mkdir($thumbsDir . '/small', 0777, true);
    }
    if (!file_exists($thumbsDir . '/large')) {
        mkdir($thumbsDir . '/large', 0777, true);
    }
}

function generateDirectoryListing($jsonFile, $photoDir, $thumbsDir) {
    $dirScan = scan($photoDir, $thumbsDir);
    $content = json_encode([
        "name" => "photos",
        "type" => "folder",
        "path" => Encoding::toUTF8($photoDir),
        "items" => $dirScan
    ]);
    file_put_contents($jsonFile, $content);
}

function serveDirectoryListing($jsonFile) {
    header('Content-type: application/json');

    echo file_get_contents($jsonFile);
}

function generateThumb($pathToImages, $pathToThumbs, $fname, $thumbWidth) {
    $basePath = dirname(__FILE__);
    // parse path for the extension
    $info = pathinfo($pathToImages . $fname);
    // continue only if this is a JPEG image
    if (strtolower($info['extension']) == 'jpg') {
        //echo "Creating thumbnail for {$fname} <br />";

        if ($fname) {
            $img = imagecreatefromjpeg("{$pathToImages}/{$fname}");
        }

        if ($img) {
            // load image and get image size
            $width = imagesx($img);
            $height = imagesy($img);

            // calculate thumbnail size
            $new_width = $thumbWidth;
            $new_height = floor($height * ($thumbWidth / $width));

            // create a new temporary image
            $tmp_img = imagecreatetruecolor($new_width, $new_height);

            // Switch antialiasing on for one image
            //imageantialias($tmp_img, true);

            // copy and resize old image into new image
            imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

            // save thumbnail into a file
            imagejpeg($tmp_img, "{$basePath}/{$pathToThumbs}/{$fname}");
        }
    }
}


$jsonFile = 'directorylist.json';
$masterFolder = 'photos';
$thumbsFolder = 'thumbs';

// Output the directory listing as JSON
getDirectoryListing($jsonFile, $masterFolder, $thumbsFolder);
