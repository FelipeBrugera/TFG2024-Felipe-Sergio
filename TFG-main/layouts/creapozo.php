<div class="contenido-detpozo">
    <h1 class="div-clubsh1h2">Nuevo pozo</h1>
    <form method="post">
    <button type="button" name="orden" value="Añadir club" class="btnLlegar" data-bs-toggle="modal" data-bs-target="#nuevoClubModal">Añadir Club</button><br></br>
    <table>
        <!-- select de clubs -->
            <tr>Selecciona Club</tr>
            <tr><td><Select name="club">
                <option>Seleccione un club</option>
            <?php foreach ($clubs as $valor): ?>
                <option value=<?=$valor->Id?>><?=$valor->Nombre?></option>
            <?php endforeach ?>
            </Select></td></tr>
            <tr><td>Precio:</td></tr>
            <tr><td><input type="text" name="precio"></td></tr>
            <tr><td>Fecha fin inscripcion:</td></tr>
            <tr><td><input type="datetime-local" name="ffinincs"></td></tr>
            <tr><td>Fecha pozo:</td></tr>
            <tr><td><input type="datetime-local" name="fpozo" required></td></tr>
            <tr><td>Número de jugadores máximos:</td></tr>
            <tr><td><input type="text" name="njmax" required></td></tr>
        </table><br>

    <input type="submit" name="orden" value="Crear Pozo" class="botoncp">
    <!--<input type="submit"  name="orden" value="Volver" class="botoncp">-->
    </form>

    <!-- Modal  AÑADIR CLUB bootstrap-->
   <div class="modal fade" id="nuevoClubModal" tabindex="-1" aria-labelledby="nuevoClubModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevoClubModalLabel">Nuevo club</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <table class="table">
                            <tr>
                                <td>Nombre:</td>
                                <td><input type="text" name="nombre" class="form-control" required></td>
                            </tr>
                            <tr>
                                <td>Localidad:</td>
                                <td><input type="text" name="localidad" class="form-control" required></td>
                            </tr>
                            <tr>
                                <td>Código postal:</td>
                                <td><input type="text" name="ccpp" class="form-control" required></td>
                            </tr>
                            <tr>
                                <td>Dirección:</td>
                                <td><input type="text" name="dir" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>Sitio web:</td>
                                <td><input type="text" name="web" class="form-control"></td>
                            </tr>
                            <tr>
                                <td>Observaciones:</td>
                                <td><textarea rows="10" cols="50" name="obs" class="form-control"></textarea></td>
                            </tr>
                        </table>
                        <input type="hidden" name="latitud" id="latitud" value="">
                        <input type="hidden" name="longitud" id="longitud" value="">
                        <p>Haz click en el mapa para guardar localización, se guardará el último click</p>
                        <div id="map" style="height: 400px;"></div>
                        <br>
                        <div class="modal-footer">
                            <button type="submit" id="Añadir" name="orden" value="crea club" class="btn btn-primary">Añadir Club</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let map;
            let marcadores = [];

            function initMap() {
                map = L.map('map').setView([40.498762, -3.374061], 8);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                map.on('click', function(event) {
                    addMarker(event.latlng);
                    saveCoordinates(event.latlng.lat, event.latlng.lng);
                });
            }

            function addMarker(location) {
                eliminarUltimoMarker();
                let marker = L.marker([location.lat, location.lng]).addTo(map);
                marcadores.push(marker);
                console.log(marker);
            }

            function eliminarUltimoMarker() {
                if (marcadores.length > 0) {
                    let ultimoMarcador = marcadores.pop();
                    map.removeLayer(ultimoMarcador);
                }
            }

            function saveCoordinates(lat, lng) {
                console.log("Latitud:", lat, "Longitud:", lng);
                document.getElementById("latitud").value = lat;
                document.getElementById("longitud").value = lng;
            }

            // Inicializa el mapa cuando la modal esté completamente abierta
            $('#nuevoClubModal').on('shown.bs.modal', function () {
                if (!map) {
                    initMap();
                } else {
                    map.invalidateSize();
                }
            });
        });
    </script>
