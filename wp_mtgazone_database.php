<?php
/**
 * Plugin Name: MTGAZone Card Database
 * Plugin URI: https://github.com/jceddy/wp_mtgazone_database
 * Description: Plugin for populating the MTGAZone Card Database.
 * Version: 0.1
 * Author: Joseph Eddy
 * Author URI: https://www.patreon.com/DailyArena
 */

/*add_filter('cron_schedules', 'wmd_add_cron_interval');

function wmd_add_cron_interval($schedules) {
  $schedules['five_seconds'] = array(
    'interval' => 5,
    'display' => esc_html__('Every Five Seconds');
  );
  
  return $schedules;
}*/

register_activation_hook(__FILE__, 'wmd_activation');

function wmd_activation() {
  if(!wp_next_scheduled('wmd_import_cards_hook')) {
    //wp_schedule_event(time(), 'five_seconds', 'wmd_import_cards_hook'); 
    wp_schedule_event(time(), 'daily', 'wmd_import_cards_hook'); 
  }
}

register_deactivation_hook(__FILE__, 'wmd_deactivate');

function wmd_deactivate() {
  $timestamp = wp_next_scheduled('wmd_import_cards_hook');
  wp_unschedule_event($timestamp, 'wmd_import_cards_hook');
}

add_action('wmd_import_cards_hook', 'wmd_import_cards_exec');

function wmd_import_cards_exec() {
  // code to import cards here
  wp_mail('dailyarena@dailyarena.net', 'MTGAZone Card Database', 'Running card import');
}
