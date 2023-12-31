<?php

interface IParte3
{

  // ● existe: retorna true, si la instancia actual está en el array de objetos de tipo AutoBD que recibe como
  // parámetro (comparar por patente). Caso contrario retorna false.
  function existe(array $autos): bool;

  // ● guardarEnArchivo: escribirá en un archivo de texto (./archivos/autosbd_borrados.txt) toda la información
  // del auto más la nueva ubicación de la foto. La foto se moverá al subdirectorio “./autosBorrados/”, con el
  // nombre formado por la patente punto 'borrado' punto hora, minutos y segundos del borrado (Ejemplo:
  // AYF714.renault.borrado.105905.jpg).
  // Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
  function guardarEnArchivo(): bool;

}