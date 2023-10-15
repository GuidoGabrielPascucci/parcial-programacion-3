<?php
require_once './clases/autoBD.php';
$auto_json = $_POST['auto_json'];
$data = json_decode($auto_json, true);
$auto = new AutoBD($data['patente'], $data['marca'], $data['color'], $data['precio']);
$exito = $auto->agregar();

if ($exito) {
  $mensaje = "Auto agregado exitosamente";
} else {
  $mensaje = "El auto no ha sido agregado";
}

$jsonResponse = ["exito" => $exito, "mensaje" => $mensaje];
echo json_encode($jsonResponse, JSON_PRETTY_PRINT);