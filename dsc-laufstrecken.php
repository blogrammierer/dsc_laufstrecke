<?php
/*
Plugin Name: Laufstrecken Plugin
Plugin URI: http://www.peakace.de
Description: Darstellung von Laufstrecken auf Karten
Version: 1.0
Author: Dennis Schlobohm
Author URI: http://www.dennis-schlobohm.de
License: GPLv2
*/

// Required Plugins
register_activation_hook( __FILE__, 'dsc_required_plugin_activate' );
function dsc_required_plugin_activate(){

    // Require parent plugin
    if ( ! is_plugin_active( 'advanced-custom-fields/acf.php' ) and current_user_can( 'activate_plugins' ) ) {
        // Stop activation redirect and show error
        wp_die('Sorry, but this plugin requires the <a href="https://wordpress.org/plugins/advanced-custom-fields/">"Advanced Custom Fields"</a> Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    }
}



// Widget include
require_once 'inc/widgets.php';

// CPT
require_once 'inc/post-types.php';

// Shortcodes
require_once 'inc/shortcodes.php';

// Static sources
require_once 'inc/static_sources.php';

// Ajax Callbacks
require_once 'inc/ajax.php';

// DONE Maps Shortcode inkl. Formular Layer
// DONE CPT "Laufstrecke"
// DONE Advanced Custom Fields required?
// DONE ScrollTo nach Submit für Modal Dialog
// DONE SVG Aufruf in den Shortcodes relativ einbauen
// DONE FEEDs
// DONE Feed onClick -> InfoWindow öffnen
// DONE Schwierigkeitsgrad Filter

// TODO CTA Widget
// TODO ACF Felder automatisiert installieren, wenn Plugin aktiviert wird
// TODO Parsen der KMF Dateien
// TODO Form nach neuen Vorgaben überarbeiten
// TODO Form Validierung
// TODO Styling
// TODO Waypoints für händische Eingabe (vervollständigen -> ab AJAX bis zur Anzeige)
// TODO Demo-Umgebung löschen / DB von df.eu entfernen


// TODO Resourcen komprimieren?
// TODO Dokumentation
?>