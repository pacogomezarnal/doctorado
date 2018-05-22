<?php
namespace Daw\models;
use Mysqli;
/**
 * Plantilla vacia de Db
 */
class Db
{
  //Datos del servidor
  private $server = "localhost";
  private $db = "evento";
  private $user = "root";
  private $pass = "";
  private $table = "registro";

  //Datos de la conexion
  private $conector=false;
  private $error=false;
  private $errorMsg="";

  function __construct()
  {
    $this->conector = new mysqli($this->server, $this->user, $this->pass, $this->db);
    if ($this->conector->connect_errno) {
        $this->errorMsg="Fallo al conectar a MySQL: (" . $this->conector->connect_errno . ") " . $this->conector->connect_error;
        $this->error=true;
    }
  }

  //Getters de las propiedades de la conexion
  public function getConector(){
    return $this->conector;
  }
  public function getError(){
    return $this->error;
  }
  public function getErrorMsg(){
    return $this->errorMsg;
  }

  //Realizamos una nueva consulta
  public function consulta($sql){
    if(!$this->error){
      $resultado=$this->conector->query($sql);
    }else{
      return false;
    }
  }

}

?>
