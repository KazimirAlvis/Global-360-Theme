<?php
/**
 * Clinic Google Reviews Display - LIVE from Google Places API
 * Automatically pulls real Google reviews for the clinic
 */

// Get clinic Google Place ID (this will be set in clinic meta)
$place_id = get_post_meta(get_the_ID(), 'google_place_id', true);

if (!$place_id) {
    // If no Place ID is set, show a message for admin
    if (current_user_can('edit_posts')) {
        echo '<div class="google-reviews-setup">⚠️ <em>Google Place ID not configured for this clinic.</em></div>';
    }
    return;
}

// Get Google reviews data (cached for performance)
$reviews_data = get_clinic_google_reviews($place_id);

if (!$reviews_data || !isset($reviews_data['rating'])) {
    if (current_user_can('edit_posts')) {
        echo '<div class="google-reviews-error">❌ <em>Could not load Google reviews. Check API key and Place ID.</em></div>';
    }
    return;
}

$rating = $reviews_data['rating'];
$review_count = $reviews_data['user_ratings_total'] ?? 0;
$reviews = $reviews_data['reviews'] ?? [];
$place_url = isset($reviews_data['url']) ? $reviews_data['url'] : "https://www.google.com/maps/place/?q=place_id:$place_id";
?>

<div class="clinic-google-reviews-simple">
    <div class="stars-rating">
        <?php 
        $full_stars = floor($rating);
        $has_half = ($rating - $full_stars) >= 0.5;
        
        // Display full stars
        for ($i = 1; $i <= $full_stars; $i++): ?>
            <span class="star full">★</span>
        <?php endfor; 
        
        // Display half star if needed
        if ($has_half): ?>
            <span class="star half">★</span>
        <?php endif;
        
        // Display empty stars
        $empty_stars = 5 - $full_stars - ($has_half ? 1 : 0);
        for ($i = 1; $i <= $empty_stars; $i++): ?>
            <span class="star empty">☆</span>
        <?php endfor; ?>
        
        <span class="rating-text"><?php echo number_format($rating, 1); ?> (<?php echo number_format($review_count); ?> Google reviews)</span>
    </div>
</div>

<?php
/**
 * Function to get Google Reviews data via Places API
 */
function get_clinic_google_reviews($place_id) {
    // Check cache first (cache for 6 hours to avoid API limits)
    // Added v2 to cache key to force refresh after debug removal
    $cache_key = 'google_reviews_v2_' . md5($place_id);
    $cached_data = get_transient($cache_key);
    
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    // Get Google Places API key from settings
    $options = get_option('360_global_settings', []);
    $api_key = $options['google_places_api_key'] ?? '';
    
    // Fallback to Google Maps API key if Places API key is not set
    if (empty($api_key)) {
        $api_key = $options['google_maps_api_key'] ?? '';
    }
    
    if (empty($api_key)) {
        error_log('Google Places/Maps API key not configured');
        return false;
    }
    

    
    // Call Google Places API
    $url = "https://maps.googleapis.com/maps/api/place/details/json";
    $params = [
        'place_id' => $place_id,
        'fields' => 'name,rating,user_ratings_total,reviews,url',
        'key' => $api_key
    ];
    
    $request_url = $url . '?' . http_build_query($params);
    

    
    $response = wp_remote_get($request_url, [
        'timeout' => 10
    ]);
    
    if (is_wp_error($response)) {
        error_log('Google Places API error: ' . $response->get_error_message());
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!$data || $data['status'] !== 'OK') {
        $error_msg = 'Google Places API Error: ' . ($data['status'] ?? 'Unknown');
        if (isset($data['error_message'])) {
            $error_msg .= ' - ' . $data['error_message'];
        }
        error_log($error_msg);
        

        return false;
    }
    
    $result = $data['result'];
    
    // Cache the result for 6 hours
    set_transient($cache_key, $result, 6 * HOUR_IN_SECONDS);
    
    return $result;
}
?>