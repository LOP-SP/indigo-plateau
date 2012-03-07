<?php
/*
Plugin Name: Indigo Plateau
Plugin URI: http://github.com/agarie/indigo-plateau
Description: A ranking creation plugin used for championships.
Version: 1.0
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
	"ganharTorneio" => 15,
	"perderQuartas" => 10,
	"defenderGinasio" => 5,
	"trazerAmigo" => 15,
	"trazerAmigo" => 5,
	"criarPost" => 5,
	"criarRegra" => 5
);

// Insert a new entry when a player receives points.
function ip_insert_win ($name, $time, $event, $reason) {
	global $wpdb, $reasons;
	
	$points = $reasons[$reason];
	
	$wpdb->insert(
		$wpdb->prefix . 'indigo_plateau',
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

// Returns the HTML table with the ranking's players sorted in decreasing order
// in total points.
function indigo_plateau_ranking () {
	$ranking = '';
	
	return $ranking;
}

?>