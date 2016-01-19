<div class="wrap">
	<h2>Indigo Plateau</h2>

	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="ip_input_secret_stuff" value="asdf" />

		<label for="ip_playerName">Nome</label>
			<input type="text" name="ip_playerName" class="ip-insert-name" /><br />

		<label for="ip_eventDate">Data (YYYY-MM-DD)</label>
			<input type="text" name="ip_eventDate" class="ip-datepicker" /><br />

		<label for="ip_eventName">Evento</label>
			<input type="text" name="ip_eventName" class="ip-insert-event" /><br />

		<label for="ip_reason">Reason</label>
			<select name="ip_reason">
				<option value="ganharTorneio">Ganhar um torneio - 15 pontos</option>
				<option value="perderFinal">Perder na final de um torneio - 10 pontos</option>
				<option value="perderQuartas">Perder nas quartas de final de um torneio - 5 pontos</option>
				<option value="defenderGinasio">Defender um ginásio - 15 pontos</option>
			</select><br />

		<p class="submit">
			<input type="submit" name="submit" value="Adicionar registro" class="button-primary" />
		</p>
	</form>

	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="ip_delete_secret_stuff" value="asdf" />

		<label for="ip_id">ID</label>
			<input type="text" name="ip_id" /><br />

		<p class="submit">
			<input type="submit" name="submit" value="Apagar registro" class="button-primary" />
		</p>
	</form>

    <script type="text/javascript">
      jQuery(document).ready(function() {
        jQuery(".ip-datepicker").datepicker({ dateFormat: "yy-mm-dd" });
        jQuery(".ip-insert-name").autocomplete({
          source: ["Carlos Dog", "Willian Hidro", "Ciro Chocotone"] // ["name1", "name2", ...]
        });
        jQuery(".ip-insert-event").autocomplete({
          source: ["Anime Friends", "Anime Dreams", "Anime Festival"] // ["event1", "event2", ...]
        });
      });
    </script>

    <?php
    //
    // Get the variables needed for a new entry or deletion of one.
    //

    // Addition of an entry.
    $ind_plat_name = $_POST["ip_playerName"];
    $ind_plat_time = $_POST["ip_eventDate"];
    $ind_plat_event = $_POST["ip_eventName"];
    $ind_plat_reason = $_POST["ip_reason"];

    // Deletion of an entry.
    $ind_plat_id = $_POST["ip_id"];

    $indigo_plateau = new IndigoPlateau();

    //
    // Checks for hidden variable to know which action to take
    // then insert or delete an entry, accordingly.

    if ( isset( $_POST["ip_input_secret_stuff"] ) ) {
        $indigo_plateau->insert_entry($ind_plat_name, $ind_plat_time, $ind_plat_event, $ind_plat_reason);
        unset($_POST["ip_input_secret_stuff"]);
    }

    if ( isset( $_POST["ip_delete_secret_stuff"] ) ) {
        $indigo_plateau->delete_entry( $ind_plat_id );
        unset( $_POST["ip_delete_secret_stuff"] );
    }

    // Show the current ranking table.
    echo $indigo_plateau->print_ranking("2015");

    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-autocomplete');
    ?>
</div>
