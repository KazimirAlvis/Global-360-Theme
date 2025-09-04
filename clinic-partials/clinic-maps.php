<?php

// 1) Define your API key (example: in wp-config.php)
if (! defined('CPT360_GOOGLE_MAPS_API_KEY')) {
    define('CPT360_GOOGLE_MAPS_API_KEY', 'AIzaSyAbNsO6_Txl5OfJzlnDqm8yfS1XwMijfmE');
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

        // Embed URL
        $src = sprintf(
            'https://www.google.com/maps/embed/v1/place?key=%s&q=%s',
            esc_attr( CPT360_GOOGLE_MAPS_API_KEY ),
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
