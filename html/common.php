<?php

session_start();

$path = $_SERVER['DOCUMENT_ROOT'];

$path = str_replace('/html','',$path);
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.$path);

$classes = $path."/classes";
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.$classes);

$database = $path."/modelos";
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.$database);

$database = $path."/html/libs";
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.$database);


define('PATH',$path);

// INCLUDES
include_once 'View.class.php';
include_once 'connect.php';
?>
