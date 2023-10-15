<?php

interface IParte2
{
  static function eliminar(string $patente): bool;
  function modificar(): bool;
}