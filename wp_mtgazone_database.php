<?php
/**
 * Plugin Name: MTGAZone Card Database
 * Plugin URI: https://github.com/jceddy/wp_mtgazone_database
 * Description: Plugin for populating the MTGAZone Card Database.
 * Version: 1.0
 * Author: Joseph Eddy
 * Author URI: https://www.patreon.com/DailyArena
 */

add_action('wmd_import_cards_hook', 'wmd_import_cards_exec');

if(!wp_next_scheduled('wmd_import_cards_hook')) {
  wp_schedule_event(time(), 'five_seconds', 'wmd_import_cards_hook'); 
}

register_deactivation_hook(__FILE__, 'wmd_deactivate');

function wmd_deactivate() {
  $timestamp = wp_next_scheduled('wmd_import_cards_hook');
  wp_unschedule_event($timestamp, 'wmd_import_cards_hook');
}
