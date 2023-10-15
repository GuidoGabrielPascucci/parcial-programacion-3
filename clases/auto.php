<?php

class Auto
{
  protected string $patente;
  protected string|null $marca;
  protected string|null $color;
  protected float|null $precio;

  public function __construct(
    string $patente = null,
    string $marca = null,
    string $color = null,
    float $precio = null
  ) {
    $this->patente = $patente;
    $this->marca = $marca;
    $this->color = $color;
    $this->precio = $precio;
  }

  public function toJSON()
  {
    $arr = array(
      "patente" => $this->patente,
      "marca" => $this->marca,
      "color" => $this->color,
      "precio" => $this->precio
    );
    return json_encode($arr, JSON_PRETTY_PRINT);
  }

  public function guardarJSON(string $path): string
  {
    $resultado = false;
    $filename = $path;
    $nuevoAuto_dataJson = $this->toJSON();

    if (!file_exists($filename)) {
      $obj_json = json_decode($nuevoAuto_dataJson);
      $arrayWrapper_dataJson = array($obj_json);
      $contenido_a_escribir = json_encode($arrayWrapper_dataJson, JSON_PRETTY_PRINT);
    } else {
      $json_content = file_get_contents($filename);
      if (strlen($json_content) > 0) {
        $array_autos = json_decode($json_content);
        $nuevoAuto_dataObject = json_decode($nuevoAuto_dataJson);
        array_push($array_autos, $nuevoAuto_dataObject);
        $contenido_a_escribir = json_encode($array_autos, JSON_PRETTY_PRINT);
      }
    }

    $resultado = file_put_contents($filename, $contenido_a_escribir);

    $mensaje = "";
    if ($resultado) {
      $mensaje = "Auto agregado exitosamente";
    } else {
      $mensaje = "Error! Ha habido un fallo y el auto no ha sido agregado";
    }
    $obj = new stdClass();
    $obj->mensaje = $mensaje;
    $obj->exito = $resultado != false;
    return json_encode($obj);
  }

  public static function traerJSON(string $path): array|bool
  {
    $filename = $path;
    if (!file_exists($filename)) {
      return false;
    }
    $array_autos = array();
    $json_content = file_get_contents($filename);
    $data = json_decode($json_content);

    foreach ($data as $obj) {
      $patente = $obj->patente;
      $marca = $obj->marca;
      $color = $obj->color;
      $precio = $obj->precio;
      $auto = new Auto($patente, $marca, $color, $precio);
      array_push($array_autos, $auto);
    }
    return $array_autos;
  }

  public static function verificarAutoJSON($auto): string
  {
    $obj_estandar = new stdClass();
    $obj_estandar->exito = false;
    $obj_estandar->mensaje = "el auto no esta registrado";
    $array_autos = Auto::traerJSON('./archivos/autos.json');

    foreach ($array_autos as $obj_auto) {
      if ($obj_auto->patente === $auto) {
        $obj_estandar->exito = true;
        $obj_estandar->mensaje = "el auto coincide";
        break;
      }
    }

    return json_encode($obj_estandar, JSON_PRETTY_PRINT);
  }

  public function agregar()
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
      $columns = "patente, marca, color, precio";
      $values = ":patente, :marca, :color, :precio";
      $query = "INSERT INTO $table ($columns) VALUES ($values)";
      $pdoStmt = $pdo->prepare($query);

      if ($pdoStmt) {
        $params = array(
          "patente" => [
            "value" => $this->patente,
            "type" => PDO::PARAM_STR
          ],
          "marca" => [
            "value" => $this->marca,
            "type" => PDO::PARAM_STR
          ],
          "color" => [
            "value" => $this->color,
            "type" => PDO::PARAM_STR
          ],
          "precio" => [
            "value" => $this->precio,
            "type" => PDO::PARAM_STR
          ]
        );

        foreach ($params as $paramKey => $paramValue) {
          $pdoStmt->bindParam(":$paramKey", $paramValue["value"], $paramValue["type"]);
        }

        $result = $pdoStmt->execute();

        if ($result) {
          $returnValue = true;
        }

      }
    } catch (PDOException $err) {
      echo $err->getMessage();
    }

    return $returnValue;
  }


  // Retorna un array de objetos de tipo Usuario, recuperados de la base de datos (con la descripción del perfil correspondiente).
  // public static function TraerTodos() {
  //   $returnValue = false;
  //   $dbname = "usuarios_test";
  //   $host = "localhost";
  //   $dsn = "mysql:host=$host;dbname=$dbname";
  //   $user = "root";
  //   $pw = "root404";

  //   try {
  //     $pdo = new PDO($dsn, $user, $pw);
  //     $array_resultSet = array();

  //     for ($i = 0; $i < 2; $i++) {
  //       switch ($i) {
  //         case 0:
  //           $table = "usuarios";
  //           break;
  //         case 1:
  //           $table = "perfiles";
  //           break;
  //       }
  //       $pdoStatement_obj = $pdo->query("SELECT * FROM `$table`");
  //       $resultSet = $pdoStatement_obj->fetchAll(PDO::FETCH_ASSOC);
  //       array_push($array_resultSet, $resultSet);
  //     }

  //     $resultSet_usuarios = $array_resultSet[0];
  //     $resultSet_perfiles = $array_resultSet[1];
  //     $array_usuarios = array();

  //     foreach ($resultSet_usuarios as $row) {
  //       $usuario = new Usuario();
  //       foreach ($row as $key => $value) {
  //         $usuario->$key = $value;
  //         if ($key === 'id_perfil') {
  //           foreach ($resultSet_perfiles as $perfil) {
  //             if ($perfil['id'] === $value) {
  //               $usuario->perfil = $perfil['descripcion'];
  //             }
  //           }
  //         }
  //       }
  //       array_push($array_usuarios, $usuario);
  //     }

  //     $returnValue = $array_usuarios;

  //   } catch (PDOException $err) {
  //     echo $err->getMessage();
  //   }

  //   return $returnValue;
  // }

  // Retorna un objeto de tipo Usuario, de acuerdo al correo y clave que se reciben en el parámetro $params.
  // public static function TraerUno($params)
  // {
  //   $returnValue = false;
  //   $dbname = "usuarios_test";
  //   $host = "localhost";
  //   $dsn = "mysql:host=$host;dbname=$dbname";
  //   $user = "root";
  //   $pw = "root404";
  //   $correo = $params->correo;
  //   $clave = $params->clave;

  //   try {
  //     $pdo = new PDO($dsn, $user, $pw);
  //     $query = "SELECT * FROM `usuarios` WHERE `correo`=:correo AND `clave`=:clave";
  //     $pdoStmt = $pdo->prepare($query);
  //     $pdoStmt->bindParam(":correo", $correo, PDO::PARAM_STR);
  //     $pdoStmt->bindParam(":clave", $clave, PDO::PARAM_STR);
  //     $success = $pdoStmt->execute();
  //     if ($success) {
  //       $user_data = $pdoStmt->fetch(PDO::FETCH_ASSOC);
  //       if ($user_data) {
  //         $id_perfil = $user_data['id_perfil'];
  //         $query = "SELECT * FROM `perfiles` WHERE `id`=:id";
  //         $pdoStmt = $pdo->prepare($query);
  //         $pdoStmt->bindParam(":id", $id_perfil, PDO::PARAM_INT);
  //         $success = $pdoStmt->execute();
  //         if ($success) {
  //           $perfil_data = $pdoStmt->fetch(PDO::FETCH_ASSOC);
  //           if ($perfil_data) {
  //             $arr = array();
  //             $usuario = new Usuario();
  //             foreach ($user_data as $user_key => $user_value) {
  //               $usuario->$user_key = $user_value;
  //             }
  //             $usuario->perfil = $perfil_data['descripcion'];
  //             $returnValue = $usuario;
  //           }
  //         }
  //       }
  //     }
  //   } catch (PDOException $err) {
  //     echo $err->getMessage();
  //   }

  //   return $returnValue;
  // }
}