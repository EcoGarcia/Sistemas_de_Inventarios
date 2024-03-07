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

// Definir el encabezado y las tarjetas según el tipo de usuario
if ($tipo_usuario == 'UsuarioCoordinacion') {
    $header = '../includes/header_usuario_coordinacion.php';
    $cards = '../cards/cards_usuario_coordinacion.php';
} else {
    // Manejar caso no reconocido (puedes redirigir a una página de error, por ejemplo)
    header('Location: error.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIF | Inicio</title>
    <!-- Incluir el archivo de estilos CSS -->
    <link rel="stylesheet" type="text/css" href="../assets/css/tarjeta.css">
    <?php include($header); ?>
</head>

<body>
    <?php include($cards); ?>
</body>
<?php include('../includes/footer.php');?>

</html>
