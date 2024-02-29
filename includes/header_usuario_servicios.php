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
        <a class="navbar-brand" href="../dashboard/dashboard_servicio.php">
            <img src="../assets/img/DIF2.png" alt="Logo DIF2" id="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php
                // Barra de navegación para el tipo de usuario 1 (administrador)
                echo '<li class="nav-item active"><a class="nav-link" href="../dashboard/dashboard_servicio.php">Inicio <span class="sr-only"></span></a></li>';
                ?>
            </ul>

            <ul class="navbar-nav ms-auto"> <!-- Agregado ms-auto para el lado derecho -->
                <?php
                echo '    <li class="nav-item">
                    <a class="nav-link" href="#" id="logoutLink">Cerrar sesión</a>
                </li>';
                ?>
            </ul>

            <!-- Formulario de búsqueda -->
            <form class="form-inline my-2 my-lg-0">
                <!-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button> -->
            </form>
        </div>
    </nav>

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
            $("#logoutLink").on("click", function(e) {
                e.preventDefault();
                $("#confirmarLogoutModal").modal("show");
            });
        });
    </script>
</body>

</html>