function validarFormulario() {
    // Validar el campo Consecutivo No
    var consecutivo = document.getElementById('consecutivo').value;
    if (consecutivo.trim() === '') {
        alert('Por favor, ingresa un Consecutivo No.');
        return false;
    }

    // Validar la selección de dirección
    var direccion = document.getElementById('fullname_direccion');
    if (direccion.value === '') {
        alert('Por favor, selecciona una dirección.');
        return false;
    }

    // Validar la selección de coordinación
    var coordinacion = document.getElementById('coordinacion_existente');
    if (coordinacion.value === '') {
        alert('Por favor, selecciona una coordinación.');
        return false;
    }

    // Validar la selección de usuario de servicio
    var usuario = document.getElementById('usuario_servicio');
    if (usuario.value === '') {
        alert('Por favor, selecciona un usuario de servicio.');
        return false;
    }

    // Validar el campo de descripción
    var descripcion = document.getElementsByName('descripcion')[0].value;
    if (descripcion.trim() === '') {
        alert('Por favor, ingresa una descripción.');
        return false;
    }

    // Validar el campo de características
    var caracteristicas = document.getElementsByName('caracteristicas')[0].value;
    if (caracteristicas.trim() === '') {
        alert('Por favor, ingresa características.');
        return false;
    }

    // Validar el campo de marca
    var marca = document.getElementsByName('marca')[0].value;
    if (marca.trim() === '') {
        alert('Por favor, ingresa una marca.');
        return false;
    }

    // Validar el campo de modelo
    var modelo = document.getElementsByName('modelo')[0].value;
    if (modelo.trim() === '') {
        alert('Por favor, ingresa un modelo.');
        return false;
    }

    // Validar el campo de NO. De Serie
    var serie = document.getElementsByName('serie')[0].value;
    if (serie.trim() === '') {
        alert('Por favor, ingresa un número de serie.');
        return false;
    }

    // Validar el campo de color
    var color = document.getElementsByName('color')[0].value;
    if (color.trim() === '') {
        alert('Por favor, ingresa un color.');
        return false;
    }

    // Validar el campo de observaciones
    var observaciones = document.getElementsByName('observaciones')[0].value;
    if (observaciones.trim() === '') {
        alert('Por favor, ingresa tus observaciones.');
        return false;
    }

    // Validar la selección de imagen (solo jpg y png)
    var imagen = document.getElementsByName('imagen')[0].value;
    if (imagen.trim() === '') {
        alert('Por favor, selecciona una imagen.');
        return false;
    } else {
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        if (!allowedExtensions.exec(imagen)) {
            alert('Solo se permiten archivos con extensiones .jpg, .jpeg, .png.');
            return false;
        }
    }

    // Si llega hasta aquí, todos los campos están llenos y la imagen es válida
    return true;
}