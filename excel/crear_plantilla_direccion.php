<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Obtén los datos necesarios, como los fullname_direccion, usuarios, y categorías (ajusta las consultas según tu base de datos)
$fullname_direcciones = obtenerFullnamesDirecciones();
$usuarios = obtenerUsuarios();
$categorias = obtenerCategorias();

// Crear el objeto Spreadsheet
$spreadsheet = new Spreadsheet();

// Crear la hoja de trabajo
$sheet = $spreadsheet->getActiveSheet();

// Encabezados
$sheet->setCellValue('A1', 'Dirección');
$sheet->setCellValue('B1', 'Usuario');
$sheet->setCellValue('C1', 'Categoría');
$sheet->setCellValue('D1', 'OtroCampo'); // Agrega más campos según sea necesario

// Llenar la lista desplegable para la columna A (Dirección)
$validation = $sheet->getCell('A2')->getDataValidation();
$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
$validation->setFormula1('"'.implode(',', $fullname_direcciones).'"');

// Llenar la lista desplegable para la columna B (Usuario)
$validation = $sheet->getCell('B2')->getDataValidation();
$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
$validation->setFormula1('"'.implode(',', $usuarios).'"');

// Llenar la lista desplegable para la columna C (Categoría)
$validation = $sheet->getCell('C2')->getDataValidation();
$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
$validation->setFormula1('"'.implode(',', $categorias).'"');

// Puedes agregar más campos y listas desplegables según sea necesario

// Guardar el archivo Excel
$writer = new Xlsx($spreadsheet);
$writer->save('../plantilla/plantilla.xlsx');

echo 'Plantilla creada con éxito';

// Funciones para obtener datos desde la base de datos
function obtenerFullnamesDirecciones(): array {
    // Conexión a la base de datos (asegúrate de tener una conexión válida)
    $conexion = mysqli_connect("localhost", "root", "", "sistemas");

    // Verificar la conexión
    if (!$conexion) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    $fullnames_direcciones = array();

    // Consulta para obtener los fullnames de la tabla direccion
    $query = "SELECT Identificador, Fullname FROM direccion";
    $result = mysqli_query($conexion, $query);

    // Recorrer los resultados y almacenar en el array
    while ($row = mysqli_fetch_assoc($result)) {
        $fullnames_direcciones[$row['Identificador']] = $row['Fullname'];
    }

    // Cerrar la conexión
    mysqli_close($conexion);

    return $fullnames_direcciones;
}

function obtenerUsuarios(): array {
    // Conexión a la base de datos (asegúrate de tener una conexión válida)
    $conexion = mysqli_connect("localhost", "root", "", "sistemas");

    // Verificar la conexión
    if (!$conexion) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    $usuarios = array();

    // Consulta para obtener los usuarios de la tabla usuarios_direccion
    $query = "SELECT Identificador_usuario_direccion, Fullname FROM usuarios_direccion";
    $result = mysqli_query($conexion, $query);

    // Recorrer los resultados y almacenar en el array
    while ($row = mysqli_fetch_assoc($result)) {
        $usuarios[$row['Identificador_usuario_direccion']] = $row['Fullname'];
    }

    // Cerrar la conexión
    mysqli_close($conexion);

    return $usuarios;
}

function obtenerCategorias(): array {
    // Conexión a la base de datos (asegúrate de tener una conexión válida)
    $conexion = mysqli_connect("localhost", "root", "", "sistemas");

    // Verificar la conexión
    if (!$conexion) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    $categorias = array();

    // Consulta para obtener las categorías de la tabla categoria
    $query = "SELECT identificador_categoria, Fullname_categoria FROM categorias";
    $result = mysqli_query($conexion, $query);

    // Recorrer los resultados y almacenar en el array
    while ($row = mysqli_fetch_assoc($result)) {
        $categorias[$row['identificador_categoria']] = $row['Fullname_categoria'];
    }

    // Cerrar la conexión
    mysqli_close($conexion);

    return $categorias;
}
?>
