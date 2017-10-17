<?php
include_once 'common.php';
include_once 'connect.php';

$view = new View();
$view->setFolder(PATH."/templates/");

if( isset($_REQUEST['p']) ){
    $page = $_REQUEST['p'];
} else {
    $page = null;
}

$cur = $dbh->query("SELECT * FROM configs;");
$configs = array();
if( isset($cur[0]) ){
    foreach( $cur as $row ){
        $configs[$row['nombre']] = $row;
    }
}
$view->set("configs",$configs);

$view->set("current",$page);
switch( $page ){
    case 'proyectos':
        $view->set("title","Proyectos");
        $cur = $dbh->query("select * from proyecto;");
        if( isset($cur[0]) ){
            $proyectos = array();
            foreach( $cur as $row ){
                $year = date("Y",strtotime($row['fecha']));
                $i = 0;
                if( $year>=2001 ){
                    $i = 1;
                }
                $proyectos[$i][] = $row;
            }
            $view->set("proyectos",$proyectos);
        }
        if( isset($_REQUEST['id']) ){
            $id = $_REQUEST['id'];
            $cur = $dbh->query("SELECT * FROM proyecto WHERE id=?;",array($id));
            if( isset($cur[0]) ){
                $sc = $dbh->query("SELECT * FROM galeria WHERE proyecto=?;",array($id));
                if( isset($sc[0]) ){
                    $cur[0]['galeria'] = $sc;
                }
                $view->set("data",$cur[0]);
            }
        }
        $template = "proyectos.html";
        break;
    case 'contacto':
        $view->set("title","Contacto");
        $template = "contacto.html";
        break;
    case '':
    case null:
    default:
        $view->set("title","Home");
        $cur = $dbh->query("select * from banner;");
        if( isset($cur[0]) ){
            $view->set("banners",$cur);
        }
        $cur = $dbh->query("Select * from texto;");
        if( isset($cur[0]) ){
            foreach( $cur as $row ){
                switch( $row['id'] ){
                    case "2":
                        $view->set("about",$row);
                        break;
                    case "3":
                        $view->set("ing",$row);
                        break;
                    case "4":
                        $view->set("tec",$row);
                        break;
                }
            }
        }
        $cur = $dbh->query("select * from cliente where activo=1;");
        if( isset($cur[0]) ){
            foreach( $cur as $k=>$v ){
                if( !empty($v['url']) ){
                    if( !preg_match('/^(http:\/\/|https:\/\/).*/',$v['url']) ){
                        $v['url'] = "http://".$v['url'];
                        $cur[$k] = $v;
                    }
                }
            }
            $view->set("clientes",$cur);
        }
        $template = "home.html";
        break;
}
if( isset($template) ){
    $view->setTemplate($template);
    $view->set("content",$view->getView());
}

$view->setTemplate("index.html");
print $view->getView();
?>
