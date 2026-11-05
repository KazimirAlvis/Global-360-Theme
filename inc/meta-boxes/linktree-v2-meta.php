<?php
/**
 * Linktree v2 page fields.
 *
 * @package Global-360-Theme
 */

const GLOBAL360_LINKTREE_V2_SLIDES_META = '_global360_linktree_v2_slides';
const GLOBAL360_LINKTREE_V2_ASSESSMENT_LABEL_META = '_global360_linktree_v2_assessment_label';
const GLOBAL360_LINKTREE_V2_WEBSITE_LABEL_META = '_global360_linktree_v2_website_label';
const GLOBAL360_LINKTREE_V2_GUIDE_LABEL_META = '_global360_linktree_v2_guide_label';
const GLOBAL360_LINKTREE_V2_GUIDE_ID_META = '_global360_linktree_v2_guide_id';

/** Register the editor panel for pages. */
function global360_linktree_v2_register_meta_box() {
	add_meta_box(
		'global360-linktree-v2-fields',
		__( 'Linktree v2', 'cpt360' ),
		'global360_linktree_v2_render_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'global360_linktree_v2_register_meta_box' );

/**
 * Hide the panel at first paint unless the saved page already uses v2.
 * JavaScript updates this class immediately when the editor template changes.
 *
 * @param string[] $classes Meta-box classes.
 * @return string[]
 */
function global360_linktree_v2_initial_meta_box_classes( $classes ) {
	global $post;
	if ( ! $post instanceof WP_Post || 'page-linktree-v2.php' !== get_page_template_slug( $post->ID ) ) {
		$classes[] = 'global360-linktree-v2-template-hidden';
	}
	return $classes;
}
add_filter( 'postbox_classes_page_global360-linktree-v2-fields', 'global360_linktree_v2_initial_meta_box_classes' );

/**
 * Render a single sortable slide row.
 *
 * @param int                 $index Slide index.
 * @param array<string,mixed> $slide Slide data.
 */
function global360_linktree_v2_render_slide_row( $index, $slide ) {
	$attachment_id = isset( $slide['attachment_id'] ) ? absint( $slide['attachment_id'] ) : 0;
	$alt           = isset( $slide['alt'] ) ? (string) $slide['alt'] : '';
	$preview       = $attachment_id ? wp_get_attachment_image( $attachment_id, 'thumbnail', false, array( 'alt' => '' ) ) : '';
	?>
	<li class="global360-linktree-slide" data-index="<?php echo esc_attr( $index ); ?>">
		<div class="global360-linktree-slide__handle" aria-hidden="true"><span class="dashicons dashicons-move"></span></div>
		<div class="global360-linktree-slide__preview"><?php echo $preview; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<div class="global360-linktree-slide__fields">
			<input class="global360-linktree-slide__id" type="hidden" name="global360_linktree_v2_slides[<?php echo esc_attr( $index ); ?>][attachment_id]" value="<?php echo esc_attr( $attachment_id ); ?>">
			<p>
				<button type="button" class="button global360-linktree-slide__select"><?php echo esc_html( $attachment_id ? __( 'Replace image', 'cpt360' ) : __( 'Select image', 'cpt360' ) ); ?></button>
				<button type="button" class="button-link-delete global360-linktree-slide__remove-image"<?php echo $attachment_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove image', 'cpt360' ); ?></button>
			</p>
			<label>
				<?php esc_html_e( 'Alt text override', 'cpt360' ); ?>
				<input class="widefat global360-linktree-slide__alt" type="text" name="global360_linktree_v2_slides[<?php echo esc_attr( $index ); ?>][alt]" value="<?php echo esc_attr( $alt ); ?>">
			</label>
			<p class="description"><?php esc_html_e( 'Leave blank to use the image’s Media Library alt text.', 'cpt360' ); ?></p>
			<div class="global360-linktree-slide__actions">
				<button type="button" class="button global360-linktree-slide__up"><?php esc_html_e( 'Move up', 'cpt360' ); ?></button>
				<button type="button" class="button global360-linktree-slide__down"><?php esc_html_e( 'Move down', 'cpt360' ); ?></button>
				<button type="button" class="button-link-delete global360-linktree-slide__remove"><?php esc_html_e( 'Remove slide', 'cpt360' ); ?></button>
			</div>
		</div>
	</li>
	<?php
}

/** Render the Linktree v2 page fields. */
function global360_linktree_v2_render_meta_box( $post ) {
	$slides           = get_post_meta( $post->ID, GLOBAL360_LINKTREE_V2_SLIDES_META, true );
	$slides           = is_array( $slides ) ? $slides : array();
	$assessment_label = (string) get_post_meta( $post->ID, GLOBAL360_LINKTREE_V2_ASSESSMENT_LABEL_META, true );
	$website_label    = (string) get_post_meta( $post->ID, GLOBAL360_LINKTREE_V2_WEBSITE_LABEL_META, true );
	$guide_label      = (string) get_post_meta( $post->ID, GLOBAL360_LINKTREE_V2_GUIDE_LABEL_META, true );
	$guide_id         = absint( get_post_meta( $post->ID, GLOBAL360_LINKTREE_V2_GUIDE_ID_META, true ) );
	$guide_url        = $guide_id ? wp_get_attachment_url( $guide_id ) : '';

	wp_nonce_field( 'global360_linktree_v2_save', 'global360_linktree_v2_nonce' );
	?>
	<div id="global360-linktree-v2-panel" data-template="page-linktree-v2.php">
		<p class="global360-linktree-v2-notice"><?php esc_html_e( 'These fields are displayed only when the “Linktree Landing v2” page template is selected.', 'cpt360' ); ?></p>
		<h3><?php esc_html_e( 'Carousel slides', 'cpt360' ); ?></h3>
		<p class="description"><?php esc_html_e( 'Drag slides or use Move up/Move down to set their order.', 'cpt360' ); ?></p>
		<input type="hidden" name="global360_linktree_v2_slides_present" value="1">
		<ul id="global360-linktree-v2-slides">
			<?php foreach ( $slides as $index => $slide ) : ?>
				<?php global360_linktree_v2_render_slide_row( $index, is_array( $slide ) ? $slide : array() ); ?>
			<?php endforeach; ?>
		</ul>
		<p><button type="button" class="button button-secondary" id="global360-linktree-v2-add-slide"><?php esc_html_e( 'Add slide', 'cpt360' ); ?></button></p>

		<hr>
		<h3><?php esc_html_e( 'Button labels', 'cpt360' ); ?></h3>
		<p>
			<label for="global360-linktree-v2-assessment-label"><strong><?php esc_html_e( 'Assessment button', 'cpt360' ); ?></strong></label><br>
			<input class="regular-text" id="global360-linktree-v2-assessment-label" name="global360_linktree_v2_assessment_label" type="text" value="<?php echo esc_attr( $assessment_label ); ?>" placeholder="<?php esc_attr_e( 'Take Assessment', 'cpt360' ); ?>">
		</p>
		<p>
			<label for="global360-linktree-v2-website-label"><strong><?php esc_html_e( 'Website button', 'cpt360' ); ?></strong></label><br>
			<input class="regular-text" id="global360-linktree-v2-website-label" name="global360_linktree_v2_website_label" type="text" value="<?php echo esc_attr( $website_label ); ?>" placeholder="<?php esc_attr_e( 'Visit Our Website', 'cpt360' ); ?>">
		</p>
		<p>
			<label for="global360-linktree-v2-guide-label"><strong><?php esc_html_e( 'Guide button', 'cpt360' ); ?></strong></label><br>
			<input class="regular-text" id="global360-linktree-v2-guide-label" name="global360_linktree_v2_guide_label" type="text" value="<?php echo esc_attr( $guide_label ); ?>" placeholder="<?php esc_attr_e( 'Download Guide', 'cpt360' ); ?>">
		</p>

		<h3><?php esc_html_e( 'Download guide', 'cpt360' ); ?></h3>
		<input id="global360-linktree-v2-guide-id" name="global360_linktree_v2_guide_id" type="hidden" value="<?php echo esc_attr( $guide_id ); ?>">
		<p id="global360-linktree-v2-guide-preview"><?php echo $guide_url ? '<a href="' . esc_url( $guide_url ) . '" target="_blank" rel="noopener">' . esc_html( wp_basename( $guide_url ) ) . '</a>' : esc_html__( 'No guide selected.', 'cpt360' ); ?></p>
		<p>
			<button type="button" class="button" id="global360-linktree-v2-guide-select"><?php echo esc_html( $guide_id ? __( 'Replace guide', 'cpt360' ) : __( 'Select guide', 'cpt360' ) ); ?></button>
			<button type="button" class="button-link-delete" id="global360-linktree-v2-guide-remove"<?php echo $guide_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove guide', 'cpt360' ); ?></button>
		</p>
		<p class="description"><?php esc_html_e( 'The guide button is hidden on the frontend when no file is selected.', 'cpt360' ); ?></p>
	</div>
	<?php
}

/** Save the Linktree v2 fields without clearing data on unrelated requests. */
function global360_linktree_v2_save_meta( $post_id ) {
	if ( ! isset( $_POST['global360_linktree_v2_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['global360_linktree_v2_nonce'] ) ), 'global360_linktree_v2_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['global360_linktree_v2_slides_present'] ) ) {
		$raw_slides = isset( $_POST['global360_linktree_v2_slides'] ) && is_array( $_POST['global360_linktree_v2_slides'] ) ? wp_unslash( $_POST['global360_linktree_v2_slides'] ) : array();
		$slides     = array();
		foreach ( $raw_slides as $raw_slide ) {
			if ( ! is_array( $raw_slide ) ) {
				continue;
			}
			$attachment_id = isset( $raw_slide['attachment_id'] ) ? absint( $raw_slide['attachment_id'] ) : 0;
			if ( ! $attachment_id || ! wp_attachment_is_image( $attachment_id ) ) {
				continue;
			}
			$slides[] = array(
				'attachment_id' => $attachment_id,
				'alt'           => isset( $raw_slide['alt'] ) ? sanitize_text_field( $raw_slide['alt'] ) : '',
			);
		}
		update_post_meta( $post_id, GLOBAL360_LINKTREE_V2_SLIDES_META, $slides );
	}

	$text_fields = array(
		'global360_linktree_v2_assessment_label' => GLOBAL360_LINKTREE_V2_ASSESSMENT_LABEL_META,
		'global360_linktree_v2_website_label'    => GLOBAL360_LINKTREE_V2_WEBSITE_LABEL_META,
		'global360_linktree_v2_guide_label'      => GLOBAL360_LINKTREE_V2_GUIDE_LABEL_META,
	);
	foreach ( $text_fields as $field => $meta_key ) {
		if ( isset( $_POST[ $field ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
		}
	}

	if ( isset( $_POST['global360_linktree_v2_guide_id'] ) ) {
		$guide_id = absint( $_POST['global360_linktree_v2_guide_id'] );
		if ( $guide_id ) {
			update_post_meta( $post_id, GLOBAL360_LINKTREE_V2_GUIDE_ID_META, $guide_id );
		} else {
			delete_post_meta( $post_id, GLOBAL360_LINKTREE_V2_GUIDE_ID_META );
		}
	}
}
add_action( 'save_post_page', 'global360_linktree_v2_save_meta' );

/** Load editor assets only for page editing. */
function global360_linktree_v2_admin_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'global360-linktree-v2-admin', get_template_directory_uri() . '/js/linktree-v2-admin.js', array( 'jquery', 'jquery-ui-sortable', 'wp-data' ), filemtime( get_template_directory() . '/js/linktree-v2-admin.js' ), true );
	global $post;
	$saved_template = $post instanceof WP_Post ? get_page_template_slug( $post->ID ) : '';
	wp_localize_script(
		'global360-linktree-v2-admin',
		'global360LinktreeV2Admin',
		array(
			'savedTemplate' => $saved_template ? $saved_template : 'default',
			'v2Template'    => 'page-linktree-v2.php',
		)
	);
	wp_enqueue_style( 'global360-linktree-v2-admin', get_template_directory_uri() . '/assets/css/linktree-v2-admin.css', array(), filemtime( get_template_directory() . '/assets/css/linktree-v2-admin.css' ) );
}
add_action( 'admin_enqueue_scripts', 'global360_linktree_v2_admin_assets' );
