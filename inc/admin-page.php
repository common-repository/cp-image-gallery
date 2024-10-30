<?php
    /**
    * Admin init
    */
    function cp_image_gallery_admin_init() {
        register_setting( 'media', 'cp-image-gallery', 'cp_gallery_settings_sanitize' );
        // settings
        add_settings_field( 'header', '<h3 class="title">' . __( 'CP Image Gallery Settings', 'cp-image-gallery' ) . '</h3>', 'cp_gallery_header_fun', 'media', 'default' );
        add_settings_field( 'lightbox', __( 'Lightbox', 'cp-image-gallery' ), 'cp_gallery_lightbox_fun', 'media', 'default' );
        add_settings_field( 'post-types', __( 'Post Types', 'cp-image-gallery' ), 'cp_gallery_post_types_fun', 'media', 'default' );
        add_settings_field( 'usepagination', __( 'Use Pagination?', 'cp-image-gallery' ), 'cp_gallery_pagination_fun', 'media', 'default' ); 
        add_settings_field( 'numperpage', __( 'Number Of images per Page', 'cp-image-gallery' ), 'cp_gallery_per_page_fun', 'media', 'default' );   
        add_settings_field( 'cp_seometabox', __( 'Want to use Seo Options for CP Gallery?', 'cp-image-gallery' ), 'cp_gallery_seo_meta_fun', 'media', 'default' );   
        add_settings_field( 'cp_single_galllery', __( 'Want to use Full Width template for CP Gallery?', 'cp-image-gallery' ), 'cp_gallery_single_template_fun', 'media', 'default' ); 
        add_settings_field( 'cpimageresizer', __( 'Add Height and Width of the Image of Gallery', 'cp-image-gallery' ), 'cp_run_time_resizer_fun', 'media', 'default' );                 
    }   
    add_action( 'admin_init', 'cp_image_gallery_admin_init' );
    /**
    * Blank header callback
    */
    function cp_gallery_header_fun() {}
    /**
    * Lightbox callback
    */
    function cp_gallery_lightbox_fun() {

        // default option when settings have not been saved
        $defaults['lightbox'] = 'prettyphoto';

        $settings = (array) get_option( 'cp-image-gallery', $defaults );

        $lightbox = esc_attr( $settings['lightbox'] );
    ?>
    <select name="cp-image-gallery[lightbox]">
        <?php foreach ( cp_gallery_lightbox_opts() as $key => $label ) { ?>
            <option value="<?php echo $key; ?>" <?php selected( $lightbox, $key ); ?>><?php echo $label; ?></option>
            <?php } ?>
    </select>
    <?php
    }
    /**
    * Post Types callback
    */
    function cp_gallery_post_types_fun() {
        // post and page defaults
        $defaults['post_types']['cpgallery'] = 'on';

        $settings = (array) get_option( 'cp-image-gallery', $defaults );

    ?>
    <?php foreach ( cp_gallery_get_post_types() as $key => $label ) {

            $post_types = isset( $settings['post_types'][ $key ] ) ? esc_attr( $settings['post_types'][ $key ] ) : '';
        ?>
        <p>
            <input type="checkbox" id="<?php echo $key; ?>" name="cp-image-gallery[post_types][<?php echo $key; ?>]" <?php checked( $post_types, 'on' ); ?>/><label for="<?php echo $key; ?>"> <?php echo $label; ?></label>
        </p>
        <?php } ?>
    <?php

    }
    /**
    * SEO callback
    */
    function cp_gallery_seo_meta_fun(){
        $defaults['cp_seometabox'] = 'off';  
        $settings = (array) get_option( 'cp-image-gallery', $defaults );
        $cpseometa =  esc_attr( $settings['cp_seometabox'] );   
    ?>
    <p>
        <input type="checkbox" id="cp-seo-meta" name="cp-image-gallery[cp_seometabox]" <?php checked( $cpseometa, 'on' ); ?>/>
    </p>
    <?php 
    }
    /**
    * Pagination callback
    */
    function cp_gallery_pagination_fun(){
        $defaults['usepagination'] = 'on';  
        $settings = (array) get_option( 'cp-image-gallery', $defaults );
        $pagination =  esc_attr( $settings['usepagination'] );   
    ?>
    <p>
        <input type="checkbox" id="cp-paging" name="cp-image-gallery[usepagination]" <?php checked( $pagination, 'on' ); ?>/>
    </p>
    <?php 
    }
    /**
    * Callback to set number of images per page
    */
    function cp_gallery_per_page_fun(){
        $defaults['numperpage'] = (int)16; 
        $settings = (array) get_option( 'cp-image-gallery', $defaults );
        $NumPerPage = !empty( $settings['numperpage'] ) ? esc_attr( $settings['numperpage'] ): 16;  
    ?>
    <p>
        <input type="number" id="NumPerPage" name="cp-image-gallery[numperpage]" value="<?php echo $NumPerPage; ?>">
    </p>
    <?php 
}
    /**
    * Callback to set full width template for CP Gallery Detail page
    */
    function cp_gallery_single_template_fun(){
        $defaults['cp_single_galllery'] = 'off';  
        $settings = (array) get_option( 'cp-image-gallery', $defaults );
        $cpsingle_gallery =  esc_attr( $settings['cp_single_galllery'] );   
    ?>
    <p>
        <input type="checkbox" id="cp-single_gallery" name="cp-image-gallery[cp_single_galllery]" <?php checked( $cpsingle_gallery, 'on' ); ?>/>
    </p>
    <?php     
    }
    function cp_run_time_resizer_fun(){
        $defaults['cpimageresizer']['cpheight'] = (int)160;  
        $defaults['cpimageresizer']['cpwidth'] = (int)240; 
        $settings = (array) get_option( 'cp-image-gallery', $defaults );
        $cpresizer =   $settings['cpimageresizer']; 
    ?>
    <p>
        <label>Image Height</label><input type="number" id="cp-resizer-ht" name="cp-image-gallery[cpimageresizer][cpheight]" value="<?php echo $resiseht = !empty($cpresizer['cpheight'])? $cpresizer['cpheight']:(int)160; ?>" />
        <label>Image Width</label><input type="number" id="cp-resizer-wd" name="cp-image-gallery[cpimageresizer][cpwidth]" value="<?php echo $resisewd = !empty($cpresizer['cpwidth'])? $cpresizer['cpwidth']:(int)240; ?>" />
    </p>
    <?php 
    }
    /**
    * Sanitization
    */
    function cp_gallery_settings_sanitize( $input ) {
        // Create our array for storing the validated options
        $output = array();

        // lightbox
        $valid = cp_gallery_lightbox_opts();

        if ( array_key_exists( $input['lightbox'], $valid ) )
            $output['lightbox'] = $input['lightbox'];
        // post types
        $post_types = isset( $input['post_types'] ) ? $input['post_types'] : '';

        // only loop through if there are post types in the array
        if ( $post_types ) {
            foreach ( $post_types as $post_type => $value )
                $output[ 'post_types' ][ $post_type ] = isset( $input[ 'post_types' ][ $post_type ] ) ? 'on' : '';    
        } 
        $output['usepagination'] = $input['usepagination'];
        $output['numperpage'] = $input['numperpage']; 
        $output['cpgalleryslug'] = $input['cpgalleryslug']; 
        $output['cp_seometabox'] = $input['cp_seometabox'];  
        $output['cp_single_galllery'] = $input['cp_single_galllery'];
        $output['cpimageresizer']['cpheight'] = $input['cpimageresizer']['cpheight'];  
        $output['cpimageresizer']['cpwidth'] = $input['cpimageresizer']['cpwidth']; 
        return apply_filters( 'cp_gallery_settings_sanitize', $output, $input );

    }
    /**
    * Setting Action Links
    */
    function cp_gallery_action_links( $links ) {
        $settings_link = '<a href="' . admin_url( 'options-media.php' ) . '">'. __( 'Settings', 'cp-image-gallery' ) .'</a>';
        array_unshift( $links, $settings_link );

        return $links;
    }