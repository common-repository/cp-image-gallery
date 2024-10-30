<?php
    /*
    * Creating a function to create our CP Gallery
    */
    function cp_gallery_type() { 
        $settings = (array) get_option( 'cp-image-gallery' ); 
        $rewrite_slug = !empty($settings['cpgalleryslug'])? $settings['cpgalleryslug']:"";      
        $labels = array(
        'name'                => _x( 'CP Galleries', 'Post Type General Name', 'cp-image-gallery' ),
        'singular_name'       => _x( 'CP Gallery', 'Post Type Singular Name', 'cp-image-gallery' ),
        'menu_name'           => __( 'CP Galleries', 'cp-image-gallery' ),
        'parent_item_colon'   => __( 'Parent CP Gallery', 'cp-image-gallery' ),
        'all_items'           => __( 'All CP Galleries', 'cp-image-gallery' ),
        'view_item'           => __( 'View CP Gallery', 'cp-image-gallery' ),
        'add_new_item'        => __( 'Add New CP Gallery', 'cp-image-gallery' ),
        'add_new'             => __( 'Add New', 'cp-image-gallery' ),
        'edit_item'           => __( 'Edit CP Gallery', 'cp-image-gallery' ),
        'update_item'         => __( 'Update CP Gallery', 'cp-image-gallery' ),
        'search_items'        => __( 'Search CP Gallery', 'cp-image-gallery' ),
        'not_found'           => __( 'Not Found', 'cp-image-gallery' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'cp-image-gallery' ),
        );

        $args = array(
        'label'               => __( 'cpgalleries', 'cp-image-gallery' ),
        'description'         => __( 'CP Gallery news and reviews', 'cp-image-gallery' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
        'taxonomies'          => array( 'genres' ),   
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'page',
        'rewrite' => array(
        'slug' => 'cpgallery'
        )  
        );
        register_post_type( 'cpgallery', $args );
        
    }
    add_action( 'init', 'cp_gallery_type', 0 );

    add_filter( 'wp_unique_post_slug', 'cp_gallery_suffix_unique_slug', 2, 6 );
    function cp_gallery_suffix_unique_slug( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {
        if ($post_type == 'cpgallery') {      
                $suffix = '-photos';
                if (strripos($slug, $suffix) === false) {
                    $slug =  $slug.$suffix;
                }
            }
        return $slug; 
}
    add_filter( 'post_type_link', 'cp_remove_gallery_slug', 10, 3 );  
    add_action( 'pre_get_posts', 'cp_parse_request_forgallery' );  
    function cp_remove_gallery_slug( $post_link, $post, $leavename ) {
        if ( ! in_array( $post->post_type, array( 'cpgallery' ) ) || 'publish' != $post->post_status )
            return $post_link;

        $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

        return $post_link;
    }

    function cp_parse_request_forgallery( $query ) {

        // Only noop the main query
        if ( ! $query->is_main_query() )
            return;

        // Only noop our very specific rewrite rule match
        if ( 2 != count( $query->query )
        || ! isset( $query->query['page'] ) )
            return;

        // 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
        if ( ! empty( $query->query['name'] ) )
            $query->set( 'post_type', array( 'post', 'cpgallery', 'page' ) );
    }
    
?>
