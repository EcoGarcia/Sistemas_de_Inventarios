<?php
include("../includes/conexion.php");
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['import_file'])) {
    $identificador_direccion = $_POST['identificador_direccion'];

    $file = $_FILES['import_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();

        // Omitir la fila de encabezados (fila 1)
        foreach ($worksheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            // Obtener valores de celdas
            $Consecutivo_No = $cellIterator->current()->getValue();
            $cellIterator->next();
            $Caracteristicas_Generales = $cellIterator->current()->getValue();
            $Marca = $cellIterator->current()->getValue();
            $Modelo = $cellIterator->current()->getValue();
            $No_Serie = $cellIterator->current()->getValue();
            $Color = $cellIterator->current()->getValue();
            $Observaciones = $cellIterator->current()->getValue();
            $usuario_responsable = $cellIterator->current()->getValue();
            $Factura = $cellIterator->current()->getValue();
            $EstadoCellValue = $cellIterator->current()->getValue();
            $Estado = ($EstadoCellValue == 'Activo') ? 1 : 0;
            // Skip the next cell since we have already processed it
            $cellIterator->next();

            // Consulta preparada para evitar inyección SQL
            $query = "INSERT INTO resguardos_direccion 
                      (Consecutivo_No, Caracteristicas_Generales, Marca, Modelo, No_Serie, Color, Observaciones, usuario_responsable, Factura, Estado)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conexion, $query);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssssssii', 
                    $Consecutivo_No, 
                    $Caracteristicas_Generales, 
                    $Marca, 
                    $Modelo, 
                    $No_Serie, 
                    $Color, 
                    $Observaciones, 
                    $usuario_responsable, 
                    $Factura, 
                    $Estado);

                $result = mysqli_stmt_execute($stmt);

                if (!$result) {
                    // Manejar error en la inserción
                    echo "Error al insertar datos en la fila " . $row->getRowIndex() . ". Error de MySQL: " . mysqli_error($conexion);
                    exit();
                }

                mysqli_stmt_close($stmt);
            } else {
                // Manejar error en la preparación de la consulta
                echo "Error al preparar la consulta SQL.";
                exit();
            }
        }

        // Redirige o muestra un mensaje de éxito
        header("Location: tu_pagina.php?identificador_direccion=$identificador_direccion&notification_message=Importación exitosa");
        exit();
    } catch (Exception $e) {
        echo "Error al procesar el archivo: " . $e->getMessage();
    }
}

mysqli_close($conexion);
?>
