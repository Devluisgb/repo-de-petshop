<?php
// Incluye una vez el archivo "header.php". Esto suele ser un archivo común para encabezados HTML o estructuras de página.
include_once "includes/header.php";

// Requiere el archivo de conexión a la base de datos. Asumiremos que este archivo contiene la variable $conexion para conectarse a la base de datos.
require "../conexion.php";

// Realiza una consulta SQL para obtener todos los registros de la tabla "usuario".
$usuarios = mysqli_query($conexion, "SELECT * FROM usuario");

// Obtiene el número total de filas (registros) devueltas por la consulta de usuarios.
$totalU = mysqli_num_rows($usuarios);

// Realiza una consulta SQL para obtener todos los registros de la tabla "cliente".
$clientes = mysqli_query($conexion, "SELECT * FROM cliente");

// Obtiene el número total de filas (registros) devueltas por la consulta de clientes.
$totalC = mysqli_num_rows($clientes);

// Realiza una consulta SQL para obtener todos los registros de la tabla "producto".
$productos = mysqli_query($conexion, "SELECT * FROM producto");

// Obtiene el número total de filas (registros) devueltas por la consulta de productos.
$totalP = mysqli_num_rows($productos);

// Realiza una consulta SQL para obtener todos los registros de la tabla "ventas".
$ventas = mysqli_query($conexion, "SELECT * FROM ventas");

// Obtiene el número total de filas (registros) devueltas por la consulta de ventas.
$totalV = mysqli_num_rows($ventas);
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray">Panel de Administración</h1>
</div>


<div class="row">
    <a class="col-xl-3 col-md-6 mb-4" href="usuarios.php">
        <div class="card border-left-primary shadow h-100 py-2 bg-warning">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Usuarios</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $totalU; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>


    <a class="col-xl-3 col-md-6 mb-4" href="clientes.php">
        <div class="card border-left-success shadow h-100 py-2 bg-success">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Clientes</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $totalC; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>


    <a class="col-xl-3 col-md-6 mb-4" href="productos.php">
        <div class="card border-left-info shadow h-100 py-2 bg-primary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Productos</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-white"><?php echo $totalP; ?></div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>


    <a class="col-xl-3 col-md-6 mb-4" href="ventas.php">
        <div class="card border-left-warning bg-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">Ventas</div>
                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $totalV; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-white-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <div class="col-lg-6">
        <div class="au-card m-b-30">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-40">Productos más vendidos</h3>
                <canvas id="polarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>