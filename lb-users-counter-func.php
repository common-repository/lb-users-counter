<?php

/*    FUNCTIONS     */

function getUserOff() {
  global $wpdb;
  $table_name = $wpdb->prefix . TABLE_NAME;
  $rs = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE login = 0");
  return $rs;
}

function getUserOn() {
  global $wpdb;
  $table_name = $wpdb->prefix . TABLE_NAME;
  $rs = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE login = 1");
  return $rs;
}

function getNameUserOn() {
  global $wpdb;
  $table_name = $wpdb->prefix . TABLE_NAME;
  $rs = $wpdb->get_results("SELECT name FROM $table_name WHERE login = 1");
  $s = '';
  foreach ($rs as $i) {
    $s .= $i->name . ', ';
  }
  return substr($s, 0, strlen($s) - 2);
}

?>
