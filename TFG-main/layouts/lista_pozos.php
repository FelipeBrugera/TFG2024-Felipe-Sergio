
<div class="contenido-lpozos">
        <div class="botones-list-pozos">
            <form method="get">
                <input type="submit" name="orden" value="Nuevo pozo" class="botoncp">
                <input type="submit" name="orden" value="Clubs" class="botoncp">
                <input type="submit" name="orden" value="Cerrar Sesion" class="botoncp"> 
            </form>
        </div>


		<!-- -----    SELECT ----- -->
		<div class="content-select">
			<select name="filtro-pozos" id="selectLP">
				<option>Seleccione una opción</option>
				<option value="7">Todos</option>
				<option value="1">Disponibles</option>
				<option value="2">Solo Reves</option>
				<option value="3">Solo Derecha</option>
				<option value="4">Terminados</option>
				<option value="5">Llenos</option>
				<option value="6">Sorteo Realizado</option>
			</select>
		</div>
		<!-- -----   FIN SELECT ----- -->





		<?php
		if(count($tvalores)  < 1){
			echo "NO HAY POZOS DISPONIBLES";
		}
		?>

		<?php foreach ($tvalores as $valor): ?>
			<?php
				$estado = $valor->NOMBRE_ESTADO;
				if($estado == "POZO DISPONIBLE"){
					$color = "green";
				}
				else if($estado == "SOLO REVES" || $estado == "SOLO DERECHA"){
					$color = "orange";
				}
				else if($estado == "TERMINADO" || $estado == "POZO LLENO"){
					$color = "red";
				}
			?>
			<div class="div-pozos">
				<table id="tabla<?= $valor->id ?>">
					<tr><td>Club:	<b><?= $valor->NOMBRE_CLUB ?> </b> Lugar: <?= $valor->Localidad ?> Dia: <b><?=$valor->fecha_pozo?></b></td><tr>
					<tr><td>Fecha fin inscripción: <?= $valor->fecha_fin_inscripcion?> Precio: <?= $valor->precio?> € </td></tr>
					<tr><td>Jugadores máximos: <?= $valor->numero_jugadores_max ?> Jugadores actuales: <?= $valor->numero_jugadores_actuales ?></td></tr>
					<tr><td class="estado" style="color: <?= $color; ?>;"><?= $valor->NOMBRE_ESTADO ?></td></tr>
					<tr><td></td></tr>
				</table>
			</div>
		<?php endforeach ?>
    </div>

<script>

		document.addEventListener('DOMContentLoaded', function () {
			// Selecciona todas las tablas que tengan un ID que empiece con "tabla"
			var tablas = document.querySelectorAll('table[id^="tabla"]');

			tablas.forEach(function (tabla) {
				tabla.addEventListener('click', function () {
					var idCompleto = event.currentTarget.id;
					var id = idCompleto.replace('tabla', '');
					document.location.href="?orden=detallespozo&id="+id;
				});
			});
		});


		// -------- EVENTOS SELECT FILTROS ----------
		document.getElementById('selectLP').addEventListener('change', function() {
            var selectedValue = this.value;

            if (selectedValue === '1') {
				document.location.href="?orden=filtro_lista&estado=1";
            } else if (selectedValue === '2') {
				document.location.href="?orden=filtro_lista&estado=2";
            } else if (selectedValue === '3') {
				document.location.href="?orden=filtro_lista&estado=3";
            } else if (selectedValue === '4') {
				document.location.href="?orden=filtro_lista&estado=4";
            } else if (selectedValue === '5') {
				document.location.href="?orden=filtro_lista&estado=5";
            } else if (selectedValue === '6') {
				document.location.href="?orden=filtro_lista&estado=6";
            }
			else if (selectedValue === '7') {
				document.location.href="index.php";
            }
        });



    </script>

