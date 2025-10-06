<?php
/*
 * Template: Front Page
 */
get_header();
?>
<main id="primary" class="site-main">
    <div class="front-page-content">
        <?php
        while ( have_posts() ) :
            the_post();
            the_content(); // Displays Gutenberg blocks and page content
        endwhile;
        ?>
    </div>
</main>
<?php
get_footer();
