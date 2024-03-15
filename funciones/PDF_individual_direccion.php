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
        // Ajusta el margen para los bordes
        $topBottomMargin = 12; // Ajusta el margen superior e inferior según tus necesidades
        $leftRightMargin = 6; // Ajusta el margen izquierdo y derecho según tus necesidades
    
        $this->SetLineStyle(array('width' => 0.5, 'color' => array(0, 0, 0)));
    
        // Borde superior
        $this->Rect($leftRightMargin, $topBottomMargin, $this->getPageWidth() - 2 * $leftRightMargin, 0, 'D');
    
        // Borde inferior
        $this->Rect($leftRightMargin, $this->getPageHeight() - $topBottomMargin, $this->getPageWidth() - 2 * $leftRightMargin, 0, 'D');
    
        // Borde izquierdo
        $this->Rect($leftRightMargin, $topBottomMargin, 0, $this->getPageHeight() - 2 * $topBottomMargin, 'D');
    
        // Borde derecho
        $this->Rect($this->getPageWidth() - $leftRightMargin, $topBottomMargin, 0, $this->getPageHeight() - 2 * $topBottomMargin, 'D');
    }
}    

if (isset($_GET['id'])) {
    $id = $_GET['id'];

 

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sistemas";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM resguardos_direccion WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        die("Error en la consulta: " . mysqli_error($conn));
    }

    // Obtener información del administrador
    $queryAdmin = "SELECT Fullname FROM  coordinación_de_recursos";
    $stmtAdmin = mysqli_prepare($conn, $queryAdmin);
    mysqli_stmt_execute($stmtAdmin);
    $resultAdmin = mysqli_stmt_get_result($stmtAdmin);
    $admin = mysqli_fetch_assoc($resultAdmin);


    $pdf = new PDF();
    $pdf->AddPage('P', 'Letter');
    $pdf->setGlobalBorder();
    $pdf->SetY(15);

    while ($row = mysqli_fetch_assoc($result)) {
        $queryDirector = "SELECT Fullname FROM director_area WHERE Area = ?";
        $stmtDirector = mysqli_prepare($conn, $queryDirector);
        mysqli_stmt_bind_param($stmtDirector, 's', $row['Fullname_direccion']);
        mysqli_stmt_execute($stmtDirector);
        $resultDirector = mysqli_stmt_get_result($stmtDirector);
        $director = mysqli_fetch_assoc($resultDirector);
        
        if ($director) {
            // Si se encontró un director para la dirección actual
            $directorName = $director['Fullname'];
        } else {
            // Si no se encontró un director, puedes manejarlo de alguna manera, como mostrar un mensaje de error o usar un valor predeterminado
            $directorName = "Director no encontrado";
        }
                        


        $queryUsuario = "SELECT Fullname FROM usuarios_direccion WHERE Fullname_direccion = ?";
        $stmtUsuario = mysqli_prepare($conn, $queryUsuario);
        mysqli_stmt_bind_param($stmtUsuario, 's', $row['Fullname_direccion']);
        mysqli_stmt_execute($stmtUsuario);
        $resultUsuario = mysqli_stmt_get_result($stmtUsuario);
        $usuario = mysqli_fetch_assoc($resultUsuario);

        $fecha_actual = date('d/m/Y');

        $html1 = '<table border="0">
            <tr>
                <th align="center">
                    <div style="display: inline-block; text-align: center; line-height: 10px; margin-top: 0;">
                        <img src="../assets/img/DIF2.jpg" alt="Logo" style="width: 60px; margin-right: 5px;">
                    </div>
                </th>

                <th align="center">
                    <div style="width: 20%; display: inline-block; text-align: center; line-height: 2px; margin-top: 0; font-size: 11px;">RESGUARDO INTERNO</div>
                </th>

                <th align="center">
                    <div style="display: inline-block; text-align: center; line-height: 10px; margin-top: 0;">' . date('d-m-y') . '</div>
                </th>
            </tr>
        </table>';

        $html2 = '<div border="1" style="text-align: center; padding: 10px;  font-size: 11px;">CONSECUTIVO No: ' . $row['Consecutivo_No'] . '</div>';

        $html3 = '<table border="1" style="border-collapse: collapse; width: 100%; margin-left: 60px;">
        <tr>
                    <th style="text-align: center; background-color: #ccc;  font-size: 11px;">ÁREA RESGUARDANTE:</th>
                    <td style="text-align: center;">' . $row['Fullname_direccion'] . '</td>
                </tr>
            </table>';

        $html4 = '<div style="text-align: center; background-color: #ccc;  font-size: 11px;">DATOS DEL BIEN</div>';

        $html5 = '<div style="width: 50%; margin: 0 auto; text-align: center;">
                <img src="' . $row['Image'] . '" alt="Imagen" class="book-image" style="width: 130px; height: 180px;">
            </div>';

        $html6 = '<table border="1" style="border-collapse: collapse; width: 100%; margin-left: 60px;">
        <tr>
                <th style="background-color: #ccc; margin-left: 60px; width: 180px; font-size: 11px; padding-bottom: 0;">DESCRIPCIÓN: </th>
                <td style="margin-left: 45px; width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Descripcion'] . '</td>
            </tr>
            <tr>
            <th style="background-color: #ccc; padding: 5px; width: 180px; font-size: 11px; vertical-align: middle; padding-bottom: 0;">CARACTERISTICAS GENERALES: </th>
            <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Caracteristicas_Generales'] . '</td>
        </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">CATEGORIA: </th>
                <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Fullname_categoria'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">MARCA: </th>
                <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Marca'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">MODELO: </th>
                <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Modelo'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">NO. DE SERIE: </th>
                <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['No_Serie'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">COLOR: </th>
                <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Color'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">OBSERVACIONES: </th>
                <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Observaciones'] . '</td>
            </tr>
            <tr>
                <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">USUARIO RESPONSABLE: </th>
                <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['usuario_responsable'] . '</td>
            </tr>

            <tr>
                <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">CONDICIONES: </th>
                <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Condiciones'] . '</td>
            </tr>
            <tr>
            <th style="background-color: #ccc; width: 180px; font-size: 11px; padding-bottom: 0;">NUMERO DE FACTURA: </th>
            <td style="width: 360px; font-size: 11px; padding-bottom: 0;">' . $row['Factura'] . '</td>
        </tr>
        </table>';

        $html7 = '<table border="1" style="border-collapse: collapse; width: 100%; margin-left: 60px;">
        <tr>
            <th align="center" style="vertical-align: bottom;">
                <div>
                <p style="margin-bottom: 1px;">' . $directorName . '</p>
                </div>
            </th>
    
            <th align="center" style="vertical-align: bottom;">
                <div>
                <p style=" margin-bottom: 0.5px;">'  . $row['usuario_responsable'] . '</p>
                </div>
            </th>
    
            <th align="center" style="vertical-align: bottom;">
                <div>
                <p style=" margin-bottom: 0.5px;">'  . (isset($admin['Fullname']) ? $admin['Fullname'] : '') . '</p>
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
    }

    // Salida del PDF
    $pdf->Output('resguardo_direccion_' . $id . '.pdf', 'I');
    mysqli_close($conn);
} else {
    echo "Faltan datos para procesar el informe.";
}
?>
