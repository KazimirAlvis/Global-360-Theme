<?php

/**
 * Clinic map output built from shared location helpers.
 */

defined('ABSPATH') || exit;

if (! function_exists('global360_get_clinic_locations') || ! function_exists('global360_render_leaflet_map')) {
    $map_utils_path = get_template_directory() . '/inc/map-utils.php';
    if (file_exists($map_utils_path)) {
        require_once $map_utils_path;
    }
}

function cpt360_render_clinic_maps()
{
    $clinic_id = get_the_ID();
    $locations = function_exists('global360_get_clinic_locations')
        ? global360_get_clinic_locations($clinic_id, array(
            'allow_geocode' => false,
            'limit'         => 3,
        ))
        : array();

    if (empty($locations)) {
        return;
    }

    $location_count = min(3, count($locations));

    echo '<div class="clinic-maps-inner clinic-maps-count-' . esc_attr((string) $location_count) . '">';

    foreach ($locations as $location) {
        echo '<div class="clinic-map-item">';
        echo '<div class="map_heading">';
        echo global_360_get_icon_svg('location-dot', 'clinic-map-icon'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<h4 class="clinic-title">' . esc_html(get_the_title($clinic_id)) . '</h4>';
        echo '<p>' . esc_html((string) ($location['address'] ?? '')) . '</p>';
        echo '</div>';
        if (function_exists('global360_render_leaflet_map')) {
            global360_render_leaflet_map(array($location), array(
                'height'        => 250,
                'zoom'          => 13,
                'max_zoom'      => 15,
                'padding'       => 20,
                'wrapper_class' => 'clinic-map-leaflet-wrap',
                'map_class'     => 'clinic-map-leaflet',
            ));
        } else {
            echo '<p>Map is temporarily unavailable.</p>';
        }
        echo '</div>';
    }

    echo '</div>';
}

cpt360_render_clinic_maps();
