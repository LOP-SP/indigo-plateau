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

// Install function
function indigo_plateau_activate () {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'indigo_plateau';
	
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
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

register_activation_hook( WP_PLUGIN_DIR . '/indigo-plateau/indigo-plateau.php', 'indigo_plateau_activate' );

// Menu
function indigo_plateau_admin () {
	include_once 'indigo-plateau-admin.php';
}

function indigo_plateau_admin_actions () {
	add_options_page( 'Indigo Plateau', 'Indigo Plateau', 10, 'Indigo-Plateau', 'indigo_plateau_admin' );
}

add_action( 'admin_menu', 'indigo_plateau_admin_actions' );

// Hash to store reasons.
global $reasons;
$reasons = array(
	"ganharTorneio" => array(15, 'Vencer um torneio'),
	"perderFinal" => array(10, 'Perder na final de um torneio'),
	"perderQuartas" => array(5, 'Perder nas quartas de final de um torneio'),
	"defenderGinasio" => array(10, 'Defender um ginásio'),
	"trazerAmigo" => array(5, 'Trazer um amigo para participar da LOP-SP pela primeira vez'),
	"criarPost" => array(5, 'Escrever uma postagem para ser publicada no nosso site'),
	"criarRegra" => array(5, 'Criar uma sugestão de regra que seja aceita')
);

// Output a list of reasons.
/**
 * indigo_plateau_reasons
 *
 * @return string
 * @author Carlos Agarie
 **/
function indigo_plateau_reasons () {
	global $reasons;
	$html = '';
	
	$html .= "<table id='tabela-ranking'>";
	$html .= "<colgroup>";
	$html .= "<col class='coluna-condicao' />";
	$html .= "<col class='coluna-pts-equiv' />";
	$html .= "</colgroup>";
	$html .= '<tr><th>Condição</th><th>Pontuação</th></tr>';
	
	foreach ($reasons as $value) {
		$html .= '<tr><td>' . $value[1] . '</td><td>' . $value[0] . '</td></tr>';
	}
	
	$html .= '</table>';
	
	return $html;
}

// Insert a new entry when a player receives points.
function ip_insert_win ($name, $time, $event, $reason) {
	global $wpdb, $reasons;
	$table_name = $wpdb->prefix . 'indigo_plateau';
	$points = $reasons[$reason][0];
	
	$wpdb->insert(
		$table_name,
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

// Used to erase a record from the database, in case of misclick or wrong info.
function ip_delete_win ($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'indigo_plateau';
	
	$wpdb->query( "DELETE FROM $table_name WHERE id = $id" );
}

function create_partial_players ($rows) {
	$players = array();

	// Produce an array with player's name and total points
	foreach ($rows as $row) {
	$players[$row->name] += $row->points;
}
			
	// Sort players in decreasing order of total points.
	arsort($players);

	return $players;
}

function create_complete_table ($rows) {
	$ranking = "";

	$ranking .= "<div id='tabela-ranking'><table>";
	$ranking .= "<tr><th>ID</th>";
	$ranking .= "<th>Jogadores</th><th>Pontua&ccedil;&atilde;o</th>";
	$ranking .= "<th>Evento</th><th>Reason</th><th>Time</th></tr>";

	foreach ($rows as $row) {
		$ranking .= "<tr>";
		$ranking .= "<td>" . $row->id . "</td>";
		$ranking .= "<td>" . $row->name . "</td>";
		$ranking .= "<td>" . $row->points . "</td>";
		$ranking .= "<td>" . $row->event . "</td>";
		$ranking .= "<td>" . $row->reason . "</td>";
		$ranking .= "<td>" . $row->time . "</td>";
		$ranking .= "</tr>";
	}

	$ranking .= "</table></div>";

	return $ranking;
}

/**
 * create_table
 *
 * @return string
 * @author Carlos Agarie
 **/
function create_partial_table ($players) {
	$ranking = "";
	
	$ranking .= "<div id='tabela-ranking'><table>";
	$ranking .= "<colgroup>";
	$ranking .=	"<col class='coluna-jogadores' />";
	$ranking .= "<col class='coluna-pontos' />";
	$ranking .= "</colgroup>";
	$ranking .= "<tr><th>Jogadores</th><th>Pontua&ccedil;&atilde;o</th></tr>";
	
	foreach ($players as $name => $points) {
		$ranking .= "<tr>";
		$ranking .= "<td>" . $name . "</td>";
		$ranking .= "<td>" . $points . "</td>";
		$ranking .= "</tr>";
	}
	
	$ranking .= "</table></div>";
	
	return $ranking;
}

/**
 * indigo_plateau_ranking
 *
 * Returns the HTML table with the ranking's players sorted in decreasing order
 * in total points.
 *
 * @return void
 * @author Carlos Agarie
 **/
function indigo_plateau_ranking () {
	global $wpdb;
	$table_name = $wpdb->prefix . 'indigo_plateau';
	
	$rows = $wpdb->get_results( $wpdb->prepare("SELECT name, points FROM $table_name") );

	// HTML table creation.
	return create_partial_table(create_partial_players($rows));
}

function indigo_plateau_complete () {
	global $wpdb;
	$table_name = $wpdb->prefix . 'indigo_plateau';
	
	$rows = $wpdb->get_results( $wpdb->prepare("SELECT id, name, event, reason, points, time FROM $table_name") );

	// HTML table creation.
	return create_complete_table($rows);
}

// Shortcodes used to display tables easily.
add_shortcode( 'indigo_plateau_reasons', 'indigo_plateau_reasons' );
add_shortcode( 'indigo_plateau_ranking', 'indigo_plateau_ranking' );

?>