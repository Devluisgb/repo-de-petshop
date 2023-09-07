<?php
// Incluye el archivo de conexión a la base de datos "../conexion.php" para establecer la conexión.
require_once "../conexion.php";

// Inicia la sesión para permitir el acceso a variables de sesión.
session_start();

// Verifica si se ha enviado un parámetro GET llamado 'q'.
if (isset($_GET['q'])) {
    $datos = array();
    $nombre = $_GET['q'];

    // Realiza una consulta SQL para buscar clientes cuyos nombres coincidan parcialmente con la cadena proporcionada.
    // También verifica que el cliente esté activo (estado = 1).
    $cliente = mysqli_query($conexion, "SELECT * FROM cliente WHERE nombre LIKE '%$nombre%' AND estado = 1");

    // Itera a través de los resultados y almacena los datos en un array.
    while ($row = mysqli_fetch_assoc($cliente)) {
        $data['id'] = $row['idcliente'];
        $data['label'] = $row['nombre'];
        $data['direccion'] = $row['direccion'];
        $data['telefono'] = $row['telefono'];
        array_push($datos, $data);
    }

    // Devuelve los datos en formato JSON y finaliza el script.
    echo json_encode($datos);
    die();
} else if (isset($_GET['pro'])) {
    $datos = array();
    $nombre = $_GET['pro'];

    // Realiza una consulta SQL para buscar productos cuyos códigos o descripciones coincidan parcialmente con la cadena proporcionada.
    // También verifica que el producto esté activo (estado = 1).
    $producto = mysqli_query($conexion, "SELECT * FROM producto WHERE codigo LIKE '%" . $nombre . "%' OR descripcion LIKE '%" . $nombre . "%' AND estado = 1");

    // Itera a través de los resultados y almacena los datos en un array.
    while ($row = mysqli_fetch_assoc($producto)) {
        $data['id'] = $row['codproducto'];
        $data['label'] = $row['codigo'] . ' - ' .$row['descripcion'];
        $data['value'] = $row['descripcion'];
        $data['precio'] = $row['precio'];
        $data['existencia'] = $row['existencia'];
        array_push($datos, $data);
    }

    // Devuelve los datos en formato JSON y finaliza el script.
    echo json_encode($datos);
    die();
} else if (isset($_GET['detalle'])) {
    $id = $_SESSION['idUser'];
    $datos = array();

    // Realiza una consulta SQL para obtener los detalles de los productos temporales para un usuario específico.
    $detalle = mysqli_query($conexion, "SELECT d.*, p.codproducto, p.descripcion FROM detalle_temp d INNER JOIN producto p ON d.id_producto = p.codproducto WHERE d.id_usuario = $id");

    // Realiza una consulta para calcular el total a pagar.
    $sumar = mysqli_query($conexion, "SELECT total, SUM(total) AS total_pagar FROM detalle_temp WHERE id_usuario = $id");

    // Itera a través de los detalles de los productos y almacena los datos en un array.
    while ($row = mysqli_fetch_assoc($detalle)) {
        $data['id'] = $row['id'];
        $data['descripcion'] = $row['descripcion'];
        $data['cantidad'] = $row['cantidad'];
        $data['precio_venta'] = $row['precio_venta'];
        $data['sub_total'] = number_format($row['precio_venta'] * $row['cantidad'], 2, '.', ',');
        array_push($datos, $data);
    }

    // Devuelve los datos en formato JSON y finaliza el script.
    echo json_encode($datos);
    die();
} else if (isset($_GET['delete_detalle'])) {
    $id_detalle = $_GET['id'];

    // Verifica si hay más de una unidad de un producto en el carrito.
    $verificar = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id = $id_detalle");
    $datos = mysqli_fetch_assoc($verificar);
    
    if ($datos['cantidad'] > 1) {
        $cantidad = $datos['cantidad'] - 1;

        // Actualiza la cantidad en el carrito.
        $query = mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = $cantidad WHERE id = $id_detalle");

        if ($query) {
            $msg = "restado";
        } else {
            $msg = "Error";
        }
    } else {
        // Elimina el producto del carrito si solo hay una unidad.
        $query = mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id = $id_detalle");

        if ($query) {
            $msg = "ok";
        } else {
            $msg = "Error";
        }
    }

    // Devuelve el mensaje y finaliza el script.
    echo $msg;
    die();
} else if (isset($_GET['procesarVenta'])) {
    $id_cliente = $_GET['id'];
    $id_user = $_SESSION['idUser'];

    // Obtiene el total a pagar consultando el carrito.
    $consulta = mysqli_query($conexion, "SELECT total, SUM(total) AS total_pagar FROM detalle_temp WHERE id_usuario = $id_user");
    $result = mysqli_fetch_assoc($consulta);
    $total = $result['total_pagar'];

    // Inserta una nueva venta en la base de datos.
    $insertar = mysqli_query($conexion, "INSERT INTO ventas(id_cliente, total, id_usuario) VALUES ($id_cliente, '$total', $id_user)");

    if ($insertar) {
        // Obtiene el ID de la venta recién insertada.
        $id_maximo = mysqli_query($conexion, "SELECT MAX(id) AS total FROM ventas");
        $resultId = mysqli_fetch_assoc($id_maximo);
        $ultimoId = $resultId['total'];

        // Obtiene los detalles de los productos en el carrito.
        $consultaDetalle = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id_usuario = $id_user");

        while ($row = mysqli_fetch_assoc($consultaDetalle)) {
            $id_producto = $row['id_producto'];
            $cantidad = $row['cantidad'];
            $precio = $row['precio_venta'];

            // Inserta los detalles de la venta en la tabla "detalle_venta".
            $insertarDet = mysqli_query($conexion, "INSERT INTO detalle_venta(id_producto, id_venta, cantidad, precio) VALUES ($id_producto, $ultimoId, $cantidad, '$precio')");

            // Actualiza el stock de los productos.
            $stockActual = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $id_producto");
            $stockNuevo = mysqli_fetch_assoc($stockActual);
            $stockTotal = $stockNuevo['existencia'] - $cantidad;
            $stock = mysqli_query($conexion, "UPDATE producto SET existencia = $stockTotal WHERE codproducto = $id_producto");
        } 

        if ($insertarDet) {
            // Elimina los productos del carrito después de completar la venta.
            $eliminar = mysqli_query($conexion, "DELETE FROM detalle_temp WHERE id_usuario = $id_user");
            $msg = array('id_cliente' => $id_cliente, 'id_venta' => $ultimoId);
        } 
    } else {
        $msg = array('mensaje' => 'error');
    }

    // Devuelve un mensaje en formato JSON y finaliza el script.
    echo json_encode($msg);
    die();
}

// Verifica si se ha enviado un formulario (POST).
if (isset($_POST['action'])) {
    $id = $_POST['id'];
    $cant = $_POST['cant'];
    $precio = $_POST['precio'];
    $id_user = $_SESSION['idUser'];
    $total = $precio * $cant;

    // Verifica si el producto ya está en el carrito del usuario.
    $verificar = mysqli_query($conexion, "SELECT * FROM detalle_temp WHERE id_producto = $id AND id_usuario = $id_user");
    $result = mysqli_num_rows($verificar);
    $datos = mysqli_fetch_assoc($verificar);

    if ($result > 0) {
        $cantidad = $datos['cantidad'] + 1;
        $total_precio = $cantidad * $total;

        // Actualiza la cantidad y el total en el carrito.
        $query = mysqli_query($conexion, "UPDATE detalle_temp SET cantidad = $cantidad, total = '$total_precio' WHERE id_producto = $id AND id_usuario = $id_user");

        if ($query) {
            $msg = "actualizado";
        } else {
            $msg = "Error al ingresar";
        }
    } else {
        // Inserta un nuevo producto en el carrito.
        $query = mysqli_query($conexion, "INSERT INTO detalle_temp(id_usuario, id_producto, cantidad, precio_venta, total) VALUES ($id_user, $id, $cant, '$precio', $total)");

        if ($query) {
            $msg = "registrado";
        } else {
            $msg = "Error al ingresar";
        }
    }

    // Devuelve un mensaje en formato JSON y finaliza el script.
    echo json_encode($msg);
    die();
}

// Verifica si se ha enviado un formulario de cambio de contraseña.
if (isset($_POST['cambio'])) {
    if (empty($_POST['actual']) || empty($_POST['nueva'])) {
        $msg = 'Los campos están vacíos';
    } else {
        $id = $_SESSION['idUser'];
        $actual = md5($_POST['actual']);
        $nueva = md5($_POST['nueva']);

        // Verifica si la contraseña actual es correcta.
        $consulta = mysqli_query($conexion, "SELECT * FROM usuario WHERE clave = '$actual' AND idusuario = $id");
        $result = mysqli_num_rows($consulta);

        if ($result == 1) {
            // Cambia la contraseña del usuario.
            $query = mysqli_query($conexion, "UPDATE usuario SET clave = '$nueva' WHERE idusuario = $id");

            if ($query) {
                $msg = 'ok';
            } else {
                $msg = 'error';
            }
        } else {
            $msg = 'dif';
        }
    }

    // Devuelve un mensaje y finaliza el script.
    echo $msg;
    die();
}
?>
