<?php
require './clases/autoBD.php';
$patente = $_POST['patente'];
$marca = $_POST['marca'];
$color = $_POST['color'];
$precio = $_POST['precio'];

$auto = new AutoBD($patente, $marca, $color, $precio);
$array_autos = AutoBD::traer();

$exito = false;
$mensaje = "";

if ($array_autos) {

  if ($auto->existe($array_autos)) {
    $mensaje = "Ya existe un auto con la patente ingresada";
  } else if ($auto->agregar()) {
    $mensaje = "El auto ha sido agregado a la base de datos";
    $exito = true;
  } else {
    $mensaje = "Error inesperado; el auto no ha sido agregado a la base de datos";
  }

}

$data = ["exito" => $exito, "mensaje" => $mensaje];
echo json_encode($data, JSON_PRETTY_PRINT);