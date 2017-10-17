<?php
include_once 'common.php';
include_once 'session.php';
include_once 'model.php';

// seccion actual
$current = "";
if ( isset($_SERVER['SCRIPT_URL']) ) {
  $current = $_SERVER['SCRIPT_URL'];
}
if ( isset($_SERVER['REQUEST_URI']) ) {
  $current = $_SERVER['REQUEST_URI'];
}
$current = str_replace("/admin/","",$current);
$current = str_replace("/","",$current);
if( strpos($current,'?') !== false ){
  $tmp = explode('?',$current);
  $current = $tmp[0];
}

// secciones del menu
$navs = array(
    'home' => array(
        'url' => 'home',
        'name' => 'Home',
        'current' => $current=='home'
    ),
    'proyectos' => array(
        'url' => 'proyectos',
        'name' => 'Proyectos',
        'current' => $current=='proyectos'
    ),
    'textos' => array(
        'url' => 'textos',
        'name' => 'Textos',
        'current' => $current=='textos'
    ),
    'configuraciones' => array(
        'url' => 'configuraciones',
        'name' => 'Configuraciones',
        'current' => $current=='configuraciones'
    )
);
$view->set("navs",$navs);
//*
if( isset($_FILES) ){
    foreach( $_FILES  as $file ){
        $name = $file['name'];
        $filepath = PATH."/html/assets/".$name;
        if( !move_uploaded_file($file['tmp_name'],$filepath) ){
            error_log("No se pudo subir el archivo '".$name."'");
        }
    }
}
//*/
switch( $current ){
    case 'home':
        include_once 'home.php';
        break;
    case 'proyectos':
        include_once 'proyectos.php';
        break;
    case 'textos':
        include_once 'textos.php';
        break;
    case 'configuraciones':
        include_once 'configs.php';
        break;
}
if( isset($content) ){
    $view->set("CONTENT",$content);
}
$view->setTemplate("index.html");
print $view->getView();
?>
