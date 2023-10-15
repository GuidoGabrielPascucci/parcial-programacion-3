<?php
require_once './clases/autoBD.php';
$autos = AutoBD::traer();

if ($autos) {
  $array_objs = array();

  foreach ($autos as $obj_autoBD) {
    $json = $obj_autoBD->toJSON();
    $data_obj = json_decode($json);
    array_push($array_objs, $data_obj);
  }

  if (isset($_GET['tabla']) && $_GET['tabla'] === 'mostrar') {
    echo generarTabla($array_objs);
  } else {
    foreach ($array_objs as $obj) {
      echo json_encode($obj, JSON_PRETTY_PRINT) . "\n";
    }
  }

} else {
  echo "No se ha podido traer la información, disculpe las molestias ocasionadas";
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