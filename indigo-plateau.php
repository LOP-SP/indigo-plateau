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

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class IndigoPlateau {
    var $table_name;

    // Reasons array, used to calculate the points gained by the
    // participants.
    //
    // This should be stored (somehow) as a "property" in WP's database and
    // should be editable.
    var $reasons;

    /*
     * Constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'indigo_plateau';

        // TODO: Do not hardcode this.
        $this->reasons = array(
            "ganharTorneio" => array(15, 'Vencer um torneio'),
            "perderFinal" => array(10, 'Perder na final de um torneio'),
            "perderQuartas" => array(5, 'Perder nas quartas de final de um torneio'),
            "defenderGinasio" => array(15, 'Defender um ginásio')
        );

        register_activation_hook(
          WP_PLUGIN_DIR . '/indigo-plateau/indigo-plateau.php',
          array($this, 'init')
        );
    }

    // Create a table indigo_plateau with WP's prefix.
    // Shouldn't store the points in the database, only the reasons.
    public function init() {
        global $wpdb;
        $table = $this->table_name;

        $sql = "CREATE TABLE IF NOT EXISTS $table (
                    id int NOT NULL AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL,
                    time DATETIME NOT NULL,
                    event text NOT NULL,
                    reason VARCHAR(255) NOT NULL,
                    points int NOT NULL,
                    UNIQUE KEY id (id)
                  );";

        $wpdb->query($sql);
    }

    //
    // CRUD operations.
    //

    // These methods should be part of an Entry (with a better name) class.
    public function insert_entry($name, $time, $event, $reason) {
        global $wpdb;
        $table = $this->table_name;
        $points = $this->reasons[$reason][0];

        $wpdb->insert(
            $table,
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

    public function delete_entry($id) {
        global $wpdb;
        $table = $this->table_name;
        $wpdb->query("DELETE FROM $table WHERE id = $id");
    }

    public function edit_entry($id, $name, $time, $event, $reason, $points) {
        // Update the entry with $id.

        // $wpdb->update(
        //     $table_name,
        //     array(
        //         'name'))
    }

    // Update two players' names according to the final_name given.
    //
    // If $final_name is blank, update $first to $second.
    // If $final_name is blank and both $first and $second are equal,
    // do nothing.
    // Else, both $first and $second are changed to $final_name.
    public function merge_players($first, $second, $final_name = "") {
        if ($final_name == "") {
            // Update the first name to the second.
        } else {
            if ($first == $final_name) {
                // Update only the second player.
            } elseif ($second == $final_name) {
                // Update only the first player.
            } else {
                // Update both.
            }
        }
    }

    //
    // Helpers.
    //

    public function get_rows($year) {
        global $wpdb;
        $table = $this->table_name;
        return $wpdb->get_results($wpdb->prepare("SELECT name, event, reason, points, time
                                                  FROM $table
                                                  WHERE YEAR(time) = $year", ""));
    }

    //
    // Functions that process the data from the database and send it
    // to the interface.
    //

    // $rows is an array of rows from the database.
    // Returns a JSON string representing all the players.
    public function create_players($rows) {
        $players = array();

        // Produce an array with a player's name, total points and history
        // of points.
        foreach ($rows as $row) {
            $players[$row->name] = array();
            $players[$row->name]["total"] = 0;
            $players[$row->name]["history"] = array();
        }

        foreach ($rows as $row) {
            // Must concatenate the arrays of 'history'
            $players[$row->name]["total"] += $row->points;
            array_push($players[$row->name]["history"], array("time" => $row->time,
                                                              "event" => $row->event,
                                                              "reason" => $row->reason,
                                                              "points" => $row->points));
        }

        // Sort the players according to their total points.
        $points = array();
        foreach ($players as $key => $value) {
            $points[$key] = $players[$key]["total"];
        }
        array_multisort($points, SORT_DESC, $players);

        return $players;
    }

    //
    // Printing functions.
    //

    // Returns a HTML table with all Indigo Plateau's reasons.
    public function print_reasons() {
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

    // Returns a HTML representation of the players from some year.
    public function print_ranking($year) {
        $players = $this->create_players($this->get_rows($year));
        $html = "<div class=\"ip-ranking ip-accordion\">";

        // Produce a "row" for each player, already with the accordion classes.
        foreach ($players as $name => $attrs) {
            // Name and total points.
            $html .= "<h3>$name <small>" . $attrs["total"] . "</small></h3>";

            // Player history.
            $html .= "<ul>";
            foreach ($attrs["history"] as $line) {
                $date_of_occurrence = DateTime::createFromFormat(
                    "Y-m-d H:i:s", $line["time"])->format("d/m");
                $reason_text = $this->reasons[$line["reason"]][1];

                $html .= "<li>";
                $html .= "<span>" . $date_of_occurrence . "</span>";
                $html .= "<span>" . $line["event"] . "</span>";
                $html .= "<span>" . $reason_text . "</span>";
                $html .= "<span>" . $line["points"] . "</span>";
                $html .= "</li>";
            }
            $html .= "</ul>";
        }
        $html .= "</div>";

        return $html;
    }

    public function return_json() {
        header("Content-Type: application/json");
        echo $this->json_encode(create_players($this->get_rows()));
    }
}

//
// Shortcode [ranking year="2015"].
//

function ip_ranking($atts) {
    extract(shortcode_atts(array(
        'year' => date("Y"),
    ), $atts));

    $ip = new IndigoPlateau;
    $ranking = $ip->print_ranking($year);

	return $ranking;
}

add_shortcode('ranking', 'ip_ranking');

//
// Admin panel configuration.
//

function indigo_plateau_admin() {
    include_once 'indigo-plateau-admin.php';
}

function indigo_plateau_admin_actions() {
    add_options_page(
        'Indigo Plateau',
        'Indigo Plateau',
        10,
        basename(__FILE__),
        'indigo_plateau_admin'
    );
}

add_action('admin_menu', 'indigo_plateau_admin_actions');

//
// Scripts.
//

// jQuery UI's accordion is used to show a pretty table of player data.
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-accordion', true);

// Register IP's script with its dependencies.
wp_register_script(
    'indigo-plateau',
    plugins_url('indigo-plateau/indigo-plateau.js'),
    array('jquery', 'jquery-ui-accordion')
);
wp_enqueue_script('indigo-plateau');

//
// Stylesheets.
//

wp_enqueue_style(
    'jquery-style',
    'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css'
);

// Register IP's stylesheet. There aren't dependencies.
wp_register_style('indigo-plateau-style', plugins_url('indigo-plateau/indigo-plateau.css'));
wp_enqueue_style('indigo-plateau-style');
?>
