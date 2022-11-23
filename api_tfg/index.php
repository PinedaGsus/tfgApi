<?php
require_once "clases/conexion/conexion.php";

$conexion = new conexion;

$query = "select * from valoraciones limit 5";

print_r($conexion->obtenerDatos($query));

?>
