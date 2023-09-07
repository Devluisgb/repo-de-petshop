<?php
// Incluye una vez el archivo "header.php". Esto suele ser un archivo común para encabezados HTML o estructuras de página.
include_once "includes/header.php";

// Requiere una vez el archivo de conexión a la base de datos.
require_once "../conexion.php";

// Obtiene el ID del usuario de la sesión actual.
$id_user = $_SESSION['idUser'];

// Define el permiso requerido para acceder a esta página ("ventas" en este caso).
$permiso = "ventas";

// Realiza una consulta SQL para verificar si el usuario tiene el permiso necesario para acceder a esta página.
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");

// Obtiene los resultados de la consulta.
$existe = mysqli_fetch_all($sql);

// Si el usuario no tiene el permiso requerido y no es el usuario con ID 1 (supuesto superadmin), redirige a la página "permisos.php".
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}

// Realiza una consulta SQL para obtener los datos de ventas y la información de los clientes asociados.
$query = mysqli_query($conexion, "SELECT v.*, c.idcliente, c.nombre FROM ventas v INNER JOIN cliente c ON v.id_cliente = c.idcliente");
?>

<table class="table table-light" id="tbl">
    <thead class="thead-dark">
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Fecha</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Itera a través de los resultados de la consulta y muestra los datos en una tabla.
        while ($row = mysqli_fetch_assoc($query)) { 
        ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nombre']; ?></td>
                <td><?php echo $row['total']; ?></td>
                <td><?php echo $row['fecha']; ?></td>
                <td>
                    <a href="pdf/generar.php?cl=<?php echo $row['id_cliente'] ?>&v=<?php echo $row['id'] ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php
// Incluye una vez el archivo "footer.php". Esto suele ser un archivo común para los pies de página en HTML.
include_once "includes/footer.php";
?>
