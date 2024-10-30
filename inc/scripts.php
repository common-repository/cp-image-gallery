<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
    /**
    * Scripts
    */
    function cp_image_gallery_script() {
        global $post;
        $settings = ( array ) get_option( 'cp-image-gallery' );   
        // return if post object is not set
        if ( !isset( $post->ID ) )
            return;
        // JS
        wp_register_script( 'pretty-photo', CP_GALLERY_PLUGIN_URL . 'inc/lib/prettyphoto/jquery.prettyPhoto.js', array( 'jquery' ), CP_GALLERY_PLUGIN_VER, true );
        wp_register_script( 'fancybox', CP_GALLERY_PLUGIN_URL . 'inc/lib/fancybox/jquery.fancybox-1.3.4.pack.js', array( 'jquery' ), CP_GALLERY_PLUGIN_VER, true );

        // CSS
        wp_register_style( 'pretty-photo', CP_GALLERY_PLUGIN_URL . 'inc/lib/prettyphoto/prettyPhoto.css', '', CP_GALLERY_PLUGIN_VER, 'screen' );
        wp_register_style( 'fancybox', CP_GALLERY_PLUGIN_URL . 'inc/lib/fancybox/jquery.fancybox-1.3.4.css', '', CP_GALLERY_PLUGIN_VER, 'screen' );

        // create a new 'css/cp-image-gallery.css' in your child theme to override CSS file completely
        if ( file_exists( get_stylesheet_directory() . '/css/cp-image-gallery.css' ) ){
            wp_register_style( 'cp-image-gallery', get_stylesheet_directory_uri() . '/css/cp-image-gallery.css', '', CP_GALLERY_PLUGIN_VER, 'screen' );        
        }  
        else{
            wp_register_style( 'cp-image-gallery', CP_GALLERY_PLUGIN_URL . 'inc/css/cp-image-gallery.css', '', CP_GALLERY_PLUGIN_VER, 'screen' );
        }
        wp_register_style( 'cp-image-gallery-paginate', CP_GALLERY_PLUGIN_URL . 'inc/css/cpPagination.css', '', CP_GALLERY_PLUGIN_VER, 'screen' );
        if($settings['usepagination'] == "on" ){
            wp_register_script( 'cp-image-gallery-paginatejs', CP_GALLERY_PLUGIN_URL . 'inc/js/jquery.cpPagination.js', array( 'jquery' ), CP_GALLERY_PLUGIN_VER, true );
            $NumPerPage = !empty( $settings['numperpage'] ) ? esc_attr( $settings['numperpage'] ): 16;
            $obj_array = array(
            'perPage' => $NumPerPage  
            );      
            wp_localize_script( 'cp-image-gallery-paginatejs', 'settingVal', $obj_array );
            wp_enqueue_script( 'cp-image-gallery-paginatejs' ); 
        }
        wp_enqueue_style( 'cp-image-gallery-paginate' );     
        if ( post_type_exists( 'cpgallery' ) ) {
            wp_enqueue_style( 'cp-image-gallery' ); 
        }

        // post type is not allowed, return
        if ( ! cp_gallery_image_allowed_post_type() )
            return;

        // needs to load only when there is a gallery
        if ( check_if_cp_gallery_active() )
            wp_enqueue_style( 'cp-image-gallery' );

        $linked_images = cp_gallery_check_linked_images();

        // only load the JS if gallery images are linked or the featured image is linked
        if ( $linked_images ) {

            $lightbox = cp_gallery_choose_lightbox();

            switch ( $lightbox ) {

                case 'prettyphoto':

                    // CSS
                    wp_enqueue_style( 'pretty-photo' );

                    // JS
                    wp_enqueue_script( 'pretty-photo' );

                    break;

                case 'fancybox':

                    // CSS
                    wp_enqueue_style( 'fancybox' );

                    // JS
                    wp_enqueue_script( 'fancybox' );

                    break;

                default:


                    break;
            }

            // allow developers to load their own scripts here
            do_action( 'cp_image_gallery_script' );

        }

    }
    add_action( 'wp_enqueue_scripts', 'cp_image_gallery_script', 20 );
    /**
    * JS
    */
    function cp_gallery_lightbox_js() {

        if ( ! cp_gallery_image_allowed_post_type() || ! check_if_cp_gallery_active() )
            return;

        if ( is_singular() && cp_gallery_check_linked_images() ) : ?>

        <?php

            $lightbox = cp_gallery_choose_lightbox();

            switch ( $lightbox ) {

                case 'prettyphoto': ob_start(); ?>

                <script>
                    jQuery(document).ready(function() {
                        jQuery("a[rel^='prettyPhoto']").prettyPhoto({
                            social_tools : false,
                            show_title : false
                        });
                    });
                </script>

                <?php 
                    $js = ob_get_clean();
                    echo apply_filters( 'cp_gallery_image_pretyphoto_js', $js );
                ?>

                <?php break;

                case 'fancybox': ob_start(); ?>

                <script>
                    jQuery(document).ready(function() {

                        jQuery("a.popup").attr('rel', 'fancybox').fancybox({
                            'transitionIn'	:	'elastic',
                            'transitionOut'	:	'elastic',
                            'speedIn'		:	200, 
                            'speedOut'		:	200, 
                            'overlayShow'	:	false
                        });
                    });
                </script>

                <?php 
                    $js = ob_get_clean();
                    echo apply_filters( 'cp_gallery_image_fancybox_js', $js );
                ?>

                <?php break;


                default:

                    break;
            }

            // allow developers to add/modify JS 
            do_action( 'cp_gallery_lightbox_js', $lightbox );
        ?>

        <?php endif; ?>

    <?php }
    add_action( 'wp_footer', 'cp_gallery_lightbox_js', 20 );
    /**
    * CSS and JS for admin
    */
    function cp_gallery_admin_assets() { ?>
    <script type="text/javascript">
        function valueChanged()
        {
            if(jQuery('input#cp-paging').length > 0){
                if(jQuery('input#cp-paging').is(":checked"))   
                    jQuery("input#NumPerPage").closest('tr').show();
                else
                    jQuery("input#NumPerPage").closest('tr').hide();
            }
        }
        jQuery(document).ready(function(){
            valueChanged();
            jQuery('input#cp-paging').change(function(){
                valueChanged();
            });
        });
    </script>
    <style>
        .attachment.details .check div {
            background-position: -60px 0;
        }

        .attachment.details .check:hover div {
            background-position: -60px 0;
        }

        .gallery_images .details.attachment {
            box-shadow: none;
        }

        .eig-metabox-sortable-placeholder {
            background: #DFDFDF;
        }

        .gallery_images .attachment.details > div {
            width: 150px;
            height: 150px;
            box-shadow: none;
        }

        .gallery_images .attachment-preview .thumbnail {
            cursor: move;
        }

        .attachment.details div:hover .check {
            display:block;
        }

        .gallery_images:after,
        #gallery_images_container:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }

        .gallery_images > li {
            float: left;
            cursor: move;
            margin: 0 20px 20px 0;
        }

        .gallery_images li.image img {
            width: 150px;
            height: auto;
        }
    </style>
    <?php }
    add_action( 'admin_head', 'cp_gallery_admin_assets' );