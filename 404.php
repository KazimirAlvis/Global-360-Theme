<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Global-360-Theme
 */

get_header();
?>

<main id="primary" class="site-main site-main-404">
	<section class="error-404 not-found">
		<header class="page-header">
			<h1 class="page-title">
				<span class="error-code">404</span>
				<span class="error-title">Page Not Found</span>
			</h1>
		</header>

		<div class="page-content">
			<p>We're sorry, the page you requested could not be found. Please go back to the homepage.</p>
			<a class="btn btn_green" href="<?php echo esc_url( home_url( '/' ) ); ?>">Go Home</a>
		</div>
	</section>
</main>

<?php
get_footer();
