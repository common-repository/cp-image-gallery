<?php
    /**
    * The template for single cp gallery
    */

    get_header(); ?>

<div id="primary-gallery" class="content-gallery">
    <div id="content" class="site-content cp-gallery-content" role="main">

        <?php /* The loop */ ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php
                the_content();
            ?>

            <?php endwhile; ?>

    </div><!-- #content -->
    </div><!-- #primary -->
<?php get_footer(); ?>