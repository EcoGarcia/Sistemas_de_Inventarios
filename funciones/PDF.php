<?php
// Verifica que se haya enviado el formulario
if (isset($_GET['identificador_direccion'])) {
    // Obtener el identificador de la dirección
    $identificador_direccion = $_GET['identificador_direccion'];

    // Conectar a la base de datos (reemplaza con tus propios detalles)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sistemas";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Comprobar la conexión
    if (!$conn) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    // Obtener información de la dirección para el PDF
    $query = "SELECT * FROM resguardos_direccion WHERE identificador_direccion = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $identificador_direccion);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conn));
    }

    // Generar un nuevo PDF con los datos obtenidos
    require('../tcpdf/tcpdf.php');

    class PDF extends TCPDF
    {
        function Header()
        {
            // Encabezado del PDF (opcional)
            $this->SetFont('helvetica', 'B', 16);
        }
    }

    // Crear el objeto PDF
    $pdf = new PDF();
    $pdf->AddPage();

    // Ajustar posición de la tabla
    $pdf->SetY(15);

    // Mostrar información de la dirección en el PDF
    while ($row = mysqli_fetch_assoc($result)) {
        // Ruta de la imagen
        $imagen_path = '../assets/img/DIF2.jpg';

        // Obtener la fecha actual
        $fecha_actual = date('d/m/Y');

        // Crear tablas HTML con celdas para la imagen de tamaño mediano
        $html1 = '<div style="text-align: center;">RESGUARDO INTERNO</div>';
        $space = '<div style="margin-bottom: 4px;"></div>';

        $html2 = '<div style="text-align: center;">
        <div style="">
            <p style="border: 1px solid #000; padding: 10px;">Consecutivo No: ' . $row['Consecutivo_No'] . '</p>
        </div>
    </div>';        $html3 = '<table border="1">
            <tr>
                <th style="text-align: center">ÁREA RESGUARDANTE:</th>
                <td style="text-align: center">' . $row['Descripcion'] . '</td>
            </tr>
        </table>';
        $html4 = '<div style="text-align: center;" class="mb-5">DATOS DEL BIEN</div>';
        $space = '<div style="margin-bottom: 20px;"></div>';
        $html5 = '<div style="width: 50%; margin: 0 auto; text-align: center;">
            <img src="' . $row['Image'] . '" alt="Imagen" class="book-image" style="width: 160px; height: auto;">
        </div>';
        $html6 = '<table border="1">
            <tr>
                <th>Descripción</th>
                <td>' . $row['Descripcion'] . '</td>
            </tr>
            <tr>
                <th>Caracteristicas Generales</th>
                <td>' . $row['Caracteristicas_Generales'] . '</td>
            </tr>
            <tr>
                <th>Marca</th>
                <td>' . $row['Marca'] . '</td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td>' . $row['Modelo'] . '</td>
            </tr>
            <tr>
                <th>No. de Serie</th>
                <td>' . $row['No_Serie'] . '</td>
            </tr>
            <tr>
                <th>Color</th>
                <td>' . $row['Color'] . '</td>
            </tr>
            <tr>
                <th>Usuario Responsable</th>
                <td>' . $row['usuario_responsable'] . '</td>
            </tr>
            <tr>
                <th>Observaciones</th>
                <td>' . $row['Observaciones'] . '</td>
            </tr>
        </table>';
        $space = '<div style="margin-bottom: 100px;"></div>';

        $html7 = '<table border="1">
        <tr>
        <th style="height: 120px;"></th>
        <th style="height: 120px;"></th>
        <th style="height: 120px;"></th>
        </tr>
        <tr>
            <td>NOMBRE Y FIRMA DIRECTOR ÁREA SOLICITANTE</td>
            <td>' . $row['Descripcion'] . '</td>
            <td>' . $row['Descripcion'] . '</td>
        </tr>
    </table>';
    
        // Salto de línea después de cada tabla
        $pdf->Ln();

        // Salida del HTML al PDF
        $pdf->writeHTML($html1, true, false, true, false, '');
        $pdf->writeHTML($space, true, false, true, false, '');
        $pdf->writeHTML($html2, true, false, true, false, '');
        $pdf->writeHTML($html3, true, false, true, false, '');
        $pdf->writeHTML($html4, true, false, true, false, '');
        $pdf->writeHTML($html5, true, false, true, false, '');
        $pdf->writeHTML($html6, true, false, true, false, '');
        $pdf->writeHTML($html7, true, false, true, false, '');
    }

    // Salida del PDF
    $pdf->Output('resguardo_direccion_' . $identificador_direccion . '.pdf', 'I'); // Mostrar el PDF en el navegador

    // Cerrar la conexión
    mysqli_close($conn);
} else {
    echo "Faltan datos para procesar el informe.";
}
?>
