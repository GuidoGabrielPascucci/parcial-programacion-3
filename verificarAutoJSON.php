<?php
require_once './clases/auto.php';
$patente = $_POST['patente'];
$verificacion = Auto::verificarAutoJSON($patente);
$obj = json_decode($verificacion);
if ($obj->exito) {
  $mensaje = "Auto verificado exitosamente. Se verifica que " . $obj->mensaje;
} else {
  $mensaje = "Se ha ingresado la patente $patente, y se verifica que " . $obj->mensaje ;
}
$jsonResponse = ["exito" => $obj->exito, "mensaje" => $mensaje];
echo json_encode($jsonResponse, JSON_PRETTY_PRINT);