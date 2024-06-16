<div class="contenido-lpozos">
	<h1 class="div-clubsh1h2">Clubs disponibles:</h1>

	<?php foreach ($clubs as $valor): ?>
	<div class="div-pozos" id="club<?=$valor->Id?>">
			<h2>Nombre del club: <?= $valor->Nombre ?></h2>
			<p>Localidad: <?= $valor->Localidad ?></p>
		</div>
		<?php endforeach ?>
</div>

  <script>
		document.addEventListener('DOMContentLoaded', function () {
			// Selecciona todas las tablas que tengan un ID que empiece con "tabla"
			var tablas = document.querySelectorAll('div[id^="club"]');

			tablas.forEach(function (tabla) {
				tabla.addEventListener('click', function () {
					var idCompleto = event.currentTarget.id;
					var id = idCompleto.replace('club', '');
					document.location.href="?orden=detallesclub&id="+id;
				});
			});
		});
  </script>



    