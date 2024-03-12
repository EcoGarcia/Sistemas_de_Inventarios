<?php

require('../tcpdf/tcpdf.php');

class PDF extends TCPDF
{
    function Header()
    {
        $this->SetFont('helvetica', 'B', 16);
    }

    function Footer()
    {
        // Pie de página del PDF (opcional)
    }

    function setGlobalBorder()
    {
        $this->SetLineStyle(array('width' => 0.5, 'color' => array(0, 0, 0)));
        $this->Rect(5, 5, 200, 287, 'D');
    }
}

if (isset($_GET['identificador_coordinacion'])) {
    $identificador_coordinacion = $_GET['identificador_coordinacion'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sistemas";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM respaldos_coordinacion WHERE identificador_coordinacion = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $identificador_coordinacion);
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

    $pdf = new PDF();

    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->AddPage();
        $pdf->setGlobalBorder();
        $pdf->SetY(15);

        $queryUsuario = "SELECT Fullname FROM usuarios_direccion WHERE Fullname_direccion = ?";
        $stmtUsuario = mysqli_prepare($conn, $queryUsuario);
        mysqli_stmt_bind_param($stmtUsuario, 's', $row['Fullname_direccion']);
        mysqli_stmt_execute($stmtUsuario);
        $resultUsuario = mysqli_stmt_get_result($stmtUsuario);
        $usuario = mysqli_fetch_assoc($resultUsuario);

        $fecha_actual = date('d/m/Y');

        // Sección 0
        $html1= '<table border="0">
            <tr>
                <th align="center">
                    <div style="display: inline-block; text-align: center; line-height: 10px; margin-top: 0;">
                        <img src="../assets/img/DIF2.jpg" alt="Logo" style="width: 60px; margin-right: 5px;">
                    </div>
                </th>

                <th align="center">
                    <div style="width: 20%; display: inline-block; text-align: center; line-height: 2px; margin-top: 0;">RESGUARDO INTERNO</div>
                </th>

                <th align="center">
                    <div style="display: inline-block; text-align: center; line-height: 10px; margin-top: 0;">' . date('d-m-y') . '</div>
                </th>
            </tr>
        </table>';

        // Sección 2
        $html2 = '<div border="1" style="text-align: center; padding: 10px;">CONSECUTIVO No: ' . $row['consecutivo'] . '</div>';

        // Sección 3
        $html3 = '<table border="1">
                <tr>
                    <th style="text-align: center; background-color: #ccc;">ÁREA RESGUARDANTE:</th>
                    <td style="text-align: center;">' . $row['Fullname_direccion'] . '</td>
                </tr>
            </table>';

        // Sección 4
        $html4 = '<div style="text-align: center; background-color: #ccc;">DATOS DEL BIEN</div>';

        // Sección 5
        $html5 = '<div style="width: 50%; margin: 0 auto; text-align: center;">
                <img src="' . $row['Image'] . '" alt="Imagen" class="book-image" style="width: 130px; height: 180px;">
            </div>';

        // Sección 6
        $html6 = '<table border="1" style="margin-top: 15px;">
            <tr>
                <th style="background-color: #ccc; margin-left: 60px; width: 180px;">DESCRIPCIÓN: </th>
                <td style="margin-left: 45px; width: 360px;">' . $row['descripcion'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; margin-left: 60px; width: 180px;">CARACTERISTICAS GENERALES: </th>
                <td style="width: 360px;">' . $row['caracteristicas'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px;">CATEGORIA: </th>
                <td style="width: 360px;">' . $row['Fullname_categoria'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px;">MARCA: </th>
                <td style="width: 360px;">' . $row['marca'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px;">MODELO: </th>
                <td style="width: 360px;">' . $row['modelo'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px;">NO. DE SERIE: </th>
                <td style="width: 360px;">' . $row['serie'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px;">COLOR: </th>
                <td style="width: 360px;">' . $row['color'] . '</td>
            </tr>
            <tr>
            <th style="background-color: #ccc; width: 180px; font-size: 10px;">OBSERVACIONES: </th>
            <td style="width: 360px;">' . $row['observaciones'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px;">USUARIO RESPONSABLE: </th>
                <td style="width: 360px;">' . $row['usuario_responsable'] . '</td>
            </tr>

            <tr>
                <th style="background-color: #ccc; width: 180px;">CONDICIONES: </th>
                <td style="width: 360px;">' . $row['Condiciones'] . '</td>
            </tr>
            <tr>
            <th style="background-color: #ccc; width: 180px;">NUMERO DE FACTURA: </th>
            <td style="width: 360px;">' . $row['Factura'] . '</td>
        </tr>
        </table>';
        // Espacio adicional
        $space = '<div style="margin-bottom: 80px;"></div>';

        // Sección 8
        $html7 = '<table border="1">
                <tr>
                    <th align="center">
                        <div style="vertical-align: text-top;">
                            <p style="margin-bottom: 1px;">' . $Usuario['Fullname'] . '</p>
                        </div>
                    </th>

                    <th align="center">
                        <div style="vertical-align: text-top;">
                            <p style="margin-bottom: 1px;">' . $usuario['Fullname'] . '</p>
                        </div>
                    </th>
                            
                    <th align="center">
                        <div style="vertical-align: text-top;">
                            <p style="margin-bottom: 1px;">' . $admin['Fullname'] . '</p>
                        </div>
                    </th>
                </tr>
                <tr>
                    <td style="text-align: center">NOMBRE Y FIRMA DIRECTOR ÁREA SOLICITANTE</td>
                    <td style="text-align: center">NOMBRE Y FIRMA USUARIO RESPONSABLE</td>
                    <td style="text-align: center">NOMBRE Y FIRMA COORDINACIÓN DE RECURSOS MATERIALES</td>
                </tr>
            </table>';

        // Salida del HTML al PDF
        $pdf->Ln();
        $pdf->writeHTML($html1, true, false, true, false, '');
        $pdf->writeHTML($html2, true, false, true, false, '');
        $pdf->writeHTML($html3, true, false, true, false, '');
        $pdf->writeHTML($html4, true, false, true, false, '');
        $pdf->writeHTML($html5, true, false, true, false, '');
        $pdf->writeHTML($html6, true, false, true, false, '');
        $pdf->writeHTML($html7, true, false, true, false, '');
        $pdf->writeHTML($space, true, false, true, false, '');
    }

    // Salida del PDF
    $pdf->Output('resguardo_direccion_' . $identificador_coordinacion . '.pdf', 'I');
    mysqli_close($conn);
} else {
    echo "Faltan datos para procesar el informe.";
}
?>
