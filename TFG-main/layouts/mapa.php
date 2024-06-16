<?php
session_start();

if (isset($_POST['añadir_coord']) && isset($_POST['latitud']) && isset($_POST['longitud'])) {
    // Actualiza los valores de la sesión
    $_SESSION['latitud'] = $_POST['latitud'];
    $_SESSION['longitud'] = $_POST['longitud'];
    // Muestra los datos recibidos
    echo "Datos de localización recibidos.";
    echo $_SESSION['latitud'] . " hola mundo " . $_SESSION['longitud'];
    // Usar JavaScript para cerrar la ventana
    echo '<script type="text/javascript">
            window.close();
          </script>';
    exit(); // Asegura que el script se detenga después de redirigir
} else {
    echo "Datos de localización no recibidos.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar mapa</title>
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="contenido-Mapa">
        <h2>Haz click en el mapa para guardar localización, se guardará el último click</h2>
        <div id="map"></div><br><br>
        <form method="POST" action="">
            <!-- Agregar campos ocultos para almacenar las coordenadas -->
            <input type="hidden" name="latitud" id="latitud" value="">
            <input type="hidden" name="longitud" id="longitud" value="">
            <button type="submit" class="botoncp" name="añadir_coord">Guardar localización</button>
        </form>
        <button class="botoncp" onclick="Cerrar()">Cerrar</button>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let map;
            let marcadores = [];

            // Inicializa el mapa y establece la vista a una latitud y longitud específicas
            function initMap() {
                map = L.map('map').setView([40.498762, -3.374061], 8);

                // Agrega una capa de OpenStreetMap al mapa
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Agregar evento de clic al mapa
                map.on('click', function(event) {
                    addMarker(event.latlng);
                    saveCoordinates(event.latlng.lat, event.latlng.lng);
                });
            }

            function addMarker(location) {
                eliminarUltimoMarker();
                // Crear un nuevo marcador
                let marker = L.marker([location.lat, location.lng]).addTo(map);
                
                // Añadir el marcador al array
                marcadores.push(marker);
                console.log(marker);
            }

            function eliminarUltimoMarker() {
                // Verificar si hay marcadores para eliminar
                if (marcadores.length > 0) {
                    // Obtener el último marcador del array
                    let ultimoMarcador = marcadores.pop();
                    
                    // Quitar el marcador del mapa
                    map.removeLayer(ultimoMarcador);
                }
            }

            function saveCoordinates(lat, lng) {
                // Aquí puedes enviar las coordenadas al servidor para guardarlas en una base de datos
                console.log("Latitud:", lat, "Longitud:", lng);
                document.getElementById("latitud").value = lat;
                document.getElementById("longitud").value = lng;
            }

            function Cerrar() {
                window.close();
            }

            function Guardar() {
                // Obtener las coordenadas del último marcador
                let ultimoMarcador = marcadores[marcadores.length - 1];
                if (ultimoMarcador) {
                    let latLng = ultimoMarcador.getLatLng();
                    let lat = latLng.lat;
                    let lng = latLng.lng;

                    // Envía una solicitud AJAX para actualizar la sesión en el servidor
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "actualizar_sesion.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            //alert("Localización guardada en la sesión.");
                        }
                    };
                    xhr.send("lat=" + lat + "&lng=" + lng);
                } else {
                    alert("No hay coordenadas para guardar.");
                }
            }

            // Inicializa el mapa cuando la página se haya cargado
            initMap();
        });
    </script>
</body>
</html>
