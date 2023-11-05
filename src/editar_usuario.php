<?php
// Incluye el archivo "header.php" para cargar la cabecera del sitio.
include_once "includes/header.php";

// Incluye el archivo "conexion.php" para establecer la conexión a la base de datos.
require "../conexion.php";

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

// Verifica si se ha enviado un formulario mediante el método POST.
if (!empty($_POST)) {
    $alert = "";

    // Comprueba si algún campo del formulario está vacío y muestra una alerta de error si es así.
    if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario'])) {
        $alert = '<div class="alert alert-danger" role="alert">Todos los campos son requeridos</div>';
    } else {
        // Obtiene el ID del usuario desde la solicitud (GET).
        $idusuario = $_GET['id'];
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $usuario = $_POST['usuario'];

        // Realiza una consulta para actualizar los datos del usuario en la base de datos.
        $sql_update = mysqli_query($conexion, "UPDATE usuario SET nombre = '$nombre', correo = '$correo', usuario = '$usuario' WHERE idusuario = $idusuario");

        // Muestra una alerta de éxito si la actualización se realiza con éxito.
        $alert = '<div class="alert alert-success" role="alert">Usuario Actualizado</div>';
    }
}

// Mostrar Datos

// Verifica si no se ha proporcionado un ID en la solicitud (REQUEST).
if (empty($_REQUEST['id'])) {
    // Redirige al usuario a la página "usuarios.php".
    header("Location: usuarios.php");
}
$idusuario = $_REQUEST['id'];

// Realiza una consulta para obtener los datos del usuario con el ID proporcionado.
$sql = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $idusuario");

// Obtiene el número de filas resultantes de la consulta.
$result_sql = mysqli_num_rows($sql);

// Comprueba si no se encontraron resultados y redirige al usuario a la página "usuarios.php".
if ($result_sql == 0) {
    header("Location: usuarios.php");
} else {
    // Si se encontraron resultados, obtiene los datos del usuario y los almacena en variables.
    if ($data = mysqli_fetch_array($sql)) {
        $idcliente = $data['idusuario'];
        $nombre = $data['nombre'];
        $correo = $data['correo'];
        $usuario = $data['usuario'];
    }
}
?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Modificar Usuario
            </div>
            <div class="card-body">
                <form class="" action="" method="post">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <input type="hidden" name="id" value="<?php echo $idusuario; ?>">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" placeholder="Ingrese nombre" class="form-control" name="nombre" id="nombre" value="<?php echo $nombre; ?>">

                    </div>
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="text" placeholder="Ingrese correo" class="form-control" name="correo" id="correo" value="<?php echo $correo; ?>">

                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuario</label>
                        <input type="text" placeholder="Ingrese usuario" class="form-control" name="usuario" id="usuario" value="<?php echo $usuario; ?>">   

                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-edit"></i></button>
                    <a href="usuarios.php" class="btn btn-danger">Atras</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>