<?php

require_once './clases/autoBD.php';

switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $array_autos_borrados = array();
    $filename = './archivos/autosbd_borrados.txt';
    $fp = fopen($filename, 'r');
    $obj = null;
    $i = 0;

    while (!feof($fp)) {
      $linea = fgets($fp);

      if ($i === 0) {
        $obj = new stdClass();
      }

      if ($linea && str_contains($linea, ':')) {
        $arr = explode(':', $linea);
        $formated_array = str_replace("\n", "", $arr);
        $prop = $formated_array[0];
        $value = $formated_array[1];
        $obj->$prop = $value;
        $i++;
      }

      if ($i === 5) {
        array_push($array_autos_borrados, $obj);
        $obj = null;
        $i = 0;
      }
    }

    echo generarTabla($array_autos_borrados);
    break;

  case 'POST':
    $auto_json = $_POST['auto_json'];
    $data = json_decode($auto_json);
    $exito = AutoBD::eliminar($data->patente);

    if ($exito) {
      $auto = new AutoBD($data->patente, $data->marca, $data->color, $data->precio, $data->pathFoto);
      $guardado = $auto->guardarEnArchivo();
      if ($guardado) {
        $data_response = [ "exito" => true, "mensaje" => "Se ha eliminado el auto exitosamente" ];
        echo json_encode($data_response, JSON_PRETTY_PRINT);
      }
    } else {
      $data_response = [ "exito" => false, "mensaje" => "No se ha podido eliminar el registro" ];
      echo json_encode($data_response, JSON_PRETTY_PRINT);
    }
    break;
}

function generarTabla($data) {

  $table_style = "'width:80%;margin:20px auto;border:5px solid black;font-size:25px;font-family:system-ui;text-align:center;border-spacing:0px;font-weight:400;border-collapse:collapse;'";
  $thead_style = "'font-size:33px;color:#f0f0f0;background-color:#0064a9;height:100px'";
  $tbody_style = "'background-color:#eeeeee'";

  function th_style($px) {
    return "style='min-width:$px\\px;'";
  }

  $rows = cargarDatos($data);

  $table = "
  <table style=$table_style>
    <thead style=$thead_style>
      <tr>
        <th " . th_style(70) . ">Patente</th>
        <th " . th_style(200) . ">Marca</th>
        <th " . th_style(200) . ">Color</th>
        <th " . th_style(170) . ">Precio</th>
        <th " . th_style(200) . ">Foto</th>
      </tr>
    </thead>
    <tbody style=$tbody_style>
      $rows
    </tbody>
  </table>
  ";

  return $table;
}

function cargarDatos($autos) {

  $rows = "";

  foreach ($autos as $auto) {

    $array_atributos = array(
      "patente" => $auto->patente,
      "marca" => $auto->marca,
      "color" => $auto->color,
      "precio" => $auto->precio,
      "foto" => $auto->pathFoto
    );

    $tableRow = "<tr style='height:60px;border:1px solid gray'>";

    foreach ($array_atributos as $atributo_key => $atributo_value) {
      $td = "<td style='border:1px solid gray'>";
      if ($atributo_key != 'foto') {
        $td .= $atributo_value;
      } else if ($atributo_key == 'foto' && $atributo_value !== null){
        $td .= "<img src='$atributo_value' style='width:200px;height:200px;object-fit:cover;'>";
      } else {
        $td .= "-";
      }
      $td .= "</td>";
      $tableRow .= $td;
    }

    $tableRow .= "</tr>";
    $rows .= $tableRow;
  }

  return $rows;
}