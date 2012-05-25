<?php
/*
Plugin Name: Indigo Plateau
Plugin URI: http://github.com/agarie/indigo-plateau
Description: A ranking creation plugin used for championships.
Version: 0.1
Author: Carlos Onox Agarie
Author URI: http://onox.com.br
License: GPL2


Copyright 2012  Carlos Agarie  (carlos.agarie@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Class declaration
if (!class_exists('IndigoPlateau')) {
	class IndigoPlateau {
		global $wpdb;
		
		define('TABLE_NAME', $wpdb->prefix . 'indigo_plateau')
		
		// Reasons array, used to calculate the points gained by the participants.
		protected $reasons = array(
								"ganharTorneio" => array(15, 'Vencer um torneio'),
								"perderFinal" => array(10, 'Perder na final de um torneio'),
								"perderQuartas" => array(5, 'Perder nas quartas de final de um torneio'),
								"defenderGinasio" => array(10, 'Defender um ginásio'),
								"trazerAmigo" => array(5, 'Trazer um amigo para participar da LOP-SP pela primeira vez'),
								"criarPost" => array(5, 'Escrever uma postagem para ser publicada no nosso site'),
								"criarRegra" => array(5, 'Criar uma sugestão de regra que seja aceita')
		);
				
		public function __construct() {
			// Shortcodes used to display tables easily.
			add_shortcode( 'indigo_plateau_reasons', array($this, 'print_reasons') );
			add_shortcode( 'indigo_plateau_ranking', array($this, 'print_ranking') );
		}
		
		public function init () {
			global $wpdb;

			$sql = "CREATE TABLE IF NOT EXISTS " . TABLE_NAME . "(
				  id int NOT NULL AUTO_INCREMENT,
				  name VARCHAR(255) NOT NULL,
				  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				  event text DEFAULT '' NOT NULL,
				  reason VARCHAR(255) NOT NULL,
				  points int NOT NULL,
				  UNIQUE KEY id (id)
				);";

			$wpdb->query($sql);
		}

		public function insert_entry ($name, $time, $event, $reason) {
			global $wpdb;
			$points = $this->reasons[$reason][0];

			$wpdb->insert(
				TABLE_NAME,
				array(
					'name' => $name,
					'time' => $time,
					'event' => $event,
					'reason' => $reason,
					'points' => $points
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%d'
				)
			);
		}

		public function delete_entry ($id) {
			global $wpdb;

			$wpdb->query( "DELETE FROM " . TABLE_NAME . " WHERE id = $id" );
		}

		public function create_partial_players ($rows) {
			$players = array();

			// Produce an array with player's name and total points
			foreach ($rows as $row) {
				$players[$row->name] += $row->points;
			}

			// Sort players in decreasing order of total points.
			arsort($players);

			return $players;
		}

		public function create_table ($rows, $kind) {
			// $rows is an object with the following properties:
			// $row->id
			// $row->name
			// $row->points
			// $row->event
			// $row->reason
			// $row->time
			//
			// $kind is a string which dictates the table layout.
			$table = '';
			
			$table .= "<div id='tabela-table'><table>";
			
			if ($kind == 'complete') {
				$table .= "<tr><th>ID</th>";
				$table .= "<th>Jogadores</th><th>Pontua&ccedil;&atilde;o</th>";
				$table .= "<th>Evento</th><th>Reason</th><th>Time</th></tr>";
			
				foreach ($rows as $row) {
					$table .= "<tr>";
					$table .= "<td>" . $row->id . "</td>";
					$table .= "<td>" . $row->name . "</td>";
					$table .= "<td>" . $row->points . "</td>";
					$table .= "<td>" . $row->event . "</td>";
					$table .= "<td>" . $row->reason . "</td>";
					$table .= "<td>" . $row->time . "</td>";
					$table .= "</tr>";
				}
			}
			else if ($kind == 'partial') {
				$table .= "<colgroup>";
				$table .=	"<col class='coluna-jogadores' />";
				$table .= "<col class='coluna-pontos' />";
				$table .= "</colgroup>";
				$table .= "<tr><th>Jogadores</th>";
				$table .= "<th>Pontua&ccedil;&atilde;o</th></tr>";

				foreach ($rows as $row) {
					$table .= "<tr>";
					$table .= "<td>" . $row->name . "</td>";
					$table .= "<td>" . $row->points . "</td>";
					$table .= "</tr>";
				}
			}
			
			$table .= "</table></div>";
			
			return $table;
		}

		public function print_ranking () {
			global $wpdb;

			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT name, points FROM " . TABLE_NAME ) );

			// HTML table creation.
			return $this->create_table($this->create_partial_players($rows), 'partial');
		}

		public function print_reasons () {
			$html = '';

			$html .= "<table id='tabela-ranking'>";
			$html .= "<colgroup>";
			$html .= "<col class='coluna-condicao' />";
			$html .= "<col class='coluna-pts-equiv' />";
			$html .= "</colgroup>";
			$html .= '<tr><th>Condição</th><th>Pontuação</th></tr>';

			foreach ($this->reasons as $value) {
				$html .= '<tr><td>' . $value[1] . '</td><td>' . $value[0] . '</td></tr>';
			}

			$html .= '</table>';

			return $html;
		}

		public function indigo_plateau_complete () {
			global $wpdb;

			$rows = $wpdb->get_results( $wpdb->prepare("SELECT id, name, event, reason, points, time FROM" . TABLE_NAME) );

			// HTML table creation.
			return $this->create_table($rows, 'complete');
		}
	}	
}

$indigo_plateau = new IndigoPlateau();

// Called when the plugin is activated. 
register_activation_hook( WP_PLUGIN_DIR . '/indigo-plateau/indigo-plateau.php', array($indigo_plateau, 'init') );

//
// Menu stuff
//

function indigo_plateau_admin () {
	include_once 'indigo-plateau-admin.php';
}

function indigo_plateau_admin_actions () {
	add_options_page( 'Indigo Plateau', 'Indigo Plateau', 10, basename(__FILE__), 'indigo_plateau_admin' );
}

add_action( 'admin_menu', 'indigo_plateau_admin_actions' );

?>