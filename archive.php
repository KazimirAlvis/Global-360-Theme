<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Global-360-Theme
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php
		$is_post_archive_style = is_category() || is_tag() || is_author() || is_date() || is_post_type_archive( 'post' );
		?>

		<?php if ( have_posts() && $is_post_archive_style ) : ?>

			<div class="entry-header sm_hero">
				<?php the_archive_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</div>

			<?php
			the_archive_description( '<div class="archive-description max_width_content_body">', '</div>' );
			?>

			<section class="latest-articles-block blog-posts-listing">
				<div class="latest-articles-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<article class="latest-article-item">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="article-image">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'medium' ); ?>
									</a>
								</div>
							<?php endif; ?>

							<div class="article-content">
								<h3 class="article-title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>

								<div class="article-meta">
									<span class="post-date"><?php echo esc_html( get_the_date() ); ?></span>
									<?php
									$categories = get_the_category();
									if ( ! empty( $categories ) ) :
										?>
										<span class="post-category"> • <?php echo esc_html( $categories[0]->name ); ?></span>
									<?php endif; ?>
								</div>

								<p class="article-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20, '...' ) ); ?></p>

								<div class="article-read-more">
									<a href="<?php the_permalink(); ?>" class="read-more-link">READ MORE →</a>
								</div>
							</div>
						</article>
					<?php endwhile; ?>

					<div class="blog-pagination">
						<?php
						echo paginate_links(
							array(
								'total'     => $wp_query->max_num_pages,
								'current'   => max( 1, get_query_var( 'paged' ) ),
								'prev_text' => '← Previous',
								'next_text' => 'Next →',
								'type'      => 'list',
							)
						);
						?>
					</div>
				</div>
			</section>

		<?php elseif ( have_posts() ) : ?>

			<header class="page-header">
				<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_type() );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php

get_footer();
