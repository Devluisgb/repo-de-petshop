<?php
// Incluye el archivo "header.php" para cargar la cabecera del sitio.
include_once "includes/header.php";

// Incluye el archivo "conexion.php" para establecer la conexión a la base de datos.
include "../conexion.php";

// Obtiene el ID de usuario almacenado en la variable de sesión 'idUser'.
$id_user = $_SESSION['idUser'];

// Define el permiso necesario para acceder a esta parte del código.
$permiso = "productos";

// Realiza una consulta a la base de datos para verificar si el usuario tiene el permiso 'productos'.
$sql = mysqli_query($conexion, "SELECT p.*, d.* FROM permisos p INNER JOIN detalle_permisos d ON p.id = d.id_permiso WHERE d.id_usuario = $id_user AND p.nombre = '$permiso'");

// Obtiene todas las filas resultantes de la consulta.
$existe = mysqli_fetch_all($sql);

// Comprueba si no existen resultados y si el ID de usuario no es igual a 1.
if (empty($existe) && $id_user != 1) {
    // Redirige al usuario a la página "permisos.php" si no tiene el permiso necesario.
    header("Location: permisos.php");
}

if (!empty($_POST)) {
    $codigo = $_POST['codigo'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $usuario_id = $_SESSION['idUser'];
    $alert = "";

    // Comprueba si algún campo del formulario está vacío o si el precio o la cantidad son menores que 0, y muestra una alerta de error si es así.
    if (empty($codigo) || empty($producto) || empty($precio) || $precio < 0 || empty($cantidad) || $cantidad < 0) {
        $alert = '<div class="alert alert-danger" role="alert">
            Todos los campos son obligatorios
        </div>';
    } else {
        // Realiza una consulta para verificar si el código del producto ya existe en la base de datos.
        $query = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo = '$codigo'");
        $result = mysqli_fetch_array($query);

        if ($result > 0) {
            // Muestra una alerta de advertencia si el código del producto ya existe.
            $alert = '<div class="alert alert-warning" role="alert">
                El código ya existe
            </div>';
        } else {
            // Inserta un nuevo producto en la base de datos.
            $query_insert = mysqli_query($conexion, "INSERT INTO producto(codigo, descripcion, precio, existencia, usuario_id) values ('$codigo', '$producto', '$precio', '$cantidad', '$usuario_id')");
            
            if ($query_insert) {
                // Muestra una alerta de éxito si el producto se registra con éxito.
                $alert = '<div class="alert alert-success" role="alert">
                    Producto Registrado
                </div>';
            } else {
                // Muestra una alerta de error si hay un problema al registrar el producto.
                $alert = '<div class="alert alert-danger" role="alert">
                    Error al registrar el producto
                </div>';
            }
        }
    }
}
?>

 <button class="btn btn-primary mb-2" type="button" data-toggle="modal" data-target="#nuevo_producto"><i class="fas fa-plus"></i></button>
 <?php echo isset($alert) ? $alert : ''; ?>
 <div class="table-responsive">
     <table class="table table-striped table-bordered" id="tbl">
         <thead class="thead-dark">
             <tr>
                 <th>#</th>
                 <th>Código</th>
                 <th>Producto</th>
                 <th>Precio</th>
                 <th>Stock</th>
                 <th>Estado</th>
                 <th></th>
             </tr>
         </thead>
         <tbody>
             <?php
                include "../conexion.php";

                $query = mysqli_query($conexion, "SELECT * FROM producto");
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
                         <td><?php echo $data['codproducto']; ?></td>
                         <td><?php echo $data['codigo']; ?></td>
                         <td><?php echo $data['descripcion']; ?></td>
                         <td><?php echo $data['precio']; ?></td>
                         <td><?php echo $data['existencia']; ?></td>
                         <td><?php echo $estado ?></td>
                         <td>
                             <?php if ($data['estado'] == 1) { ?>
                                 <a href="agregar_producto.php?id=<?php echo $data['codproducto']; ?>" class="btn btn-primary"><i class='fas fa-audio-description'></i></a>

                                 <a href="editar_producto.php?id=<?php echo $data['codproducto']; ?>" class="btn btn-success"><i class='fas fa-edit'></i></a>

                                 <form action="eliminar_producto.php?id=<?php echo $data['codproducto']; ?>" method="post" class="confirmar d-inline">
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
 <div id="nuevo_producto" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header bg-primary text-white">
                 <h5 class="modal-title" id="my-modal-title">Nuevo Producto</h5>
                 <button class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 <form action="" method="post" autocomplete="off">
                     <?php echo isset($alert) ? $alert : ''; ?>
                     <div class="form-group">
                         <label for="codigo">Código de Barras</label>
                         <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control">
                     </div>
                     <div class="form-group">
                         <label for="producto">Producto</label>
                         <input type="text" placeholder="Ingrese nombre del producto" name="producto" id="producto" class="form-control">
                     </div>
                     <div class="form-group">
                         <label for="precio">Precio</label>
                         <input type="text" placeholder="Ingrese precio" class="form-control" name="precio" id="precio">
                     </div>
                     <div class="form-group">
                         <label for="cantidad">Cantidad</label>
                         <input type="number" placeholder="Ingrese cantidad" class="form-control" name="cantidad" id="cantidad">
                     </div>
                     <input type="submit" value="Guardar Producto" class="btn btn-primary">
                 </form>
             </div>
         </div>
     </div>
 </div>

 <?php include_once "includes/footer.php"; ?>
