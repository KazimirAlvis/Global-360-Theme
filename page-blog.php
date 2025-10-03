<?php
/**
 * Template for displaying the Blog page with latest articles styling
 *
 * @package Global-360-Theme
 */

get_header();
?>

<main id="primary" class="site-main">
	
	<!-- Page Header -->
	<div class="entry-header sm_hero">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</div><!-- .entry-header -->

	<!-- Blog Posts Section with Latest Articles Styling -->
	<section class="latest-articles-block blog-posts-listing">
		<div class="latest-articles-grid">
			<?php
			// Custom query to get all blog posts
			$blog_posts = new WP_Query(array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'posts_per_page' => 12, // Show 12 posts per page
				'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
				'orderby' => 'date',
				'order' => 'DESC'
			));

			if ($blog_posts->have_posts()) :
				while ($blog_posts->have_posts()) :
					$blog_posts->the_post();
			?>
					<article class="latest-article-item">
						<?php if (has_post_thumbnail()) : ?>
							<div class="article-image">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail('medium'); ?>
								</a>
							</div>
						<?php endif; ?>
						
						<div class="article-content">
							<h3 class="article-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							
							<div class="article-meta">
								<span class="post-date"><?php echo get_the_date(); ?></span>
								<?php 
								$categories = get_the_category();
								if ($categories) : ?>
									<span class="post-category"> • <?php echo esc_html($categories[0]->name); ?></span>
								<?php endif; ?>
							</div>
							
							<p class="article-excerpt">
								<?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
							</p>
							
							<div class="article-read-more">
								<a href="<?php the_permalink(); ?>" class="read-more-link">READ MORE →</a>
							</div>
						</div>
					</article>
			<?php
				endwhile;
				
				// Pagination
				$pagination_args = array(
					'total' => $blog_posts->max_num_pages,
					'current' => max(1, get_query_var('paged')),
					'prev_text' => '← Previous',
					'next_text' => 'Next →',
					'type' => 'list',
				);
				?>
				
				<!-- Pagination -->
				<div class="blog-pagination">
					<?php echo paginate_links($pagination_args); ?>
				</div>
				
			<?php
				wp_reset_postdata();
			else :
			?>
				<div class="no-posts-found">
					<h3>No blog posts found</h3>
					<p>There are currently no blog posts to display. Please check back later!</p>
				</div>
			<?php endif; ?>
		</div>
	</section>

</main><!-- #main -->

<?php
get_footer();
