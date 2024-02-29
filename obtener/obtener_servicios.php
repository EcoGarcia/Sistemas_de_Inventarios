<?php
if (isset($_GET['direccionId']) && isset($_GET['coordinacionId'])) {
    $direccionId = $_GET['direccionId'];
    $coordinacionId = $_GET['coordinacionId'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sistemas";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "SELECT identificador_servicio, Fullname_servicio FROM servicios WHERE identificador_direccion = '$direccionId' AND identificador_coordinacion = '$coordinacionId'";

    $result = $conn->query($sql);

    $servicios = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $servicios[] = array(
                'identificador_servicio' => $row['identificador_servicio'],
                'Fullname_servicio' => $row['Fullname_servicio']
            );
        }
    }

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($servicios);
} else {
    echo json_encode(array());
}
?>
