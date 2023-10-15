<?php
require_once './clases/autoBD.php';
$auto_json = $_POST['auto_json'];
$data = json_decode($auto_json);
$auto = new AutoBD($data->patente, $data->marca, $data->color, $data->precio);
$exito = AutoBD::eliminar($data->patente);
$obj = new stdClass();
$obj->exito = false;

if ($exito) {
  $json = $auto->guardarJSON('./archivos/autos_eliminados.json');
  $data = json_decode($json);
  
  if ($data->exito) {
    $obj->exito = true;
    $obj->mensaje = "Auto eliminado de la base de datos y agregado al archivo 'autos_eliminados.json'";
  } else {
    $obj->mensaje = "Auto eliminado de la base de datos, pero no se pudo agregar al archivo 'autos_eliminados.json'";
  }

} else {
  $obj->mensaje = "Error, el auto no ha sido eliminado";
}

echo json_encode($obj, JSON_PRETTY_PRINT);