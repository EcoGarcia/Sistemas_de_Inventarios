    <?php
    require '../vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    // Incluir la conexión a la base de datos
    include('../includes/conexion.php');

    // Crear un nuevo objeto Spreadsheet
    $spreadsheet = new Spreadsheet();

    // Seleccionar la hoja activa
    $sheet = $spreadsheet->getActiveSheet();

    // Agregar datos y formato a las celdas
    $sheet->setCellValue('A1', 'Dirección:');
    $sheet->setCellValue('C1', 'Nombre:');

    // Obtener datos de la tabla direccion
    $query_direcciones = "SELECT identificador, Fullname FROM direccion"; 
    $result_direcciones = mysqli_query($conexion, $query_direcciones);

    $direcciones = array(); // Array para almacenar las direcciones

    if ($result_direcciones && mysqli_num_rows($result_direcciones) > 0) {
        while ($row = mysqli_fetch_assoc($result_direcciones)) {
            // Agregar la dirección al array $direcciones
            $direcciones[$row['Fullname']] = $row['identificador']; // Asignar el identificador de la dirección como valor
        }
    }

    // Configurar una lista desplegable en la celda A2 con los datos de $direcciones
    $validationDireccion = $sheet->getCell('A2')->getDataValidation();
    $validationDireccion->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $validationDireccion->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    $validationDireccion->setAllowBlank(false);
    $validationDireccion->setShowInputMessage(true);
    $validationDireccion->setShowErrorMessage(true);
    $validationDireccion->setShowDropDown(true);
    $validationDireccion->setErrorTitle('Error');
    $validationDireccion->setError('El valor no es válido');
    $validationDireccion->setPromptTitle('Selecciona una dirección');
    $validationDireccion->setPrompt('Por favor, selecciona una dirección');
    $validationDireccion->setFormula1('"'.implode(',', array_keys($direcciones)).'"');

    // Configurar una lista desplegable en la celda C2 para los usuarios (dinámicamente cargados)
    $validationUsuarios = $sheet->getCell('C2')->getDataValidation();
    $validationUsuarios->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $validationUsuarios->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
    $validationUsuarios->setAllowBlank(false);
    $validationUsuarios->setShowInputMessage(true);
    $validationUsuarios->setShowErrorMessage(true);
    $validationUsuarios->setShowDropDown(true);
    $validationUsuarios->setErrorTitle('Error');
    $validationUsuarios->setError('El valor no es válido');
    $validationUsuarios->setPromptTitle('Selecciona un usuario');
    $validationUsuarios->setPrompt('Por favor, selecciona un usuario');

    // Configurar un script JavaScript para manejar el cambio de la dirección seleccionada y cargar usuarios
    $sheet->setCellValue('A2', '');
    $sheet->setCellValue('C2', '');

    $script = <<<EOD
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var direccionCell = document.querySelector('[data-coordinate="A2"]');
        var usuarioCell = document.querySelector('[data-coordinate="C2"]');
        
        direccionCell.addEventListener('change', function() {
            var direccionNombre = this.value;
            fetch('get_usuarios.php?direccion_nombre=' + encodeURIComponent(direccionNombre))
                .then(response => response.json())
                .then(data => {
                    usuarioCell.innerHTML = ''; // Limpiar opciones anteriores
                    data.forEach(usuario => {
                        var option = document.createElement('option');
                        option.text = usuario.Fullname;
                        option.value = usuario.Identificador_usuario_direccion;
                        usuarioCell.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar usuarios:', error);
                });
        });
    });
    </script>
    EOD;

    $spreadsheet->getActiveSheet()->getCell('A2')->setValue('')->getParent()->getActiveSheet()->getCell('A2')->setValue('')->getParent()->getActiveSheet()->getCell('A2')->setValue('')->getParent()->getActiveSheet()->getCell('A2')->setValue('')->getParent()->getActiveSheet()->getCell('A2')->setValue('')->getParent()->getActiveSheet()->getCell('A2')->setValue('')->getParent()->getActiveSheet()->getCell('A2')->setValue('')->getParent()->getActiveSheet()->getCell('A2')->setValue('')->getParent()->getActiveSheet()->getCell('A2')->setValue('');

    // Configurar la cabecera de descarga
    $filename = "plantilla_excel.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'. $filename .'"');
    header('Cache-Control: max-age=0');

    // Guardar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

    // Salir del script
    exit;
    ?>
