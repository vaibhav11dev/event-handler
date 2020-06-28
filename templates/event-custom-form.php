<?php

/**
 * Create custom post type event registration html form for frontend-side
 * 
 * 
 * @param type $username
 * @param type $password
 * @param type $email
 * @param type $first_name
 * @param type $last_name
 */
function event_registration_form( $post_title, $post_desc, $post_category, $feature_image, $post_event_date ) {
    ?>
    <!-- New Post Form -->
    <div class="col-sm-12 event-post-form">
        <h3><?php esc_attr_e( 'Event Form', 'eventhandler' ) ?></h3>
        <form name="eventform" method="post" enctype="multipart/form-data">
            <div class="col-md-12">
                <label class="control-label"><?php esc_attr_e( 'Event Name', 'eventhandler' ) ?></label>
                <input type="text" class="form-control" name="event_title" value="<?php ( isset( $_POST[ 'event_title' ] ) ? $post_title : null ) ?>" />
            </div>
            
            <div class="col-md-12">
                <label class="control-label"><?php esc_attr_e( 'Event description', 'eventhandler' ) ?></label>
                <textarea class="form-control" rows="8" name="event_desc"><?php ( isset( $_POST[ 'event_desc' ] ) ? $post_desc : null ) ?></textarea>
            </div>

            <div class="col-md-12">
                <label class="control-label"><?php esc_attr_e( 'Event Date', 'eventhandler' ) ?></label>
                <input type="text" id="datepicker" class="form-control" name="event_date" readonly="true" style="background:white;" value="<?php ( isset( $_POST[ 'event_date' ] ) ? $post_event_date : null ) ?>" />
            </div>

            <div class="col-md-12">
                <label class="control-label"><?php esc_attr_e( 'Event City', 'eventhandler' ) ?></label>
                <select name="event_category" class="form-control">
                    <?php
                    $catList = get_terms( 'event_citys', array( 'hide_empty' => false ) );
                    foreach ( $catList as $listval ) {
                        echo '<option value="' . $listval->term_id . '">' . $listval->name . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-12">
                <label class="control-label"><?php esc_attr_e( 'Event Image', 'eventhandler' ) ?></label>
                <input type="file" name="event_image" class="form-control" />
            </div>

            <div class="col-md-12">
                <input type="submit" name="event_custom_form_submit" class="btn btn-primary" value="SUBMIT" />
            </div>
        </form>
        <div class="clearfix"></div>
    </div>
    <!--// New Post Form -->
    <?php
}

/**
 * Custom post type event input field validation with WP_Error
 * 
 * 
 * @global WP_Error $reg_errors
 * @param type $username
 * @param type $password
 * @param type $email
 * @param type $first_name
 * @param type $last_name
 */
function event_registration_validation( $post_title, $post_desc, $post_category, $feature_image, $post_event_date ) {
    global $reg_errors;
    $reg_errors = new WP_Error;

    if ( empty( $post_title ) || empty( $post_category ) || empty( $feature_image ) || empty( $post_event_date ) ) {
        $reg_errors->add( 'field', 'Required form field is missing' );
    }

    if ( is_wp_error( $reg_errors ) ) {

        foreach ( $reg_errors->get_error_messages() as $error ) {

            echo '<div>';
            echo '<strong>ERROR</strong>:';
            echo $error . '<br/>';
            echo '</div>';
        }
    }
}

/**
 * Publish new custom post type event after that form is submit
 * 
 * @global WP_Error $reg_errors
 * @global type $username
 * @global type $password
 * @global type $email
 * @global type $first_name
 * @global type $last_name
 */
function event_complete_registration() {
    global $reg_errors, $post_title, $post_desc, $post_category, $feature_image, $post_event_date;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $new_post = array(
            'post_title'  => $post_title,
            'post_content' => $post_desc,
            'post_type'   => 'event',
            'post_status' => 'draft',
        );
        $pid      = wp_insert_post( $new_post );

        //insert taxonomies like categories tags or custom
        wp_set_post_terms( $pid, (array) ($post_category), 'event_citys', true );

        //insert custom fields
        update_post_meta( $pid, 'enthl_event_date', $post_event_date );

        //insert feature image
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }
        if ( $feature_image ) {
            foreach ( $_FILES as $file => $array ) {
                if ( $_FILES[ $file ][ 'error' ] !== UPLOAD_ERR_OK ) {
                    return "upload error : " . $_FILES[ $file ][ 'error' ];
                }
                $attach_id = media_handle_upload( $file, $pid );
            }
        }
        if ( $attach_id > 0 ) {
            set_post_thumbnail( $pid, $attach_id );
        }
        
        echo "Thank you!! You have create new post";
    }
}

/**
 * Display or Use this function for show custom post type event
 * registration form on frontend page.
 * 
 * 
 * @global type $username
 * @global type $password
 * @global type $email
 * @global type $website
 * @global type $first_name
 * @global type $last_name
 * @global type $nickname
 * @global type $bio
 */
function event_custom_form_function() {
    if ( isset( $_POST[ 'event_custom_form_submit' ] ) ) {
        event_registration_validation(
        $_POST[ 'event_title' ], $_POST[ 'event_desc' ], $_POST[ 'event_category' ], $_FILES[ 'event_image' ][ 'name' ], $_POST[ 'event_date' ]
        );

        // sanitize user form input
        global $post_title, $post_desc, $post_category, $feature_image, $post_event_date;
        $post_title    = sanitize_text_field( $_POST[ 'event_title' ] );
        $post_desc    = sanitize_textarea_field( $_POST[ 'event_desc' ] );
        $post_category = sanitize_text_field( $_POST[ 'event_category' ] );
        $feature_image = sanitize_text_field( $_FILES[ 'event_image' ][ 'name' ] );
        $post_event_date = sanitize_text_field( $_POST[ 'event_date' ] );

        // call @function event_complete_registration to create the user
        // only when no WP_error is found
        event_complete_registration(
        $post_title, $post_desc, $post_category, $feature_image, $post_event_date
        );
    }

    if ( is_user_logged_in() ) {
        event_registration_form(
        $post_title, $post_desc, $post_category, $feature_image, $post_event_date
        );
    }
}

/**
 * Register a new shortcode: For display form on frontend-side page
 * [event_custom_form]
 * 
 */
add_shortcode( 'event_custom_form', 'event_custom_form_shortcode' );

function event_custom_form_shortcode() {
    ob_start();
    event_custom_form_function();
    return ob_get_clean();
}