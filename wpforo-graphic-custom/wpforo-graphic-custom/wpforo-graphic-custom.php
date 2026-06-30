<?php
/**
 * Plugin Name: WpForo Graphic Custom
 * Description: Personalizzazioni grafiche avanzate per wpForo (Statistiche, Footer, Avatar).
 * Version: 1.0.0
 * Author: MRLab Community
 * Icon: <i class="fa-solid fa-palette"></i>
 */

if (!defined('ABSPATH')) exit;

define('WPGC_PATH', plugin_dir_path(__FILE__));
define('WPGC_URL', plugin_dir_url(__FILE__));

// Carica la classe per l'evidenziazione statistiche
require_once WPGC_PATH . 'includes/class-highlight-stats.php';

class WpForo_Graphic_Custom {
    
    public function __construct() {
        // Inizializza i moduli solo se wpForo è attivo
        add_action('plugins_loaded', [$this, 'init_modules']);
    }

    public function init_modules() {
        if (class_exists('wpForo')) {
            new WPGC_Highlight_Stats();
            
            // Qui in futuro aggiungerai:
            // new WPGC_Footer_Avatar();
            // new WPGC_Footer_User_Stats();
            // new WPGC_Post_Date_Highlight();
        }
    }
}

new WpForo_Graphic_Custom();