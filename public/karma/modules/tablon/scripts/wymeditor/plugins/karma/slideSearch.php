<?php
 
$api_key = $_GET['api_key'];
$secret = $_GET['secret'];
$ts = time();
$hash = sha1($secret.$ts);
$limit = $_GET['items_per_page'];
$page = $_GET['page'];
$search = $_GET['q'];
$url = 'http://www.slideshare.net/api/2/search_slideshows?api_key='.$api_key.'&ts='.$ts.'&hash='.$hash .'&items_per_page='.$limit.'&q='.$search.'&page='.$page.'&lang=es';
$xml = simplexml_load_file($url);
die(json_encode($xml));
?>