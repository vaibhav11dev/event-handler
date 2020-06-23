<?php
/**
 *
 * Plugin Name: Event Handler
 * Plugin URI: 
 * Description: Event custom post type and Event form is ability to user add new event to front side, it's available with custom php form and jQuery validation.
 * Version: 1.0.0
 * Author: Vaibhav Parmar @vaibhav11dev
 * Author URI: 
 *
 */
if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

/**
 * Register the plugin text domain
 *
 */
load_plugin_textdomain( 'eventhandler', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Add bootstrap-css, datepicker-css, datepicker-js, validate-plugin-js, ddcustom-js
 * 
 */
function enthl_scripts() {
    wp_enqueue_style( 'eventhandler-bootstrap', plugins_url( '/', __FILE__ ) . 'assets/css/bootstrap.min.css', '', '' );
    wp_enqueue_style( 'eventhandler-ui-css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css', '', '' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-validate', plugins_url( '/', __FILE__ ) . 'assets/js/jquery.validate.min.js', array( 'jquery' ), '', true );
    wp_enqueue_script( 'jquery-ddcustom', plugins_url( '/', __FILE__ ) . 'assets/js/form-validation.js', array( 'jquery' ), '', true );
}

add_action( 'wp_enqueue_scripts', 'enthl_scripts' );

/**
 * Call different actions for event post type
 *
 */
add_action( 'init', 'enthl_event_post' );
add_action( 'add_meta_boxes', 'enthl_add_meta_boxes' );
add_action( 'save_post', 'enthl_save_meta_boxes' );

/**
 * Create Custom Post Type as Event
 * 
 * 
 * 
 */
function enthl_event_post() {
    //create custom post type
    register_post_type(
    'task', array(
        'label'               => esc_html__( 'Events', 'eventhandler' ),
        'supports'            => array( 'title', 'editor', 'thumbnail' ),
        'public'              => true,
        'rewrite'             => array( 'slug' => 'task' ),
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'exclude_from_search' => true,
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'menu-icon'           => 'dashicon-move',
        'labels'              => array(
            'name'               => _x( 'Events', 'Post Type General Name', 'eventhandler' ),
            'singular_name'      => _x( 'Event', 'Post Type Singular Name', 'eventhandler' ),
            'menu_name'          => esc_html__( 'Events', 'eventhandler' ),
            'name_admin_bar'     => esc_html__( 'Events', 'eventhandler' ),
            'archives'           => esc_html__( 'List Archives', 'eventhandler' ),
            'parent_item_colon'  => esc_html__( 'Parent List:', 'eventhandler' ),
            'all_items'          => esc_html__( 'All Events', 'eventhandler' ),
            'add_new_item'       => esc_html__( 'Add New Event', 'eventhandler' ),
            'add_new'            => esc_html__( 'Add New', 'eventhandler' ),
            'new_item'           => esc_html__( 'New Event', 'eventhandler' ),
            'edit_item'          => esc_html__( 'Edit Event', 'eventhandler' ),
            'update_item'        => esc_html__( 'Update Event', 'eventhandler' ),
            'view_item'          => esc_html__( 'View Event', 'eventhandler' ),
            'search_items'       => esc_html__( 'Search Event', 'eventhandler' ),
            'not_found'          => esc_html__( 'Not found', 'eventhandler' ),
            'not_found_in_trash' => esc_html__( 'Not found in Trash', 'eventhandler' )
        ),
    )
    );

    //Create custom texonomy for event post type
    register_taxonomy( 'event_citys', [ 'task' ], array(
        'hierarchical'      => true, // make it hierarchical (like categories)
        'labels'            => array(
            'name'              => _x( 'Event Citys', 'taxonomy general name', 'eventhandler' ),
            'singular_name'     => _x( 'Event City', 'taxonomy singular name', 'eventhandler' ),
            'search_items'      => esc_html__( 'Search Event Citys', 'eventhandler' ),
            'all_items'         => esc_html__( 'All Event Citys', 'eventhandler' ),
            'parent_item'       => esc_html__( 'Parent Event City', 'eventhandler' ),
            'parent_item_colon' => esc_html__( 'Parent Event City:', 'eventhandler' ),
            'edit_item'         => esc_html__( 'Edit Event City', 'eventhandler' ),
            'update_item'       => esc_html__( 'Update Event City', 'eventhandler' ),
            'add_new_item'      => esc_html__( 'Add New Event City', 'eventhandler' ),
            'new_item_name'     => esc_html__( 'New Event City Name', 'eventhandler' ),
            'menu_name'         => esc_html__( 'Event City', 'eventhandler' ),
        ),
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => [ 'slug' => 'event_city' ],
    )
    );
}

/**
 * Create Custom Metabox as Event Options
 * create event date as one option
 * 
 * 
 */
//Create event metabox value
function enthl_add_meta_boxes() {
    $post_types = get_post_types( array( 'public' => true ) );

    $disallowed = array( 'task' );

    enthl_add_meta_box( 'enthl_post_options', 'Event Options', 'task' );
}

//Add event metabox value
function enthl_add_meta_box( $id, $label, $post_type ) {
    add_meta_box(
    'enthl_' . $id, $label, 'enthl_post_options', $post_type
    );
}

//Save event metabox value
function enthl_save_meta_boxes( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    foreach ( $_POST as $key => $value ) {
        if ( strstr( $key, 'enthl_' ) ) {
            update_post_meta( $post_id, $key, $value );
        }
    }
}

//Create text feild for metabox
function enthl_post_options() {
    enthl_text( 'event_date', esc_html__( 'Event Date', 'eventhandler' ) );
}

//Create html format for text feild for metabox
function enthl_text( $id, $label, $desc = '' ) {
    global $post;

    $html = '';
    $html .= '<div class="krm_metabox_field">';
    $html .= '<label for="enthl_' . esc_attr( $id ) . '">';
    $html .= $label;
    $html .= '</label>';
    $html .= '<div class="field">';
    $html .= '<input type="text" id="enthl_' . esc_attr( $id ) . '" name="enthl_' . esc_attr( $id ) . '" value="' . get_post_meta( $post->ID, 'enthl_' . $id, true ) . '" />';
    if ( $desc ) {
        $html .= '<p>' . $desc . '</p>';
    }
    $html .= '</div>';
    $html .= '</div>';

    echo $html;
}

/**
 * Create shortcode for event city
 * 
 * get an event based on the city
 * 
 */
function enthl_get_event_city( $atts ) {
    if ( ! isset( $atts[ 'city_name' ] ) || empty( $atts[ 'city_name' ] ) ) {
        return '';
    }

    wp_reset_query();
    $args = array( 'post_type' => 'task',
        'tax_query' => array(
            array(
                'taxonomy' => 'event_citys',
                'field'    => 'slug',
                'terms'    => $atts[ 'city_name' ],
            ),
        ),
    );

    $loop = new WP_Query( $args );
    if ( $loop->have_posts() ) {
        echo '<h2>' . $custom_term->name . '</h2>';

        while ( $loop->have_posts() ) : $loop->the_post();
            echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a><br>';
        endwhile;
    }
}

add_shortcode( 'event-city', 'enthl_get_event_city' );

/**
 * Create Event Form Template
 * 
 * Note: First create "Event Form" page
 * 
 */
function enthl_event_form__template( $page_template ) {
    if ( is_page( 'event-form' ) ) {
        $page_template = dirname( __FILE__ ) . '/templates/event-form.php';
    }
    return $page_template;
}

add_filter( 'page_template', 'enthl_event_form__template' );
