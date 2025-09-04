<?php
/**
 * Global-360-Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Global-360-Theme
 */

require_once get_template_directory() . '/inc/meta-boxes/clinic-meta.php';
require_once get_template_directory() . '/inc/meta-boxes/doctors-meta.php';
require_once get_template_directory() . '/inc/settings.php';

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function global_360_theme_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Global-360-Theme, use a find and replace
		* to change 'global-360-theme' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'global-360-theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'global-360-theme' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'global_360_theme_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'global_360_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function global_360_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'global_360_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'global_360_theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function global_360_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'global-360-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'global-360-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'global_360_theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function global_360_theme_scripts() {
	wp_enqueue_style( 'global-360-theme-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'global-360-theme-style', 'rtl', 'replace' );

	wp_enqueue_script( 'global-360-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'global_360_theme_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Register Clinics CPT
 */
add_action( 'init', function() {
	$labels = [
		'name'               => 'Clinics',
		'singular_name'      => 'Clinic',
		'add_new_item'       => 'Add New Clinic',
		'edit_item'          => 'Edit Clinic',
		'new_item'           => 'New Clinic',
		'view_item'          => 'View Clinic',
		'search_items'       => 'Search Clinics',
		'not_found'          => 'No clinics found',
		'not_found_in_trash' => 'No clinics in trash',
		'all_items'          => 'All Clinics',
	];
	register_post_type( 'clinic', [
		'labels'             => $labels,
		'public'             => true,
		'show_in_rest'       => true,
		'has_archive'        => false,
		'rewrite'            => [ 'slug' => 'clinics' ],
		'supports'           => [ 'title', 'editor', 'thumbnail' ],
	] );
} );

/**
 * Register Doctors CPT
 */
add_action( 'init', function() {
	$labels = [
		'name'               => 'Doctors',
		'singular_name'      => 'Doctor',
		'add_new_item'       => 'Add New Doctor',
		'edit_item'          => 'Edit Doctor',
		'new_item'           => 'New Doctor',
		'view_item'          => 'View Doctor',
		'search_items'       => 'Search Doctors',
		'not_found'          => 'No doctors found',
		'not_found_in_trash' => 'No doctors in trash',
		'all_items'          => 'All Doctors',
	];
	register_post_type( 'doctor', [
		'labels'             => $labels,
		'public'             => true,
		'show_in_rest'       => true,
		'has_archive'        => false,
		'rewrite'            => [ 'slug' => 'doctors' ],
		'supports'           => [ 'title', 'editor', 'thumbnail' ],
	] );
} );

/**
 * Enqueue media uploader for clinic and doctor CPT
 */
add_action( 'admin_enqueue_scripts', function( $hook ) {
	// Only run in admin area to avoid fatal error
	if ( ! is_admin() ) {
		return;
	}
	// Only load on post edit screens for clinic or doctor CPT
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->post_type, [ 'clinic', 'doctor' ] ) ) {
		wp_enqueue_media();
		wp_enqueue_script(
			'global-360-media-meta',
			get_template_directory_uri() . '/js/media-meta-boxes.js',
			[ 'jquery' ],
			'1.0',
			true
		);
	}
	// Also load on the global settings page
	if ( $hook === 'toplevel_page_360-settings' ) {
		wp_enqueue_media();
		wp_enqueue_script(
			'global-360-media-meta',
			get_template_directory_uri() . '/js/media-meta-boxes.js',
			[ 'jquery' ],
			'1.0',
			true
		);
	}
} );

/*--------------------------------------------------------------
 adds clinic class to the article tag
--------------------------------------------------------------*/
add_filter('post_class', function ($classes, $class, $post_id) {
  // only on your CPT (or wherever you need it)
  if (get_post_type($post_id) === 'clinic') {
	$classes[] = sanitize_html_class(get_the_title($post_id), 'untitled');
  }

  return $classes;
}, 10, 3);

/*--------------------------------------------------------------
 google fonts
--------------------------------------------------------------*/
add_action('wp_enqueue_scripts', 'myclinic_enqueue_google_fonts');
function myclinic_enqueue_google_fonts()
{
	$font_url = 'https://fonts.googleapis.com/css2'
		. '?family=Roboto:ital,wght@0,400;0,700'
		. '&family=Marcellus'
		. '&family=Inter:ital,wght@0,400;0,700'
		. '&family=Arvo'
		. '&family=Bodoni+Moda'
		. '&family=Cabin'
		. '&family=Chivo'
		. '&display=swap';

  wp_enqueue_style('myclinic-google-fonts', esc_url($font_url), [], null);
}


/*--------------------------------------------------------------
 Font Awesome
--------------------------------------------------------------*/
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
	'my-plugin-fa',
	'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
	[],
	'6.5.1'
  );
});

/*--------------------------------------------------------------
 Slick CDN
--------------------------------------------------------------*/

add_action('wp_enqueue_scripts', function () {
  if (! is_singular('clinic')) {
	return;
  }

  // 1) Slick CSS from CDN
  wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], '1.8.1');
  wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', ['slick-css'], '1.8.1');

  // 2) Slick JS from CDN — depend on WP’s built-in jQuery
  wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);

  // 3) Our init script
  wp_add_inline_script('slick-js', "
	jQuery(function($){
	  $('.clinic-reviews-slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		dots: true,
		infinite:       true,      // loop back to start
		autoplay:       true,      // turn on auto‐sliding
		autoplaySpeed:  3000,

		responsive: [
		  { breakpoint: 768, settings: { slidesToShow: 1 } }
		]
	  });
	});
  ");
});


	/*--------------------------------------------------------------
	Map headings same height
	--------------------------------------------------------------*/

add_action( 'wp_footer', function(){
  if ( ! is_singular( 'clinic' ) ) {
	return;
  }
  ?>
  <script>
  // wait for *all* assets + HTML to be loaded
  window.addEventListener('load', function(){
	const headings = document.querySelectorAll('.map_heading');
	if ( ! headings.length ) return;

	// measure
	let maxH = 0;
	headings.forEach(el => {
	  const h = el.offsetHeight;           // offsetHeight is simpler here
	  if ( h > maxH ) maxH = h;
	});

	// apply
	headings.forEach(el => {
	  el.style.height = maxH + 'px';
	});
  });
  </script>
  <?php
});

	/*--------------------------------------------------------------
	Admin styles
	--------------------------------------------------------------*/
	
add_action('admin_enqueue_scripts', function() {
	wp_enqueue_style(
		'global-360-admin-meta',
		get_template_directory_uri() . '/style-admin-meta.css', // adjust path as needed
		[],
		'1.0'
	);
});


/*--------------------------------------------------------------
	state page rewriote rules
	--------------------------------------------------------------*/

add_action('init', function() {
    add_rewrite_rule('^find-a-doctor/([^/]+)/?', 'index.php?find_a_doctor_state=$matches[1]', 'top');
});
add_filter('query_vars', function($vars) {
    $vars[] = 'find_a_doctor_state';
    return $vars;
});
add_action('template_include', function($template) {
    $state = get_query_var('find_a_doctor_state');
    if ($state) {
        return get_template_directory() . '/template-find-a-doctor-state.php';
    }
    return $template;
});

/*--------------------------------------------------------------
	wrap contetn block
--------------------------------------------------------------*/

// Add to your theme's functions.php
function add_custom_block_classes($block_content, $block) {
    // Add wrapper to paragraph blocks
    if ($block['blockName'] === 'core/paragraph') {
        $block_content = '<div class="main-paragraph-con max_width_content">' . $block_content . '</div>';
    }
    
    // Add wrapper to heading blocks
    if ($block['blockName'] === 'core/heading') {
        $block_content = '<div class="main-heading-con max_width_content">' . $block_content . '</div>';
    }
    
    // Add wrapper to list blocks
    if ($block['blockName'] === 'core/list') {
        $block_content = '<div class="main-list-con max_width_content">' . $block_content . '</div>';
    }
    
    return $block_content;
}
add_filter('render_block', 'add_custom_block_classes', 10, 2);