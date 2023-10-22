<?php
include_once "includes/header.php"; // Incluye el archivo de cabecera

include "../conexion.php"; // Incluye el archivo de conexión a la base de datos

$id_user = $_SESSION['idUser']; // Obtiene el ID del usuario de la sesión actual
$permiso = "usuarios"; // Define el permiso requerido para acceder a esta sección

// Consulta a la base de datos para verificar si el usuario tiene el permiso necesario
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");
$existe = mysqli_fetch_all($sql); // Almacena los resultados de la consulta en un array

// Si el usuario no tiene el permiso y no es el usuario con ID 1 (supervisor), se redirige a la página de permisos
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}

// Si se ha enviado un formulario mediante POST
if (!empty($_POST)) {
    $alert = ""; // Variable para almacenar mensajes de alerta

    // Comprueba si alguno de los campos del formulario está vacío
    if (empty($_POST['nombre']) || empty($_POST['correo']) || empty($_POST['usuario']) || empty($_POST['clave'])) {
        $alert = '<div class="alert alert-danger" role="alert">
        Todo los campos son obligatorios
        </div>';
    } else {
        // Recupera los valores enviados a través del formulario
        $nombre = $_POST['nombre'];
        $email = $_POST['correo'];
        $user = $_POST['usuario'];
        $clave = md5($_POST['clave']); // Encripta la contraseña usando el algoritmo MD5

        // Consulta a la base de datos para verificar si ya existe un usuario con el mismo correo electrónico
        $query = mysqli_query($conexion, "SELECT * FROM usuario where correo = '$email'");
        $result = mysqli_fetch_array($query);

        // Si se encuentra algún resultado en la consulta, muestra un mensaje de advertencia
        if ($result > 0) {
            $alert = '<div class="alert alert-warning" role="alert">
                        El correo ya existe
                    </div>';
        } else {
            // Inserta los datos del nuevo usuario en la base de datos
            $query_insert = mysqli_query($conexion, "INSERT INTO usuario(nombre,correo,usuario,clave) values ('$nombre', '$email', '$user', '$clave')");

            // Si la inserción es exitosa, muestra un mensaje de éxito y redirige a la página de usuarios
            if ($query_insert) {
                $alert = '<div class="alert alert-primary" role="alert">
                            Usuario registrado
                        </div>';
                header("Location: usuarios.php");
            } else {
                // Si ocurre un error durante la inserción, muestra un mensaje de error
                $alert = '<div class="alert alert-danger" role="alert">
                        Error al registrar
                    </div>';
            }
        }
    }  
}



?>

<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#nuevo_usuario"><i class="fas fa-plus"></i></button>
<div id="nuevo_usuario" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="my-modal-title">Nuevo Usuario</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" autocomplete="off">
                    <?php echo isset($alert) ? $alert : ''; ?>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombre" id="nombre">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" placeholder="Ingrese Correo Electrónico" name="correo" id="correo">
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuario</label>
                        <input type="text" class="form-control" placeholder="Ingrese Usuario" name="usuario" id="usuario">
                    </div>
                    <div class="form-group">
                        <label for="clave">Contraseña</label>
                        <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="clave" id="clave">
                    </div>
                    <input type="submit" value="Registrar" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered mt-2" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php ?>
            <?php
// Incluye el archivo de conexión a la base de datos.
include "../conexion.php";

// Realiza una consulta SQL para seleccionar todos los registros de la tabla "usuario" y los ordena por la columna "estado" de forma descendente.
$query = mysqli_query($conexion, "SELECT * FROM usuario ORDER BY estado DESC");

// Obtiene el número total de filas (registros) devueltas por la consulta.
$result = mysqli_num_rows($query);

// Comprueba si la consulta devolvió resultados.
if ($result > 0) {
    // Inicia un bucle while para recorrer cada fila de resultados.
    while ($data = mysqli_fetch_assoc($query)) {
        // Determina el estado del usuario (activo o inactivo) y crea una etiqueta HTML de badge en consecuencia.
        if ($data['estado'] == 1) {
            $estado = '<span class="badge badge-pill badge-success">Activo</span>';
        } else {
            $estado = '<span class="badge badge-pill badge-danger">Inactivo</span>';
        }
?>
        <tr>
            <!-- Muestra los datos del usuario en las celdas de la tabla -->
            <td><?php echo $data['idusuario']; ?></td>
            <td><?php echo $data['nombre']; ?></td>
            <td><?php echo $data['correo']; ?></td>
            <td><?php echo $data['usuario']; ?></td>
            <td><?php echo $estado; ?></td>
            <td>
                <?php if ($data['estado'] == 1) { ?>
                    <!-- Muestra botones para acciones (cambiar rol, editar, eliminar) solo si el usuario está activo. -->
                    <a href="rol.php?id=<?php echo $data['idusuario']; ?>" class="btn btn-warning"><i class='fas fa-key'></i></a>
                    <a href="editar_usuario.php?id=<?php echo $data['idusuario']; ?>" class="btn btn-success"><i class='fas fa-edit'></i></a>
                    <form action="eliminar_usuario.php?id=<?php echo $data['idusuario']; ?>" method="post" class="confirmar d-inline">
                        <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                    </form>
                <?php } ?>
            </td>
        </tr>
<?php
    }
}
?>
</tbody>
</table>
</div>
<?php include_once "includes/footer.php"; ?>
