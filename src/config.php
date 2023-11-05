<?php
// Incluye el archivo "header.php" para cargar la cabecera del sitio.
include_once "includes/header.php";

// Incluye el archivo "conexion.php" para establecer la conexión a la base de datos.
require_once "../conexion.php";

// Obtiene el ID de usuario almacenado en la variable de sesión 'idUser'.
$id_user = $_SESSION['idUser'];

// Define el permiso necesario para acceder a esta parte del código.
$permiso = "configuracion";

// Realiza una consulta a la base de datos para verificar si el usuario tiene el permiso 'configuracion'.
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");

// Obtiene todas las filas resultantes de la consulta.
$existe = mysqli_fetch_all($sql);

// Comprueba si no existen resultados y si el ID de usuario no es igual a 1.
if (empty($existe) && $id_user != 1) {
    // Redirige al usuario a la página "permisos.php" si no tiene el permiso necesario.
    header("Location: permisos.php");
}

// Realiza una consulta para obtener los datos de configuración desde la base de datos.
$query = mysqli_query($conexion, "SELECT * FROM configuracion");

// Obtiene una fila de resultados como un array asociativo.
$data = mysqli_fetch_assoc($query);

// Verifica si se ha enviado un formulario mediante el método POST.
if ($_POST) {
    $alert = '';

    // Comprueba si algún campo del formulario está vacío y muestra una alerta de error si es así.
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['email']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-danger" role="alert">
            Todo los campos son obligatorios
        </div>';
    } else {
        // Si todos los campos están completos, se procesan los datos del formulario.
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $id = $_POST['id'];

        // Realiza una consulta para actualizar los datos de configuración en la base de datos.
        $update = mysqli_query($conexion, "UPDATE configuracion SET nombre = '$nombre', telefono = '$telefono', email = '$email', direccion = '$direccion' WHERE id = $id");

        // Muestra una alerta de éxito si la actualización se realiza con éxito.
        if ($update) {
            $alert = '<div class="alert alert-success" role="alert">
            Datos modificados
        </div>';
        }
    }
}
?>


<div class="row">
<div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Datos de la Empresa
                </div>
                <div class="card-body">
                    <form action="" method="post" class="p-3">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="hidden" name="id" value="<?php echo $data['id'] ?>">
                            <input type="text" name="nombre" class="form-control" value="<?php echo $data['nombre']; ?>" id="txtNombre" placeholder="Nombre de la Empresa" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="number" name="telefono" class="form-control" value="<?php echo $data['telefono']; ?>" id="txtTelEmpresa" placeholder="teléfono de la Empresa" required>
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico:</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>" id="txtEmailEmpresa" placeholder="Correo de la Empresa" required>
                        </div>
                        <div class="form-group">
                            <label>Dirección:</label>
                            <input type="text" name="direccion" class="form-control" value="<?php echo $data['direccion']; ?>" id="txtDirEmpresa" placeholder="Dirreción de la Empresa" required>
                        </div>
                        <?php echo isset($alert) ? $alert : ''; ?>
                        <div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Modificar Datos</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
</div>
<?php include_once "includes/footer.php"; ?>