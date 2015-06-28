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

// Register ACF Felder
add_action('init', 'dsc_register_fields');
function dsc_register_fields() {
    if(function_exists("register_field_group"))
    {
        register_field_group(array (
            'id' => 'acf_laufstrecken-felder',
            'title' => 'Laufstrecken Felder',
            'fields' => array (
                array (
                    'key' => 'field_5579438670b62',
                    'label' => 'Startpunkt - Latitude',
                    'name' => 'startpunkt_-_latitude',
                    'type' => 'text',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_5579439770b63',
                    'label' => 'Startpunkt - Longitude',
                    'name' => 'startpunkt_-_longitude',
                    'type' => 'text',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_5579439e70b64',
                    'label' => 'Endpunkt - Latitude',
                    'name' => 'endpunkt_-_latitude',
                    'type' => 'text',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_557943ab70b65',
                    'label' => 'Endpunkt - Longitude',
                    'name' => 'endpunkt_-_longitude',
                    'type' => 'text',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_557fb8a2cf2c6',
                    'label' => 'Wegpunkte',
                    'name' => 'wegpunkte',
                    'type' => 'textarea',
                    'default_value' => '',
                    'placeholder' => '',
                    'maxlength' => '',
                    'rows' => '',
                    'formatting' => 'none',
                ),
                array (
                    'key' => 'field_557943ca70b66',
                    'label' => 'Blogger-Name',
                    'name' => 'blogger_name',
                    'type' => 'text',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_557943d670b67',
                    'label' => 'Blogger-URL',
                    'name' => 'blogger_url',
                    'type' => 'text',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'html',
                    'maxlength' => '',
                ),
                array (
                    'key' => 'field_557943f170b68',
                    'label' => 'Streckendatei',
                    'name' => 'streckendatei',
                    'type' => 'file',
                    'save_format' => 'object',
                    'library' => 'all',
                ),
                array (
                    'key' => 'field_557e6355ff891',
                    'label' => 'Startpunkt JSON',
                    'name' => 'startpunkt_json',
                    'type' => 'textarea',
                    'default_value' => '',
                    'placeholder' => '',
                    'maxlength' => '',
                    'rows' => '',
                    'formatting' => 'none',
                ),
                array (
                    'key' => 'field_557e6377ff892',
                    'label' => 'Endpunkt JSON',
                    'name' => 'endpunkt_json',
                    'type' => 'textarea',
                    'default_value' => '',
                    'placeholder' => '',
                    'maxlength' => '',
                    'rows' => '',
                    'formatting' => 'none',
                ),
                array (
                    'key' => 'field_557e65d65f946',
                    'label' => 'Route JSON',
                    'name' => 'route_json',
                    'type' => 'textarea',
                    'default_value' => '',
                    'placeholder' => '',
                    'maxlength' => '',
                    'rows' => '',
                    'formatting' => 'none',
                ),
                array (
                    'key' => 'field_558882b339100',
                    'label' => 'Länge',
                    'name' => 'blogger_laenge',
                    'type' => 'text',
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'formatting' => 'none',
                    'maxlength' => '',
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'laufstrecke',
                        'order_no' => 0,
                        'group_no' => 0,
                    ),
                ),
            ),
            'options' => array (
                'position' => 'side',
                'layout' => 'default',
                'hide_on_screen' => array (
                ),
            ),
            'menu_order' => 0,
        ));
    }
}
?>
