<?php
/**
 * Clinic Google Reviews Display - LIVE from Google Places API
 * Automatically pulls real Google reviews for the clinic
 */

// Get clinic Google Place ID (this will be set in clinic meta)
$place_id = get_post_meta(get_the_ID(), 'google_place_id', true);
$can_edit_posts = function_exists('current_user_can') ? call_user_func('current_user_can', 'edit_posts') : false;

if (!$place_id) {
    // If no Place ID is set, show a message for admin
    if ($can_edit_posts) {
        echo '<div class="google-reviews-setup">⚠️ <em>Google Place ID not configured for this clinic.</em></div>';
    }
    return;
}

// Get Google reviews data (cached for performance)
$reviews_data = get_clinic_google_reviews($place_id);
$show_admin_debug = $can_edit_posts && function_exists('is_admin') && call_user_func('is_admin');

if (is_wp_error($reviews_data)) {
    if ($can_edit_posts) {
        $error_message = $reviews_data->get_error_message();
        $error_data    = $reviews_data->get_error_data();
        $status_code   = is_array($error_data) && isset($error_data['status']) ? $error_data['status'] : '';
        $available_keys = is_array($error_data) && isset($error_data['keys']) ? $error_data['keys'] : [];

        if ($status_code === 'NO_RATING') {
            $place_url = isset($error_data['url']) && $error_data['url'] ? $error_data['url'] : "https://www.google.com/maps/place/?q=place_id:$place_id";
            $admin_note = ! empty($error_message) ? $error_message : __('Google Places returned this location without an aggregate rating.', 'global-360-theme');
            echo '<div class="google-reviews-error">⚠️ <em>' . esc_html__('No Google reviews are available yet for this clinic.', 'global-360-theme') . '</em>';
            echo '<br /><small><strong>Debug:</strong> ' . esc_html($admin_note) . '</small>';
            if ($status_code) {
                echo '<br /><small><strong>Status:</strong> ' . esc_html($status_code) . '</small>';
            }
            if (! empty($available_keys)) {
                echo '<br /><small><strong>Keys:</strong> ' . esc_html(implode(', ', $available_keys)) . '</small>';
            }
            echo '<br /><small><a href="' . esc_url($place_url) . '" target="_blank" rel="noopener">' . esc_html__('View listing on Google Maps', 'global-360-theme') . '</a></small>';
            echo '</div>';
        } else {
            echo '<div class="google-reviews-error">❌ <em>Could not load Google reviews. Check API key and Place ID.</em>';
            if ($error_message) {
                echo '<br /><small><strong>Debug:</strong> ' . esc_html($error_message) . '</small>';
            }
            if ($status_code) {
                echo '<br /><small><strong>Status:</strong> ' . esc_html($status_code) . '</small>';
            }
            if (! empty($available_keys)) {
                echo '<br /><small><strong>Keys:</strong> ' . esc_html(implode(', ', $available_keys)) . '</small>';
            }
            echo '</div>';
        }
    }
    return;
}
$reviews_array = is_array($reviews_data) ? $reviews_data : [];
$has_rating   = isset($reviews_array['rating']) && is_numeric($reviews_array['rating']);
$rating_status = $reviews_array['rating_status'] ?? ($has_rating ? 'OK' : 'NO_RATING');
$rating_source = $reviews_array['rating_source'] ?? ($has_rating ? 'google' : 'unavailable');
$rating_debug  = $reviews_array['rating_debug'] ?? [];
$rating = $has_rating ? (float) $reviews_array['rating'] : null;
$review_count = isset($reviews_array['user_ratings_total']) ? (int) $reviews_array['user_ratings_total'] : 0;
$reviews = isset($reviews_array['reviews']) && is_array($reviews_array['reviews']) ? $reviews_array['reviews'] : [];
$place_url = ! empty($reviews_array['url']) ? $reviews_array['url'] : "https://www.google.com/maps/place/?q=place_id:$place_id";
$place_name = ! empty($reviews_array['name']) ? $reviews_array['name'] : '';

if (! $has_rating) {
    $no_reviews_message = $place_name
        ? sprintf(
            esc_html__('No Google reviews yet for %s.', 'global-360-theme'),
            esc_html($place_name)
        )
        : esc_html__('No Google reviews yet for this clinic.', 'global-360-theme');
    ?>
    <div class="clinic-google-reviews-simple no-rating">
        <div class="no-rating-message"><?php echo $no_reviews_message; ?></div>
        <div class="no-rating-cta"><a href="<?php echo esc_url($place_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('Leave a review on Google', 'global-360-theme'); ?></a></div>
        <?php if ($show_admin_debug) : ?>
            <div class="google-reviews-debug">
                <small><strong><?php esc_html_e('Debug:', 'global-360-theme'); ?></strong> <?php esc_html_e('Google returned this location without an aggregate rating.', 'global-360-theme'); ?></small>
                <br /><small><strong><?php esc_html_e('Status:', 'global-360-theme'); ?></strong> <?php echo esc_html($rating_status); ?></small>
                <?php if ($rating_source && 'unavailable' !== $rating_source) : ?>
                    <br /><small><strong><?php esc_html_e('Source:', 'global-360-theme'); ?></strong> <?php echo esc_html($rating_source); ?></small>
                <?php endif; ?>
                <?php
                $available_keys = isset($rating_debug['available_keys']) && is_array($rating_debug['available_keys']) ? $rating_debug['available_keys'] : [];
                if (! empty($available_keys)) :
                ?>
                    <br /><small><strong><?php esc_html_e('Keys:', 'global-360-theme'); ?></strong> <?php echo esc_html(implode(', ', $available_keys)); ?></small>
                <?php endif; ?>
                <br /><small><strong><?php esc_html_e('Cached reviews:', 'global-360-theme'); ?></strong> <?php echo esc_html((string) count($reviews)); ?></small>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return;
}

$place_label = $place_name ? $place_name : __('this clinic', 'global-360-theme');
$review_count = $review_count > 0 ? $review_count : count($reviews);
$place_label = esc_html($place_label);
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
    <?php if ($show_admin_debug && 'computed_from_reviews' === $rating_source) : ?>
        <div class="google-reviews-debug">
            <small><strong><?php esc_html_e('Debug:', 'global-360-theme'); ?></strong> <?php esc_html_e('Rating calculated from returned reviews because Google omitted the aggregate rating.', 'global-360-theme'); ?></small>
        </div>
    <?php endif; ?>
</div>

<?php
/**
 * Function to get Google Reviews data via Places API
 */
function get_clinic_google_reviews($place_id) {
    // Check cache first (cache for 6 hours to avoid API limits)
    // Added v3 to cache key to force refresh after rating fallback improvements
    $cache_key = 'google_reviews_v3_' . md5($place_id);
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
        $message = 'Google Places/Maps API key not configured';
        error_log($message);
        return new WP_Error('google_reviews_missing_key', $message);
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
        $message = 'Google Places API request failed: ' . $response->get_error_message();
        error_log($message);
        return new WP_Error('google_reviews_http_error', $message);
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!$data || ($data['status'] ?? '') !== 'OK') {
        $status    = $data['status'] ?? 'Unknown';
        $api_error = $data['error_message'] ?? '';
        $error_msg = 'Google Places API Error: ' . $status;
        if ($api_error) {
            $error_msg .= ' - ' . $api_error;
        }
        error_log($error_msg);

        return new WP_Error(
            'google_reviews_api_error',
            $error_msg,
            [
                'status' => $status,
                'error_message' => $api_error,
            ]
        );
    }
    
    $result = $data['result'] ?? [];
    $result['place_id'] = $place_id;

    $available_keys = array_keys($result);
    $reviews = isset($result['reviews']) && is_array($result['reviews']) ? $result['reviews'] : [];
    $numeric_ratings = [];

    foreach ($reviews as $review) {
        if (isset($review['rating']) && is_numeric($review['rating'])) {
            $numeric_ratings[] = (float) $review['rating'];
        }
    }

    $has_google_rating = isset($result['rating']) && is_numeric($result['rating']);

    if (! $has_google_rating && ! empty($numeric_ratings)) {
        $calculated_rating = round(array_sum($numeric_ratings) / count($numeric_ratings), 1);
        $result['rating'] = $calculated_rating;
        $result['rating_source'] = 'computed_from_reviews';
        if (! isset($result['user_ratings_total'])) {
            $result['user_ratings_total'] = count($numeric_ratings);
        }
    }

    $has_rating = isset($result['rating']) && is_numeric($result['rating']);

    if ($has_rating) {
        if (! isset($result['rating_source'])) {
            $result['rating_source'] = $has_google_rating ? 'google' : 'computed_from_reviews';
        }
        $result['rating_status'] = $has_google_rating ? 'OK' : 'FALLBACK_REVIEWS';
        if (! isset($result['user_ratings_total'])) {
            $result['user_ratings_total'] = count($numeric_ratings);
        }
    } else {
        $result['rating'] = null;
        $result['rating_source'] = 'unavailable';
        $result['rating_status'] = 'NO_RATING';
        $result['user_ratings_total'] = isset($result['user_ratings_total']) ? (int) $result['user_ratings_total'] : count($numeric_ratings);
        $result['rating_debug'] = [
            'available_keys' => $available_keys,
            'review_count'   => count($numeric_ratings),
        ];

        $log_message = 'Google Places API returned no rating information for Place ID ' . $place_id;
        if (! empty($available_keys)) {
            $log_message .= ' | Result keys: ' . implode(',', $available_keys);
        }
        error_log($log_message);
    }

    if (! isset($result['url']) || empty($result['url'])) {
        $result['url'] = "https://www.google.com/maps/place/?q=place_id:$place_id";
    }

    $cache_ttl = 6 * HOUR_IN_SECONDS;
    if ('NO_RATING' === ($result['rating_status'] ?? '')) {
        $cache_ttl = HOUR_IN_SECONDS;
    }

    set_transient($cache_key, $result, $cache_ttl);
    
    return $result;
}
?>