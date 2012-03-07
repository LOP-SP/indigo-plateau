<div class='wrap'>
	<h2>Indigo Plateau Input Menu</h2>
	
	<form method='post' action='<?php echo $_SERVER["REQUEST_URI"]; ?>'>
		<label for='playerName'>Nome</label>
			<input type='text' name='playerName' /><br />
		
		<label for='eventDate'>Data (YYYY-MM-DD)</label>
			<input type='text' name='eventDate' /><br />
		
		<label for='eventName'>Evento</label>
			<input type='text' name='eventName' /><br />
			
		<label for='reason'>Reason</label>
			<select name="reason">
				<option value="ganharTorneio">Ganhar um torneio - 15 pontos</option>
				<option value="perderFinal">Perder na final de um torneio - 10 pontos</option>
				<option value="perderQuartas">Perder nas quartas de final de um torneio - 5 pontos</option>
				<option value="defenderGinasio">Defender um gin&aacute;sio - 15 pontos</option>
				<option value="trazerAmigo">Trazer um amigo para participar de torneio - 5 pontos</option>
				<option value="criarPost">Criar um post para o site da LOP-SP - 5 pontos</option>
				<option value="criarRegra">Criar uma regra aceita pela LOP-SP - 5 pontos</option>
			</select><br />
			
		<p class='submit'>
			<input type='submit' name='submit' value='Adicionar registro' />
		</p>
	</form>
	
	<?php
	// Insert the new registry into DB.
	
	echo 'derp';
	?>
	
	<?php echo indigo_plateau_ranking(); ?>
	
</div>