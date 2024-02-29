<?php
session_start();
include('../includes/header.php');

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Total de usuarios</title>
    <!-- Incluir el archivo de estilos CSS -->
    <link rel="stylesheet" href="../assets/css/tarjeta.css">
</head>
<body>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="category-container">

                 <!-- Tarjeta para Usuarios de administracion -->
                 <div class='category-card'>
                        <h3>Usuarios Administrativos</h3>
                        <a href='../tabla/tabla_usuarios_administracion.php' class='btn btn-second'>Ver los administradores</a>
                    </div>

                    <!-- Tarjeta para Usuarios de Dirección -->
                    <div class='category-card'>
                        <h3>Usuarios de Dirección</h3>
                        <a href='../tabla/tabla_usuarios_direccion.php' class='btn btn-second'>Ver la dirección</a>
                    </div>

                    <!-- Tarjeta para Usuarios de Coordinación -->
                    <div class='category-card'>
                        <h3>Usuarios de Coordinación</h3>
                        <a href='../tabla/tabla_usuarios_coordinacion.php' class='btn btn-second'>Ver la coordinación</a>
                    </div>

                    <!-- Tarjeta para Usuarios de Servicios -->
                    <div class='category-card'>
                        <h3>Usuarios de Servicios</h3>
                        <a href='../tabla/tabla_usuarios_servicios.php' class='btn btn-second'>Ver los servicios</a>
                    </div>


                </div>
            </div>
        </div>
    </div>
</body>

</html>
