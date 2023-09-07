<?php
session_start(); // Iniciar o reanudar la sesión

if (!empty($_SESSION['active'])) { // Verificar si la variable de sesión 'active' no está vacía
    header('location: src/'); // Redirigir al usuario a la carpeta 'src/'
} else {
    if (!empty($_POST)) { // Verificar si se ha enviado una solicitud POST
        $alert = ''; // Variable para almacenar mensajes de alerta

        if (empty($_POST['usuario']) || empty($_POST['clave'])) { // Verificar si los campos de usuario o clave están vacíos
            $alert = '<div class="alert alert-danger" role="alert">
            Ingrese su usuario y su clave
            </div>'; // Establecer mensaje de alerta si los campos están vacíos
        } else {
            require_once "conexion.php"; // Incluir el archivo "conexion.php" para la conexión a la base de datos

            $user = mysqli_real_escape_string($conexion, $_POST['usuario']); // Obtener el valor del campo 'usuario' y evitar inyecciones SQL
            $clave = md5(mysqli_real_escape_string($conexion, $_POST['clave'])); // Obtener el valor del campo 'clave' y cifrarlo con md5()

            $query = mysqli_query($conexion, "SELECT * FROM usuario WHERE usuario = '$user' AND clave = '$clave' AND estado = 1"); // Consulta a la base de datos para validar las credenciales

            mysqli_close($conexion); // Cerrar la conexión a la base de datos

            $resultado = mysqli_num_rows($query); // Obtener el número de filas devueltas por la consulta

            if ($resultado > 0) { // Verificar si se encontró al menos un registro que coincida con las credenciales
                $dato = mysqli_fetch_array($query); // Obtener los datos del usuario encontrado

                // Establecer variables de sesión
                $_SESSION['active'] = true;
                $_SESSION['idUser'] = $dato['idusuario'];
                $_SESSION['nombre'] = $dato['nombre'];
                $_SESSION['user'] = $dato['usuario'];

                header('location: src/'); // Redirigir al usuario a la carpeta 'src/'
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                Usuario o Contraseña Incorrecta
                </div>'; // Establecer mensaje de alerta si las credenciales son incorrectas
                session_destroy(); // Destruir la sesión
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Iniciar Sessión</title>
    <link href="assets/css/styles.css" rel="stylesheet" />
    <script src="assets/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header text-center">
                                    <img class="img-thumbnail" src="../assets/img/logokaninos.png" width="160">
                                    <h3 class="font-weight-light my-4">Iniciar Sesión</h3>
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST">
                                        <div class="form-group">
                                            <label class="small mb-1" for="usuario"><i class="fas fa-user"></i> Usuario</label>
                                            <input class="form-control py-4" id="usuario" name="usuario" type="text" placeholder="Ingrese usuario" required />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="clave"><i class="fas fa-key"></i> Contraseña</label>
                                            <input class="form-control py-4" id="clave" name="clave" type="password" placeholder="Ingrese Contraseña" required />
                                        </div>
                                        <div class="alert alert-danger text-center d-none" id="alerta" role="alert">

                                        </div>
                                        <?php echo isset($alert) ? $alert : ''; ?>
                                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">

                </div>
            </footer>
        </div>
    </div>
    <script src="assets/js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/js/scripts.js"></script>
</body>

</html>