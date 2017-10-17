<?php
include_once("common.php");

abstract class Model extends stdClass{
	protected $dbh;
	protected $table;
	protected $validFields;
	protected $idField;
	protected $isNew;

	public $query;

	/* 
	* CONSTRUCTOR:
	* parametros:
	*	- $dbh = Objeto de tipo dbh.
	*	- $table = String con el nombre de la tabla.
	*/	
	function __construct($dbh, $table){
		$this->query = new query($dbh,$table);
		$this->table = $table;
		$this->dbh = $dbh;
		$validState = $this->validateStrings();
		if( $validState!==true ){
			$error = compile_error("<p><b>Error: </b>" . $validState['message'] . "</p>");
			Throw new Exception($error);
		}
		$this->setValidFields();
		if( gettype($this->validFields)!='array' ){
			$error = compile_error("<p><b>Error Fatal: </b>Debe inicializar la variable 'validFields' de la clase '" . get_class($this) . "'.</p><p>Para esto debe ingresar los datos en la funcion 'setValidFields' con los campos de la tabla y sus propiedades.</p><p><b>Referencia:</b> inc/classes/modelos/Producto.class.php</p>");
			Throw new Exception($error);
		}
		foreach( $this->validFields as $k=>$v ){
			$this->data[$k] = null;
		}
	}
	function createTable(){
		return $this->query->create($this->create);
	}
	function insertValue($values=null){
		if( $values!=null && gettype($values)!='array' ){
			$error = compile_error("<p><b>Error Fatal: </b>El insert debe contener un arreglo con los valores a insertar</p>");
			Throw new Exception($error);
		} else {
			if( $values!=null && is_array($values) ){
				$this->setValues($values);
			}
			$retval = $this->query->raw_query($this->insert,array_values($this->data));
			return $retval;
		}
		return;
	}
	function updateValue($values=null){
		if( $values!=null && gettype($values)!='array' ){
			$error = compile_error("<p><b>Error Fatal: </b>El update debe contener un arreglo con los valores a insertar</p>");
			Throw new Exception($error);
		} else {
			if( $values!=null && is_array($values) ){
				$this->setValues($values);
			}
			$data = array_slice($this->data,1);
			$data[] = (array_values($this->data)[0]);
			$data = array_values($data);
			$retval = $this->query->raw_query($this->update,$data);
			return $retval;
		}
		return;
	}
	function delete($ID){
		if( !is_numeric($ID) ){
			$error = compile_error("<p><b>Error:</b> La id debe ser un número, se le paso '$ID'.</p>");
			Throw new Exception($error);
		}
		return $this->query->raw_query($this->delete,array($ID));
	}
	function setValues($values){
		global $view, $page;
		if( is_object($values) ){
			$values = (array)$values;
		}
		if( is_array($values) ){
			foreach( $values as $k=>$v ){
				if( in_array($k,array_keys($this->validFields)) ){
					if( $this->validFields[$k]['null'] ){
						$this->data[$k] = empty($v) ? null : $v;
					} else {
						if( empty($v) || $v==null ){
							$error = compile_error("<p><b>Error:</b> El campo '$k' no puede ser nulo.</p>");
							Throw new Exception($error);
						} else {
							$this->data[$k] = $v;
						}
					}
				}
			}
		}
		if( isset($values['grabar']) ){
			Try{
				if( $this->isNew ){
					$retval = $this->insertValue();
				} else {
					$retval = $this->updateValue();
				}
				if( !is_numeric($retval) ){
					$view->set("error",$retval);
				} else {
					if( @$this->isNew ){
						// primero debemos grabar el producto para setear los foraneos.
                        $this->id = $retval;
						$this->data['id'] = $retval;
                    } else {
                        $this->id = $this->data['id'];
                    }
					foreach($_POST as $k=>$v){
						if( !is_array($v) ){
							continue;
						}
						$this->setForeign(array($k=>$v));
					}
					if( isset($page) ){
						header("Location: " . url . "admin/$page/");
						exit;
					}
				}
			} catch(Exception $e){
				$view->set("error",$e->getMessage());
			}
		}
	}
	function validateStrings(){
		if( empty($this->create) ){
			return array("error"=>1, "message"=>get_class($this)." => CRUD incompleto: Falta string con el create.");
		}
		if( empty($this->update) ){
			return array("error"=>1, "message"=>get_class($this)." => CRUD incompleto: Falta string con el update.");
		}
		if( empty($this->insert) ){
			return array("error"=>1, "message"=>get_class($this)." => CRUD incompleto: Falta string con el insert.");
		}
		return true;
	}
	function setNew($new=false){
		$this->isNew = $new;
	}
	// metodos abstractos
	abstract function setValidFields();
	abstract function setForeign($elements);
}
class query{
	protected $table;

	function __construct($dbh,$table){
		$this->table = $table;
		$this->dbh = $dbh;
	}
	function all(){
		if( isset($_REQUEST['excel']) ){
			$retval = $this->dbh->q_read($q = "SELECT * FROM " . $this->table . "");
		} else {
			$paginationArray = $this->setUpPagination();
			$retval = $this->dbh->q_read($q = "SELECT * FROM " . $this->table . " limit ?,?;",$paginationArray);
		}
		if(isset($_REQUEST['debug'])){
			print_r($this->dbh->errorInfo());exit;
		}
		return $retval;
	}
	function create($query){
		return $this->$dbh->query($query);
	}
	function raw_query($query,$values=null){
		if( ($values!=null) && (gettype($values)!='array') ){
			$error = compile_error("<p>Los valores a insertar deben ser un arreglo con los valores en el orden de insercion.</p>");
			Throw new Exception($error);
		} else {
			$retval = $this->dbh->query($query,$values);
			if( !empty($this->dbh->errorInfo()[2]) ){
				return $this->dbh->errorInfo()[2];
			}
			return $retval;
		}
	}
	function byField($where,$exclude=null,$order=null){
		/* $where = array('nombreCampo'=>'valorCampo') */
		$query = "SELECT * FROM ".$this->table;//." WHERE ";

		$fields = array();
		$values = array();

		$antiFields = array();
		$antiValues = array();
		$whereString = "";
		if( $where!=null ){
			if( gettype($where)=='array' ){
				$query.=" WHERE ";
				$whereString.=" WHERE ";
				foreach( $where as $k=>$v ){
					$fields[]="$k=?";
					$values[] = $v;
				}
				$query.=join(' AND ',$fields);
				$whereString.=join(' AND ',$fields);
			} else {
				$error = compile_error("El método sólo acepta los valores como arreglo.\nUso: $[clase]->query->byField(array([CAMPO]=>[VALOR]))");
				Throw new Exception($error);
			}
		}
		if( $exclude!=null ){
			if( gettype($exclude)=='array' ){
				if( !empty($fields) ){
					$query.=" AND ";
					$whereString.=" AND ";
				} else {
					$query.=" WHERE ";
					$whereString.=" WHERE ";
				}
				foreach( $exclude as $k=>$v ){
					$antiFields[]="$k!=?"; // OJO!!: dice !=, por lo tanto los resultados con ese valor quedan fuera
					// no guardar en el arreglo values aun para depurar errores.
					$antiValues[] = $v;
				}
				$query.=join(' AND ',$antiFields);
				$whereString.=join(' AND ',$antiFields);
			} else {
				$error = compile_error("El método 'byField' sólo acepta los valores como arreglo.\nUso: $[clase]->query->byField(array([CAMPO]=>[VALOR]), array([CAMPO A EXCLUIR]=>[VALOR A EXCLUIR]))");
				Throw new Exception($error);
			}
		}
		$values = array_merge($values,$antiValues);
		$paginationArray = $this->setUpPagination($whereString,$values);
		if($order!=null){
			$query.=" ORDER BY ".$order;
		}
		$query.=" LIMIT ?,?";
		$values = array_merge($values,$paginationArray);
		return $this->dbh->q_read($query,$values);
	}
	function setUpPagination($cond=null,$values=null){
		global $view;
		if( $cond==null ){
			$cond = " WHERE 1=1";
			// No queremos accidentes...
			$values = null;
		} else {
			if( !is_array($values) ){
				$error = compile_error("La condicion de debe armar con prepare, por lo que los valores deben venir en formato de arrgelo");
				Throw new Exception($error);
			} else {
				// "prepare" no funciona con arreglos asociativos ya que estamos usando el operador '?'.
				$values = array_values($values);
			}
		}
		/* ELEMENTOS A MOSTRAR Y PAGINA ACTUAL */
		$limit = null;
		$page = 0;
		if( !isset($_REQUEST['porPagina']) ){
			$limit="10";
		} else {
			$limit = (int)$_REQUEST['porPagina'];
		}
		if( !isset($_REQUEST['pagina']) ){
			$page = 1;
		} else {
			$page = (int)$_REQUEST['pagina'];
		}
		$offset = $limit*($page-1);
		/* CANTIDAD DE PAGINAS DISPONIBLES */
		$table = $this->table;
		$pages = $this->dbh->q_read("SELECT COUNT(1) AS total FROM $table $cond;",$values);
		if( isset($_REQUEST['debug']) ){
			echo $cond;
			print_r($this->dbh->errorInfo());
			exit;
		}
		$totalElements = $pages[0]['total'];
		$pages = ceil($totalElements/$limit);
		$maxPages = $pages;
		$tmp = array();
		for( $i=0;$i<$pages;$i++ ){
			$cur = $i==($page-1);
			$tmp[] = array(
				"value" => ($i+1),
				"current" => $cur
			);
		}
		$pages = $tmp;
		$view->set("pagina","$page");
		$view->set("pages",$pages);
		$view->set("porPagina",$limit);
		// PREV
		if( $page>1 ){
			$view->set('PREV',true);
		}
		// NExT
		if( $page<$maxPages ){
			$view->set('NEXT',true);
		}
		/* SE DEVUELVE SOLO LOS LIMITES PARA USARLOS EN EL SELECT */
		return array($offset,$limit);
	}
}
/* MODELOS BASE DE DATOS */
include_once 'Banner.model.php';
include_once 'Cliente.model.php';
include_once 'Texto.model.php';
include_once 'Proyecto.model.php';
include_once 'Galeria.model.php';
include_once 'Configuracion.model.php';

?>
