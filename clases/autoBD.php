<?php
require_once './clases/auto.php';
require_once './clases/IParte1.php';
require_once './clases/IParte2.php';
require_once './clases/IParte3.php';

class AutoBD extends Auto implements IParte1, IParte2, IParte3
{

  protected string|null $pathFoto;

  public function __construct(
    string $patente = null,
    string $marca = null,
    string $color = null,
    float $precio = null,
    string|null $pathFoto = null
  ) {
    parent::__construct($patente, $marca, $color, $precio);
    $this->pathFoto = $pathFoto;
  }

  public function toJSON()
  {
    $data_json = parent::toJSON();
    $data = json_decode($data_json);
    $data->pathFoto = $this->pathFoto;
    $data_json = json_encode($data, JSON_PRETTY_PRINT);
    return $data_json;
  }

  public function agregar(): bool
  {
    $returnValue = false;
    $dbname = "garage_bd";
    $host = "localhost";
    $dsn = "mysql:host=$host;dbname=$dbname";
    $user = "root";
    $pw = "";

    try {
      $pdo = new PDO($dsn, $user, $pw);
      $table = "autos";
      $columns = "patente, marca, color, precio, foto";
      $values = ":patente, :marca, :color, :precio, :foto";
      $query = "INSERT INTO `$table` ($columns) VALUES ($values)";
      $stmt = $pdo->prepare($query);

      if ($stmt) {

        $foto = $_FILES['foto'];
        $patente = $this->patente;
        $date = new DateTime('now', new DateTimeZone('GMT-3'));
        $time = $date->format('His');
        $pathinfo = pathinfo($foto['name']);
        $extension = $pathinfo['extension'];
        $upload_dir = './autos/imagenes/';
        $filename = "$patente.$time.$extension";
        $this->pathFoto = $upload_dir . $filename;
        move_uploaded_file($foto['tmp_name'], $this->pathFoto);

        $params = array(
          "patente" => [
            "value" => $this->patente,
            "type" => PDO::PARAM_STR,
            "maxLength" => 30
          ],
          "marca" => [
            "value" => $this->marca,
            "type" => PDO::PARAM_STR,
            "maxLength" => 30
          ],
          "color" => [
            "value" => $this->color,
            "type" => PDO::PARAM_STR,
            "maxLength" => 15
          ],
          "precio" => [
            "value" => $this->precio,
            "type" => PDO::PARAM_STR,
            "maxLength" => 0
          ],
          "foto" => [
            "value" => $this->pathFoto,
            "type" => PDO::PARAM_STR,
            "maxLength" => 50
          ]
        );

        foreach ($params as $paramKey => $paramValue) {
          $stmt->bindParam(":$paramKey", $paramValue["value"], $paramValue["type"], $paramValue["maxLength"]);
        }

        $result = $stmt->execute();

        if ($result) {
          $returnValue = true;
        }

      }
    } catch (PDOException $err) {
      echo $err->getMessage();
    }

    return $returnValue;
  }

  public static function traer(): array
  {
    $returnValue = false;
    $dbname = "garage_bd";
    $host = "localhost";
    $dsn = "mysql:host=$host;dbname=$dbname";
    $user = "root";
    $pw = "";

    try {
      $pdo = new PDO($dsn, $user, $pw);
      $table = "autos";
      $stmt = $pdo->query("SELECT * FROM $table");
      $resultSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $array_autos = array();

      foreach ($resultSet as $row) {
        $obj = new stdClass();
        foreach ($row as $key => $value) {
          $obj->$key = $value;
        }
        $auto = new AutoBD($obj->patente, $obj->marca, $obj->color, $obj->precio, $obj->foto);
        array_push($array_autos, $auto);
      }

      $returnValue = $array_autos;

    } catch (PDOException $err) {
      echo $err->getMessage();
    }

    return $returnValue;
  }

  public static function eliminar(string $patente): bool
  {
    $returnValue = false;
    $dbname = "garage_bd";
    $host = "localhost";
    $dsn = "mysql:host=$host;dbname=$dbname";
    $user = "root";
    $pw = "";

    try {
      $pdo = new PDO($dsn, $user, $pw);
      $table = "autos";
      $query = "DELETE FROM $table WHERE patente=:patente";
      $stmt = $pdo->prepare($query);

      if ($stmt) {
        $stmt->bindParam(":patente", $patente, PDO::PARAM_STR);
        $result = $stmt->execute();

        if ($result && $stmt->rowCount()) {
          $returnValue = true;
        }
      }

    } catch (PDOException $err) {
      echo $err->getMessage();
    }

    return $returnValue;
  }

  public function modificar(): bool
  {
    $returnValue = false;
    $dbname = "garage_bd";
    $host = "localhost";
    $dsn = "mysql:host=$host;dbname=$dbname";
    $user = "root";
    $pw = "";

    try {
      $pdo = new PDO($dsn, $user, $pw);
      $table = "autos";
      $set = "marca=:marca,color=:color,precio=:precio,foto=:foto";
      $query = "UPDATE $table SET $set WHERE patente=:patente";
      $stmt = $pdo->prepare($query);

      if ($stmt) {

        if (isset($_FILES['foto'])) {
          $foto = $_FILES['foto'];
          $patente = $this->patente;
          $date = new DateTime('now', new DateTimeZone('GMT-3'));
          $time = $date->format('His');
          $pathinfo = pathinfo($foto['name']);
          $extension = $pathinfo['extension'];
          $upload_dir = './autos/imagenes/';
          $filename = "$patente.$time.$extension";
          $pathFoto = $upload_dir . $filename;
          move_uploaded_file($foto['tmp_name'], $pathFoto);
          $this->pathFoto = $pathFoto;
        }

        $params = array(
          "marca" => [
            "value" => $this->marca,
            "type" => PDO::PARAM_STR,
            "maxLength" => 30
          ],
          "color" => [
            "value" => $this->color,
            "type" => PDO::PARAM_STR,
            "maxLength" => 15
          ],
          "precio" => [
            "value" => $this->precio,
            "type" => PDO::PARAM_STR,
            "maxLength" => 0
          ],
          "foto" => [
            "value" => $this->pathFoto,
            "type" => PDO::PARAM_STR,
            "maxLength" => 50
          ],
          "patente" => [
            "value" => $this->patente,
            "type" => PDO::PARAM_STR,
            "maxLength" => 30
          ]
        );

        foreach ($params as $paramKey => $paramValue) {
          $stmt->bindParam(":$paramKey", $paramValue["value"], $paramValue["type"], $paramValue["maxLength"]);
        }

        $result = $stmt->execute();

        if ($result && $stmt->rowCount()) {
          $returnValue = true;
        }
      }
    } catch (PDOException $err) {
      echo $err->getMessage();
    }

    return $returnValue;
  }

  function existe(array $autos): bool
  {

    foreach ($autos as $auto) {
      if ($auto->patente === $this->patente) {

        // ???? ESTO NO PUEDE ESTAR BIEN
        $this->marca = $auto->marca;
        $this->color = $auto->color;
        $this->precio = $auto->precio;
        $this->pathFoto = $auto->pathFoto;

        return true;
      }
    }

    return false;
  }

  function guardarEnArchivo(): bool
  {
    $date = new DateTime("now", new DateTimeZone('GMT-3'));
    $time = $date->format('His');
    $extension = "jpg";
    $foto_name = "$this->patente.$this->marca.borrado.$time.$extension";
    $from = $this->pathFoto;
    $to = "./autosBorrados/$foto_name";
    rename($from, $to);

    $filename = './archivos/autosbd_borrados.txt';
    $data =
      'patente:' . $this->patente . "\n" .
      'marca:' . $this->marca . "\n" .
      'color:' . $this->color . "\n" .
      'precio:' . $this->precio . "\n" .
      'pathFoto:' . $to . "\n" .
      '-------------------------------------------------------------------------------------------------------' . "\n";

    if (!file_exists($filename)) {
      $encabezado = "AUTOS BORRADOS\n-------------------------------------------------------------------------------------------------------\n";
      $file_contents = $encabezado . $data;
    } else {
      $file_contents = file_get_contents($filename);
      $file_contents .= $data;
    }

    return file_put_contents($filename, $file_contents) ? true : false;
  }

}