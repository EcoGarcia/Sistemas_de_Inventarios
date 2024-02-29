// Espera a que el contenido del documento HTML esté completamente cargado
document.addEventListener("DOMContentLoaded", function () {
  
  // Obtiene la referencia al elemento de entrada de contraseña por su atributo 'name'
  var passwordInput = document.querySelector('[name="contrasena"]');
  
  // Obtiene la referencia al elemento de botón de alternar contraseña por su ID
  var togglePassword = document.getElementById("togglePassword");

  // Agrega un evento de clic al botón de alternar contraseña
  togglePassword.addEventListener("click", function () {
    // Verifica si el tipo de entrada de contraseña es "password"
    if (passwordInput.type === "password") {
      // Si es "password", cambia el tipo a "text" para mostrar la contraseña
      passwordInput.type = "text";
    } else {
      // Si no es "password", cambia el tipo a "password" para ocultar la contraseña
      passwordInput.type = "password";
    }
  });
});
