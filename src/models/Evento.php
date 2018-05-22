<?php
namespace Daw\models;
/**
 * Plantilla vacia de Db
 */
class evento extends Db
{

  function __construct()
  {
    parent::__construct();
  }

  //Realizamos un nuevo Registro
  public function nuevoRegistro($nombre){
    $consulta="INSERT INTO registro (nombre) ";
    $consulta=$consulta."VALUES ('$nombre')";
    return parent::consulta($consulta);
  }

}

?>
