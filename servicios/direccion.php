<?php
// Iniciar la sesión
session_start();

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario'])) {
    // Redirigir a la página de inicio si no hay tipo de usuario definido
    header('Location: index.php');
    exit();
}

// Obtener el tipo de usuario desde la sesión
$tipo_usuario = $_SESSION['tipo_usuario'];

// Incluir el archivo de conexión a la base de datos
include('../includes/conexion.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Añadir Dirección</title>
    <!-- Incluir el archivo de estilos CSS -->
    <link rel="stylesheet" href="../assets/css/tarjeta.css">
</head>

<body>

    <!-- Incluir la cabecera (header) del documento -->
    <div class="contenedor">
        <!-- Formulario para añadir una dirección -->
        <form action="../guardar/add_direccion.php" method="POST" enctype="multipart/form-data" class="tarjeta contenido">
            <label>Dirección<span style="color:red;">*</span></label>
            <!-- Campo para ingresar el nombre de la categoría (dirección) -->
            <input type="text" name="category_name" placeholder="Añade el nombre de la categoria (dirección)" required>

            <div class="form-group">
                <label>Imagen de portada</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <!-- Botón para enviar el formulario -->
            <input type="submit" value="Añadir">
        </form>
    </div>
</body>

</html>
