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

// Verifica si se ha enviado un formulario mediante el método POST.
if (!empty($_POST)) {
  $alert = "";

  // Comprueba si algún campo del formulario está vacío y muestra una alerta de error si es así.
  if (empty($_POST['codigo']) || empty($_POST['producto']) || empty($_POST['precio'])) {
    $alert = '<div class="alert alert-primary" role="alert">
              Todos los campos son requeridos
            </div>';
  } else {
    // Obtiene el ID del producto desde la solicitud (GET).
    $codproducto = $_GET['id'];
    $codigo = $_POST['codigo'];
    $producto = $_POST['producto'];
    $precio = $_POST['precio'];

    // Realiza una consulta para actualizar los datos del producto en la base de datos.
    $query_update = mysqli_query($conexion, "UPDATE producto SET codigo = '$codigo', descripcion = '$producto', precio= $precio WHERE codproducto = $codproducto");

    // Muestra una alerta de éxito si la actualización se realiza con éxito, de lo contrario, muestra una alerta de error.
    if ($query_update) {
      $alert = '<div class="alert alert-primary" role="alert">
              Producto Modificado
            </div>';
    } else {
      $alert = '<div class="alert alert-primary" role="alert">
                Error al Modificar
              </div>';
    }
  }
}

// Validar producto

// Verifica si no se ha proporcionado un ID en la solicitud (REQUEST).
if (empty($_REQUEST['id'])) {
  // Redirige al usuario a la página "productos.php".
  header("Location: productos.php");
} else {
  // Obtiene el ID del producto desde la solicitud (REQUEST).
  $id_producto = $_REQUEST['id'];

  // Comprueba si el ID del producto es un valor numérico.
  if (!is_numeric($id_producto)) {
    header("Location: productos.php");
  }

  // Realiza una consulta para obtener los datos del producto con el ID proporcionado.
  $query_producto = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");

  // Obtiene el número de filas resultantes de la consulta.
  $result_producto = mysqli_num_rows($query_producto);

  // Comprueba si se encontraron resultados y almacena los datos del producto en una variable.
  if ($result_producto > 0) {
    $data_producto = mysqli_fetch_assoc($query_producto);
  } else {
    // Redirige al usuario a la página "productos.php" si no se encuentra el producto.
    header("Location: productos.php");
  }
}
?>

<div class="row">
  <div class="col-lg-6 m-auto">

    <div class="card">
      <div class="card-header bg-primary text-white">
        Modificar producto
      </div>
      <div class="card-body">
        <form action="" method="post">
          <?php echo isset($alert) ? $alert : ''; ?>
          <div class="form-group">
            <label for="codigo">Código de Barras</label>
            <input type="text" placeholder="Ingrese código de barras" name="codigo" id="codigo" class="form-control" value="<?php echo $data_producto['codigo']; ?>">
          </div>
          <div class="form-group">
            <label for="producto">Producto</label>
            <input type="text" class="form-control" placeholder="Ingrese nombre del producto" name="producto" id="producto" value="<?php echo $data_producto['descripcion']; ?>">

          </div>
          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="text" placeholder="Ingrese precio" class="form-control" name="precio" id="precio" value="<?php echo $data_producto['precio']; ?>">

          </div>
          <input type="submit" value="Actualizar Producto" class="btn btn-primary">
          <a href="productos.php" class="btn btn-danger">Atras</a>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include_once "includes/footer.php"; ?>