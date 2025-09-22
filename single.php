<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Global-360-Theme
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="entry-header sm_hero">
		<h1 class="entry-title"><?php the_title(); ?></h1>

	</div><!-- .entry-header -->
	<div class="post_single_body max_width_content">
		<?php
		while (have_posts()) :
			the_post();
		?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<!-- Hero Section with Title -->


				<!-- Post Content -->
				<div class="entry-content">
					<?php
					the_content(
						sprintf(
							wp_kses(
								/* translators: %s: Name of current post. Only visible to screen readers */
								__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'global-360-theme'),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							wp_kses_post(get_the_title())
						)
					);

					wp_link_pages(
						array(
							'before' => '<nav class="post-pagination"><div class="page-links">',
							'after'  => '</div></nav>',
							'link_before' => '<span class="page-number">',
							'link_after' => '</span>',
							'next_or_number' => 'number',
							'separator' => '',
							'pagelink' => '%',
						)
					);
					?>
				</div><!-- .entry-content -->

				<!-- Post Footer -->
				<footer class="entry-footer">
					<?php 
					// Custom pill-styled tags display
					$tags = get_the_tags();
					$categories = get_the_category();
					
					if ($tags) : ?>
						<div class="post-tags">
							<?php foreach ($tags as $tag) : ?>
								<a href="<?php echo get_tag_link($tag->term_id); ?>" class="tag-pill">
									<?php echo esc_html($tag->name); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php elseif ($categories) : ?>
						<div class="post-tags">
							<?php foreach ($categories as $category) : ?>
								<a href="<?php echo get_category_link($category->term_id); ?>" class="tag-pill">
									<?php echo esc_html($category->name); ?>
								</a>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<!-- Debug: No tags or categories found -->
						<div class="post-tags">
							<span class="tag-pill" style="background-color: #ffc107; color: #000;">No tags assigned</span>
						</div>
					<?php endif; ?>
				</footer><!-- .entry-footer -->

			</article><!-- #post-<?php the_ID(); ?> -->

			<!-- Related Posts Section -->
			<section class="related-posts">
				<div class="related-posts-header">
					<h2 class="related-posts-title">You might also like to read</h2>
				</div>
				<div class="related-posts-grid">
					<?php
					// Get 3 latest posts excluding current post
					$related_posts = get_posts(array(
						'numberposts' => 3,
						'post_status' => 'publish',
						'post_type' => get_post_type(),
						'exclude' => array(get_the_ID()),
					));

					if ($related_posts) :
						foreach ($related_posts as $post) :
							setup_postdata($post);
					?>
							<article class="related-post-item">
								<?php if (has_post_thumbnail()) : ?>
									<div class="related-post-thumbnail">
										<a href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail('medium'); ?>
										</a>
									</div>
								<?php endif; ?>
								
								<div class="related-post-content">
									<h3 class="related-post-title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>
									
									<p class="related-post-excerpt">
										<?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?>
									</p>
									
									<div class="article-read-more">
										<a href="<?php the_permalink(); ?>" class="read-more-link">READ MORE →</a>
									</div>
								</div>
							</article>
					<?php
						endforeach;
						wp_reset_postdata();
					else :
					?>
						<p class="no-related-posts">No related articles found.</p>
					<?php endif; ?>
				</div>
			</section>

		<?php
		endwhile; // End of the loop.
		?>
	</div><!-- .post_single_body -->

</main><!-- #main -->

<?php
get_footer();
