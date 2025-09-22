<?php
/**
 * Template for displaying tag archive pages with latest articles styling
 *
 * @package Global-360-Theme
 */

get_header();
?>

<main id="primary" class="site-main">
	
	<!-- Page Header -->
	<div class="entry-header sm_hero">
		<h1 class="entry-title">
			<?php
			// Display tag name with proper formatting
			printf( esc_html__( 'Posts tagged: %s', 'global-360-theme' ), '<span>' . single_tag_title( '', false ) . '</span>' );
			?>
		</h1>
		<?php
		// Display tag description if it exists
		$tag_description = tag_description();
		if ( $tag_description ) :
		?>
			<p class="archive-description"><?php echo $tag_description; ?></p>
		<?php endif; ?>
	</div><!-- .entry-header -->

	<!-- Tag Posts Section with Latest Articles Styling -->
	<section class="latest-articles-block blog-posts-listing tag-posts-listing">
		<div class="latest-articles-grid">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
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
								
								<?php
								$tags = get_the_tags();
								if ($tags && count($tags) > 1) : ?>
									<span class="post-tags-count"> • <?php echo count($tags); ?> tags</span>
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
				?>
				<div class="blog-pagination">
					<?php
					echo paginate_links( array(
						'prev_text' => '← Previous',
						'next_text' => 'Next →',
						'type' => 'list',
					) );
					?>
				</div>
				
			<?php
			else :
			?>
				<div class="no-posts-found">
					<h3>No posts found with this tag</h3>
					<p>There are currently no posts tagged with "<?php single_tag_title(); ?>". <a href="<?php echo home_url('/blog'); ?>">Browse all blog posts</a> or try a different tag.</p>
				</div>
			<?php endif; ?>
		</div>
	</section>

</main><!-- #main -->

<?php
get_footer();
