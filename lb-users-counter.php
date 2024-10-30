<?php

/*
  Plugin Name: LB Users Counter
  Plugin URI: http://www.lucabonaldo.it
  Description: Questo plugin aggiunge un Widget, per visualizzare il numero di utenti online loggati e non nel sito.
  Version: 1.0
  Author: Luca Bonaldo
  Author URI: http://www.lucabonaldo.it/lb-users-counter/
  License: GPLv2 or later
 */

/*
  Copyright 2013  LB Users Counter  (email : info@lucabonaldo.it)
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
 
define('ABSPATH', get_bloginfo('wpurl'));
define('TABLE_NAME', 'lb_users_counter');
define('PLUGIN_NAME', 'lb-users-counter');

global $lb_db_version;
$lb_db_version = "1.0";

require_once 'lb-users-counter-func.php';
require_once 'lb-users-counter-widget.php';

if (!function_exists('lb_lang')) {

  add_action('plugins_loaded', 'lb_lang');

  function lb_lang() {
    load_plugin_textdomain(PLUGIN_NAME, false, dirname(plugin_basename(__FILE__)) . '/languages/');
  }

}

/*   INIT PLUGIN    */

if (!function_exists('lb_init')) {

  add_action('init', 'lb_init');

  function lb_init() {

    global $wpdb;
    global $lbuc_db_version;

    $table_name = $wpdb->prefix . TABLE_NAME;

    //if ($wpdb->get_var("show tables like '$table_name'") != $table_name)

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
             ipaddress VARCHAR(50) NOT NULL,
             timestamp INT NOT NULL,
             login TINYINT(1) NOT NULL,
             name VARCHAR(100),
             PRIMARY KEY (ipaddress)
            )DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;"; //DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
    dbDelta($sql);

    add_option("lb_db_version", $lbuc_db_version);
  }

}

/*     OPTION PAGE      */

//if (!function_exists('lbuc_menu')) {
//
//  add_action('admin_menu', 'lbuc_menu');
//
//  function lbuc_menu() {
//    add_options_page('My Options', 'LB Users Counter', 'manage_options', 'lb-users-counter.php', 'lbuc_option_page');
//  }
//
//  function lbuc_option_page() {
//    echo 'ciao';
//  }
//
//}

/*    UPDATE USERS COUNTER     */

if (!function_exists('lb_update')) {

  add_action('wp_head', 'lb_update');

  function lb_update() {

    global $wpdb;
    $table_name = $wpdb->prefix . TABLE_NAME;
    $time = time();
    $TIMESTAMP = 120;
    $ip = $_SERVER['REMOTE_ADDR'];
    $name = '';
    $login = 0;

    $current_user = wp_get_current_user();

    if (0 != $current_user->ID) {
      $name = $current_user->display_name;
      $login = 1;
    }

    $esiste = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE ipaddress = '" . $ip . "'");

    if ($esiste == 0) {
      $wpdb->query("INSERT INTO $table_name (ipaddress,timestamp,login,name) VALUES('$ip','$time','$login','$name')");
    } else {
      $wpdb->query("UPDATE $table_name SET name = '$name', login = '$login', timestamp = '$time' WHERE ipaddress = '" . $ip . "'");
    }

    $wpdb->query("DELETE FROM $table_name WHERE timestamp < '" . ($time - $TIMESTAMP) . "'");
  }

}

if (!function_exists('lb_unistall')) {

  register_uninstall_hook(__FILE__, 'lb_unistall');

  function lb_unistall() {

//    if (!defined('WP_UNINSTALL_PLUGIN'))
//      exit();

    global $wpdb;
    $table_name = $wpdb->prefix . TABLE_NAME;
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    delete_option("lb_db_version");
  }

}
?>