<?php

// Incluye la librería TCPDF
require('../tcpdf/tcpdf.php');

// Clase extendida de TCPDF
class PDF extends TCPDF
{
    function Header()
    {
        // Encabezado del PDF (opcional)
        $this->SetFont('helvetica', 'B', 16);
    }

    function Footer()
    {
        // Pie de página del PDF (opcional)
    }

    function setGlobalBorder()
    {
        // Establecer un borde global para toda la página
        $this->SetLineStyle(array('width' => 0.5, 'color' => array(0, 0, 0)));
        $this->Rect(5, 5, 200, 287, 'D'); // Ajusta los valores según tu diseño y necesidades
    }
}

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

    // Crear el objeto PDF
    $pdf = new PDF();
    $pdf->AddPage();
    // Llamar a la función para establecer el borde global
    $pdf->setGlobalBorder();

    // Ajustar posición de la tabla
    $pdf->SetY(15);

    // Mostrar información de la dirección en el PDF
    while ($row = mysqli_fetch_assoc($result)) {

        // Obtener la fecha actual
        $fecha_actual = date('d/m/Y');

        // Sección 0
        $html0 = '<div style="text-align: center;">RESGUARDO INTERNO</div>';

        // Sección 1
        $html1 = '<div style="width: 50%; margin: 0 auto; text-align: right;">' . date('Y-m-d') . '   </div>';

        // Sección 2
        $html2 = '<div style="width: 40%; margin: 0 auto; text-align: left;">
                        <img src="../assets/img/DIF2.jpg" alt="Logo" style="width: 100px; margin-right: 100px;">
                    </div>';

        // Separación
        $htmlSeparator = '<br>'; // Puedes cambiar a otros elementos o estilos según tus necesidades.

        // Sección 3
        $html3 = '<div border="1" style="text-align: center; ">CONSECUTIVO No: ' . $row['Consecutivo_No'] . '</div>';

        // Sección 4
        $html4 = '<table border="1">
                        <tr>
                            <th style="text-align: center; width: 180px;">ÁREA RESGUARDANTE:</th>
                            <td style="text-align: center; width: 360px;">' . $row['Fullname_direccion'] . '</td>
                        </tr>
                    </table>';

        // Sección 5
        $html5 = '<div style="text-align: center;" class="mb-5">DATOS DEL BIEN</div>';
        $space = '<div style="margin-bottom: 20px;"></div>';

        // Sección 6
        $html6 = '<div style="width: 50%; margin: 0 auto; text-align: center;">
                        <img src="' . $row['Image'] . '" alt="Imagen" class="book-image" style="width: 150px; height: auto;">
                    </div>';

        // Sección 7
        $html7 = '<table border="1">
                        <tr>
                            <th style="width: 180px;">Descripción</th>
                            <td style="width: 360px;">' . $row['Descripcion'] . '</td>
                        </tr>
                        <tr>
                            <th>Caracteristicas Generales</th>
                            <td>' . $row['Caracteristicas_Generales'] . '</td>
                        </tr>
                        <tr>
                            <th>Categoria</th>
                            <td>' . $row['Fullname_categoria'] . '</td>
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

        // Espacio adicional
        $space = '<div style="margin-bottom: 100px;"></div>';

        // Sección 8
        $html8 = '<table border="1">
                        <tr>
                            <th style="height: 115px;"></th>
                            <th style="height: 115px;"></th>
                            <th style="height: 115px;"></th>
                        </tr>
                        <tr>
                            <td style= "text-align: center";>NOMBRE Y FIRMA DIRECTOR ÁREA SOLICITANTE</td>
                            <td style= "text-align: center";>NOMBRE Y FIRMA USUARIO RESPONSABLE</td>
                            <td style= "text-align: center";>NOMBRE Y FIRMA COORDINACIÓN DE RECURSOS MATERIALES</td>
                        </tr>
                    </table>';

        // Salto de línea después de cada tabla
        $pdf->Ln();

        // Salida del HTML al PDF
        $pdf->writeHTML($html0, true, false, true, false, '');
        $pdf->writeHTML($html1, true, false, true, false, '');
        $pdf->writeHTML($html2, true, false, true, false, '');
        $pdf->writeHTML($html3, true, false, true, false, '');
        $pdf->writeHTML($html4, true, false, true, false, '');
        $pdf->writeHTML($html5, true, false, true, false, '');
        $pdf->writeHTML($html6, true, false, true, false, '');
        $pdf->writeHTML($html7, true, false, true, false, '');
        $pdf->writeHTML($html8, true, false, true, false, '');
    }

    // Salida del PDF
    $pdf->Output('resguardo_direccion_' . $identificador_direccion . '.pdf', 'I'); // Mostrar el PDF en el navegador

    // Cerrar la conexión
    mysqli_close($conn);
} else {
    echo "Faltan datos para procesar el informe.";
}
?>
