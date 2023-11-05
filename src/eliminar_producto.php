<?php
// Inicia la sesión.
session_start();

// Incluye el archivo "conexion.php" para establecer la conexión a la base de datos.
require("../conexion.php");

// Obtiene el ID de usuario almacenado en la variable de sesión 'idUser'.
$id_user = $_SESSION['idUser'];

// Define el permiso necesario para acceder a esta parte del código.
$permiso = "usuarios";

// Realiza una consulta a la base de datos para verificar si el usuario tiene el permiso 'usuarios'.
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");

// Obtiene todas las filas resultantes de la consulta.
$existe = mysqli_fetch_all($sql);

// Comprueba si no existen resultados y si el ID de usuario no es igual a 1.
if (empty($existe) && $id_user != 1) {
    // Redirige al usuario a la página "permisos.php" si no tiene el permiso necesario.
    header("Location: permisos.php");
}

// Verifica si se ha proporcionado un ID en la solicitud (GET).
if (!empty($_GET['id'])) {
    // Obtiene el ID del producto desde la solicitud (GET).
    $id = $_GET['id'];

    // Realiza una consulta para actualizar el estado del producto a 0 (eliminación lógica) en la base de datos.
    $query_delete = mysqli_query($conexion, "UPDATE producto SET estado = 0 WHERE codproducto = $id");

    // Cierra la conexión a la base de datos.
    mysqli_close($conexion);

    // Redirige al usuario a la página "productos.php" después de realizar la eliminación.
    header("Location: productos.php");
}
?>
