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
	define( '_S_VERSION', '1.0.20251021213045' );
}

/**
 * Enable theme updates from WordPress admin
 */
add_filter( 'auto_update_theme', '__return_true' );

/**
 * Theme Update Checker
 * This enables the theme to be updated via WordPress admin using commit-based versioning
 * TEMPORARILY DISABLED - Use manual upload instead
 */
class Global_360_Theme_Updater {
	
	private $theme_slug;
	private $theme_version;
	private $github_username;
	private $github_repo;
	private $updater_enabled;
	private $latest_remote_version;
	private $remote_version_cache_key;
	
	function __construct() {
		$this->theme_slug = get_option( 'template' );
		$this->theme_version = _S_VERSION;
		$this->github_username = 'KazimirAlvis';
		$this->github_repo = 'Global-360-Theme';
		$this->latest_remote_version = null;
		$this->remote_version_cache_key = 'global_360_theme_latest_remote_version';
		$cached_remote_version = get_transient( $this->remote_version_cache_key );
		if ( $cached_remote_version ) {
			$this->latest_remote_version = $cached_remote_version;
		}
		
		// Enable auto-updater with folder name protection
		$this->updater_enabled = true; // Re-enabled with folder name fix
		
		if ($this->updater_enabled) {
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
			// Use a unique hook priority to override any cached notices
			add_action( 'admin_notices', array( $this, 'update_notice' ), 999 );
		}
		
		// Force clear any old cached update notices
		add_action( 'admin_init', array( $this, 'clear_old_notices' ), 1 );
		
		// Always keep the folder name fix active for manual updates
		add_filter( 'upgrader_source_selection', array( $this, 'fix_theme_folder_name' ), 10, 4 );
		
		// Additional hook to catch theme installations
		add_filter( 'wp_update_themes', array( $this, 'ensure_correct_folder_name' ), 999 );
	}
	
	/**
	 * Fix the theme folder name after download from GitHub
	 * GitHub adds -main to the folder name, but WordPress expects the original theme folder name
	 */
	public function fix_theme_folder_name( $source, $remote_source, $upgrader, $extra ) {
		// Log for debugging
		error_log('Theme Folder Fix - Source: ' . $source);
		error_log('Theme Folder Fix - Extra: ' . print_r($extra, true));
		
		// Always run for any theme that looks like our theme
		$source_basename = basename( $source );
		
		// Check if this is our theme (look for Global-360-Theme variations)
		if ( strpos( $source_basename, 'Global-360-Theme' ) === false ) {
			return $source;
		}
		
		// Force rename to exactly 'Global-360-Theme'
		$correct_name = 'Global-360-Theme';
		$correct_source = dirname( $source ) . '/' . $correct_name;
		
		// Only rename if the name is different
		if ( $source_basename !== $correct_name ) {
			error_log('Theme Folder Fix - Renaming from: ' . $source_basename . ' to: ' . $correct_name);
			
			// Remove existing target if it exists
			if ( file_exists( $correct_source ) && $correct_source !== $source ) {
				$this->recursive_delete( $correct_source );
			}
			
			// Rename the folder
			if ( rename( $source, $correct_source ) ) {
				error_log('Theme Folder Fix - Rename successful');
				$this->sync_package_version( $correct_source );
				return trailingslashit( $correct_source );
			} else {
				error_log('Theme Folder Fix - Rename failed');
			}
		}
		
		$this->sync_package_version( $source );
		return trailingslashit( $source );
	}
	
	/**
	 * Recursively delete a directory
	 */
	private function recursive_delete( $dir ) {
		if ( ! is_dir( $dir ) ) {
			return false;
		}
		
		$files = array_diff( scandir( $dir ), array( '.', '..' ) );
		foreach ( $files as $file ) {
			$path = $dir . '/' . $file;
			is_dir( $path ) ? $this->recursive_delete( $path ) : unlink( $path );
		}
		
		return rmdir( $dir );
	}

	private function sync_package_version( $package_root ) {
		if ( empty( $package_root ) || ! is_dir( $package_root ) ) {
			return;
		}

		$target_version = $this->latest_remote_version;
		if ( empty( $target_version ) ) {
			$cached_version = get_transient( $this->remote_version_cache_key );
			if ( $cached_version ) {
				$target_version = $cached_version;
			}
		}
		if ( empty( $target_version ) ) {
			return;
		}

		$package_root = rtrim( $package_root, '/\\' );
		$style_file = $package_root . '/style.css';
		$functions_file = $package_root . '/functions.php';

		if ( file_exists( $style_file ) && is_readable( $style_file ) && is_writable( $style_file ) ) {
			$style_contents = file_get_contents( $style_file );
			if ( $style_contents !== false ) {
				$updated_style = preg_replace( '/^Version:\s*.*$/mi', 'Version: ' . $target_version, $style_contents, 1 );
				if ( $updated_style && $updated_style !== $style_contents ) {
					file_put_contents( $style_file, $updated_style );
				}
			}
		}

		if ( file_exists( $functions_file ) && is_readable( $functions_file ) && is_writable( $functions_file ) ) {
			$functions_contents = file_get_contents( $functions_file );
			if ( $functions_contents !== false ) {
				$updated_functions = preg_replace(
					"/define\(\s*'_S_VERSION'\s*,\s*'[^']*'\s*\);/",
					"define( '_S_VERSION', '" . $target_version . "' );",
					$functions_contents,
					1
				);
				if ( $updated_functions && $updated_functions !== $functions_contents ) {
					file_put_contents( $functions_file, $updated_functions );
				}
			}
		}
	}
	
	/**
	 * Ensure the theme folder name is always correct after updates
	 */
	public function ensure_correct_folder_name() {
		$themes_dir = get_theme_root();
		$target_name = 'Global-360-Theme';
		
		// Look for any folder that might be our theme with wrong name
		$folders = scandir( $themes_dir );
		foreach ( $folders as $folder ) {
			if ( $folder === '.' || $folder === '..' || $folder === $target_name ) {
				continue;
			}
			
			$folder_path = $themes_dir . '/' . $folder;
			if ( ! is_dir( $folder_path ) ) {
				continue;
			}
			
			// Check if this folder contains our theme (look for style.css with our theme name)
			$style_css = $folder_path . '/style.css';
			if ( file_exists( $style_css ) ) {
				$style_content = file_get_contents( $style_css );
				if ( strpos( $style_content, 'Theme Name: Global 360 Theme' ) !== false ) {
					// This is our theme with wrong folder name - rename it
					$correct_path = $themes_dir . '/' . $target_name;
					
					if ( $folder_path !== $correct_path ) {
						error_log( 'Post-update folder fix: Renaming ' . $folder . ' to ' . $target_name );
						
						// Remove target if exists
						if ( file_exists( $correct_path ) ) {
							$this->recursive_delete( $correct_path );
						}
						
						// Rename to correct name
						if ( rename( $folder_path, $correct_path ) ) {
							error_log( 'Post-update folder fix: Successfully renamed theme folder' );
						} else {
							error_log( 'Post-update folder fix: Failed to rename theme folder' );
						}
					}
					break;
				}
			}
		}
	}
	
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}
		
		$remote_version = $this->get_remote_version();
		
		if ( $remote_version && version_compare( $this->theme_version, $remote_version, '<' ) ) {
			$transient->response[ $this->theme_slug ] = array(
				'theme' => $this->theme_slug,
				'new_version' => $remote_version,
				'url' => 'https://github.com/' . $this->github_username . '/' . $this->github_repo,
				'package' => $this->get_download_url()
			);
		}
		
		return $transient;
	}
	
	private function get_remote_version() {
		// Use commit-based versioning
		$commits_url = 'https://api.github.com/repos/' . $this->github_username . '/' . $this->github_repo . '/commits';
		
		$request = wp_remote_get( $commits_url, array(
			'timeout' => 10,
			'user-agent' => 'WordPress Theme Updater'
		) );
		
		if ( ! is_wp_error( $request ) && wp_remote_retrieve_response_code( $request ) === 200 ) {
			$body = wp_remote_retrieve_body( $request );
			$commits = json_decode( $body, true );
			
			if ( !empty( $commits ) && isset( $commits[0]['commit']['author']['date'] ) ) {
				$commit_date = $commits[0]['commit']['author']['date'];
				
				try {
					$date = new DateTime( $commit_date );
					$formatted_datetime = $date->format( 'YmdHis' );
					
					// Generate version like 1.0.20250922153045 (date + time for uniqueness per commit)
					$base_version = '1.0';
					$this->latest_remote_version = $base_version . '.' . $formatted_datetime;
					$cache_ttl = defined( 'HOUR_IN_SECONDS' ) ? HOUR_IN_SECONDS : 3600;
					set_transient( $this->remote_version_cache_key, $this->latest_remote_version, $cache_ttl );
					return $this->latest_remote_version;
				} catch ( Exception $e ) {
					// Log error if needed
					error_log( 'Theme updater date parsing error: ' . $e->getMessage() );
					return false;
				}
			}
		} else {
			// Log the error for debugging
			if ( is_wp_error( $request ) ) {
				error_log( 'Theme updater GitHub API error: ' . $request->get_error_message() );
			} else {
				error_log( 'Theme updater GitHub API response code: ' . wp_remote_retrieve_response_code( $request ) );
			}
		}
		
		if ( $this->latest_remote_version ) {
			return $this->latest_remote_version;
		}
		
		$cached_version = get_transient( $this->remote_version_cache_key );
		if ( $cached_version ) {
			$this->latest_remote_version = $cached_version;
			return $cached_version;
		}
		
		return false;
	}
	
	private function get_download_url() {
		// Use a more reliable GitHub download method
		// Option 1: Direct archive URL with proper headers
		return 'https://api.github.com/repos/' . $this->github_username . '/' . $this->github_repo . '/zipball/main';
		
		// Fallback: Original URL (commented out)
		// return 'https://github.com/' . $this->github_username . '/' . $this->github_repo . '/archive/refs/heads/main.zip';
	}
	
	public function get_remote_version_public() {
		$version = $this->get_remote_version();
		if ( ! $version && $this->latest_remote_version ) {
			return $this->latest_remote_version;
		}
		return $version;
	}
	
	public function update_notice() {
		$screen = get_current_screen();
		if ( $screen->id !== 'themes' ) {
			return;
		}
		
		$remote_version = $this->get_remote_version();
		
		if ( $remote_version && version_compare( $this->theme_version, $remote_version, '<' ) ) {
			echo '<div class="notice notice-success is-dismissible" id="global-360-theme-update-notice">';
			echo '<p><strong>🔄 FRESH UPDATE NOTICE - Global 360 Theme Update Available!</strong></p>';
			echo '<p>Version ' . esc_html( $remote_version ) . ' is now available. You are currently using version ' . esc_html( $this->theme_version ) . '.</p>';
			if ( $this->updater_enabled ) {
				echo '<p><strong>✅ Auto-updater is ENABLED and WORKING</strong></p>';
				echo '<p><a href="' . admin_url( 'update-core.php?action=do-theme-upgrade' ) . '" class="button button-primary">Update Theme Now</a></p>';
			} else {
				echo '<p><strong>❌ Auto-updater is disabled</strong></p>';
			}
			echo '</div>';
			
			// Hide any old cached notices with JavaScript
			echo '<script>
			jQuery(document).ready(function($) {
				$(".notice").each(function() {
					var text = $(this).text();
					if (text.includes("Manual Update Required") || text.includes("temporarily disabled")) {
						$(this).hide();
					}
				});
			});
			</script>';
		}
	}
	
	public function clear_old_notices() {
		// Clear all possible cached update notices
		delete_transient('global_360_theme_update_notice');
		delete_option('global_360_theme_update_notice');
		delete_transient('_transient_global_360_theme_notices');
	}
	

}

// Initialize the updater
new Global_360_Theme_Updater();

/**
 * FORCE CLEAR ALL CACHES - Manual update recovery
 */
add_action('after_setup_theme', function() {
    // Nuclear option - clear everything theme related
    global $wp_object_cache;
    if ($wp_object_cache) {
        $wp_object_cache->flush();
    }
    
    // Clear all transients
    delete_site_transient('update_themes');
    delete_transient('update_themes');
    delete_option('_site_transient_update_themes');
    delete_option('_site_transient_timeout_update_themes');
    
    // Force WordPress to forget the old version
    wp_clean_themes_cache();
    
    // Clear theme data cache
    wp_cache_delete('themes', 'themes');
    wp_cache_delete(get_option('stylesheet'), 'themes');
    wp_cache_delete(get_option('template'), 'themes');
    
    error_log('NUCLEAR CACHE CLEAR - Version: ' . _S_VERSION);
}, 1);

/**
 * Sync style.css version with _S_VERSION constant
 */
add_action('init', function() {
	$style_css_path = get_template_directory() . '/style.css';
	
	if (file_exists($style_css_path)) {
		$style_content = file_get_contents($style_css_path);
		
		// Check if version in style.css matches _S_VERSION
		if (preg_match('/Version:\s*(.+)/i', $style_content, $matches)) {
			$style_version = trim($matches[1]);
			
			if ($style_version !== _S_VERSION) {
				// Update style.css version to match _S_VERSION
				$updated_content = preg_replace(
					'/Version:\s*(.+)/i', 
					'Version: ' . _S_VERSION, 
					$style_content
				);
				
				file_put_contents($style_css_path, $updated_content);
				error_log('Auto-synced style.css version to: ' . _S_VERSION);
			}
		}
	}
}, 1);

/**
 * Aggressive cache clearing to fix persistent old notices
 */
add_action('init', function() {
    // Clear ALL WordPress caches related to themes and updates
    delete_site_transient('update_themes');
    delete_transient('update_themes');
    delete_option('_site_transient_update_themes');
    delete_option('_site_transient_timeout_update_themes');
    delete_transient('update_themes');
    
    // Clear theme-specific caches
    wp_clean_themes_cache();
    
    // Clear all admin notices and update notices
    delete_transient('global_360_theme_notices');
    delete_transient('global_360_theme_update_notice');
    delete_option('global_360_theme_update_notice');
    
    // Force WordPress to re-read theme data
    if (function_exists('wp_get_theme')) {
        wp_get_theme(get_option('template'), get_option('template'));
    }
}, 1); // Run early

// Additional cache clearing for admin pages
add_action('admin_init', function() {
    // Force refresh theme data on admin pages
    if (function_exists('wp_clean_themes_cache')) {
        wp_clean_themes_cache();
    }
});

/**
 * Add theme update menu to admin
 */
add_action( 'admin_menu', function() {
	add_theme_page(
		'Theme Updates',
		'Theme Updates',
		'update_themes',
		'global-360-theme-updates',
		'global_360_theme_updates_page'
	);
});

/**
 * Theme updates admin page
 */
function global_360_theme_updates_page() {
	$current_version = _S_VERSION;
	$updater = new Global_360_Theme_Updater();
	
	echo '<div class="wrap">';
	echo '<h1>Global 360 Theme Updates</h1>';
	
	// Force check for updates
	if ( isset( $_POST['check_updates'] ) ) {
		delete_transient( 'update_themes' );
		delete_site_transient( 'update_themes' );
		// Clear our custom transient too
		delete_transient( '360_global_theme_update_check' );
		wp_update_themes();
		echo '<div class="notice notice-success"><p>Update check completed!</p></div>';
	}
	
	echo '<div class="card">';
	echo '<h2>Current Theme Version</h2>';
	echo '<p><strong>Installed Version:</strong> ' . esc_html( $current_version ) . '</p>';
	
	// Debug information
	echo '<p><em>Debug - _S_VERSION constant: ' . esc_html( _S_VERSION ) . '</em></p>';
	echo '<p><em>Debug - WordPress theme version: ' . esc_html( wp_get_theme()->get('Version') ) . '</em></p>';
	
	$remote_version = $updater->get_remote_version_public();
	if ( $remote_version ) {
		echo '<p><strong>Latest Available:</strong> ' . esc_html( $remote_version ) . '</p>';
		
		if ( version_compare( $current_version, $remote_version, '<' ) ) {
			echo '<p style="color: #d63638;"><strong>Update Available!</strong> A new version is ready to install.</p>';
			echo '<a href="' . admin_url( 'themes.php' ) . '" class="button button-primary">Go to Themes Page to Update</a>';
		} else {
			echo '<p style="color: #00a32a;"><strong>Up to Date!</strong> You have the latest version installed.</p>';
		}
	} else {
		echo '<p style="color: #d63638;">Unable to check for updates at this time.</p>';
		echo '<p><em>This might be due to GitHub API limits or network issues. Try again in a few minutes.</em></p>';
	}
	
	echo '</div>';
	
	echo '<div class="card" style="margin-top: 20px;">';
	echo '<h2>Manual Update Check</h2>';
	echo '<p>Click the button below to manually check for theme updates.</p>';
	echo '<form method="post">';
	echo '<input type="hidden" name="check_updates" value="1">';
	submit_button( 'Check for Updates', 'secondary', 'submit', false );
	echo '</form>';
	echo '</div>';
	
	echo '<div class="card" style="margin-top: 20px;">';
	echo '<h2>Update Information</h2>';
	echo '<p><strong>Repository:</strong> <a href="https://github.com/KazimirAlvis/Global-360-Theme" target="_blank">GitHub Repository</a></p>';
	echo '<p><strong>Automatic Updates:</strong> Enabled - WordPress will automatically check for and install theme updates.</p>';
	echo '<p><strong>Update Source:</strong> GitHub Releases</p>';
	echo '</div>';
	
	echo '</div>';
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
	// Enqueue main stylesheet with high priority
	wp_enqueue_style( 'global-360-theme-style', get_stylesheet_uri(), array(), _S_VERSION, 'all' );
	wp_style_add_data( 'global-360-theme-style', 'rtl', 'replace' );
	
	// Add preload for stylesheet to improve loading
	wp_enqueue_script( 'global-360-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'global_360_theme_scripts', 5 ); // Higher priority

/**
 * Add preload link for main stylesheet to prevent FOUC
 */
function global_360_theme_preload_styles() {
    $stylesheet_uri = get_stylesheet_uri();
    echo '<link rel="preload" href="' . esc_url($stylesheet_uri) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    echo '<noscript><link rel="stylesheet" href="' . esc_url($stylesheet_uri) . '"></noscript>' . "\n";
}
add_action( 'wp_head', 'global_360_theme_preload_styles', 1 );

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
 * Register Clinics CPT (only if not already registered by plugin)
 */
add_action( 'init', function() {
	if (!post_type_exists('clinic')) {
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
			'show_in_rest'       => false, // Disable for better classic editor experience
			'has_archive'        => false,
			'rewrite'            => [ 'slug' => 'clinics' ],
			'supports'           => [ 'title', 'thumbnail' ], // Removed 'editor' since using custom meta fields
		] );
	}
} );

/**
 * Register Doctors CPT (only if not already registered by plugin)
 */
add_action( 'init', function() {
	if (!post_type_exists('doctor')) {
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
			'show_in_rest'       => false, // Disable for better classic editor experience
			'has_archive'        => false,
			'rewrite'            => [ 'slug' => 'doctors' ],
			'supports'           => [ 'title', 'thumbnail' ], // Removed 'editor' since using custom meta fields
		] );
	}
} );

/**
 * Disable Gutenberg (Block Editor) for Clinic and Doctor CPTs
 * This provides a cleaner editing experience focused on the custom meta fields
 */
add_filter( 'use_block_editor_for_post_type', function( $enabled, $post_type ) {
	if ( in_array( $post_type, [ 'clinic', 'doctor' ] ) ) {
		return false;
	}
	return $enabled;
}, 10, 2 );

/**
 * Remove Gutenberg assets for clinic and doctor CPTs to improve performance
 */
add_action( 'enqueue_block_editor_assets', function() {
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->post_type, [ 'clinic', 'doctor' ] ) ) {
		// Dequeue block editor assets since we're using classic editor
		wp_dequeue_script( 'wp-block-editor' );
		wp_dequeue_script( 'wp-editor' );
		wp_dequeue_style( 'wp-block-editor-theme' );
	}
} );

/**
 * Ensure classic editor meta boxes display properly for clinic and doctor CPTs
 */
add_action( 'add_meta_boxes', function() {
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->post_type, [ 'clinic', 'doctor' ] ) ) {
		// Remove default editor meta box since we're using classic editor
		remove_meta_box( 'postdivrich', $screen->post_type, 'normal' );
		
		// Add back the classic editor if needed (optional - you can remove this if you don't want any content editor)
		// add_meta_box( 
		//     'postdivrich', 
		//     __('Content'), 
		//     'post_editor_meta_box', 
		//     $screen->post_type, 
		//     'normal', 
		//     'default' 
		// );
	}
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
add_action('wp_enqueue_scripts', 'global_360_theme_enqueue_google_fonts');
function global_360_theme_enqueue_google_fonts()
{
	// Only enqueue if not already loaded by plugin
	if (!wp_style_is('google-fonts', 'enqueued') && !wp_style_is('myclinic-google-fonts', 'enqueued')) {
		$font_url = 'https://fonts.googleapis.com/css2'
			. '?family=Roboto:ital,wght@0,400;0,700'
			. '&family=Anton'
			. '&family=Marcellus'
			. '&family=Inter:ital,wght@0,400;0,700'
			. '&family=Arvo'
			. '&family=Bodoni+Moda'
			. '&family=Cabin'
			. '&family=Chivo'
			. '&display=swap';

		wp_enqueue_style('global-360-theme-google-fonts', esc_url($font_url), [], null);
	}
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
    // State pages: /find-a-doctor/state-name/
    add_rewrite_rule('^find-a-doctor/([^/]+)/?$', 'index.php?find_a_doctor_state=$matches[1]', 'top');
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

/**
 * Social Sharing Function
 * Display social sharing buttons for posts
 */
function global_360_social_sharing($post_id = null) {
    if (!$post_id) {
        global $post;
        $post_id = $post->ID;
    }
    
    // Get post data
    $post_title = get_the_title($post_id);
    $post_url = get_permalink($post_id);
    $post_excerpt = get_the_excerpt($post_id);
    
    // Clean up text for sharing - don't double encode
    $share_title = wp_strip_all_tags($post_title);
    $share_excerpt = wp_trim_words(wp_strip_all_tags($post_excerpt), 20, '...');
    
    // Social media URLs - use rawurlencode for better compatibility
    $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($post_url);
    $twitter_url = 'https://twitter.com/intent/tweet?url=' . rawurlencode($post_url) . '&text=' . rawurlencode($share_title);
    $linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($post_url);
    $email_url = 'mailto:?subject=' . rawurlencode($share_title) . '&body=' . rawurlencode($share_excerpt . ' ' . $post_url);
    
    ob_start();
    ?>
    <div class="social-sharing">
        <h4 class="sharing-title">Share this article:</h4>
        <div class="sharing-buttons">
            <a href="<?php echo $facebook_url; ?>" target="_blank" rel="noopener" class="share-button facebook" aria-label="Share on Facebook">
                <i class="fab fa-facebook-f"></i>
                <span>Facebook</span>
            </a>
            <a href="<?php echo $twitter_url; ?>" target="_blank" rel="noopener" class="share-button twitter" aria-label="Share on Twitter/X">
                <i class="fab fa-x-twitter"></i>
                <span>Twitter</span>
            </a>
            <a href="<?php echo $linkedin_url; ?>" target="_blank" rel="noopener" class="share-button linkedin" aria-label="Share on LinkedIn">
                <i class="fab fa-linkedin-in"></i>
                <span>LinkedIn</span>
            </a>
            <a href="<?php echo $email_url; ?>" class="share-button email" aria-label="Share via Email">
                <i class="fas fa-envelope"></i>
                <span>Email</span>
            </a>
            <button class="share-button copy-link" onclick="copyToClipboard('<?php echo $post_url; ?>')" aria-label="Copy Link">
                <i class="fas fa-link"></i>
                <span>Copy Link</span>
            </button>
        </div>
    </div>
    
    <script>
    function copyToClipboard(url) {
        if (navigator.clipboard && window.isSecureContext) {
            // Use modern clipboard API
            navigator.clipboard.writeText(url).then(function() {
                showCopyMessage();
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
                fallbackCopy(url);
            });
        } else {
            // Fallback for older browsers
            fallbackCopy(url);
        }
    }
    
    function fallbackCopy(url) {
        const textArea = document.createElement('textarea');
        textArea.value = url;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showCopyMessage();
        } catch (err) {
            console.error('Fallback copy failed: ', err);
        }
        textArea.remove();
    }
    
    function showCopyMessage() {
        const button = document.querySelector('.copy-link');
        const originalText = button.querySelector('span').innerText;
        const originalIcon = button.querySelector('i').className;
        
        // Change button to show success
        button.querySelector('span').innerText = 'Copied!';
        button.querySelector('i').className = 'fas fa-check';
        button.style.backgroundColor = '#28a745';
        button.style.borderColor = '#28a745';
        
        // Reset after 2 seconds
        setTimeout(() => {
            button.querySelector('span').innerText = originalText;
            button.querySelector('i').className = originalIcon;
            button.style.backgroundColor = '';
            button.style.borderColor = '';
        }, 2000);
    }
    </script>
    <?php
    return ob_get_clean();
}