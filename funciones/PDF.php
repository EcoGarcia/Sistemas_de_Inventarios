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

// Obtener información del usuario de la dirección
$queryUsuario = "SELECT Fullname FROM  director_area";
$stmtUsuario = mysqli_prepare($conn, $queryUsuario);
mysqli_stmt_execute($stmtUsuario);
$resultUsuario = mysqli_stmt_get_result($stmtUsuario);
$Usuario = mysqli_fetch_assoc($resultUsuario);

// Obtener información del administrador
$queryAdmin = "SELECT Fullname FROM  coordinación_de_recursos";
$stmtAdmin = mysqli_prepare($conn, $queryAdmin);
mysqli_stmt_execute($stmtAdmin);
$resultAdmin = mysqli_stmt_get_result($stmtAdmin);
$admin = mysqli_fetch_assoc($resultAdmin);


    // Crear el objeto PDF
    $pdf = new PDF();
    $pdf->AddPage();
    // Llamar a la función para establecer el borde global
    $pdf->setGlobalBorder();

    // Ajustar posición de la tabla
    $pdf->SetY(15);

    // Mostrar información de la dirección en el PDF
    while ($row = mysqli_fetch_assoc($result)) {
        // Obtener información del usuario de la dirección
        $queryUsuario = "SELECT Fullname FROM usuarios_direccion WHERE Fullname_direccion = ?";
        $stmtUsuario = mysqli_prepare($conn, $queryUsuario);
        mysqli_stmt_bind_param($stmtUsuario, 's', $row['Fullname_direccion']);
        mysqli_stmt_execute($stmtUsuario);
        $resultUsuario = mysqli_stmt_get_result($stmtUsuario);
        $usuario = mysqli_fetch_assoc($resultUsuario);

    
        // Obtener la fecha actual
        $fecha_actual = date('d/m/Y');

        // Sección 0
        $html0 = '<div style="width: 40%; margin: 0 auto; text-align: left;">
        <img src="../assets/img/DIF2.jpg" alt="Logo" style="width: 80px; margin-right: 123px;">
    </div>';

        // Sección 1
        $html1 = '<div style="width: 50%; margin: 0 auto; text-align: right;">' . date('d-m-y') . '   </div>';

        // Sección 2
        $html2 = '<div style="text-align: center;" >RESGUARDO INTERNO</div>';


        // Sección 3
        $html3 = '<div border="1" style="text-align: center;padding: 10px ">CONSECUTIVO No: ' . $row['Consecutivo_No'] . '</div>';

        // Sección 4
        $html4 = '<table border="1">
                        <tr>
                            <th style="text-align: center; width: 180px; background-color: #ccc;">ÁREA RESGUARDANTE:</th>
                            <td style="text-align: center; width: 360px;">' . $row['Fullname_direccion'] . '</td>
                        </tr>
                    </table>';

        // Sección 5
        $html5 = '<div style="text-align: center; background-color: #ccc;">DATOS DEL BIEN</div>';
        
        // Sección 6
        $html6 = '<div style="width: 50%; margin: 0 auto; text-align: center;">
                        <img src="' . $row['Image'] . '" alt="Imagen" class="book-image" style="width: 130px; height: 180px;">
                    </div>';

// Sección 7
$html7 = '<table border="1" style="margin-top: 15px;">
                    <tr>
                    <th style="width: 180px; height: 20px; background-color: #ccc; margin-left: 60px; padding: 30px;">Descripción</th>
                        <td style="width: 360px; height: 20px margin-left: 45px; padding: 30px;">' . $row['Descripcion'] . '</td>
                    
                    </tr>
                    <tr>
                        <th style= "background-color: #ccc; padding: 30px; height: auto; margin-left: 60px;">Caracteristicas Generales</th>
                        <td style="padding: 30px; height: auto;">' . $row['Caracteristicas_Generales'] . '</td>
                    </tr>
                    <tr>
                        <th style= "background-color: #ccc; padding: 30px; height: 20px;">Categoria</th>
                        <td style="padding: 30px; height: 20px;">' . $row['Fullname_categoria'] . '</td>
                    </tr>
                    <tr>
                        <th style= "background-color: #ccc; padding: 30px; height: 20px;">Marca</th>
                        <td style="padding: 30px; height: 20px;">' . $row['Marca'] . '</td>
                    </tr>
                    <tr>
                    <th style= "background-color: #ccc; padding: 30px; height: 20px;">Modelo</th>
                    <td style="padding: 30px; height: 20px;">' . $row['Modelo'] . '</td>
                </tr>
                <tr>
                    <th style= "background-color: #ccc; padding: 30px; height: 20px;">No. de Serie</th>
                    <td style="padding: 30px; height: 20px;">' . $row['No_Serie'] . '</td>
                </tr>
                <tr>
                    <th style= "background-color: #ccc; padding: 30px; height: 20px;">Color</th>
                    <td style="padding: 30px; height: 20px;">' . $row['Color'] . '</td>
                </tr>
                <tr>
                  <th style= "background-color: #ccc; padding: 30px; height: 20px;">Usuario Responsable</th>
                    <td style="padding: 30px; height: 20px;">' . $row['usuario_responsable'] . '</td>
                </tr>
            <tr>
                <th style= "background-color: #ccc; padding: 30px; height: 20px;">Observaciones</th>
                <td style="padding: 30px; height: 20px;">' . $row['Observaciones'] . '</td>
            </tr>

            </table>';
        // Espacio adicional
        $space = '<div style="margin-bottom: 100px;"></div>';

        // Sección 8
        $html8 = '<table border="1">
                        <tr>
                        <th align="center" style="height: 0px;">
                        <div style="vertical-align: text-top;">
                        <p style="margin-bottom: 1px;">' . $Usuario['Fullname'] . '</p>
                        </div>
                        </th>

                        <th align="center" style="height: 0px;">
                        <div style="vertical-align: text-top;">
                        <p style="margin-bottom: 1px;">' . $usuario['Fullname'] . '</p>
                        </div>
                        </th>
                    
                        <th align="center" style="height: 0px;">
                        <div style="vertical-align: text-top;">
                        <p style="margin-bottom: 1px;">' . $admin['Fullname'] . '</p>
                        </div>
                        </th>

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
