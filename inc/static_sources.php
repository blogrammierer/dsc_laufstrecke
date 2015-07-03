<?php
add_action('wp_enqueue_scripts', 'dsc_add_js');
add_action('wp_enqueue_scripts', 'dsc_add_css');

/**
 * Javascript
 */
function dsc_add_js() {
    // Google Maps API
    wp_register_script('google-maps', '//maps.googleapis.com/maps/api/js?v=3.exp', array(), 'v3', true);
    wp_enqueue_script('google-maps'); //

    wp_register_script('dsc-laufstrecken-js', plugin_dir_url(dirname( __FILE__) ) . 'assets/js/dsc-laufstrecken.js', array(), '1.0.0', true);
    wp_enqueue_script('dsc-laufstrecken-js'); //

    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-dialog', array('jquery'),false, true);
    wp_enqueue_script( 'jquery-form', array('jquery'), false, true );

    wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}
/**
 * CSS
 */
function dsc_add_css() {
    wp_register_style('dsc-laufstrecken-css', plugin_dir_url(dirname( __FILE__)) . 'assets/css/dsc-laufstrecken.css', array(), '1.0.1', 'all');
    wp_enqueue_style('dsc-laufstrecken-css');

    wp_enqueue_style("wp-jquery-ui-dialog");
}


/**
 * AJAX URL
 */
add_action('wp_head','pluginname_ajaxurl');
function pluginname_ajaxurl() {
?>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        var dsc_pluginurl = '<?php echo plugin_dir_url(dirname( __FILE__)) ?>';
    </script>
<?php }
?>