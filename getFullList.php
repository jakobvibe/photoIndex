<?php

require_once('config.php'); 

$db = getDBHandler();

if (isset($_REQUEST['q'])) {
	if ($_GET['q'] == 'nophotoname') {
	        $query = $db->prepare("select *,CONCAT(path,'/',name) as path from photos where type='file' and (artist='N/A') order by date DESC LIMIT 1000");
	}
	else {
		$query = $db->prepare("select *,CONCAT(path,'/',name) as path from photos where type='file' and (LOWER(CONVERT(keywords using latin1)) like LOWER(:q) OR LOWER(CONVERT(imageDescription using latin1)) like LOWER(:q) OR LOWER(path) like LOWER(:q) OR LOWER(name) like LOWER(:q)) order by date DESC LIMIT 1000");
	}
	$query->execute(array('q' => "%{$_REQUEST['q']}%"));
} else {
	if (isset($_REQUEST['offset'])) {
		$offset=$_REQUEST['offset'];
	} else {
		$offset=0;
	}
	$query = $db->query("select *,CONCAT(path,'/',name) as path from photos where type='file' order by date DESC LIMIT {$offset},500");
}

$files = $query->fetchAll(PDO::FETCH_ASSOC);
$files = utf8_converter($files);

$maxquery = $db->prepare("select count(*) as count from photos where type='file'");
$maxquery->execute();
$maxcount = $maxquery->fetchAll(PDO::FETCH_ASSOC);

$content = json_encode(array(
	"name" => "photos",
	"type" => "folder",
	"path" => "photos",
	"count" => $maxcount[0]['count'],
	"items" => $files
));	
header('Content-type:application/json;charset=utf-8');
echo $content;
