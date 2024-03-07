<?php
// Iniciar la sesión
session_start();

// Verificar si el tipo de usuario está definido en la sesión
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'Administrador') {
    // Redirigir a la página de inicio si no hay tipo de usuario definido o si no es Administrador
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
    <title>DIF | Inicio</title>
    <!-- Incluir el archivo de estilos CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="../assets/css/tarjeta.css">
    
    <?php
    // Cargar el encabezado correspondiente según el tipo de usuario
    include('../includes/header.php');
    ?>
</head>
<body>

    <?php
    // Incluir las tarjetas específicas para el Administrador
    include('../cards/cards.php');
    ?>
</body>
<?php include('../includes/footer.php');?>

</html>
