<?php
// Incluye una vez el archivo "header.php". Esto suele ser un archivo común para encabezados HTML o estructuras de página.
include_once "includes/header.php";

// Incluye el archivo de conexión a la base de datos.
include "../conexion.php";

// Obtiene el ID del usuario de la sesión actual.
$id_user = $_SESSION['idUser'];

// Define el permiso requerido para acceder a esta página ("productos" en este caso).
$permiso = "productos";

// Realiza una consulta SQL para verificar si el usuario tiene el permiso necesario para acceder a esta página.
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");

// Obtiene los resultados de la consulta.
$existe = mysqli_fetch_all($sql);

// Si el usuario no tiene el permiso requerido y no es el usuario con ID 1 (supuesto superadmin), redirige a la página "permisos.php".
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}

// Comprueba si se proporciona un ID de producto en la URL.
if (empty($_GET['id'])) {
    header("Location: productos.php"); // Redirige a la página "productos.php" si no se proporciona un ID.
} else {
    $id_producto = $_GET['id'];

    // Verifica si el ID proporcionado es un número válido.
    if (!is_numeric($id_producto)) {
        header("Location: productos.php"); // Redirige a la página "productos.php" si el ID no es válido.
    }

    // Realiza una consulta SQL para obtener los detalles del producto con el ID proporcionado.
    $consulta = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
    
    // Obtiene los datos del producto.
    $data_producto = mysqli_fetch_assoc($consulta);
}

// Comprueba si se ha enviado un formulario (POST).
if (!empty($_POST)) {
    $alert = "";

    // Comprueba si los campos del formulario no están vacíos.
    if (!empty($_POST['cantidad']) || !empty($_POST['precio']) || !empty($_POST['producto_id'])) {
        $precio = $_POST['precio'];
        $cantidad = $_POST['cantidad'];
        $producto_id = $_GET['id'];
        $total = $cantidad + $data_producto['existencia'];

        // Realiza una consulta SQL para actualizar la existencia del producto.
        $query_insert = mysqli_query($conexion, "UPDATE producto SET existencia = $total WHERE codproducto = $id_producto");

        // Comprueba si la consulta se ejecutó correctamente.
        if ($query_insert) {
            $alert = '<div class="alert alert-success" role="alert">
                        Stock actualizado
                    </div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">
                        Error al ingresar la cantidad
                    </div>';
        }

        // Cierra la conexión a la base de datos.
        mysqli_close($conexion);
    } else {
        $alert = '<div class="alert alert-danger" role="alert">
                        Todos los campos son obligatorios
                    </div>';
    }
}
?>

<div class="row">
    <div class="col-lg-6 m-auto">
        <div class="card">
            <div class="card-header bg-primary">
                Agregar Producto
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="form-group">
                        <label for="precio">Precio Actual</label>
                        <input type="text" class="form-control" value="<?php echo $data_producto['precio']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="precio">Cantidad de productos Disponibles</label>
                        <input type="number" class="form-control" value="<?php echo $data_producto['existencia']; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="precio">Nuevo Precio</label>
                        <input type="text" placeholder="Ingrese nombre del precio" name="precio" class="form-control" value="<?php echo $data_producto['precio']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Agregar Cantidad</label>
                        <input type="number" placeholder="Ingrese cantidad" name="cantidad" id="cantidad" class="form-control">
                    </div>

                    <input type="submit" value="Actualizar" class="btn btn-primary">
                    <a href="productos.php" class="btn btn-danger">Regresar</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>