<?php

require_once './clases/autoBD.php';
$auto_json = $_POST['auto_json'];
$data = json_decode($auto_json);
$auto = new AutoBD($data->patente, $data->marca, $data->color, $data->precio);
$exito = $auto->modificar();
$obj = new stdClass();
$obj->exito = $exito;

if ($exito) {
  $obj->mensaje = "Auto modificado exitosamente en la base de datos";
} else {
  $obj->mensaje = "Ha ocurrido un error y no se ha modificado la base de datos";
}

echo json_encode($obj, JSON_PRETTY_PRINT);