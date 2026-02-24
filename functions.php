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
	define( '_S_VERSION', '1.0.20260224112511' );
}

if (!function_exists('global_360_get_icon_svg')) {
	/**
	 * Return inline SVG markup for a given icon key.
	 *
	 * @param string $icon   Icon identifier.
	 * @param string $classes Optional space-separated class list to append to the SVG element.
	 * @return string
	 */
	function global_360_get_icon_svg($icon, $classes = '') {
		static $icon_map = [
			'facebook' => [
				'viewBox' => '0 0 640 640',
				'paths'   => '<path d="M240 363.3L240 576L356 576L356 363.3L442.5 363.3L460.5 265.5L356 265.5L356 230.9C356 179.2 376.3 159.4 428.7 159.4C445 159.4 458.1 159.8 465.7 160.6L465.7 71.9C451.4 68 416.4 64 396.2 64C289.3 64 240 114.5 240 223.4L240 265.5L174 265.5L174 363.3L240 363.3z"/>'
			],
			'instagram' => [
				'viewBox' => '0 0 640 640',
				'paths'   => '<path d="M320.3 205C256.8 204.8 205.2 256.2 205 319.7C204.8 383.2 256.2 434.8 319.7 435C383.2 435.2 434.8 383.8 435 320.3C435.2 256.8 383.8 205.2 320.3 205zM319.7 245.4C360.9 245.2 394.4 278.5 394.6 319.7C394.8 360.9 361.5 394.4 320.3 394.6C279.1 394.8 245.6 361.5 245.4 320.3C245.2 279.1 278.5 245.6 319.7 245.4zM413.1 200.3C413.1 185.5 425.1 173.5 439.9 173.5C454.7 173.5 466.7 185.5 466.7 200.3C466.7 215.1 454.7 227.1 439.9 227.1C425.1 227.1 413.1 215.1 413.1 200.3zM542.8 227.5C541.1 191.6 532.9 159.8 506.6 133.6C480.4 107.4 448.6 99.2 412.7 97.4C375.7 95.3 264.8 95.3 227.8 97.4C192 99.1 160.2 107.3 133.9 133.5C107.6 159.7 99.5 191.5 97.7 227.4C95.6 264.4 95.6 375.3 97.7 412.3C99.4 448.2 107.6 480 133.9 506.2C160.2 532.4 191.9 540.6 227.8 542.4C264.8 544.5 375.7 544.5 412.7 542.4C448.6 540.7 480.4 532.5 506.6 506.2C532.8 480 541 448.2 542.8 412.3C544.9 375.3 544.9 264.5 542.8 227.5zM495 452C487.2 471.6 472.1 486.7 452.4 494.6C422.9 506.3 352.9 503.6 320.3 503.6C287.7 503.6 217.6 506.2 188.2 494.6C168.6 486.8 153.5 471.7 145.6 452C133.9 422.5 136.6 352.5 136.6 319.9C136.6 287.3 134 217.2 145.6 187.8C153.4 168.2 168.5 153.1 188.2 145.2C217.7 133.5 287.7 136.2 320.3 136.2C352.9 136.2 423 133.6 452.4 145.2C472 153 487.1 168.1 495 187.8C506.7 217.3 504 287.3 504 319.9C504 352.5 506.7 422.6 495 452z"/>'
			],
			'x' => [
				'viewBox' => '0 0 640 640',
				'paths'   => '<path d="M453.2 112L523.8 112L369.6 288.2L551 528L409 528L297.7 382.6L170.5 528L99.8 528L264.7 339.5L90.8 112L236.4 112L336.9 244.9L453.2 112zM428.4 485.8L467.5 485.8L215.1 152L173.1 152L428.4 485.8z"/>'
			],
			'youtube' => [
				'viewBox' => '0 0 640 640',
				'paths'   => '<path d="M581.7 188.1C575.5 164.4 556.9 145.8 533.4 139.5C490.9 128 320.1 128 320.1 128C320.1 128 149.3 128 106.7 139.5C83.2 145.8 64.7 164.4 58.4 188.1C47 231 47 320.4 47 320.4C47 320.4 47 409.8 58.4 452.7C64.7 476.3 83.2 494.2 106.7 500.5C149.3 512 320.1 512 320.1 512C320.1 512 490.9 512 533.5 500.5C557 494.2 575.5 476.3 581.8 452.7C593.2 409.8 593.2 320.4 593.2 320.4C593.2 320.4 593.2 231 581.8 188.1zM264.2 401.6L264.2 239.2L406.9 320.4L264.2 401.6z"/>'
			],
			'tiktok' => [
				'viewBox' => '0 0 640 640',
				'paths'   => '<path d="M544.5 273.9C500.5 274 457.5 260.3 421.7 234.7L421.7 413.4C421.7 446.5 411.6 478.8 392.7 506C373.8 533.2 347.1 554 316.1 565.6C285.1 577.2 251.3 579.1 219.2 570.9C187.1 562.7 158.3 545 136.5 520.1C114.7 495.2 101.2 464.1 97.5 431.2C93.8 398.3 100.4 365.1 116.1 336C131.8 306.9 156.1 283.3 185.7 268.3C215.3 253.3 248.6 247.8 281.4 252.3L281.4 342.2C266.4 337.5 250.3 337.6 235.4 342.6C220.5 347.6 207.5 357.2 198.4 369.9C189.3 382.6 184.4 398 184.5 413.8C184.6 429.6 189.7 444.8 199 457.5C208.3 470.2 221.4 479.6 236.4 484.4C251.4 489.2 267.5 489.2 282.4 484.3C297.3 479.4 310.4 469.9 319.6 457.2C328.8 444.5 333.8 429.1 333.8 413.4L333.8 64L421.8 64C421.7 71.4 422.4 78.9 423.7 86.2C426.8 102.5 433.1 118.1 442.4 131.9C451.7 145.7 463.7 157.5 477.6 166.5C497.5 179.6 520.8 186.6 544.6 186.6L544.6 274z"/>'
			],
			'linkedin' => [
				'viewBox' => '0 0 640 640',
				'paths'   => '<path d="M196.3 512L103.4 512L103.4 212.9L196.3 212.9L196.3 512zM149.8 172.1C120.1 172.1 96 147.5 96 117.8C96 103.5 101.7 89.9 111.8 79.8C121.9 69.7 135.6 64 149.8 64C164 64 177.7 69.7 187.8 79.8C197.9 89.9 203.6 103.6 203.6 117.8C203.6 147.5 179.5 172.1 149.8 172.1zM543.9 512L451.2 512L451.2 366.4C451.2 331.7 450.5 287.2 402.9 287.2C354.6 287.2 347.2 324.9 347.2 363.9L347.2 512L254.4 512L254.4 212.9L343.5 212.9L343.5 253.7L344.8 253.7C357.2 230.2 387.5 205.4 432.7 205.4C526.7 205.4 544 267.3 544 347.7L544 512L543.9 512z"/>'
			],
			'email' => [
				'viewBox' => '0 0 24 24',
				'paths'   => '<path fill-rule="evenodd" d="M1.5 5.25A2.25 2.25 0 013.75 3h16.5A2.25 2.25 0 0122.5 5.25v13.5A2.25 2.25 0 0120.25 21h-16.5A2.25 2.25 0 011.5 18.75V5.25zm1.91-.75a.75.75 0 00-.66.375 49.504 49.504 0 009.25 5.41 49.503 49.503 0 009.25-5.41.75.75 0 00-.66-.375H3.41zm17.34 3.438a50.861 50.861 0 01-8.94 4.912.75.75 0 01-.62 0A50.862 50.862 0 012.25 7.938V18.75c0 .414.336.75.75.75h16.5a.75.75 0 00.75-.75V7.938z" clip-rule="evenodd"/>'
			],
			'link' => [
				'viewBox' => '0 0 24 24',
				'paths'   => '<path d="M8.25 4.5h2.25a4.5 4.5 0 013.396 7.542l-.884.884a.75.75 0 11-1.06-1.06l.884-.884a3 3 0 00-2.236-5.102H8.25a3 3 0 100 6h1.5a.75.75 0 010 1.5h-1.5a4.5 4.5 0 010-9z"/><path d="M15.75 19.5h-2.25a4.5 4.5 0 01-3.396-7.542l.884-.884a.75.75 0 111.06 1.06l-.884.884a3 3 0 002.236 5.102h2.25a3 3 0 100-6h-1.5a.75.75 0 010-1.5h1.5a4.5 4.5 0 010 9z"/>'
			],
			'check' => [
				'viewBox' => '0 0 24 24',
				'paths'   => '<path d="M9 16.2L4.8 12 3.4 13.4l5.6 5.6L20.6 7.4 19.2 6z"/>'
			],
			'globe' => [
				'viewBox' => '0 0 24 24',
				'paths'   => '<path fill-rule="evenodd" d="M12 1.5a10.5 10.5 0 100 21 10.5 10.5 0 000-21zm0 1.5a9 9 0 018.862 7.5H15.75a16.93 16.93 0 00-2.27-6.482A9.012 9.012 0 0112 3zm-1.48.018A16.928 16.928 0 008.25 10.5H3.138A9.002 9.002 0 0110.52 3.018zM3.01 12a9 9 0 0017.98 0H15.75a16.93 16.93 0 01-2.27 6.482A9.012 9.012 0 0112 21a9.012 9.012 0 01-1.48-.018A16.928 16.928 0 008.25 12H3.01zm6.24 0a15.432 15.432 0 002.25 6.93A15.432 15.432 0 0013.75 12a15.432 15.432 0 00-2.25-6.93A15.432 15.432 0 009.25 12z" clip-rule="evenodd"/>'
			],
			'location-pin' => [
				'viewBox' => '0 0 24 24',
				'paths'   => '<path fill-rule="evenodd" d="M12 2.25a6.75 6.75 0 00-6.75 6.75c0 4.097 3.265 8.455 5.262 10.637a1.125 1.125 0 001.676 0C15.485 17.455 18.75 13.097 18.75 9a6.75 6.75 0 00-6.75-6.75zm0 9.75a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>'
			],
			'location-dot' => [
				'viewBox' => '0 0 24 24',
				'paths'   => '<path fill-rule="evenodd" d="M12 2.25a6.75 6.75 0 00-6.75 6.75c0 4.097 3.265 8.455 5.262 10.637a1.125 1.125 0 001.676 0C15.485 17.455 18.75 13.097 18.75 9a6.75 6.75 0 00-6.75-6.75zm0 9.75a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>'
			],
			'quote' => [
				'viewBox' => '0 0 24 24',
				'paths'   => '<path d="M7.5 6h4A1.5 1.5 0 0113 7.5V14a3 3 0 11-6 0v-3h-3V9a3 3 0 013-3zm9 0h4A1.5 1.5 0 0122 7.5V14a3 3 0 11-6 0v-3h-3V9a3 3 0 013-3z"/>'
			],
		];
		if (!isset($icon_map[$icon])) {
			return '';
		}
		$base_class = 'icon icon--' . preg_replace('/[^a-z0-9\-]/', '-', strtolower($icon));
		$all_classes = trim($base_class . ' ' . $classes);
		$view_box = $icon_map[$icon]['viewBox'];
		$paths = $icon_map[$icon]['paths'];
		return sprintf(
			'<svg class="%1$s" xmlns="http://www.w3.org/2000/svg" viewBox="%2$s" fill="currentColor" aria-hidden="true" focusable="false">%3$s</svg>',
			esc_attr($all_classes),
			esc_attr($view_box),
			$paths
		);
	}
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
					sprintf("define( '_S_VERSION', '1.0.20260224112511' );", $target_version),
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
 * Prefer the minified stylesheet everywhere; fall back to the standard file if needed.
 */
function global_360_theme_get_stylesheet_asset() {
	$minified_path = get_template_directory() . '/style-min.css';

	if ( file_exists( $minified_path ) ) {
		return get_template_directory_uri() . '/style-min.css';
	}

	return get_stylesheet_uri();
}

/**
 * Enqueue scripts and styles.
 */
function global_360_theme_scripts() {
	// Enqueue main stylesheet with high priority
	$stylesheet_uri = global_360_theme_get_stylesheet_asset();
	wp_enqueue_style( 'global-360-theme-style', $stylesheet_uri, array(), _S_VERSION, 'all' );
	wp_style_add_data( 'global-360-theme-style', 'rtl', 'replace' );
	
	// Add preload for stylesheet to improve loading
	wp_enqueue_script( 'global-360-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'global-360-theme-lazy-cf7', get_template_directory_uri() . '/js/lazy-cf7.js', array(), _S_VERSION, true );
	wp_localize_script(
		'global-360-theme-lazy-cf7',
		'Global360LazyCF7',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'global_360_lazy_cf7' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'global_360_theme_scripts', 5 ); // Higher priority

/**
 * Contact Form 7: lazy-load assets + markup for footer modal.
 */
function global_360_theme_should_load_cf7_assets() {
	if ( is_admin() ) {
		return true;
	}

	if ( ! empty( $GLOBALS['global_360_lazy_cf7_force_assets'] ) ) {
		return true;
	}

	$queried_id = get_queried_object_id();
	if ( ! $queried_id ) {
		return false;
	}

	$content = get_post_field( 'post_content', $queried_id );
	if ( ! is_string( $content ) || $content === '' ) {
		return false;
	}

	if ( has_shortcode( $content, 'contact-form-7' ) ) {
		return true;
	}

	if ( function_exists( 'has_block' ) ) {
		if ( has_block( 'contact-form-7/contact-form-selector', $content ) ) {
			return true;
		}
	}

	return false;
}

function global_360_theme_wpcf7_load_assets_filter( $load ) {
	return global_360_theme_should_load_cf7_assets();
}

add_filter( 'wpcf7_load_js', 'global_360_theme_wpcf7_load_assets_filter' );
add_filter( 'wpcf7_load_css', 'global_360_theme_wpcf7_load_assets_filter' );

function global_360_theme_ajax_lazy_cf7() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'global_360_lazy_cf7' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid nonce.' ), 403 );
	}

	$form_id = isset( $_POST['form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['form_id'] ) ) : '';
	if ( $form_id === '' ) {
		wp_send_json_error( array( 'message' => 'Missing form_id.' ), 400 );
	}

	if ( ! shortcode_exists( 'contact-form-7' ) ) {
		wp_send_json_error( array( 'message' => 'Contact Form 7 is not available.' ), 500 );
	}

	// Force CF7 assets for this AJAX response.
	$GLOBALS['global_360_lazy_cf7_force_assets'] = true;

	$form_shortcode = sprintf( '[contact-form-7 id="%s"]', esc_attr( $form_id ) );
	$form_html      = do_shortcode( $form_shortcode );

	ob_start();
	if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
		wpcf7_enqueue_styles();
	}
	if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
		wpcf7_enqueue_scripts();
	}

	// Print the CF7 handles (WordPress will include dependencies).
	wp_print_styles( 'contact-form-7' );
	wp_print_scripts( 'contact-form-7' );
	$assets_html = ob_get_clean();

	wp_send_json_success(
		array(
			'html'   => $form_html,
			'assets' => $assets_html,
		)
	);
}

add_action( 'wp_ajax_global_360_lazy_cf7', 'global_360_theme_ajax_lazy_cf7' );
add_action( 'wp_ajax_nopriv_global_360_lazy_cf7', 'global_360_theme_ajax_lazy_cf7' );

/**
 * Add preload link for main stylesheet to prevent FOUC
 * Disabled to prevent duplicate loading with child themes
 */
function global_360_theme_preload_styles() {
	// $stylesheet_uri = global_360_theme_get_stylesheet_asset();
    // echo '<link rel="preload" href="' . esc_url($stylesheet_uri) . '" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    // echo '<noscript><link rel="stylesheet" href="' . esc_url($stylesheet_uri) . '"></noscript>' . "\n";
}
// add_action( 'wp_head', 'global_360_theme_preload_styles', 1 );

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
 * Allow administrators to upload favicon bundle assets (SVG, ICO, and webmanifest files).
 */
function cpt360_allow_site_icon_mimes( $mimes ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return $mimes;
	}

	$mimes['ico'] = 'image/x-icon';
	$mimes['cur'] = 'image/x-icon';
	$mimes['svg'] = 'image/svg+xml';
	$mimes['webmanifest'] = 'application/json';

	return $mimes;
}
add_filter( 'upload_mimes', 'cpt360_allow_site_icon_mimes' );

/**
 * Ensure favicon bundle files pass WordPress' upload validation.
 */
function cpt360_allow_manifest_filetype( $data, $file, $filename, $mimes, $real_mime ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return $data;
	}

	$raw_filename = $filename;
	$filename     = strtolower( $filename );

	$display_name = $raw_filename;
	if ( $display_name === '' ) {
		if ( is_array( $file ) && isset( $file['name'] ) ) {
			$display_name = $file['name'];
		} elseif ( is_string( $file ) ) {
			$display_name = basename( $file );
		}
	}

	$sanitized_name = sanitize_file_name( $display_name );

	if ( substr( $filename, -4 ) === '.ico' ) {
		$data['ext']  = 'ico';
		$data['type'] = 'image/x-icon';
		if ( empty( $data['proper_filename'] ) ) {
			$data['proper_filename'] = $sanitized_name;
		}
		return $data;
	}

	if ( substr( $filename, -13 ) === '.webmanifest' || substr( $filename, -13 ) === 'manifest.json' ) {
		$data['ext']  = 'webmanifest';
		$data['type'] = 'application/json';
		if ( empty( $data['proper_filename'] ) ) {
			$data['proper_filename'] = $sanitized_name;
		}
		return $data;
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'cpt360_allow_manifest_filetype', 10, 5 );

/**
 * Prevent WordPress from attempting to rasterize ICO uploads, which often fails on constrained hosts.
 */
function cpt360_treat_ico_as_non_image( $is_image, $attachment_id ) {
	$mime = get_post_mime_type( $attachment_id );

	if ( in_array( $mime, [ 'image/x-icon', 'image/vnd.microsoft.icon' ], true ) ) {
		return false;
	}

	return $is_image;
}
add_filter( 'wp_attachment_is_image', 'cpt360_treat_ico_as_non_image', 10, 2 );

/**
 * Ensure ICO uploads skip the image processing pipeline entirely.
 */
function cpt360_flag_ico_upload_non_image( $upload, $context ) {
	if ( isset( $upload['type'] ) && in_array( $upload['type'], [ 'image/x-icon', 'image/vnd.microsoft.icon' ], true ) ) {
		$upload['is_image'] = false;
	}

	return $upload;
}
add_filter( 'wp_handle_upload', 'cpt360_flag_ico_upload_non_image', 10, 2 );

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
			_S_VERSION,
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
			_S_VERSION,
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
 Self-hosted fonts
--------------------------------------------------------------*/
add_action( 'wp_enqueue_scripts', 'global_360_theme_enqueue_self_hosted_fonts', 4 );
function global_360_theme_enqueue_self_hosted_fonts() {
	$settings = get_option( _360_Global_Settings::OPTION_KEY, [] );
	$font_settings = [ 'body_font', 'heading_font' ];
	$has_web_font = false;

	foreach ( $font_settings as $key ) {
		if ( empty( $settings[ $key ] ) ) {
			continue;
		}
		$slug = sanitize_key( $settings[ $key ] );
		if ( $slug && $slug !== 'system-font' ) {
			$has_web_font = true;
			break;
		}
	}

	if ( ! $has_web_font ) {
		return;
	}

	$local_css_path = get_template_directory() . '/assets/fonts/fonts.css';
	$local_css_url  = get_template_directory_uri() . '/assets/fonts/fonts.css';

	if ( file_exists( $local_css_path ) ) {
		wp_enqueue_style( 'global-360-theme-fonts', $local_css_url, [], _S_VERSION );
	}
}


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
		_S_VERSION
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
				<span class="share-icon" aria-hidden="true"><?php echo global_360_get_icon_svg('facebook', 'share-icon__svg'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="share-label">Facebook</span>
            </a>
            <a href="<?php echo $twitter_url; ?>" target="_blank" rel="noopener" class="share-button twitter" aria-label="Share on Twitter/X">
				<span class="share-icon" aria-hidden="true"><?php echo global_360_get_icon_svg('x', 'share-icon__svg'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="share-label">Twitter</span>
            </a>
            <a href="<?php echo $linkedin_url; ?>" target="_blank" rel="noopener" class="share-button linkedin" aria-label="Share on LinkedIn">
				<span class="share-icon" aria-hidden="true"><?php echo global_360_get_icon_svg('linkedin', 'share-icon__svg'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="share-label">LinkedIn</span>
            </a>
            <a href="<?php echo $email_url; ?>" class="share-button email" aria-label="Share via Email">
				<span class="share-icon" aria-hidden="true"><?php echo global_360_get_icon_svg('email', 'share-icon__svg'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="share-label">Email</span>
            </a>
			<button type="button" class="share-button copy-link" onclick="copyToClipboard('<?php echo esc_js($post_url); ?>')" aria-label="Copy Link">
				<span class="share-icon share-icon--default" aria-hidden="true"><?php echo global_360_get_icon_svg('link', 'share-icon__svg'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="share-icon share-icon--success" aria-hidden="true" hidden><?php echo global_360_get_icon_svg('check', 'share-icon__svg'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="share-label copy-label">Copy Link</span>
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
		const button = document.querySelector('.share-button.copy-link');
		if (!button) {
			return;
		}
		const label = button.querySelector('.copy-label');
		const defaultIcon = button.querySelector('.share-icon--default');
		const successIcon = button.querySelector('.share-icon--success');
		if (!label) {
			return;
		}
		const originalText = label.innerText;
        
        // Change button to show success
		label.innerText = 'Copied!';
		if (defaultIcon && successIcon) {
			defaultIcon.hidden = true;
			successIcon.hidden = false;
		}
        button.style.backgroundColor = '#28a745';
        button.style.borderColor = '#28a745';
        
        // Reset after 2 seconds
        setTimeout(() => {
			label.innerText = originalText;
			if (defaultIcon && successIcon) {
				defaultIcon.hidden = false;
				successIcon.hidden = true;
			}
            button.style.backgroundColor = '';
            button.style.borderColor = '';
        }, 2000);
    }
    </script>
    <?php
    return ob_get_clean();
}

/*--------------------------------------------------------------
# Yoast SEO integration for Clinics and Doctors
--------------------------------------------------------------*/

add_action( 'plugins_loaded', function () {
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return;
	}

	add_filter( 'wpseo_pre_analysis_post_content', 'global_360_theme_yoast_append_meta', 10, 2 );
} );

function global_360_theme_yoast_append_meta( $content, $post ) {
	if ( ! ( $post instanceof WP_Post ) ) {
		return $content;
	}

	if ( ! in_array( $post->post_type, array( 'clinic', 'doctor' ), true ) ) {
		return $content;
	}

	$extras = array();

	if ( 'clinic' === $post->post_type ) {
		$extras = array_merge( $extras, global_360_theme_collect_clinic_meta_for_yoast( $post->ID ) );
	}

	if ( 'doctor' === $post->post_type ) {
		$extras = array_merge( $extras, global_360_theme_collect_doctor_meta_for_yoast( $post->ID ) );
	}

	$extras = array_filter( $extras );

	if ( empty( $extras ) ) {
		return $content;
	}

	return $content . "\n\n" . implode( "\n\n", $extras );
}

function global_360_theme_collect_clinic_meta_for_yoast( $post_id ) {
	$pieces = array();

	$bio = get_post_meta( $post_id, '_cpt360_clinic_bio', true );
	if ( $bio ) {
		$pieces[] = '<section class="yoast-clinic-bio"><h2>Clinic Bio</h2>' . wpautop( wp_kses_post( $bio ) ) . '</section>';
	}

	$phone = get_post_meta( $post_id, '_cpt360_clinic_phone', true );
	if ( $phone ) {
		$pieces[] = '<p class="yoast-clinic-phone"><strong>Clinic Phone Number:</strong> ' . esc_html( $phone ) . '</p>';
	}

	$website = get_post_meta( $post_id, '_clinic_website_url', true );
	if ( $website ) {
		$pieces[] = '<p class="yoast-clinic-website"><strong>Clinic Website:</strong> <a href="' . esc_url( $website ) . '" rel="nofollow noopener">Visit Clinic Website</a></p>';
	}

	$addresses = get_post_meta( $post_id, 'clinic_addresses', true );
	if ( is_array( $addresses ) && ! empty( $addresses ) ) {
		$rows = array();
		foreach ( $addresses as $address ) {
			$line = array_filter( array(
				$address['street'] ?? '',
				$address['city'] ?? '',
				$address['state'] ?? '',
				$address['zip'] ?? '',
			) );

			if ( $line ) {
				$rows[] = '<li>' . esc_html( implode( ', ', $line ) ) . '</li>';
			}
		}

		if ( $rows ) {
			$pieces[] = '<section class="yoast-clinic-addresses"><h2>Clinic Addresses</h2><ul>' . implode( '', $rows ) . '</ul></section>';
		}
	}

	$info_items = get_post_meta( $post_id, 'clinic_info', true );
	if ( is_array( $info_items ) ) {
		$rows = array();
		foreach ( $info_items as $item ) {
			$title = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
			$desc  = isset( $item['description'] ) ? sanitize_textarea_field( $item['description'] ) : '';
			if ( $title || $desc ) {
				$rows[] = '<p><strong>' . esc_html( $title ) . ':</strong> ' . esc_html( $desc ) . '</p>';
			}
		}

		if ( $rows ) {
			$pieces[] = '<section class="yoast-clinic-info"><h2>Clinic Information</h2>' . implode( '', $rows ) . '</section>';
		}
	}

	$reviews = get_post_meta( $post_id, 'clinic_reviews', true );
	if ( is_array( $reviews ) ) {
		$rows = array();
		foreach ( $reviews as $review ) {
			$reviewer = isset( $review['reviewer'] ) ? sanitize_text_field( $review['reviewer'] ) : '';
			$text     = isset( $review['review'] ) ? sanitize_textarea_field( $review['review'] ) : '';

			if ( $reviewer || $text ) {
				$rows[] = '<blockquote><p>' . esc_html( $text ) . '</p><cite>' . esc_html( $reviewer ) . '</cite></blockquote>';
			}
		}

		if ( $rows ) {
			$pieces[] = '<section class="yoast-clinic-reviews"><h2>Clinic Reviews</h2>' . implode( '', $rows ) . '</section>';
		}
	}

	$states = cpt360_get_clinic_state_names( $post_id, true );
	if ( $states ) {
		$pieces[] = '<p class="yoast-clinic-states"><strong>States Served:</strong> ' . esc_html( implode( ', ', $states ) ) . '</p>';
	}

	$assessment = cpt360_get_assessment_id( $post_id );
	if ( $assessment ) {
		$pieces[] = '<p class="yoast-clinic-assessment"><strong>Clinic Assessment ID:</strong> ' . esc_html( $assessment ) . '</p>';
	}

	$google_place = get_post_meta( $post_id, 'google_place_id', true );
	if ( $google_place ) {
		$pieces[] = '<p class="yoast-clinic-google"><strong>Google Place ID:</strong> ' . esc_html( $google_place ) . '</p>';
	}

	$associated_doctors = (array) get_posts( array(
		'post_type'      => 'doctor',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => 'clinic_id',
				'value'   => $post_id,
				'compare' => 'LIKE',
			),
		),
	) );

	if ( $associated_doctors ) {
		$links = array();
		foreach ( $associated_doctors as $doctor_id ) {
			$links[] = '<li><a href="' . esc_url( get_permalink( $doctor_id ) ) . '">' . esc_html( get_the_title( $doctor_id ) ) . '</a></li>';
		}

		if ( $links ) {
			$pieces[] = '<section class="yoast-clinic-doctors"><h2>Associated Doctors</h2><ul>' . implode( '', $links ) . '</ul></section>';
		}
	}

	return $pieces;
}

function global_360_theme_collect_doctor_meta_for_yoast( $post_id ) {
	$pieces = array();

	$name = get_post_meta( $post_id, 'doctor_name', true );
	if ( $name ) {
		$pieces[] = '<p class="yoast-doctor-name"><strong>Doctor Name:</strong> ' . esc_html( $name ) . '</p>';
	}

	$title = get_post_meta( $post_id, 'doctor_title', true );
	if ( $title ) {
		$pieces[] = '<p class="yoast-doctor-title"><strong>Doctor Title:</strong> ' . esc_html( $title ) . '</p>';
	}

	$bio = get_post_meta( $post_id, 'doctor_bio', true );
	if ( $bio ) {
		$pieces[] = '<section class="yoast-doctor-bio"><h2>Doctor Bio</h2>' . wpautop( wp_kses_post( $bio ) ) . '</section>';
	}

	$clinic_ids = (array) get_post_meta( $post_id, 'clinic_id', true );
	if ( $clinic_ids ) {
		$links = array();
		foreach ( $clinic_ids as $clinic_id ) {
			$clinic_title = get_the_title( $clinic_id );
			if ( $clinic_title ) {
				$links[] = '<li><a href="' . esc_url( get_permalink( $clinic_id ) ) . '">' . esc_html( $clinic_title ) . '</a></li>';
			}
		}

		if ( $links ) {
			$pieces[] = '<section class="yoast-doctor-clinics"><h2>Practice Locations</h2><ul>' . implode( '', $links ) . '</ul></section>';
		}
	}

	return $pieces;
}