<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <?php
    // Inicializa la sesión si no está iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    ?>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="../dashboard/dashboard.php">
            <img src="../assets/img/DIF2.png" alt="Logo DIF2" id="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
                // Barra de navegación para el tipo de usuario 1 (administrador)
                echo '<li class="nav-item active"><a class="nav-link" href="../dashboard/dashboard.php">Inicio <span class="sr-only"></span></a></li>';

                // Menú desplegable "Nuevos usuarios"
                echo '<li class="nav-item dropdown">';
                echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsuarios" role="button" data-bs-toggle="dropdown" aria-expanded="false">Nuevos usuarios</a>';
                echo '<div class="dropdown-menu" aria-labelledby="navbarDropdownUsuarios">';
                echo '<a class="dropdown-item" href="#" id="abrirModalAdminCoordinacion">Nuevo administrador</a>';
                echo '<a class="dropdown-item" href="#" id="abrirModalUsuarioDireccion">Nuevo usuario dirección</a>';
                echo '<a class="dropdown-item" href="#" id="abrirModalUsuarioCoordinacion">Nuevo usuario coordinación</a>';
                echo '<a class="dropdown-item" href="#" id="abrirModalUsuarioServicio">Nuevo usuario servicio</a>';
                echo '</div>';
                echo '</li>';

                // Menú desplegable "Servicios"
                echo '<li class="nav-item dropdown">';
                echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownServicios" role="button" data-bs-toggle="dropdown" aria-expanded="false">Servicios</a>';
                echo '<div class="dropdown-menu" aria-labelledby="navbarDropdownServicios">';
                echo '<a class="dropdown-item" href="#" id="abrirModalDireccion">Nueva dirección</a>';
                echo '<a class="dropdown-item" href="#" id="abrirModalCoordinacion">Nueva coordinación</a>';
                echo '<a class="dropdown-item" href="#" id="abrirModalServicio">Nuevo servicio</a>';
                echo '</div>';
                echo '</li>';

                echo '<li class="nav-item"><a class="nav-link" href="../tabla/total_usuarios.php">Lista completa de los usuarios</a></li>';
                // Menú desplegable "Servicios"
                echo '<li class="nav-item dropdown">';
                echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownServicios" role="button" data-bs-toggle="dropdown" aria-expanded="false">Nuevos resguardos</a>';
                echo '<div class="dropdown-menu" aria-labelledby="navbarDropdownServicios">';
                echo '<a class="dropdown-item" href="../resguardos/resguardos_administrador.php">Nuevo resguardo de administrador</a>';
                echo '<a class="dropdown-item" href="../resguardos/resguardos_direccion.php">Nuevo resguardo de direccion</a>';
                echo '<a class="dropdown-item" href="../resguardos/resguardos_coordinacion.php">Nuevo resguardo de coordinacion</a>';
                echo '<a class="dropdown-item" href="../resguardos/resguardos_servicios.php">Nuevo resguardo de servicios</a>';
                echo '</div>';
                echo '</li>';
                
                echo '    <li class="nav-item">
                <a class="nav-link" href="#" id="abrirModalCategoria">Nueva categoria</a>            </li>';

                echo '    <li class="nav-item">
                    <a class="nav-link" href="#" id="logoutLink">Cerrar sesión</a>
                </li>
            ';                ?>
            </ul>

            <!-- Formulario de búsqueda -->
            <form class="form-inline my-2 my-lg-0">
                <!-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button> -->
            </form>
        </div>
    </nav>

    <!-- Modal para el formulario de registro de usuario de dirección -->
    <div class="modal fade" id="usuarioDireccionModal" tabindex="-1" aria-labelledby="usuarioDireccionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usuarioDireccionModalLabel">Registro de Usuario de Dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Contenedor para cargar el contenido de usuario_direccion.php -->
                    <div id="modalContenido"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para el formulario de registro de administrador -->
    <div class="modal fade" id="adminCoordinacionModal" tabindex="-1" aria-labelledby="adminCoordinacionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminCoordinacionModalLabel">Registro de Administrador de Coordinación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para cargar el contenido de modal_administrador.php -->
                    <div id="modalContenidoAdmin"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para el formulario de registro de usuario de dirección -->
    <div class="modal fade" id="usuarioCoordinacionModal" tabindex="-1" aria-labelledby="usuarioCoordinacionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usuarioCoordinacionModalLabel">Registro de Usuario de Coordinación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para cargar el contenido de usuario_direccion.php -->
                    <div id="modalContenido1"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para el formulario de resguardo de administrador -->
    <div class="modal fade" id="resAdministradorModal" tabindex="-1" aria-labelledby="resAdministradorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resAdministradorModalLabel">Registro de un nuevo resguardo de administrador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para cargar el contenido del formulario de resguardo de administrador -->
                    <div id="modalContenidoResAdministrador"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para el formulario de registro de usuario de dirección -->
    <div class="modal fade" id="usuarioServicioModal" tabindex="-1" aria-labelledby="usuarioServicioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usuarioServicioModalLabel">Registro de Usuario de Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para cargar el contenido de usuario_direccion.php -->
                    <div id="modalContenido2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para el formulario de registro de usuario de dirección -->
    <div class="modal fade" id="DireccionModal" tabindex="-1" aria-labelledby="DireccionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DireccionModalLabel">Registro de una nueva dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para cargar el contenido de usuario_direccion.php -->
                    <div id="modalContenido3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para el formulario de registro de usuario de dirección -->
    <div class="modal fade" id="coordinaciónModal" tabindex="-1" aria-labelledby="coordinaciónModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="coordinaciónModalLabel">Registro de una nueva coordinación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para cargar el contenido de usuario_direccion.php -->
                    <div id="modalContenido4"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para el formulario de registro de usuario de dirección -->
    <div class="modal fade" id="servicioModal" tabindex="-1" aria-labelledby="servicioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="servicioModalLabel">Registro de un nuevo servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para cargar el contenido de usuario_direccion.php -->
                    <div id="modalContenido5"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>


      <!-- Modal para el formulario de registro de usuario de dirección -->
      <div class="modal fade" id="categoriasModal" tabindex="-1" aria-labelledby="categoriasModalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoriasModalModalLabel">Registrar una nueva categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Contenedor para cargar el contenido de resguardos_direccion.php -->
                    <div id="modalContenido9"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <!-- Puedes agregar más botones según tus necesidades -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Logout -->
    <div class="modal fade" id="confirmarLogoutModal" tabindex="-1" aria-labelledby="confirmarLogoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarLogoutModalLabel">¿Quieres cerrar sesión?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas cerrar sesión?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="../logout.php" class="btn btn-primary">Cerrar sesión</a>
                </div>
            </div>
        </div>
    </div>



    <!-- Agrega las etiquetas de script y los enlaces a las librerías de Bootstrap, Popper.js, y jQuery aquí -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#abrirModalUsuarioDireccion").on("click", function(e) {
                e.preventDefault();
                $("#modalContenido").load("../usuarios/usuario_direccion.php", function() {
                    $("#usuarioDireccionModal").modal("show");
                });
            });
            $("#abrirModalAdminCoordinacion").on("click", function(e) {
                e.preventDefault();
                $("#modalContenidoAdmin").load("../usuarios/administrador_coordinacion.php", function() {
                    $("#adminCoordinacionModal").modal("show");
                });
            });


            $("#abrirModalUsuarioCoordinacion").on("click", function(e) {
                e.preventDefault();
                $("#modalContenido1").load("../usuarios/usuario_coordinación.php", function() {
                    $("#usuarioCoordinacionModal").modal("show");
                });
            });

            $("#abrirModalUsuarioServicio").on("click", function(e) {
                e.preventDefault();
                $("#modalContenido2").load("../usuarios/usuario_servicio.php", function() {
                    $("#usuarioServicioModal").modal("show");
                });
            });

            $("#abrirModalDireccion").on("click", function(e) {
                e.preventDefault();
                $("#modalContenido3").load("../servicios/direccion.php", function() {
                    $("#DireccionModal").modal("show");
                });
            });

            $("#abrirModalCoordinacion").on("click", function(e) {
                e.preventDefault();
                $("#modalContenido4").load("../servicios/coordinacion.php", function() {
                    $("#coordinaciónModal").modal("show");
                });
            });

            $("#abrirModalServicio").on("click", function(e) {
                e.preventDefault();
                $("#modalContenido5").load("../servicios/servicios.php", function() {
                    $("#servicioModal").modal("show");
                });
            });

            $("#abrirModalCategoria").on("click", function(e) {
                e.preventDefault();
                $("#modalContenido9").load("../categoria/añadir.php", function() {
                    $("#categoriasModal").modal("show");
                });
            });

            $(document).ready(function() {
                $("#logoutLink").on("click", function(e) {
                    e.preventDefault();
                    $("#confirmarLogoutModal").modal("show");
                });
            });


        });
    </script>
</body>

</html>