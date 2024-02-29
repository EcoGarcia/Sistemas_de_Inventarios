function validarFormulario() {
    var nombre = document.getElementById('nombre').value;
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;
    var tipoUsuario = document.getElementById('tipo_usuario').value;

    // Validación básica (puedes personalizar según tus necesidades)
    if (nombre.trim() === '' || email.trim() === '' || password.trim() === '' || tipoUsuario.trim() === '') {
        alert('Por favor, completa todos los campos.');
        return false; // Evita que el formulario se envíe si hay campos vacíos
    }

    // Puedes agregar más validaciones aquí...

    return true; // Envía el formulario si pasa todas las validaciones
}