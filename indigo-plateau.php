<?php
/*
Plugin Name: Indigo Plateau
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

define( 'INDIGO_PLATEAU_DIR', WP_PLUGIN_DIR . '/indigo-plateau' );

global $ip_db_version;
$ip_db_version = '1.0';

// install function
function indigo_plateau_activate () {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'indigo_plateau';
	
	$create_table_sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  name tinytext NOT NULL,
	  event text DEFAULT '' NOT NULL,
	  reason VARCHAR(255) NOT NULL,
		points int NOT NULL,
	  UNIQUE KEY id (id)
	);";
	$wpdb->query($create_table_sql);
	
	add_option( 'ip_db_version', $ip_db_version );
}

register_activation_hook( __FILE__, 'indigo_plateau_activate' );

// uninstall function

// menu
function indigo_plateau_menu () {
	global $wpdb;
	
	require_once './indigo-plateau-admin.php';
}

function indigo_plateau_admin_actions () {
    add_options_page("Indigo Plateau", "Indigo Plateau", 1,
"Player's ranking", "indigo_plateau_menu");
}

add_action( 'admin_menu', 'indigo_plateau_admin_actions' );

require_once INDIGO_PLATEAU_DIR . 'input_controller.php';
require_once INDIGO_PLATEAU_DIR . 'output_controller.php';

?>