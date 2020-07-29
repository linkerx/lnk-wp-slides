<?php

/**
 Plugin Name: LNK Slide
 Plugin URI: https://github.com/linkerx/lnk-wp-slider
 Description: Tipo de Dato Slide para Wordpress
 Version: 1
 Author: Diego
 Author URI: https://linkerx.com.ar/
 License: GPL2
 */

/**
 * Genera el tipo de dato formulario
 */
function lnk_slide_create_type(){
    register_post_type(
        'slide',
        array(
            'labels' => array(
                'name' => __('Slides','slides_name'),
                'singular_name' => __('Slide','slides_singular_name'),
                'menu_name' => __('Slides','slides_menu_name'),
                'all_items' => __('Lista de Slides','slides_all_items'),
            ),
            'description' => 'Tipo de dato de slide',
            'public' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 8,
            'support' => array(
                'title',
                'excerpt',
                'editor',
                'thumbnail',
                'revisions'
            ),
            "capability_type" => 'slides',
            "map_meta_cap" => true
        )
    );
}
add_action('init', 'lnk_slide_create_type');
add_post_type_support('slide', array('thumbnail','excerpt'));

/**
 * agrega columnas al listado de formularios
 */
function lnk_slide_add_columns($columns) {
    global $post_type;
    if($post_type == 'slide'){
        $columns['lnk_slide_orden'] = "Orden";
        $columns['lnk_slide_imagen'] = "Imagen";
    }
    return $columns;
}
add_filter ('manage_posts_columns', 'lnk_slide_add_columns');

function lnk_slide_show_columns_values($column_name) {
    global $wpdb, $post;
    $id = $post->ID;

    if($post->post_type == 'slide'){
        $id = $post->ID;
        if($column_name === 'lnk_slide_orden'){
            print get_post_meta($post->ID, 'lnk_slide_orden', true );
        } elseif($column_name === 'lnk_slide_isbn'){
            // imagen destacada
        }
    }
}
add_action ('manage_posts_custom_column', 'lnk_slide_show_columns_values');

function lnk_slide_disable_gutenberg($current_status, $post_type)
{
    if ($post_type === 'slide') return false;
    return $current_status;
}
add_filter('use_block_editor_for_post_type', 'lnk_slide_disable_gutenberg', 10, 2);

/**
 * Agrega los hooks para los datos meta en el editor de slides
 */
function lnk_slide_custom_meta() {
    global $post;
    if($post->post_type == 'slide'){
        add_meta_box('lnk_slide_orden',"Orden", 'lnk_slide_orden_meta_box', null, 'side','core');
    }
}
add_action ('add_meta_boxes','lnk_slide_custom_meta');

function lnk_slide_orden_meta_box() {
    global $post;
    $orden = get_post_meta( $post->ID, 'lnk_slide_orden', true );
    $html .= '<input type="number" id="lnk_slide_orden" name="lnk_slide_orden" value="'.$orden.'" size="3">';
    echo $html;
}

function lnk_slide_save_post_meta($id) {
    global $wpdb,$post_type;
    if($post_type == 'slide'){
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return $id;
        if (defined('DOING_AJAX') && DOING_AJAX)
                return $id;

        update_post_meta($id, 'lnk_slide_orden', $_POST['lnk_slide_orden']);
    }
}
add_action('save_post','lnk_slide_save_post_meta');
