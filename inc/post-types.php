<?php
add_action('init', 'dsc_create_post_type');

function dsc_create_post_type() {
    register_post_type('laufstrecke', // Register Custom Post Type
        array(
            'labels' => array(
                'name' => __('Laufstrecken', 'dsc'), // Rename these to suit
                'singular_name' => __('Laufstrecke', 'dsc'),
                'add_new' => __('Neue Laufstrecke anlegen', 'dsc'),
                'add_new_item' => __('Neue Laufstrecke hinzufügen', 'dsc'),
                'edit' => __('Edit', 'dsc'),
                'edit_item' => __('Laufstrecke bearbeiten', 'dsc'),
                'new_item' => __('Neue Laufstrecke', 'dsc'),
                'view' => __('Laufstrecke ansehen', 'dsc'),
                'view_item' => __('Laufstrecke ansehen', 'dsc'),
                'search_items' => __('Laufstrecke suchen', 'dsc'),
                'not_found' => __('Keine Laufstrecke vorhanden', 'dsc'),
                'not_found_in_trash' => __('Keine Laufstrecke im Papierkorb gefunden', 'dsc')
            ),
            'public' => true,
            'hierarchical' => false, // Allows your posts to behave like Hierarchy Pages
            'has_archive' => false,
            'supports' => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail'
            ), // Go to Dashboard Custom HTML5 Blank post for supports
            'can_export' => true, // Allows export in Tools > Export
        )
    );
}

// Allow kmz for laufstrecken
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {

    // add the file extension to the array
    $existing_mimes['kmz'] = 'mime/type';
    $existing_mimes['gpx'] = 'mime/type';

    // call the modified list of extensions
    return $existing_mimes;

}
?>