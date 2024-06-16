<?php
$id=$_GET['id'];
$_SESSION['idPozo']=$id;

$hora = $info['HORA'];
$sorteoestado = "display: none;"; //tabla sorteo
$modbtn = "display: none;"; //boton modificar
$jugadoresActuales = "display: table;";
$horapozo = $pozo->fecha_pozo;
$tu = $info['total_usuarios'];
$creador = $pozo->ID_CREADOR;

echo "<script>console.log('$creador');</script>";
if($creador == $_SESSION['idUser']){    //El botón modificar se muestra cuando eres el creador del pozo
    $modbtn = "display: inline-block;";
}

// Convertir las cadenas de fecha a objetos DateTime
$hora_dt = ($hora != '0000-00-00 00:00:00') ? new DateTime($hora) : null;
$horapozo_dt = ($horapozo != '0000-00-00 00:00:00') ? new DateTime($horapozo) : null;
$hecho;
$est = $pozo->ESTADO;

    // Obtener la diferencia en horas
    $intervalo = $horapozo_dt->diff($hora_dt);
    $diferencia_horas = $intervalo->h;
    
    // Verificar si quedan menos de 2 horas para mostrar el boton de sorteo
    if ($diferencia_horas < 2 && $tu == $pozo->numero_jugadores_max) {
        $hecho = "display: block;";
    } else {
        $hecho = "display: none;";
    }

    if($pozo->ESTADO == 6){
        $sorteoestado = "display: block;";
        $jugadoresActuales = "display: none;";
    }
?>



    <div class="contenido-detpozo">
        <h1><?=$pozo->Nombre?> <?=$pozo->fecha_pozo?></h1>
        <h2><?=$pozo->Localidad?></h2>

        <b>Informacion del pozo:</b><br>
        <table>
            <tr><td>Fecha inicio de inscripción:</td></tr>
            <tr><td><?=$pozo->fecha_inicio_inscripcion?></td></tr>
            <tr><td>Fecha fin de inscripcion:</td></tr>
            <tr><td><?=$pozo->fecha_fin_inscripcion?></td></tr>
            <tr><td>Fecha:</td></tr>
            <tr><td><?=$pozo->fecha_pozo?></td></tr>
            <tr><td>Precio:</td></tr>
            <tr><td><?=$pozo->precio?></td></tr>
            <tr><td>Jugadores maximos:</td></tr>
            <tr><td><?=$pozo->numero_jugadores_max?></td></tr>
        </table><br>
        
        <button onclick="verclub()" class="botoncp">Detalles del club</button><br>
        
        Jugadores actuales: <?=$pozo->numero_jugadores_actuales?><br>
        Jugadores apuntados:
        <table style="<?=$jugadoresActuales?>">
            <?php foreach ($jugadores_pozo as $valor): ?>
            <tr>
                <td><?=$valor?></td>
            </tr>
            <?php endforeach ?>
        </table>
        <br>
        <!-- -------- SORTEO POZO --------- -->
        <div class="sorteo-container" style="<?=$sorteoestado?>">
            <h2>Resultados del Sorteo</h2>
            <table>
                <thead>
                    <tr>
                        <th>Pista</th>
                        <th>Jugador Derecha</th>
                        <th>Jugador Revés</th>
                    </tr>
                </thead>
                <tbody>
                <?php $Cont = 1?>
                <?php $ContP = 1?>
                <?php foreach ($sorteo as $valor): ?>
            <tr>
                <td><?=$ContP?></td>
                <td><?=$valor['NOMBRED'];?></td>
                <td><?=$valor['NOMBRER'];?></td>
            </tr>
                <?php $Cont++?>
                <?php if($Cont % 2 != 0)$ContP++?>
            <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <!-- -------- FIN SORTEO --------- -->
        <!-- -------- MODAL APUNTARSE POZO --------- -->
        <div class="modal-overlay" id="ventanaDetalles">
            <div class="modal-apuntarme">
                <h1>Elige una posición:</h1>
                <form method="post">
                    <div class="radio-group">
                        <input type="radio" id="derecha" name="options" value="D">
                        <label for="derecha">Derecha</label>
                    </div>
                    <div class="radio-group">
                        <input type="radio" id="reves" name="options" value="R">
                        <label for="reves">Reves</label>
                    </div>
                    <div class="radio-group">
                        <input type="radio" id="indiferente" name="options" value="I">
                        <label for="indiferente">Indiferente</label>
                    </div>
                    <div class="button-group">
                        <input type="submit" name="orden" value="Apuntarse" class="botoncp">
                        <button type="button" onclick="cerrarPanel()" class="botoncp">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- -------- FIN MODAL--------- -->
        <div class="botonescascada">
            <button onclick="abrirPanel()" class="btnLlegar">Apuntarme</button><br/>
            <button onclick="compartirWhatsApp()" class="compartir"><i class="fab fa-whatsapp"></i></button><br/>
            <form method="post">
            <button type="submit" name="orden" value="sorteo" class="btnLlegar" style="<?=$hecho?>">Sorteo</button>
            </form>
        </div>

        <div class="botones-final">
            <button id="openModalBtn" class="botoncp" style="<?=$modbtn?>">Modificar pozo</button>
            <button onclick="abandonarPozo()" class="botoncp">Quitarme del pozo</button>
            <a href="./index.php"><button class="botoncp">Volver</button></a>
        </div>


        <!-- -------- MODAL MODIFICAR POZO --------- -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close">&times; Cerrar</span>
                <h2>MODIFICAR POZO</h2>
                <form method="post">
                    <table>
                        <tr>El club no se puede modificar</tr>
                        <tr><td>Precio:</td></tr>
                        <tr><td><input type="text" name="precio" value="<?=$pozo->precio?>"></td></tr>
                        <tr><td>Fecha fin inscripcion:</td></tr>
                        <tr><td><input type="datetime-local" name="ffinincs" value="<?=$pozo->fecha_fin_inscripcion?>"></td></tr>
                        <tr><td>Fecha pozo:</td></tr>
                        <tr><td><input type="datetime-local" name="fpozo" value="<?=$pozo->fecha_pozo?>" required></td></tr>
                        <tr><td>Número de jugadores máximos:</td></tr>
                        <tr><td><input type="number" name="njmax" value="<?=$pozo->numero_jugadores_max?>" required></td></tr>
                    </table><br>
                    <button type="submit" name="orden" value="Modificar_pozo" class="botoncp">Modificar</button>
                </form>
            </div>
        </div>
        <!-- -------- FIN MODAL--------- -->

    </div>


<script>
    function abrirPanel(){
        document.getElementById("ventanaDetalles").style.display="block";
    }

    function cerrarPanel(){
        document.getElementById("ventanaDetalles").style.display="none";
    }

    function volver(){
        history.back();
    }

    function verclub(){
        document.location.href="?orden=detallesclub&id="+<?=$pozo->Id_club?>;
    }

    function abandonarPozo(){
        var confirmacion = confirm("¿Estás seguro de que deseas continuar?");
        if (confirmacion) {
            console.log("hola1");
            document.location.href="?orden=desapuntarpozo&idpozo=<?=$pozo->id?>";
        } else {
            alert("Sigues participando en el pozo!!!");
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
    var modal = document.getElementById("myModal");
    var openModalBtn = document.getElementById("openModalBtn");
    var closeBtn = document.getElementsByClassName("close")[0];

    // Cuando el usuario haga clic en el botón, abre el modal
    openModalBtn.onclick = function() {
        modal.style.display = "block";
    }

    // Cuando el usuario haga clic en <span> (x), cierra el modal
    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    // Cuando el usuario haga clic en cualquier lugar fuera del modal, cierra el modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});

function compartirWhatsApp() {
    var url = window.location.href;
    var mensaje = '¡Echa un vistazo a este pozo! ' + url;
    var enlaceWhatsApp = 'https://wa.me/?text=' + encodeURIComponent(mensaje);
    window.open(enlaceWhatsApp, '_blank');
}

</script>



