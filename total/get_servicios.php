<?php
$identificador_coordinacion = $_GET['identificador_coordinacion'];
$identificador_direccion = $_GET['identificador_direccion'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistemas";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$query_servicios = "SELECT identificador, nombre_subsub FROM subsub WHERE identificador_subcategoria = '$identificador_coordinacion' AND identificador_categoria IN (SELECT identificador_categoria FROM subcategoria WHERE identificador_subcategoria = '$identificador_direccion')";
$result_servicios = mysqli_query($conn, $query_servicios);

while ($row_servicio = mysqli_fetch_assoc($result_servicios)) {
    echo "<option value='" . $row_servicio['identificador'] . "'>" . $row_servicio['nombre_subsub'] . "</option>";
}

mysqli_close($conn);
?>
