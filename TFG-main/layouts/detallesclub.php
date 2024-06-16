<?php
  $_SESSION['latitud'] = $club->Latitud;
  $_SESSION['longitud'] = $club->Longitud;
  $_SESSION['ID_club'] = $club->Id;
?>

<div class="div-clubs">
<h1 class="div-clubsh1h2"><?=$club->Nombre?> - <?=$club->Localidad?></h1>


  <form method="post" id="myForm">
        <h2 class="div-clubsh1h2">Información del club:</h2>
        <label>Nombre: </label><input type="text" name="txtNombre" id="txtNombre" value="<?= $club->Nombre ?>" disabled><br></br>
        <Label>Localidad: </Label><input type="text" name="txtLocalidad" id="txtLocalidad" value="<?= $club->Localidad ?>" disabled><br></br>
        <label>Codigo postal: </label><input type="text" name="txtCcpp" id="txtCcpp" value="<?= $club->ccpp ?>" disabled><br></br>
        <label>Dirección: </label><input type="text" name="txtDir" id="txtDir" value="<?= $club->Direccion ?>" disabled><br></br>
        <Label>Web: </Label><input type="text" name="txtWeb" id="txtWeb" value="<?= $club->web ?>" disabled> <button onclick="VisitarWeb()">Visitar Web</button><br></br>
        <Label>Observaciones: </Label><Textarea name="txtObsl" id="txtObsl" disabled><?=$club->Observaciones?></Textarea></p><br></br>
        <?php if ($club->Latitud != null || $club->Longitud != null): ?>
            <div id="map"></div><br></br>
            <button type="button" onclick="BotComoLlegar()" class="btnLlegar">¿Como llegar?</button><br></br>
        <?php endif; ?>

        <div class="EditarActivadoNO" id="EditarActivado">
        <Label>Editar mapa: </Label><input type="checkbox" id="checkmap"><br></br>
        <button type="submit" name="orden" value="Modificar" id="botMod" class="botoncp" disabled>Modificar</button>
        </div>
        </form>   

<button id="botEditar" onclick="Modificar()" class="botoncp">Editar</button>
<!--<button onclick="volver()" class="botoncp">Volver</button>-->
  </div>

<?php if ($club->Latitud != null || $club->Longitud != null): ?>
  <script>
        // Inicializa el mapa y establece la vista a las coordenadas del club
        var map = L.map('map').setView([<?= $club->Latitud ?>, <?= $club->Longitud ?>], 13);

        // Agrega una capa de OpenStreetMap al mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Agrega un marcador en la ubicación del club
        var marker = L.marker([<?= $club->Latitud ?>, <?= $club->Longitud ?>]).addTo(map)
            .bindPopup('<b><?= $club->Nombre ?></b><br />Aquí está el club.')
            .openPopup();

        function BotComoLlegar(){
          document.location.href='https://www.google.com/maps/dir/?api=1&destination=<?=$club->Latitud?>,<?=$club->Longitud?>';
        }
    </script>
</body>
</html>
<?php endif; ?>

<script>
  var checkmapa = document.getElementById("checkmap");

  checkmapa.addEventListener("change", function() {
  if (checkmapa.checked) {
    // Abre una ventana emergente con el archivo "mapa.php"
    var ventana = window.open("./layouts/mapa.php", "Modificar mapa", "width=1200,height=900,top=100,left=100");
  }
});



    function VisitarWeb(){
      window.location.href = "<?= $club->web ?>";
    }

        function Modificar(){
    var nombre = document.getElementById("txtNombre");
    var localidad = document.getElementById("txtLocalidad");
    var ccpp = document.getElementById("txtCcpp");
    var dir = document.getElementById("txtDir");
    var web = document.getElementById("txtWeb");
    var obs = document.getElementById("txtObsl");
    var botMod = document.getElementById("botMod");
    var botEdi = document.getElementById("botEditar");

    // Habilitar campos para edición
    nombre.disabled = false;
    ccpp.disabled = false;
    dir.disabled = false;
    web.disabled = false;
    localidad.disabled = false;
    obs.disabled = false;
    botMod.disabled = false;

    botEdi.textContent = "Cancelar";
    botEdi.onclick = function() {
      location.reload();
    };

    document.getElementById("EditarActivado").style.display="block";
  }

      </script>



