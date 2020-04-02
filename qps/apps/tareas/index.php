<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/normalize.css">
    <link rel="stylesheet" href="assets/css/fileinput.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <title>Actualizaciones masivas</title>
    
    <!-- Actualiza el texto debajo de la etiqueta -->
    <script>
        $(function(){
            var texto = ["El csv solo debe poseer una columna con el código del producto.",
                "El csv debe poseer (2) campos:\n" +
                "1 Código del producto\n" +
                "2 ID de la promoción",
                "El csv debe poseer trece (13) campos:\n" +
                "1 Código del producto\n" +
                "2 Los otros 12 son los id de las promociones",
                "El csv solo debe poseer una sola columna con el código del producto.",
                "El csv solo debe poseer una columna con el código de la promoción.",
                "El csv debe poseer dos (2) campos:\n" +
                "1 Código del producto\n" +
                "2 ID de la promoción",
                "El csv debe poseer trece (13) campos:\n" +
                "1 Código del producto\n" +
                "2 Los otros 12 son los id de las promociones"];

            var nombre = ["cargar_precio_neto",
                "cargar_promocion",
                "cargar_promocion_paises",
                "eliminar_precio_neto",
                "eliminar_promocion_global",
                "eliminar_promocion",
                "eliminar_promocion_paises",
                "eliminar_descrip_cortas"];

            $("#cambiarTexto").on("change", function(){
                var index = parseInt($(this).val());
                $("#mostrarTexto").html(texto[index]);

                /- Propiedad del boton procesar -/
                document.getElementById("enviar").name = nombre[index];
            });
        });
        //Funcion que evalua que se hayan seleccionado opciones en los select
        function validarFormulario(){

            var cmbCambiarTexto = document.getElementById('cambiarTexto').selectedIndex;
            var cmbSelector = document.getElementById('empresa').selectedIndex;

            //Test comboBox
            if(cmbSelector == null || cmbSelector == 0){
                alert('ERROR: Debe seleccionar una Empresa');
                return false;
            }
            //Test comboBox
            if(cmbCambiarTexto == null || cmbCambiarTexto == 0){
                alert('ERROR: Debe seleccionar una opcion');
                return false;
            }
            return true;
        }
        //Funcion parara habilitar/deshabilitar boton de enviar
        function isChecked() {
            var button = document.getElementById('enviar');
            var cmbCambiarTexto = document.getElementById('cambiarTexto').selectedIndex;
            var cmbSelector = document.getElementById('empresa').selectedIndex;
            var flag = false;
            var flag2 = false;

            if(cmbSelector == null || cmbSelector == 0){
                flag = false;
            }else {
                flag = true;
            }
            //Test comboBox
            if(cmbCambiarTexto == null || cmbCambiarTexto == 0){
                flag2 = false;
            }else {
                flag2 = true;
            }
            if(flag && flag2){
                button.disabled = "";
            } else {
                button.disabled = "disabled";
            }
        }
    </script>
    
</head>
<body>
<header id="header">
    <div class="container">
        <div class="row">
            <div id="logo" class="col-sm-2">
                <img src="assets\img\logo.jpg"/>
            </div>
        </div>
    </div>
    <!--ul id="navbar">
        <li><a href="#">Home</a> </li>
    </ul-->
</header>
<section id="content" class="mb-3">
    <form action="procesos.php" method="post" enctype="multipart/form-data" id="import_form" onsubmit="return validarFormulario()">
    <div id="task-selector">
        <div class="container">
            <div class="row">
                <h2 id="title" class="col-sm-12">Actualizaciones masivas</h2>
            </div>
        </div>
    </div><!-- #task-selector -->
    
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-3">                
                <h3>Selecciona la tarea que quieres ejecutar</h3>
            </div>

            <div class="col-md-12 text-center mb-3">
                <table class="table table-responsive" >
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Actualizar</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <select id="empresa" name="empresa" class="form-control" onchange="isChecked()">
                                <option selected>Seleccionar una opción
                                <option value="Noffra">Noffra</option>
                                <option value="QPS">QPS</option>
                                <option value="QPSPANAMA">QPS PANAMA</option>
                                <option value="NoffraP">Noffra Prueba</option>
                                <option value="QPSP">QPS Prueba</option>
                            </select>
                        </td>
                        <td>
                            <select id="cambiarTexto" name="cambiarTexto" class="form-control" onchange="isChecked()">
                                <option selected>Seleccionar una opción
                                <option value="0"> Cargar Precio Neto</option>
                                <option value="1"> Cargar por Producto</option>
                                <option value="2"> Cargar Promociones por Paises </option>
                                <option value="3">Eliminar Precio Neto</option>
                                <option value="4">Eliminar Promociones Globales</option>
                                <option value="5">Eliminar Promociones por Producto </option>
                                <option value="6">Eliminar Promociones por Paises </option>
                                <option value="7">Eliminar Descripciones Cortas</option>
                            </select>
                        </td>
                        <td>
                            <div tabindex="500" class="btn btn-primary btn-lg btn-file">
                                <i class="fas fa-folder"></i>
                                <span class="hidden-xs"> Examinar…</span>
                                <input class="form-control" type="file" name="file" id="file" required/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="form-group">
                    <div id="mostrarTexto" class="alert alert-info">
                        Seleccione una opción
                    </div>

                    <div class="col-md-12 text-center">
                        <button type="submit" id="enviar" class="btn btn-primary btn-lg" value="Procesar" disabled="disabled">
                            <i class="fas fa-check-circle"></i> Procesar
                        </button>
                    </div>
                </div>
        </div>
    </div>    
    </form>
</section>

<footer class="page-footer font-small">
    <!-- Copyright -->
    <div class="footer-copyright text-center py-3">© 2018 Copyright
    </div>
    <!-- Copyright -->
</footer>
</body>
</html>