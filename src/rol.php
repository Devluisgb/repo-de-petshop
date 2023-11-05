<?php
// Incluye el archivo "header.php" para cargar la cabecera del sitio.
include_once "includes/header.php";

// Incluye el archivo "conexion.php" para establecer la conexión a la base de datos.
require_once "../conexion.php";

// Obtiene el ID del usuario desde la solicitud (GET).
$id = $_GET['id'];

// Realiza una consulta para obtener todos los permisos disponibles.
$sqlpermisos = mysqli_query($conexion, "SELECT * FROM permisos");

// Realiza una consulta para obtener los datos del usuario con el ID proporcionado.
$usuarios = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $id");

// Realiza una consulta para obtener los permisos asignados al usuario con el ID proporcionado.
$consulta = mysqli_query($conexion, "SELECT * FROM detalle_permisos WHERE id_usuario = $id");

// Obtiene el número de filas resultantes de la consulta de usuarios.
$resultUsuario = mysqli_num_rows($usuarios);

// Comprueba si no se encontraron resultados y redirige al usuario a la página "usuarios.php".
if (empty($resultUsuario)) {
    header("Location: usuarios.php");
}

// Inicializa un array vacío para almacenar los permisos asignados al usuario.
$datos = array();

// Recorre los resultados de la consulta de permisos asignados y los almacena en el array $datos.
foreach ($consulta as $asignado) {
    $datos[$asignado['id_permiso']] = true;
}

// Verifica si se ha enviado un formulario mediante el método POST.
if (isset($_POST['permisos'])) {
    $id_user = $_GET['id'];
    $permisos = $_POST['permisos'];

    // Elimina todos los permisos asignados previamente al usuario con el ID proporcionado.
    mysqli_query($conexion, "DELETE FROM detalle_permisos WHERE id_usuario = $id_user");

    if ($permisos != "") {
        // Recorre los permisos seleccionados en el formulario y los asigna al usuario.
        foreach ($permisos as $permiso) {
            $sql = mysqli_query($conexion, "INSERT INTO detalle_permisos(id_usuario, id_permiso) VALUES ($id_user, $permiso)");

            if ($sql == 1) {
                // Redirige al usuario a la página "rol.php" con un mensaje de éxito.
                header("Location: rol.php?id=" . $id_user . "&m=si");
            } else {
                $alert = '<div class="alert alert-primary" role="alert">
                            Error al actualizar permisos
                        </div>';
            }
        }
    }
}
?>


<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-warning text-white">
                Permisos
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <?php if(isset($_GET['m']) && $_GET['m'] == 'si') { ?>
                        <div class="alert alert-success" role="alert">
                            Permisos actualizado
                        </div>

                    <?php } ?>
                    <?php while ($row = mysqli_fetch_assoc($sqlpermisos)) { ?>
                        <div class="form-check form-check-inline m-4">
                            <label for="permisos" class="p-2 text-uppercase"><?php echo $row['nombre']; ?></label>
                            <input id="permisos" type="checkbox" name="permisos[]" value="<?php echo $row['id']; ?>" <?php
                                                                                                                                                    if (isset($datos[$row['id']])) {
                                                                                                                                                        echo "checked";
                                                                                                                                                    }
                                                                                                                                                    ?>>
                        </div>
                    <?php } ?>
                    <br>
                    <button class="btn btn-primary btn-block" type="submit">Modificar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>