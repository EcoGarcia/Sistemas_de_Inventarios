<?php
include("../includes/conexion.php");
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica si se ha enviado un archivo
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        // Validación de tipo de archivo permitido
        $allowedExtensions = array('xlsx', 'xls');
        $uploadedFileExtension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));

        if (!in_array($uploadedFileExtension, $allowedExtensions)) {
            echo "Solo se permiten archivos Excel.";
            exit;
        }

        // Validación de tamaño de archivo
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        if ($_FILES["file"]["size"] > $maxFileSize) {
            echo "El tamaño del archivo es demasiado grande. Por favor, elige un archivo más pequeño.";
            exit;
        }

        $identificador_direccion = $_POST['identificador_direccion'];
        $archivo_temporal = $_FILES["file"]["tmp_name"];

        try {
            // Consulta para obtener el nombre de la dirección
            $consulta_nombre_direccion = "SELECT Fullname FROM direccion WHERE identificador = '$identificador_direccion'";
            $resultado_nombre_direccion = mysqli_query($conexion, $consulta_nombre_direccion);

            if ($resultado_nombre_direccion) {
                $fila_nombre_direccion = mysqli_fetch_assoc($resultado_nombre_direccion);
                $nombre_direccion = $fila_nombre_direccion['Fullname'];

                $spreadsheet = IOFactory::load($archivo_temporal);
                $sheet = $spreadsheet->getActiveSheet();

                // Obtener nombres de columnas de la primera fila
                $headerRow = $sheet->getRowIterator()->current();
                $headers = array();
                foreach ($headerRow->getCellIterator() as $cell) {
                    $headers[] = $cell->getValue();
                }

                // Mapa de columnas de Excel a columnas de la base de datos
                $column_mapping = array(
                    'Numero Consecutivo' => 'Consecutivo_No',
                    'Nombre de la dirección' => 'nombre_direccion',
                    'Descripción' => 'Descripcion',
                    'Caracteristicas Generales' => 'Caracteristicas_Generales',
                    'Marca' => 'Marca',
                    'Modelo' => 'Modelo',
                    'No. De Serie' => 'No_De_Serie',
                    'Color' => 'Color',
                    'Observaciones' => 'Observaciones',
                    'Usuario Responsable' => 'usuario_responsable',
                    'Numero de Factura' => 'Factura',
                    'Estado' => 'Estado',
                );

                // Procesa los datos del archivo Excel
                foreach ($sheet->getRowIterator() as $row) {
                    if ($row->getRowIndex() == 1) {
                        continue; // Salta la primera fila (encabezados)
                    }

                    $rowData = array();
                    foreach ($row->getCellIterator() as $index => $cell) {
                        // Verifica si la posición de la celda existe en el array $headers
                        if (isset($headers[$index])) {
                            $rowData[$column_mapping[$headers[$index]]] = $cell->getValue();
                        }
                    }

                    // Completa los datos faltantes
                    $rowData['Fullname_direccion'] = $nombre_direccion;
                    $rowData['Condiciones'] = ''; // Ajusta según tu lógica
                    $rowData['fecha_creacion'] = date('Y-m-d H:i:s');
                    $rowData['identificador_direccion'] = $identificador_direccion;
                    $rowData['Estado'] = 1; // Valor predeterminado al importar

                    // Inserta los datos en la tabla resguardos_direccion
                    $query = "INSERT INTO resguardos_direccion (".implode(", ", array_keys($rowData)).") VALUES ('".implode("', '", $rowData)."')";
                    mysqli_query($conexion, $query);
                }

                echo "Importación exitosa"; // Puedes ajustar este mensaje según tus necesidades
            } else {
                echo "Error al obtener el nombre de la dirección: " . mysqli_error($conexion);
            }
        } catch (Exception $e) {
            echo "Error al importar: " . $e->getMessage();
        }
    } else {
        echo "No se ha seleccionado ningún archivo.";
    }
} else {
    echo "Acceso no permitido.";
}
?>
