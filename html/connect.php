<?php
class SQLHelper extends PDO{
    public function __construct(){
        $db = 'sqlite:'.PATH.'/db/db.sqlite'; // SQLITE
        parent::__construct($db);
    }
    public function query($query,$data=null){
        $result = array();
        if( $query==null || empty($query) ){
            return false;
        }
        $cur = $this->prepare($query);
        if( !empty($this->errorInfo()[2]) ){
            return false;
        }
        if( $cur->execute($data) ){
            while( $row = $cur->fetch(PDO::FETCH_ASSOC) ){
                $result[] = $row;
            }
            if( empty($result) && $this->lastInsertId()!=0){
                return $this->lastInsertId();
            }
        } else {
            return false;
        }
        return $result;
    }
}
$dbh = new SQLHelper();
?>
