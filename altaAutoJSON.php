<?php
require_once './clases/auto.php';
$patente = $_POST['patente'];
$marca = $_POST['marca'];
$color = $_POST['color'];
$precio = $_POST['precio'];
$auto = new Auto($patente, $marca, $color, $precio);
echo $auto->guardarJSON('./archivos/autos.json');