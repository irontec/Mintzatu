<?php
 
$api_key = $_GET['api_key'];
$secret = $_GET['secret'];
$ts = time();
$hash = sha1($secret.$ts);
$username = $_GET['username'];
$limit = $_GET['limit'];
if(isset($_GET['offset']))
{
	$offset = $_GET['offset'];
}
else
{
	$offset = '0';
}
$url = 'http://www.slideshare.net/api/2/get_slideshows_by_user?api_key='.$api_key.'&ts='.$ts.'&hash='.$hash .'&username_for='.$username.'&limit='.$limit.'&offset='.$offset;
$xml = simplexml_load_file($url);

die(json_encode($xml));
?>