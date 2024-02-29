<?php
// includes/autenticacion.php
session_start();

function autenticarUsuario($usuario, $contrasena, $conexion) {
    $consulta = "SELECT * FROM usuarios WHERE FullName = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        if (password_verify($contrasena, $fila['Passwo'])) {
            $_SESSION['tipo_usuario'] = $fila['Puesto'];

            // Verificar el tipo de usuario antes de redirigir
            if ($_SESSION['tipo_usuario'] == 'Administrador') {
                header('Location: dashboard.php');
                exit();
            } else {
                return 'Tipo de usuario no autorizado.';
            }
        } else {
            return 'Credenciales incorrectas';
        }
    } else {
        return 'Credenciales incorrectas';
    }

    $stmt->close();
}
?>
