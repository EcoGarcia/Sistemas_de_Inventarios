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

<h2>Lista de usuarios</h2>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="category-container">

                 <!-- Tarjeta para Usuarios de administracion -->
                 <div class='category-card'>
                        <h5>Administrador (Coordinación de Recursos Materiales)</h5>
                        <a href='../tabla/tabla_usuarios_administracion.php' class='btn btn-second'>Ver los usuarios</a>
                    </div>

                 <!-- Tarjeta para Usuarios de administracion -->
                 <div class='category-card'>
                        <h5>Director del área</h5>
                        <a href='../tabla/Tabla_director.php' class='btn btn-second'>Ver los usuarios</a>
                    </div>

                    <!-- Tarjeta para Usuarios de Dirección -->
                    <div class='category-card'>
                        <h5>Usuarios de Dirección</h5>
                        <a href='../tabla/tabla_usuarios_direccion.php' class='btn btn-second'>Ver los usuarios</a>
                    </div>

                    <!-- Tarjeta para Usuarios de Coordinación -->
                    <div class='category-card'>
                        <h5>Usuarios de Coordinación</h5>
                        <a href='../tabla/tabla_usuarios_coordinacion.php' class='btn btn-second'>Ver los usuarios</a>
                    </div>

                    <!-- Tarjeta para Usuarios de Servicios -->
                    <div class='category-card'>
                        <h5>Usuarios de Servicios</h5>
                        <a href='../tabla/tabla_usuarios_servicios.php' class='btn btn-second'>Ver los usuarios</a>
                    </div>


                </div>
            </div>
        </div>
    </div>
</body>

</html>
