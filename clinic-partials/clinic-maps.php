<?php

// 1) Get API key from theme settings (secure implementation)
function cpt360_get_google_maps_api_key() {
    return _360_Global_Settings::get_google_maps_api_key();
}

// 2) Helper to output up to 3 map embeds
function cpt360_render_clinic_maps() {
    $addrs = get_post_meta( get_the_ID(), 'clinic_addresses', true );
    if ( empty( $addrs ) || ! is_array( $addrs ) ) {
        return;
    }

    // Only the first 3 addresses
    $slice = array_slice( $addrs, 0, 3 );

    echo '<div class="clinic-maps-inner">';

    foreach ( $slice as $addr ) {
        // Build a single-line address
        $parts        = array_filter( [
            $addr['street'] ?? '',
            $addr['city']   ?? '',
            $addr['state']  ?? '',
            $addr['zip']    ?? '',
        ] );
        $full_address = implode( ', ', $parts );
        $q            = rawurlencode( $full_address );

        // Get API key securely
        $api_key = cpt360_get_google_maps_api_key();
        if (empty($api_key)) {
            continue; // Skip this map if no API key is configured
        }

        // Embed URL
        $src = sprintf(
            'https://www.google.com/maps/embed/v1/place?key=%s&q=%s',
            esc_attr( $api_key ),
            $q
        );

        // Single wrapper for this map + text
        echo '<div class="clinic-map-item">';

          // Heading + address
          echo '<div class="map_heading">';
          echo '<i class="fa-solid fa-location-dot clinic-map-icon"></i>';
            echo '<h4 class="clinic-title">' . esc_html( get_the_title() ) . '</h4>';
            echo '<p>' . esc_html( $full_address ) . '</p>';
          echo '</div>';

          // The iframe
          printf(
            '<iframe
               width="100%%" height="250" frameborder="0" style="border:0"
               loading="lazy" allowfullscreen src="%s">
             </iframe>',
            esc_url( $src )
          );

        echo '</div>'; // .clinic-map-item
    }

    echo '</div>'; // .clinic-maps-inner
}


// 3) Hook it into your single-clinic template
// In your single-clinic.php, wherever you want the maps to show:
cpt360_render_clinic_maps();
