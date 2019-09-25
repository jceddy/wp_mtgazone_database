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

// import arena cards from scryfall
function wmd_import_cards_exec() {
  // notification email
  wp_mail('dailyarena@dailyarena.net', 'MTGAZone Card Database', 'Running card import');
  
  // import the card database
  $card_database = json_decode(file_get_contents("https://clans.dailyarena.net/card_database.json"), true);
  
  $next_card_index = wp_cache_get('wmd_next_card_index');
  if($next_card_index == null) {
     $next_card_index = 0;
     wp_cache_add('wmd_next_card_index', 0);
  }
  
  $card_keys = array_keys($card_database['cards']);
  for($i = $next_card_index; $i < size_of($card_keys); $i++) {
    $arena_id = $card_keys[$i];
    $card = $card_database['cards'][$arena_id];
    
    // get the fields for the card post
    $title = $card['name'];
    $content = '<!-- wp:image {"id":' . $arena_id . '} -->' . // NEED TO UPLOAD IMAGE...
      '<figure class="wp-block-image"><img src="https://mtgazone.com/wp-content/uploads/2019/09/eld-022-mysterious-pathlighter.png" alt="eld-022-mysterious-pathlighter" class="wp-image-' . $arena_id . '"/></figure>' .
      '<!-- /wp:image -->';
    
    // update next card index
    wp_cache_add('wmd_next_card_index', $i + 1);
  }
}
