<?php
// Definición de los parámetros de conexión a la base de datos.
$host = "localhost"; // Nombre del servidor de la base de datos.
$user = "root"; // Nombre de usuario de la base de datos.
$clave = ""; // Contraseña de la base de datos.
$bd = "petshop"; // Nombre de la base de datos a la que se desea conectar.

// Establece una conexión a la base de datos utilizando los parámetros definidos.
$conexion = mysqli_connect($host, $user, $clave, $bd);

// Verifica si la conexión a la base de datos fue exitosa.
if (mysqli_connect_errno()) {
    echo "No se pudo conectar a la base de datos"; // Muestra un mensaje de error en caso de falla en la conexión.
    exit(); // Finaliza la ejecución del script en caso de error de conexión.
}

// Selecciona la base de datos "petshop" para realizar operaciones en ella.
mysqli_select_db($conexion, $bd) or die("No se encuentra la base de datos");

// Establece el conjunto de caracteres de la conexión a "utf8" para garantizar el soporte de caracteres especiales.
mysqli_set_charset($conexion, "utf8");
?>
