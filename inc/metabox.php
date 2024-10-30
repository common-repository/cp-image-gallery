<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
    /**
    * Add meta boxes to selected post types
    */
    function cp_gallery_add_meta_box() {
        global $post;
        $post_types = cp_gallery_allowed_post_types_opts();

        if ( ! $post_types )
            return;
        $settings = (array) get_option( 'cp-image-gallery' ); 
        foreach ( $post_types as $post_type => $status ) {
            add_meta_box( 'cp_gallery_opts', apply_filters( 'cp_gallery_meta_box_title', __( 'CP Image Gallery', 'cp-image-gallery' ) ), 'cp_image_gallery_metabox', $post_type, apply_filters( 'cp_image_gallery_meta_box_context', 'normal' ), apply_filters( 'cp_image_gallery_meta_box_priority', 'high' ) );
            if($settings['cp_seometabox'] == "on"){   
            add_meta_box('cp_gallery_seo_opts', 'SEO Settings for this CP Gallery', 'cp_gallery_seo_opts', $post_type, apply_filters( 'cp_image_gallery_meta_box_context', 'normal' ), 'high'); 
            }
        }
    }
    add_action( 'add_meta_boxes', 'cp_gallery_add_meta_box' );
    // SEO Metabox Fields
    function cp_gallery_seo_opts() {
        global $post;
        echo '<input type="hidden" name="cpseo_nonce" id="cpseo_nonce" value="' . 
        wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
        $cp_keywords = get_post_meta($post->ID, '_cp_seo_keywords', true);
        $cp_seo_desc = get_post_meta($post->ID, '_cp_seo_desc', true);   
        $getRobotsval = get_robots_dropbox();
        $cp_robots_val = get_post_meta($post->ID, '_cp_seo_robots', true);  
        echo '<label style="display: block;">Add comma seperated SEO Keywords</label><input type="text" name="_cp_seo_keywords" value="' . $cp_keywords  . '" class="widefat" /></n></n>';
        echo '<label style="display: block;">Add SEO Description</label><textarea id="_cp_seo_desc" name="_cp_seo_desc" rows="5" cols="40">'.$cp_seo_desc.'</textarea>';
        echo '<label style="display: block;">Choose Robots</label>';
        echo '<select name="_cp_seo_robots">';
        foreach($getRobotsval as $Robostkey=>$Robosval){
            if($cp_robots_val == $Robosval){ $selectrobots = "selected";}else{$selectrobots = "";}
            echo '<option name="'.$Robostkey.'" '.$selectrobots.'>'.$Robosval.'</option>';
        }
        echo '</select>';
    }
    /**
    * Render gallery metabox
    */
    function cp_image_gallery_metabox() {

            global $post;
        ?>

        <div id="gallery_images_container">

            <ul class="gallery_images">
                <?php

                    $image_gallery = get_post_meta( $post->ID, '_cp_gallery_opts', true );
                    $attachments = array_filter( explode( ',', $image_gallery ) );

                    if ( $attachments )
                        foreach ( $attachments as $attachment_id ) {
                            echo '<li class="image attachment details" data-attachment_id="' . $attachment_id . '"><div class="attachment-preview"><div class="thumbnail">
                            ' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '</div>
                            <a href="#" class="delete check" title="' . __( 'Remove image', 'cp-image-gallery' ) . '"><div class="media-modal-icon"></div></a>

                            </div></li>';
                    }
                ?>
            </ul>


            <input type="hidden" id="image_gallery" name="image_gallery" value="<?php echo esc_attr( $image_gallery ); ?>" />
            <?php wp_nonce_field( 'cp_gallery_opts', 'cp_gallery_opts' ); ?>

        </div>

        <p class="add_gallery_images hide-if-no-js">
            <a href="#"><?php _e( 'Add gallery images', 'cp-image-gallery' ); ?></a>
        </p>

        <?php

            // options don't exist yet, set to checked by default
            if ( ! get_post_meta( get_the_ID(), '_cp_gallery_linked_images', true ) )
                $checked = ' checked="checked"';
            else
                $checked = cp_gallery_check_linked_images() ? checked( get_post_meta( get_the_ID(), '_cp_gallery_linked_images', true ), 'on', false ) : '';

        ?>

        <p>
            <label for="cp_gallery_total_linked_images">
                <input type="checkbox" id="cp_gallery_total_linked_images" value="on" name="cp_gallery_total_linked_images"<?php echo $checked; ?> /> <?php _e( 'Link images to larger sizes', 'cp-image-gallery' )?>
            </label>
        </p>


        <?php
            /**
            * Props to WooCommerce for the following JS code
            */
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){

                // Uploading files
                var image_gallery_frame;
                var $image_gallery_ids = $('#image_gallery');
                var $gallery_images = $('#gallery_images_container ul.gallery_images');

                jQuery('.add_gallery_images').on( 'click', 'a', function( event ) {

                    var $el = $(this);
                    var attachment_ids = $image_gallery_ids.val();

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( image_gallery_frame ) {
                        image_gallery_frame.open();
                        return;
                    }

                    // Create the media frame.
                    image_gallery_frame = wp.media.frames.downloadable_file = wp.media({
                        // Set the title of the modal.
                        title: '<?php _e( 'Add Images to Gallery', 'cp-image-gallery' ); ?>',
                        button: {
                            text: '<?php _e( 'Add to gallery', 'cp-image-gallery' ); ?>',
                        },
                        multiple: true
                    });

                    // When an image is selected, run a callback.
                    image_gallery_frame.on( 'select', function() {

                        var selection = image_gallery_frame.state().get('selection');

                        selection.map( function( attachment ) {

                            attachment = attachment.toJSON();

                            if ( attachment.id ) {
                                attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

                                $gallery_images.append('\
                                <li class="image attachment details" data-attachment_id="' + attachment.id + '">\
                                <div class="attachment-preview">\
                                <div class="thumbnail">\
                                <img src="' + attachment.url + '" />\
                                </div>\
                                <a href="#" class="delete check" title="<?php _e( 'Remove image', 'cp-image-gallery' ); ?>"><div class="media-modal-icon"></div></a>\
                                </div>\
                                </li>');

                            }

                        } );

                        $image_gallery_ids.val( attachment_ids );
                    });

                    // Finally, open the modal.
                    image_gallery_frame.open();
                });

                // Image ordering
                $gallery_images.sortable({
                    items: 'li.image',
                    cursor: 'move',
                    scrollSensitivity:40,
                    forcePlaceholderSize: true,
                    forceHelperSize: false,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: 'eig-metabox-sortable-placeholder',
                    start:function(event,ui){
                        ui.item.css('background-color','#f6f6f6');
                    },
                    stop:function(event,ui){
                        ui.item.removeAttr('style');
                    },
                    update: function(event, ui) {
                        var attachment_ids = '';

                        $('#gallery_images_container ul li.image').css('cursor','default').each(function() {
                            var attachment_id = jQuery(this).attr( 'data-attachment_id' );
                            attachment_ids = attachment_ids + attachment_id + ',';
                        });

                        $image_gallery_ids.val( attachment_ids );
                    }
                });

                // Remove images
                $('#gallery_images_container').on( 'click', 'a.delete', function() {

                    $(this).closest('li.image').remove();

                    var attachment_ids = '';

                    $('#gallery_images_container ul li.image').css('cursor','default').each(function() {
                        var attachment_id = jQuery(this).attr( 'data-attachment_id' );
                        attachment_ids = attachment_ids + attachment_id + ',';
                    });

                    $image_gallery_ids.val( attachment_ids );

                    return false;
                } );

            });
        </script>
        <?php
    }
    /**
    * Save function
    */
    function cp_gallery_save_gallery( $post_id,$post ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        $post_types = cp_gallery_allowed_post_types_opts();

        // check user permissions
        if ( isset( $_POST[ 'post_type' ] ) && !array_key_exists( $_POST[ 'post_type' ], $post_types ) ) {
            if ( !current_user_can( 'edit_page', $post_id ) )
                return;
        }
        else {
            if ( !current_user_can( 'edit_post', $post_id ) )
                return;
        }

        if ( ! isset( $_POST[ 'cp_gallery_opts' ] ) || ! wp_verify_nonce( $_POST[ 'cp_gallery_opts' ], 'cp_gallery_opts' ) )
            return; 

        if ( isset( $_POST[ 'image_gallery' ] ) && !empty( $_POST[ 'image_gallery' ] ) ) {

            $attachment_ids = sanitize_text_field( $_POST['image_gallery'] );

            // turn comma separated values into array
            $attachment_ids = explode( ',', $attachment_ids );

            // clean the array
            $attachment_ids = array_filter( $attachment_ids  );

            // return back to comma separated list with no trailing comma. This is common when deleting the images
            $attachment_ids =  implode( ',', $attachment_ids );

            update_post_meta( $post_id, '_cp_gallery_opts', $attachment_ids );
        } else {
            delete_post_meta( $post_id, '_cp_gallery_opts' );
        }

        // link to larger images
        if ( isset( $_POST[ 'cp_gallery_total_linked_images' ] ) )
            update_post_meta( $post_id, '_cp_gallery_linked_images', $_POST[ 'cp_gallery_total_linked_images' ] );
        else
            update_post_meta( $post_id, '_cp_gallery_linked_images', 'off' );

        do_action( 'cp_gallery_save_gallery', $post_id ); 
        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !wp_verify_nonce( $_POST['cpseo_nonce'], plugin_basename(__FILE__) )) {
            return $post->ID;
        }

        // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ))
            return $post->ID;

        $cpseo_meta['_cp_seo_keywords'] = $_POST['_cp_seo_keywords'];
        $cpseo_meta['_cp_seo_desc'] = $_POST['_cp_seo_desc'];  
        $cpseo_meta['_cp_seo_robots'] = $_POST['_cp_seo_robots'];       
        foreach ($cpseo_meta as $key => $value) { 
            if( $post->post_type == 'revision' ) return; 
            $value = implode(',', (array)$value); 
            if(get_post_meta($post->ID, $key, FALSE)) { 
                update_post_meta($post->ID, $key, $value);
            } else { 
                add_post_meta($post->ID, $key, $value);
            }
            if(!$value) delete_post_meta($post->ID, $key); 
        }          
    }
    add_action( 'save_post', 'cp_gallery_save_gallery', 1, 2 );
