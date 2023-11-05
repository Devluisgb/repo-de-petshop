<?php
// Incluye el archivo "header.php" para cargar la cabecera del sitio.
include_once "includes/header.php";

// Incluye el archivo "conexion.php" para establecer la conexión a la base de datos.
include "../conexion.php";

// Obtiene el ID de usuario almacenado en la variable de sesión 'idUser'.
$id_user = $_SESSION['idUser'];

// Define el permiso necesario para acceder a esta parte del código.
$permiso = "clientes";

// Realiza una consulta a la base de datos para verificar si el usuario tiene el permiso 'clientes'.
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");

// Obtiene todas las filas resultantes de la consulta.
$existe = mysqli_fetch_all($sql);

// Comprueba si no existen resultados y si el ID de usuario no es igual a 1.
if (empty($existe) && $id_user != 1) {
    // Redirige al usuario a la página "permisos.php" si no tiene el permiso necesario.
    header("Location: permisos.php");
}

// Verifica si se ha enviado un formulario mediante el método POST.
if (!empty($_POST)) {
    $alert = "";

    // Comprueba si algún campo del formulario está vacío y muestra una alerta de error si es así.
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son requeridos</div>';
    } else {
        $idcliente = $_POST['id'];
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];

        // Realiza una consulta para actualizar los datos del cliente en la base de datos.
        $sql_update = mysqli_query($conexion, "UPDATE cliente SET nombre = '$nombre', telefono = '$telefono', direccion = '$direccion' WHERE idcliente = $idcliente");

        // Muestra una alerta de éxito si la actualización se realiza con éxito, de lo contrario, muestra una alerta de error.
        if ($sql_update) {
            $alert = '<div class="alert alert-success" role="alert">Cliente Actualizado correctamente</div>';
        } else {
            $alert = '<div class="alert alert-danger" role="alert">Error al Actualizar el Cliente</div>';
        }
    }
}

// Mostrar Datos

// Verifica si no se ha proporcionado un ID en la solicitud (REQUEST).
if (empty($_REQUEST['id'])) {
    // Redirige al usuario a la página "clientes.php".
    header("Location: clientes.php");
}

// Obtiene el ID del cliente de la solicitud (REQUEST).
$idcliente = $_REQUEST['id'];

// Realiza una consulta para obtener los datos del cliente con el ID proporcionado.
$sql = mysqli_query($conexion, "SELECT * FROM cliente WHERE idcliente = $idcliente");

// Obtiene el número de filas resultantes de la consulta.
$result_sql = mysqli_num_rows($sql);

// Comprueba si no se encontraron resultados y redirige al usuario a la página "clientes.php".
if ($result_sql == 0) {
    header("Location: clientes.php");
} else {
    // Si se encontraron resultados, obtiene los datos del cliente y los almacena en variables.
    if ($data = mysqli_fetch_array($sql)) {
        $idcliente = $data['idcliente'];
        $nombre = $data['nombre'];
        $telefono = $data['telefono'];
        $direccion = $data['direccion'];
    }
}
?>


<div class="container-fluid">

    <div class="row">
        <div class="col-lg-6 m-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Modificar Cliente
                </div>
                <div class="card-body">
                    <form class="" action="" method="post">
                        <?php echo isset($alert) ? $alert : ''; ?>
                        <input type="hidden" name="id" value="<?php echo $idcliente; ?>">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" placeholder="Ingrese Nombre" name="nombre" class="form-control" id="nombre" value="<?php echo $nombre; ?>">
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="number" placeholder="Ingrese Teléfono" name="telefono" class="form-control" id="telefono" value="<?php echo $telefono; ?>">
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" placeholder="Ingrese Direccion" name="direccion" class="form-control" id="direccion" value="<?php echo $direccion; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i> Editar Cliente</button>
                        <a href="clientes.php" class="btn btn-danger">Atras</a>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div>
<!-- /.container-fluid -->
<?php include_once "includes/footer.php"; ?>