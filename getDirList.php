<?php
require_once('config.php'); 
	
function buildContent($path,$name) {
	
	//if(substr($path,0,6) == 'photos') $path = substr($path,strlen('photos'));
	$files = utf8_converter(getFiles($path));
	
	if ($_REQUEST['recursive'] == true)	{
		$folders = utf8_converter(getFolderRecursive($path));
	} else {
		$folders = utf8_converter(getFolder($path));
	}
	
	$items = array_merge($files,$folders);
	
	$result = array(
			"name" => $name,
			"type" => "folder",
			"path" => $path,
			"items" => $items
		);
		
	return $result;
}
	
	
function getFiles($path) {
	$db = getDBHandler();
	//$path = addslashes($path);
	$path = utf8_decode($path);
	$query = $db->query("SELECT *,CONCAT(path,'/',name) as path FROM photos WHERE type='file' AND path = '{$path}'");
	
	$files = array();
	if($query) $files = $query->fetchAll(PDO::FETCH_ASSOC);
	
	return $files;
}

function getFolderRecursive($path) {
	$db = getDBHandler();
	//$path = addslashes($path);
	$query = $db->query("SELECT name, path FROM photos WHERE type='folder' AND path = '{$path}'");
	
	$folders = array();
	
	if($query) $folders = $query->fetchAll(PDO::FETCH_ASSOC);
	$result = array();
	foreach($folders as $folder) {
		if ($folder['path']) $path = $folder['path']."/".$folder['name'];
		else $path = $folder['name'];
		$result[] = buildContent($path, $folder['name']);
	}
	
	return $result;
}

function getFolder($path) {
	$db = getDBHandler();
	//$path = addslashes($path);
	$path = utf8_decode($path);
	$query = $db->query("SELECT name, path FROM photos WHERE type='folder' AND path = '{$path}'");
	$folders = array();
	
	if($query) $folders = $query->fetchAll(PDO::FETCH_ASSOC);
	$result = array();
	foreach($folders as $folder) {
		//if ($folder['path']) $path = $folder['path']."/".$folder['name'];
		//else $path = $folder['name'];
		
		
		$result[] = array(
			"name" => $folder['name'],
			"type" => "folder",
			//"path" => $path,
			"path" => $path . "/" . $folder['name'], //Modified AN 2017-04-28: Path is used directly in the URL so the path needs to contain the entire path of the folder to be browseable
			"items" => ''
		);
	}
	
	return $result;	
	
}

if ($_REQUEST['path']) {
	$initialPath = $_REQUEST['path'];
} else {
	$initialPath = 'photos';
}


$initialPath = unicode_decode($initialPath);


if(substr($initialPath,0,7) == 'photos/') $initialName = substr($initialPath,strlen('photos/'));
else $initialName = $initialPath;

$content = buildContent($initialPath, $initialName);

header('Content-type:application/json;charset=utf-8');
echo json_encode($content);

function unicode_decode($str) {
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
}

function replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}