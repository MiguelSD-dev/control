$(document).ready(function () {
    $('#login-button').click(function () {
        var dni = $('#dni').val();
        var password = $('#password').val();
        $.ajax({
            type: 'POST',
            url: 'ajax_handler.php',
            data: { dni: dni, password: password, ajax: true },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $('#logout-button').show();
                        $('#login-form').hide();
                        $('#nombre-trabajador').text(data.nombre);
                        $('#control-form').show();
                        // Mostrar las horas trabajadas si están disponibles
                        if (data.horasTrabajadas) {
                            $('#horas-trabajadas').text('Horas trabajadas: ' + data.horasTrabajadas).show();
                        }
                        // Mostrar el botón correcto (Entrar o Salir)
                        if (data.mostrarEntrar) {
                            $('#entrar-button').show();
                            $('#salir-button').hide();
                        } else {
                            $('#entrar-button').hide();
                            $('#salir-button').show();
                        }
                    } else {
                        $('#login-error').text(data.error);
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: ", e);
                    $('#login-error').text("Error en la respuesta del servidor.");
                }
            }
        });
    });

    $('#entrar-button').click(function () {
        $.ajax({
            type: 'POST',
            url: 'ajax_handler.php',
            data: { action: 'entrar', ajax: true },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $('#entrar-button').hide();
                        $('#salir-button').show();
                        $('#control-mensaje').text(data.mensaje);
                        $('#control-error').text('');
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        $('#control-error').text(data.error);
                        $('#control-mensaje').text('');
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: ", e);
                    $('#control-error').text("Error en la respuesta del servidor.");
                }
            }
        });
    });

    $('#salir-button').click(function () {
        $.ajax({
            type: 'POST',
            url: 'ajax_handler.php',
            data: { action: 'salir', ajax: true },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $('#salir-button').hide();
                        $('#entrar-button').show();
                        $('#control-mensaje').text(data.mensaje);
                        $('#control-error').text('');
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        $('#control-error').text(data.error);
                        $('#control-mensaje').text('');
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: ", e);
                    $('#control-error').text("Error en la respuesta del servidor.");
                }
            }
        });
    });

    $('#logout-button').click(function () {
        $.ajax({
            type: 'POST',
            url: 'ajax_handler.php',
            data: { action: 'logout', ajax: true },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        window.location.reload();
                    } else {
                        $('#control-error').text(data.error);
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: ", e);
                    $('#control-error').text("Error en la respuesta del servidor.");
                }
            }
        });
    });
});
