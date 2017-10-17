<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
if( isset($_GET['cliente']) || isset($_POST['id']) ){
    $id = (int)@$_GET['cliente'];
    if( $id==0 ){
        $id = (int)$_POST['id'];
    }
    $cur = $dbh->query("select * from cliente where id=?;",array($id));
    if( isset($cur[0]) ){
        foreach( $cur[0] as $key=>$value ){
            $view->set($key,$value);
        }
    }
}
if( isset($_GET['banner']) ){
    $id = $_GET['banner'];
    $cur = $dbh->query("select * from banner where id=?;",array($id));
    if( isset($cur[0]) ){
        foreach( $cur[0] as $llave=>$valor ){
            $view->set($llave,$valor);
        }
    }
}
// NO DEJA SUBIR FOTO ACA.
/*
if( isset($_FILES['logoupload']) ){
    $file = $_FILES['logoupload'];
    $logo = date("YmdHis")."-".$file['name'];
    $filePath = PATH."/html/assets/$logo";
    print_r($filePath);
    if( !move_uploaded_file($file['tmp_name'], $filePath) ){
        echo "error";
        print_r($_FILES);
        $view->set("error","No se pudo subir la imagen.");
    } else {
        $_REQUEST['logo'] = $logo;
        echo $logo;
    }
}
//*/
if( isset( $_REQUEST['grabar'] ) ){
    if( isset($_REQUEST['foto']) ){
        $data = $_REQUEST;
        $ban = new Banner($dbh);
        $ban->setNew(!isset($_REQUEST['id']));
        $ban->setValues($data);
    }
    if( isset($_REQUEST['grabar']['cliente']) ){
        $cliente = new Cliente($dbh);
        $cliente->setNew(!isset($_REQUEST['id']));
        $_REQUEST['grabar'] = 1;
        $cliente->setValues($_REQUEST);
    }
    header("Location: /admin/home");
}
if( isset($_REQUEST['eliminar']) ){
    if( isset($_REQUEST['eliminar']['banner']) ){
        $dbh->query("delete from banner where id=?;",array($_REQUEST['eliminar']['banner']));
    }
    if( isset($_REQUEST['eliminar']['cliente']) ){
        $dbh->query("delete from cliente where id=?;",array($_REQUEST['eliminar']['cliente']));
    }
    header("Location: /admin/home");
}
$cur = $dbh->query("select * from banner;");
if( isset($cur[0]) ){
    $view->set("banners",$cur);
}
$cur = $dbh->query("select * from cliente;");
if( isset($cur[0]) ){
    $view->set("clientes",$cur);
}

$view->setTemplate("home.html");
$content = $view->getView();
