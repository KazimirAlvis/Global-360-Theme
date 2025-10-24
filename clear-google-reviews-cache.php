<?php
/**
 * Temporary file to clear Google Reviews cache
 * Upload this to your live site and visit it once to clear cached debug data
 * Then delete this file
 */

// Only allow admin users
if (!current_user_can('manage_options')) {
    die('Access denied');
}

// Get all transients that start with google_reviews_
global $wpdb;

$transients = $wpdb->get_results(
    "SELECT option_name FROM {$wpdb->options} 
     WHERE option_name LIKE '_transient_google_reviews_%'"
);

$cleared = 0;
foreach ($transients as $transient) {
    $key = str_replace('_transient_', '', $transient->option_name);
    if (delete_transient($key)) {
        $cleared++;
    }
}

echo "<h1>Google Reviews Cache Cleared</h1>";
echo "<p>Cleared {$cleared} cached Google reviews entries.</p>";
echo "<p><strong>Important:</strong> Delete this file after running it once.</p>";
?>