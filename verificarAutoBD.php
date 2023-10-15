<?php
require_once './clases/autoBD.php';
$obj_auto = $_POST['obj_auto'];
$data = json_decode($obj_auto);
$auto = new AutoBD($data->patente);
$array_autos = AutoBD::traer();
if ($auto->existe($array_autos)) {
  echo $auto->toJSON();
} else {
  echo json_encode(new stdClass, JSON_PRETTY_PRINT);
}