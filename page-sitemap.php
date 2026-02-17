<?php
/*
Template Name: Sitemap
*/

get_header();

$post_types = get_post_types( [ 'public' => true ], 'objects' );
$exclude    = [
	'attachment',
	'revision',
	'nav_menu_item',
	'custom_css',
	'customize_changeset',
	'wp_block',
	'oembed_cache',
	'user_request',
];

?>

<main id="primary" class="site-main">
	<div class="max_width_content_body" style="padding: 40px 0;">
		<header class="page-header">
			<h1 class="page-title"><?php the_title(); ?></h1>
		</header>

		<div class="page-content">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>

		<div class="sitemap">
			<?php
			foreach ( $post_types as $post_type ) {
				if ( empty( $post_type->name ) || in_array( $post_type->name, $exclude, true ) ) {
					continue;
				}

				$label = isset( $post_type->labels->name ) ? $post_type->labels->name : $post_type->name;
				echo '<h2>' . esc_html( $label ) . '</h2>';

				if ( 'page' === $post_type->name ) {
					echo '<ul>';
					wp_list_pages(
						[
							'title_li'    => '',
							'sort_column' => 'menu_order,post_title',
						]
					);
					echo '</ul>';
					continue;
				}

				$post_ids = get_posts(
					[
						'post_type'              => $post_type->name,
						'post_status'            => 'publish',
						'posts_per_page'         => -1,
						'orderby'                => 'title',
						'order'                  => 'ASC',
						'fields'                 => 'ids',
						'no_found_rows'          => true,
						'update_post_meta_cache' => false,
						'update_post_term_cache' => false,
					]
				);

				if ( empty( $post_ids ) ) {
					echo '<p>' . esc_html__( 'No entries found.', 'cpt360' ) . '</p>';
					continue;
				}

				echo '<ul>';
				foreach ( $post_ids as $post_id ) {
					$title = get_the_title( $post_id );
					$url   = get_permalink( $post_id );
					if ( ! $url ) {
						continue;
					}
					echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $title ? $title : $url ) . '</a></li>';
				}
				echo '</ul>';
			}
			?>
		</div>
	</div>
</main>

<?php
get_footer();
