<?php
/*
 * Plugin Name: Commemorating Kings Mountain
 * Plugin URI: https://github.com/macbookandrew/commemorating-kings-mountain/
 * Description: Provides shortcodes for displaying historical information on a timeline
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/macbookandrew/commemorating-kings-mountain/
 */

if (!defined('ABSPATH')) {
    exit;
}
// register style
add_action( 'wp_enqueue_scripts', 'ckm_styles' );
function ckm_styles() {
    wp_register_style( 'ckm', plugins_url( 'css/ckm.min.css', __FILE__ ) );
}

