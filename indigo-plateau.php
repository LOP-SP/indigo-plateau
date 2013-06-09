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
        // Reasons array, used to calculate the points gained by the
        // participants.
        protected $reasons = array(
            "ganharTorneio" => array(15, 'Vencer um torneio'),
            "perderFinal" => array(10, 'Perder na final de um torneio'),
            "perderQuartas" => array(5, 'Perder nas quartas de final de um torneio'),
            "defenderGinasio" => array(10, 'Defender um ginásio'),
            "trazerAmigo" => array(5, 'Trazer um amigo para participar da LOP-SP pela primeira vez'),
            "criarPost" => array(5, 'Escrever uma postagem para ser publicada no nosso site'),
            "criarRegra" => array(5, 'Criar uma sugestão de regra que seja aceita')
        );

        // public function table_name() {
        //     global $wpdb;
        //     return $wpdb->prefix . "indigo_plateau";
        // }

        public function __construct() {
            // Shortcodes used to display tables easily.
            add_shortcode('indigo_plateau_reasons', array($this, 'print_reasons'));
            add_shortcode('indigo_plateau_ranking', array($this, 'print_ranking'));
        }

        // Create a table indigo_plateau with WP's prefix.
        public function init() {
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

        //
        // CRUD operations.
        //

        public function insert_entry($name, $time, $event, $reason) {
                global $wpdb;
                $table_name = $wpdb->prefix . 'indigo_plateau';
                $points = $this->reasons[$reason][0];

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

            public function delete_entry($id) {
                global $wpdb;
                $table_name = $wpdb->prefix . "indigo_plateau";

                $wpdb->query("DELETE FROM $table_name WHERE id = $id");
            }

            public function edit_entry($id, $name, $time, $event, $reason, $points) {
                global $wpdb;
                $table_name = $wpdb->prefix . "indigo_plateau";

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
            // Functions that process the data from the database and send it
            // to the interface.
            //

            // $rows is an array of rows from the database.
            // Returns a JSON string representing all the players.
            public function jsonify_players($rows) {
              $players = array();
              $total_points = array();

              // Produce an array with a player's name, total points and history
              // of points.
              foreach ($rows as $row) {
                  $total_points[$row->name] += $row->points;
              }

              foreach ($rows as $row) {
                  // Create a Player class to store everything and enable to_json.
                  $players[$row->name] = array("total" => $total_points[$row->name],
                                               "history" => array("time" => $row->time,
                                                                  "event" => $row->event,
                                                                  "reason" => $row->reason,
                                                                  "points" => $row->points));
              }

                return json_encode($players);
            }

            // Returns a table with all the reasons.
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

            // Returns a table of players to be populated with JavaScript.
            public function print_ranking() {
                global $wpdb;
                $table_name = $wpdb->prefix . 'indigo_plateau';

                $rows = $wpdb->get_results($wpdb->prepare("SELECT name, event, reason, points, time FROM $table_name"));

                return $this->jsonify_players($rows);
            }
        }
    }

$indigo_plateau = new IndigoPlateau();

// Called when the plugin is activated.
register_activation_hook(WP_PLUGIN_DIR . '/indigo-plateau/indigo-plateau.php', array($indigo_plateau, 'init'));

//
// Menu stuff
//

function indigo_plateau_admin() {
    include_once 'indigo-plateau-admin.php';
}

function indigo_plateau_admin_actions() {
    add_options_page('Indigo Plateau', 'Indigo Plateau', 10, basename(__FILE__), 'indigo_plateau_admin');
}

add_action('admin_menu', 'indigo_plateau_admin_actions');
?>
