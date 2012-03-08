<div class="wrap">
	<h2>Indigo Plateau Input Menu</h2>
	
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="ip_secret_stuff" value="asdf" />
	
		<label for="ip_playerName">Nome</label>
			<input type="text" name="ip_playerName" /><br />
		
		<label for="ip_eventDate">Data (YYYY-MM-DD)</label>
			<input type="text" name="ip_eventDate" /><br />
		
		<label for="ip_eventName">Evento</label>
			<input type="text" name="ip_eventName" /><br />
			
		<label for="ip_reason">Reason</label>
			<select name="ip_reason">
				<option value="ganharTorneio">Ganhar um torneio - 15 pontos</option>
				<option value="perderFinal">Perder na final de um torneio - 10 pontos</option>
				<option value="perderQuartas">Perder nas quartas de final de um torneio - 5 pontos</option>
				<option value="defenderGinasio">Defender um gin&aacute;sio - 15 pontos</option>
				<option value="trazerAmigo">Trazer um amigo para participar de torneio - 5 pontos</option>
				<option value="criarPost">Criar um post para o site da LOP-SP - 5 pontos</option>
				<option value="criarRegra">Criar uma regra aceita pela LOP-SP - 5 pontos</option>
			</select><br />
			
		<p class="submit">
			<input type="submit" name="submit" value="Adicionar registro" />
		</p>
	</form>
	
	<?php
	// Insert the new registry into DB.
	$ind_plat_name = $_POST["ip_playerName"];
	$ind_plat_time = $_POST["ip_eventDate"];
	$ind_plat_event = $_POST["ip_eventName"];
	$ind_plat_reason = $_POST["ip_reason"];
	
	if (isset($_POST["ip_secret_stuff"])) {
		ip_insert_win( $ind_plat_name, $ind_plat_time, $ind_plat_event, $ind_plat_reason );
		
		unset($_POST["ip_secret_stuff"]);
	}
	?>
	
	<?php indigo_plateau_ranking(); ?>

</div>