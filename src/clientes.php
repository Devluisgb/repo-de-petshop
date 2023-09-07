<?php
// Incluye una vez el archivo "includes/header.php". Se utiliza para cargar una estructura común de encabezado en la página.
include_once "includes/header.php";

// Incluye el archivo "../conexion.php" para establecer la conexión a la base de datos.
include "../conexion.php";

// Obtiene el ID de usuario de la sesión actual.
$id_user = $_SESSION['idUser'];

// Define el permiso requerido para acceder a esta página (en este caso, "clientes").
$permiso = "clientes";

// Realiza una consulta SQL para verificar si el usuario tiene el permiso "clientes".
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");

// Obtiene los resultados de la consulta y los almacena en la variable $existe.
$existe = mysqli_fetch_all($sql);

// Si el usuario no tiene el permiso "clientes" y no es el usuario con ID 1 (superadmin), se redirige a la página "permisos.php".
if (empty($existe) && $id_user != 1) {
    header("Location: permisos.php");
}

// Comprueba si se ha enviado un formulario (POST).
if (!empty($_POST)) {
    $alert = "";

    // Comprueba si los campos requeridos (nombre, teléfono y dirección) están vacíos.
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-danger" role="alert">
                    Todos los campos son obligatorios
                </div>';
    } else {
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $usuario_id = $_SESSION['idUser'];

        $result = 0;

        // Realiza una consulta para verificar si el cliente ya existe en la base de datos.
        $query = mysqli_query($conexion, "SELECT * FROM cliente WHERE nombre = '$nombre'");
        $result = mysqli_fetch_array($query);

        // Si el cliente ya existe, muestra un mensaje de error.
        if ($result > 0) {
            $alert = '<div class="alert alert-danger" role="alert">
                        El cliente ya existe
                    </div>';
        } else {
            // Si el cliente no existe, realiza una inserción en la base de datos.
            $query_insert = mysqli_query($conexion, "INSERT INTO cliente(nombre,telefono,direccion, usuario_id) values ('$nombre', '$telefono', '$direccion', '$usuario_id')");

            // Comprueba si la inserción se realizó con éxito y muestra un mensaje de éxito o error.
            if ($query_insert) {
                $alert = '<div class="alert alert-success" role="alert">
                            Cliente registrado
                        </div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                            Error al registrar
                        </div>';
            }
        }
    }

    // Cierra la conexión a la base de datos.
    mysqli_close($conexion);
}
?>

<button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#nuevo_cliente"><i class="fas fa-plus"></i></button>
<?php echo isset($alert) ? $alert : ''; ?>
<div class="table-responsive">
    <table class="table table-striped table-bordered" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            include "../conexion.php";

            $query = mysqli_query($conexion, "SELECT * FROM cliente");
            $result = mysqli_num_rows($query);
            if ($result > 0) {
                while ($data = mysqli_fetch_assoc($query)) {
                    if ($data['estado'] == 1) {
                        $estado = '<span class="badge badge-pill badge-success">Activo</span>';
                    } else {
                        $estado = '<span class="badge badge-pill badge-danger">Inactivo</span>';
                    }
            ?>
                    <tr>
                        <td><?php echo $data['idcliente']; ?></td>
                        <td><?php echo $data['nombre']; ?></td>
                        <td><?php echo $data['telefono']; ?></td>
                        <td><?php echo $data['direccion']; ?></td>
                        <td><?php echo $estado; ?></td>
                        <td>
                            <?php if ($data['estado'] == 1) { ?>
                                <a href="editar_cliente.php?id=<?php echo $data['idcliente']; ?>" class="btn btn-success"><i class='fas fa-edit'></i></a>
                                <form action="eliminar_cliente.php?id=<?php echo $data['idcliente']; ?>" method="post" class="confirmar d-inline">
                                    <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i> </button>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>

    </table>
</div>
<div id="nuevo_cliente" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="my-modal-title">Nuevo Cliente</h5>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" placeholder="Ingrese Nombre" name="nombre" id="nombre" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="number" placeholder="Ingrese Teléfono" name="telefono" id="telefono" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" placeholder="Ingrese Direccion" name="direccion" id="direccion" class="form-control">
                    </div>
                    <input type="submit" value="Guardar Cliente" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "includes/footer.php"; ?>