<?php
require_once './clases/auto.php';
$array_autos = Auto::traerJSON('./archivos/autos.json');
if ($array_autos) {
  foreach ($array_autos as $obj) {
    echo $obj->toJSON() . ",\n";
  }
} else {
  echo "Error: no se ha encontrado el recurso";
}