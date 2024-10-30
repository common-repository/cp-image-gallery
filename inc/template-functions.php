<?php
    /**
    * Template functions
    *
    * @since 1.0
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
    /**
    * Is gallery
    * @return boolean
    */
    function check_if_cp_gallery_active() {

        $attachment_ids = get_post_meta( get_the_ID(), '_cp_gallery_opts', true );

        if ( $attachment_ids ) {
            return true;
        }

        return false;
    }
    /**
    * Check the current post has gallery short code
    * @return boolean
    */
    function cp_gallery_image_has_shortcode( $shortcode = '' ) {
        global $post;

        // false because we have to search through the post content first
        $found = false;

        // if no short code was provided, return false
        if ( !$shortcode ) {
            return $found;
        }

        if (  is_object( $post ) && stripos( $post->post_content, '[' . $shortcode ) !== false ) {
            // we have found the short code
            $found = true;
        }

        // return our final results
        return $found;
    }     
    /**
    * Setup Lightbox array
    * @return array
    */
    function cp_gallery_lightbox_opts() {

        $lightboxes = array(
        'fancybox' => __( 'fancyBox', 'cp-image-gallery' ),
        'prettyphoto' => __( 'prettyPhoto', 'cp-image-gallery' ),
        );

        return apply_filters( 'cp_gallery_lightbox_opts', $lightboxes );

    }
    /**
    * Get lightbox from settings
    * @return string
    */
    if ( !function_exists( 'cp_gallery_choose_lightbox' ) ) :
        function cp_gallery_choose_lightbox() {

            $settings = (array) get_option( 'cp-image-gallery' );

            // set fancybox as default for when the settings page hasn't been saved
            $lightbox = isset( $settings['lightbox'] ) ? esc_attr( $settings['lightbox'] ) : 'prettyphoto';

            return $lightbox;

        }
    endif;
    /**
    * Returns the correct rel attribute for the anchor links as per setting for lightbox
    * @return string
    */
    function cp_gallery_lightbox_rel_opts() {

        $lightbox = cp_gallery_choose_lightbox();

        switch ( $lightbox ) {

            case 'prettyphoto':

                $rel = 'prettyPhoto';

                break;

            case 'fancybox':

                $rel = 'fancybox';

            default:

                $rel = 'prettyPhoto';

                break;
        }

        return $rel;
    }
    /**
    * Has linked images
    * @return boolean true
    */
    function cp_gallery_check_linked_images() {

        $link_images = get_post_meta( get_the_ID(), '_cp_gallery_linked_images', true );

        if ( 'on' == $link_images )
            return true;
    }
    /**
    * Get list of post types for populating the checkboxes on the admin page
    * @return array
    */
    function cp_gallery_get_post_types() {
        $args = array(
        'public' => true
        );
        $post_types = array_map( 'ucfirst', get_post_types( $args ) );

        // remove attachment
        unset( $post_types[ 'attachment' ] );

        return apply_filters( 'cp_gallery_get_post_types', $post_types );

    }

    /**
    * Retrieve the allowed post types from the option row
    * Defaults to post and page when the settings have not been saved
    *
    * @return array
    */
    function cp_gallery_allowed_post_types_opts() {

        $defaults['post_types']['cpgallery'] = 'on';

        // get the allowed post type from the DB
        $settings = ( array ) get_option( 'cp-image-gallery', $defaults );
        $post_types = isset( $settings['post_types'] ) ? $settings['post_types'] : '';

        // post types don't exist, bail
        if ( ! $post_types )
            return;

        return $post_types;

    }
    /**
    * Is the currently viewed post type allowed?
    * For use on the front-end when loading scripts etc
    * @return boolean
    */
    function cp_gallery_image_allowed_post_type() {

        // post and page defaults
        $defaults['post_types']['cpgallery'] = 'on';

        // get currently viewed post type
        $post_type = ( string ) get_post_type();

        //echo $post_type; exit; // download

        // get the allowed post type from the DB
        $settings = ( array ) get_option( 'cp-image-gallery', $defaults );
        $post_types = isset( $settings['post_types'] ) ? $settings['post_types'] : '';

        // post types don't exist, bail
        if ( ! $post_types )
            return;

        // check the two against each other
        if ( array_key_exists( $post_type, $post_types ) )
            return true;
    }
    /**
    * Retrieve attachment IDs
    * @return string
    */
    function cp_gallery_image_get_ids() {
        global $post;

        if( ! isset( $post->ID) )
            return;

        $attachment_ids = get_post_meta( $post->ID, '_cp_gallery_opts', true );
        $attachment_ids = explode( ',', $attachment_ids );

        return array_filter( $attachment_ids );
    }
    /**
    * Count number of images in array
    * @return integer
    */
    function cp_gallery_image_count() {

        $images = get_post_meta( get_the_ID(), '_cp_gallery_opts', true );
        $images = explode( ',', $images );

        $number = count( $images );

        return $number;
    }
    /**
    * Output gallery
    */
    function cp_image_gallery_display() {

        $attachment_ids = cp_gallery_image_get_ids();

        global $post;

        if ( $attachment_ids ) { ?>

        <?php
            $settings = (array) get_option( 'cp-image-gallery' );  
            $cpresizer =   $settings['cpimageresizer'];
            $resiseht = !empty($cpresizer['cpheight'])? $cpresizer['cpheight']:(int)160;
            $resisewd = !empty($cpresizer['cpwidth'])? $cpresizer['cpwidth']:(int)240;
            $has_gallery_images = get_post_meta( get_the_ID(), '_cp_gallery_opts', true );

            if ( !$has_gallery_images )
                return;

            // convert string into array
            $has_gallery_images = explode( ',', get_post_meta( get_the_ID(), '_cp_gallery_opts', true ) );

            // clean the array (remove empty values)
            $has_gallery_images = array_filter( $has_gallery_images );

            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'feature' );
            $image_title = esc_attr( get_the_title( get_post_thumbnail_id( $post->ID ) ) );

            // css classes array
            $classes = array();

            // thumbnail count
            $classes[] = $has_gallery_images ? 'thumbnails-' . cp_gallery_image_count() : '';

            // linked images
            $classes[] = cp_gallery_check_linked_images() ? 'linked' : '';

            $classes = implode( ' ', $classes );

            ob_start();
        ?>
        <div id="paging_container"></div>
        <div  class="cpgallery-images-listing gallery-items gallery-images">    
            <?php
                foreach ( $attachment_ids as $attachment_id ) {

                    $classes = array( 'popup' );
                    $atts = array('height'=>(int)$resiseht,'width'=>(int)$resisewd,'attach_id'=>$attachment_id,'return'=>'Image');
                    // get original image
                    $image_link    = wp_get_attachment_image_src( $attachment_id, apply_filters( 'cp_gallery_linked_image_Size', 'large' ) );
                    $image_link    = $image_link[0];    

                    $image = cp_rtir_updateimage($atts);
                    $image_caption = get_post( $attachment_id )->post_excerpt ? esc_attr( get_post( $attachment_id )->post_excerpt ) : '';

                    $image_class = esc_attr( implode( ' ', $classes ) );

                    $lightbox = cp_gallery_choose_lightbox();

                    $rel = cp_gallery_image_count() > 1 ? 'rel="'. $lightbox .'[group]"' : 'rel="'. $lightbox .'"';

                    if ( cp_gallery_check_linked_images() )
                        $html = sprintf( '<div class="cpglry-item"><a %s href="%s" class="%s" title="%s">%s</a></div>', $rel, $image_link, $image_class, $image_caption, $image );
                    else
                        $html = sprintf( '<div class="cpglry-item">%s</div>', $image );

                    echo apply_filters( 'cp_gallery_html_of_images', $html, $rel, $image_link, $image_class, $image_caption, $image, $attachment_id, $post->ID );
                }
            ?>
        </div>
        <?php
            $gallery = ob_get_clean();

            return apply_filters( 'cp_gallery_opts', $gallery );
        ?>

        <?php }
    }
    add_filter('single_template', 'cp_gallery_single_template');
    function cp_gallery_single_template($template)
    {
        $settings = (array) get_option( 'cp-image-gallery', $defaults );
        $cpsingle_gallery =  esc_attr( $settings['cp_single_galllery'] ); 
        if ('cpgallery' == get_post_type(get_queried_object_id()) && !empty($settings['cp_single_galllery'])) {

            $template = CP_GALLERY_INC_PATH . 'single-cpgallery.php';
        }
        return $template;
    }
    /**
    * Append gallery images to page automatically
    */
    function cp_gallery_image_append_content( $content ) {

        if ( is_singular() && is_main_query() && cp_gallery_image_allowed_post_type() ) {
            $new_content = cp_image_gallery_display();
            $content .= $new_content;
        }

        return $content;

    }
    add_filter( 'the_content', 'cp_gallery_image_append_content' ); 
    /**
    * Remove the_content filter if shortcode is detected on page
    */
    function cp_gallery_image_template_redirect() {

        if ( cp_gallery_image_has_shortcode( 'cp_gallery_opts' ) )
            remove_filter( 'the_content', 'cp_gallery_image_append_content' );

    }
    /**
    * Add Shortcode for listing all gallery in one page
    */
    add_shortcode( 'display_cpgallery', 'cp_image_gallery_list_page' );    
    function cp_image_gallery_list_page(){
        ob_start();
        $query = new WP_Query( array(
        'post_type' => 'cpgallery',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'title',
        ) );
        $settings = (array) get_option( 'cp-image-gallery' );  
        $cpresizer =   $settings['cpimageresizer'];
        $resiseht = !empty($cpresizer['cpheight'])? $cpresizer['cpheight']:(int)160;
        $resisewd = !empty($cpresizer['cpwidth'])? $cpresizer['cpwidth']:(int)240;
        if ( $query->have_posts() ) { ?>
        <div class="cpgallery-listing gallery-items">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                <div id="cpgallery-<?php the_ID(); ?>" class="cpglry-item">
                    <a href="<?php the_permalink(); ?>">
                        <?php
                        $post_thumbnail_id = get_post_thumbnail_id( get_the_ID() ); 
                         $atts = array('height'=>(int)$resiseht,'width'=>(int)$resisewd,'attach_id'=>$post_thumbnail_id,'return'=>'Image');     
                         echo $image = cp_rtir_updateimage($atts);   ?>
                        <span><?php the_title(); ?></span></a>
                </div>
                <?php endwhile;
                wp_reset_postdata(); ?>
        </div>
        <?php $myvariable = ob_get_clean();
        return $myvariable;
    }
}  
    /**
    * Add SEO robots option dropdown on allowed post type edit page
    */
    function get_robots_dropbox(){
    $robots_drop = array(
    'cp_idx_fw' =>__("index,follow", 'cp-image-gallery'),
    'cp_nidx_fw' =>__("noindex,follow", 'cp-image-gallery'),
    'cp_idx_nfw' =>__("index,nofollow", 'cp-image-gallery'),
    'cp_nidx_nfw' =>__("noindex,nofollow", 'cp-image-gallery'),  
    );
    return $robots_drop; 
}
    /**
    * Add SEO meta values in head
    */
    function cp_gallery_wp_seo() {
    global $post;
    $settings = (array) get_option( 'cp-image-gallery' );  
    if(empty($settings['cp_seometabox'])){ return; }
     if ( ! cp_gallery_image_allowed_post_type() )
            return;
    $settings = ( array ) get_option( 'cp-image-gallery', $defaults );  
    $cp_keywords = get_post_meta($post->ID, '_cp_seo_keywords', true);
    if(empty($cp_keywords)){$cp_keywords = $post->post_title. ',Gallery Photos';}
    $cp_seo_desc = get_post_meta($post->ID, '_cp_seo_desc', true); 
    if(empty($cp_seo_desc)){$cp_seo_desc = 'Photos of '.$post->post_title;} 
    $cp_seo_robots = get_post_meta($post->ID, '_cp_seo_robots', true); 
    $default_keywords = $cp_keywords;
    $output = '';
    // description
    $output .=   "\t\t" .'<meta name="description" content="' . esc_attr($cp_seo_desc) . '">' . "\n";
    // keywords
    $output .= "\t\t" . '<meta name="keywords" content="' . esc_attr($cp_keywords) . '">' . "\n";
    // robots
    if (!empty($cp_seo_robots)) {
        $output .=  "\t\t" . '<meta name="robots" content="'.$cp_seo_robots.'">' . "\n";
    } else {
        $output .= "\t\t" . '<meta name="robots" content="index,follow">' . "\n";
    }
    echo $output;
}
    add_action('wp_head','cp_gallery_wp_seo',5);
    function cp_rtir_updateimage($atts){
        $upload = wp_upload_dir();
        $upload_dir = $upload['basedir'];
        $upload_url = $upload['baseurl'];
        if($atts['imgresizer_for_user'] == "imgresizer_for_user"){
            $attach_id = "";
            $width =  $atts['wt'];
            $height =  $atts['ht'];
            $img_url = $atts['image_url'];
            $picQuality = 90;   
        }else{
            $attach_id = $atts['attach_id'];
            $width = $atts['width'];  
            $height = $atts['height'];
            $img_url = $atts['imgurl'];
            $picQuality = $atts['picquality'];   
        }   

        $crop = true;
        $plugin_dir_path = $upload_dir . '/cpgallery-cache';
        $plugins_url = $upload_url . '/cpgallery-cache/';
        if ((!is_dir($plugin_dir_path)) || (!is_writable($plugin_dir_path))) {
            echo "<h3>Pls Check your Upload directory File Permissions.</h3>"; 
            return;
        }
        // this is an attachment, so we have the ID
        if ( $attach_id ) {
            $image_src = wp_get_attachment_image_src( $attach_id, 'full' );
            $file_path = get_attached_file( $attach_id );
            // this is not an attachment, let's use the image url
        } elseif ( $img_url ) {  
            $img_url_arr = explode("/uploads",$img_url);    
            $file_path = parse_url( $img_url );
            $file_path = $upload['basedir'].end($img_url_arr);
            $file_path = str_replace("\\",'/',$file_path);            
            $orig_size = getimagesize( $file_path );
            $image_src[0] = $img_url;
            $image_src[1] = $orig_size[0];
            $image_src[2] = $orig_size[1];
        }

        $file_info = pathinfo( $file_path );
        // check if file exists
        if ( !file_exists($file_path) ) 
            return;
        $extension = '.'. $file_info['extension'];
        // the image path without the extension
        $no_ext_path = $plugin_dir_path.'/'.$file_info['filename'];
        $cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;
        // checking if the file size is larger than the target size
        // if it is smaller or the same size, stop right here and return
        if ( $image_src[1] > $width ) {            
            // the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
            if ( file_exists( $cropped_img_path ) ){ 
                $cropped_img_url =  $plugins_url.basename( $cropped_img_path );
                $resized_image = array (
                'url' => $cropped_img_url,
                'width' => $width,
                'height' => $height
                ); 
                if(($atts['return'] == "Image") || ($atts['return'] == "image")){
                    return '<img src="'.$resized_image['url'].'" width="'.$resized_image['width'].'" height="'.$resized_image['height'].'">';
                }else{
                    return $resized_image['url'];  
                }
            }
            // $crop = false or no height set
            if ( $crop == false OR !$height ) {
                // calculate the size proportionaly
                $proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
                $resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;                
                // checking if the file already exists
                if ( file_exists( $resized_img_path ) ) {
                    $resized_img_url = $plugins_url.basename( $resized_img_path );   
                    $resized_image = array (
                    'url' => $resized_img_url,
                    'width' => $proportional_size[0],
                    'height' => $proportional_size[1]
                    );
                    if(($atts['return'] == "Image") || ($atts['return'] == "image")){
                        return '<img src="'.$resized_image['url'].'" width="'.$resized_image['width'].'" height="'.$resized_image['height'].'">';
                    }else{
                        return $resized_image['url'];  
                    }
                }
            }
            // check if image width is smaller than set width
            $img_size = getimagesize( $file_path );        
            if ( $img_size[0] <= $width ) $width = $img_size[0];
            // Check if GD Library installed

            if (!function_exists ('imagecreatetruecolor')) {
                echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
                return;
            }           
            // no cache files - let's finally resize it 

            $new_img_path = wp_get_image_editor( $file_path );

            if ( ! is_wp_error( $new_img_path ) ) {
                $resize = $new_img_path->resize( $width, $height, $crop );
                if(!empty($picQuality)){
                    $new_img_path->set_quality((int)$picQuality);
                }
                if ($resize !== FALSE) {

                    $new_size = $new_img_path->get_size();
                    $new_img_width = $new_size['width'];
                    $new_img_height = $new_size['height'];
                }

                $filename = $new_img_path->generate_filename( $new_img_width.'x'.$new_img_height,$plugin_dir_path, NULL  );
                $saved = $new_img_path->save( $filename );

            }

            $new_image_path = $saved['path'];
            $new_image_name = $saved['file'];
            $new_img =  $plugins_url.$new_image_name;   
            // resized output
            $resized_image = array (
            'url' => $new_img,
            'width' => $new_img_width,
            'height' => $new_img_height
            );
            if(($atts['return'] == "Image") || ($atts['return'] == "image")){
                return '<img src="'.$resized_image['url'].'" width="'.$resized_image['width'].'" height="'.$resized_image['height'].'">';
            }else{
                return $resized_image['url'];  
            }

        }
        // default output - without resizing
        $resized_image = array (
        'url' => $image_src[0],
        'width' => $width,
        'height' => $height
        );
        if(($atts['return'] == "Image") || ($atts['return'] == "image")){
            return '<img src="'.$resized_image['url'].'" width="'.$resized_image['width'].'" height="'.$resized_image['height'].'">';
        }else{
            return $resized_image['url'];  
        }
}