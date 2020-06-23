<?php
/*
 *
 * Template Name: Event Form
 *
 */

get_header();
?>

<!-- New Post Form -->
<div class="col-sm-12">
    <h3><?php esc_attr_e( 'Event Form', 'eventhandler' ) ?></h3>
    <form name="eventform" method="post" enctype="multipart/form-data">
        <input type="hidden" name="ispost" value="1" />

        <div class="col-md-12">
            <label class="control-label"><?php esc_attr_e( 'Event Name', 'eventhandler' ) ?></label>
            <input type="text" class="form-control" name="event_name" />
        </div>

        <div class="col-md-12">
            <label class="control-label"><?php esc_attr_e( 'Event description', 'eventhandler' ) ?></label>
            <textarea class="form-control" rows="8" name="event_desc"></textarea>
        </div>

        <div class="col-md-12">
            <label class="control-label"><?php esc_attr_e( 'Event Date', 'eventhandler' ) ?></label>
            <input type="text" id="datepicker" class="form-control" name="event_date" />
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
            <label class="control-label"><?php esc_attr_e( 'Event Photo', 'eventhandler' ) ?></label>
            <input type="file" name="event_image" class="form-control" />
        </div>

        <div class="col-md-12">
            <input type="submit" class="btn btn-primary" value="SUBMIT" name="submitpost" />
        </div>
    </form>
    <div class="clearfix"></div>
</div>
<!--// New Post Form -->

<?php
if ( is_user_logged_in() ) {
    if ( isset( $_POST[ 'ispost' ] ) ) {
        global $current_user;
        get_currentuserinfo();

        $user_login     = $current_user->user_login;
        $user_email     = $current_user->user_email;
        $user_firstname = $current_user->user_firstname;
        $user_lastname  = $current_user->user_lastname;
        $user_id        = $current_user->ID;

        $post_title   = $_POST[ 'event_name' ];
        $sample_image = $_FILES[ 'event_image' ][ 'name' ];
        $post_content = $_POST[ 'event_desc' ];

        $new_post = array(
            'post_title'   => $post_title,
            'post_content' => $post_content,
            'post_type'    => 'task',
        );

        $pid = wp_insert_post( $new_post );

        //insert taxonomies like categories tags or custom
        wp_set_post_terms( $pid, (array) ($_POST[ 'event_category' ]), 'event_citys', true );

        //insert custom fields
        update_post_meta( $pid, 'enthl_event_date', $_POST[ 'event_date' ] );

        //insert feature image
        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once(ABSPATH . "wp-admin" . '/includes/image.php');
            require_once(ABSPATH . "wp-admin" . '/includes/file.php');
            require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        }
        if ( $_FILES ) {
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
    }
} else {
    echo "<h2 style='text-align:center;'>User must be login for add post!</h2>";
}

get_footer();
