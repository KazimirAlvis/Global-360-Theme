<?php

/**
 * Shared map/location helpers for clinic and state map templates.
 *
 * Phase 1 adds reusable functions without changing rendering behavior.
 */

defined('ABSPATH') || exit;

if (! function_exists('global360_normalize_clinic_address')) {
    /**
     * Normalize a clinic address array into a stable shape.
     *
     * @param array $addr Raw address meta row.
     * @return array
     */
    function global360_normalize_clinic_address($addr)
    {
        if (! is_array($addr)) {
            return array(
                'street'       => '',
                'city'         => '',
                'state'        => '',
                'zip'          => '',
                'full_address' => '',
            );
        }

        $street = sanitize_text_field((string) ($addr['street'] ?? ''));
        $city   = sanitize_text_field((string) ($addr['city'] ?? ''));
        $state  = sanitize_text_field((string) ($addr['state'] ?? ''));
        $zip    = sanitize_text_field((string) ($addr['zip'] ?? ''));

        $parts = array_filter(array($street, $city, $state, $zip));

        return array(
            'street'       => $street,
            'city'         => $city,
            'state'        => $state,
            'zip'          => $zip,
            'full_address' => implode(', ', $parts),
        );
    }
}

if (! function_exists('global360_extract_state_from_address')) {
    /**
     * Extract a likely 2-letter state code from a freeform address.
     *
     * @param string $full_address
     * @return string
     */
    function global360_extract_state_from_address($full_address)
    {
        $full_address = trim((string) $full_address);
        if ($full_address === '') {
            return '';
        }

        $matches = array();
        preg_match_all('/,\s*([A-Za-z]{2})\s*\d{5}/', $full_address, $matches);
        if (empty($matches[1])) {
            return '';
        }

        return strtoupper((string) end($matches[1]));
    }
}

if (! function_exists('global360_get_google_geocode_for_address')) {
    /**
     * Geocode an address using the Maps Geocoding API.
     *
     * @param string $full_address
     * @param string $api_key
     * @return array|null ['lat' => float, 'lng' => float] or null.
     */
    function global360_get_google_geocode_for_address($full_address, $api_key = '')
    {
        $full_address = trim((string) $full_address);
        if ($full_address === '') {
            return null;
        }

        if ($api_key === '' && class_exists('_360_Global_Settings')) {
            $api_key = (string) _360_Global_Settings::get_google_maps_api_key();
        }

        if ($api_key === '') {
            return null;
        }

        $query = urlencode($full_address);
        $url   = "https://maps.googleapis.com/maps/api/geocode/json?address={$query}&key={$api_key}";

        $response = wp_remote_get($url, array('timeout' => 10));
        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $lat = $data['results'][0]['geometry']['location']['lat'] ?? null;
        $lng = $data['results'][0]['geometry']['location']['lng'] ?? null;

        if (is_numeric($lat) && is_numeric($lng)) {
            return array(
                'lat' => (float) $lat,
                'lng' => (float) $lng,
            );
        }

        return null;
    }
}

if (! function_exists('global360_get_address_coords')) {
    /**
     * Resolve address coordinates from stored values or optional geocoding.
     *
     * @param array $addr
     * @param bool  $allow_geocode
     * @param string $api_key
     * @return array|null ['lat' => float, 'lng' => float] or null.
     */
    function global360_get_address_coords($addr, $allow_geocode = true, $api_key = '')
    {
        if (! is_array($addr)) {
            return null;
        }

		// Core exposes canonical coordinate names; legacy address rows use lat/lng.
		$lat = $addr['latitude'] ?? $addr['lat'] ?? '';
		$lng = $addr['longitude'] ?? $addr['lng'] ?? '';

        if (is_numeric($lat) && is_numeric($lng)) {
            return array(
                'lat' => (float) $lat,
                'lng' => (float) $lng,
            );
        }

        if (! $allow_geocode) {
            return null;
        }

        $normalized   = global360_normalize_clinic_address($addr);
        $full_address = (string) $normalized['full_address'];

        if ($full_address === '') {
            return null;
        }

        // Keep existing geocode fallback behavior by trying a cleaned query first.
        $clean_address = preg_replace('/(Suite|Ste|STE|#|Unit|Apt|Apartment)\s*\d+[A-Za-z]?/i', '', $full_address);
        $clean_address = preg_replace('/\s{2,}/', ' ', (string) $clean_address);
        $clean_address = trim((string) $clean_address);

        $coords = global360_get_google_geocode_for_address($clean_address, $api_key);
        if ($coords !== null) {
            return $coords;
        }

        // Less-specific fallback mirrors current template logic.
        $parts = array();
        if (preg_match('/^([^,]+),\s*([^,]+),\s*([A-Za-z ]{2,}),?\s*(\d{5})$/', $clean_address, $parts)) {
            $less_specific = trim($parts[1] . ', ' . $parts[2] . ', ' . $parts[3] . ' ' . $parts[4]);
            return global360_get_google_geocode_for_address($less_specific, $api_key);
        }

        return null;
    }
}

if (! function_exists('global360_build_location_item')) {
    /**
     * Build a standard location payload used by map renderers.
     *
     * @param int   $post_id
     * @param array $addr
     * @param array|null $coords
     * @return array|null
     */
    function global360_build_location_item($post_id, $addr, $coords = null)
    {
        $post_id = (int) $post_id;
        if ($post_id <= 0 || ! is_array($addr)) {
            return null;
        }

        if ($coords === null) {
            $coords = global360_get_address_coords($addr, false);
        }

        if (! is_array($coords) || ! isset($coords['lat'], $coords['lng'])) {
            return null;
        }

        $normalized = global360_normalize_clinic_address($addr);

        return array(
            'coords'  => array((float) $coords['lat'], (float) $coords['lng']),
            'name'    => get_the_title($post_id),
            'address' => (string) $normalized['full_address'],
            'link'    => get_permalink($post_id),
        );
    }
}

if (! function_exists('global360_get_clinic_locations')) {
    /**
     * Return standardized map locations for a clinic post.
     *
     * @param int $clinic_id
     * @param array $args
     * @return array
     */
    function global360_get_clinic_locations($clinic_id, $args = array())
    {
        $defaults = array(
            'allow_geocode' => false,
            'limit'         => 0,
            'api_key'       => '',
        );
        $args = wp_parse_args($args, $defaults);

        $clinic_id = (int) $clinic_id;
        if ($clinic_id <= 0) {
            return array();
        }

        $clinic_view = function_exists('global360_theme_clinic') ? global360_theme_clinic($clinic_id) : null;
        $addresses = is_array($clinic_view) ? ($clinic_view['addresses'] ?? array()) : array();
        if (! is_array($addresses) || empty($addresses)) {
            return array();
        }

        if ((int) $args['limit'] > 0) {
            $addresses = array_slice($addresses, 0, (int) $args['limit']);
        }

        $locations = array();

        foreach ($addresses as $addr) {
            if (! is_array($addr)) {
                continue;
            }

            $coords = global360_get_address_coords(
                $addr,
                (bool) $args['allow_geocode'],
                (string) $args['api_key']
            );
            $item = global360_build_location_item($clinic_id, $addr, $coords);

            if ($item !== null) {
                $locations[] = $item;
            }
        }

        return $locations;
    }
}

if (! function_exists('global360_get_state_locations')) {
    /**
     * Return standardized map locations for clinics in a state.
     *
     * @param string $state_abbr
     * @param string $state_name
     * @param array  $args
     * @return array
     */
    function global360_get_state_locations($state_abbr, $state_name = '', $args = array())
    {
        $defaults = array(
            'allow_geocode' => false,
            'api_key'       => '',
        );
        $args = wp_parse_args($args, $defaults);

        $state_abbr = strtoupper(trim((string) $state_abbr));
        $state_name = strtoupper(trim((string) $state_name));
        if ($state_abbr === '') {
            return array();
        }

        $clinic_ids = get_posts(array(
            'post_type'      => 'clinic',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'no_found_rows'  => true,
            'orderby'        => 'title',
            'order'          => 'ASC',
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
        ));

        if (empty($clinic_ids)) {
            return array();
        }

        $locations = array();

		foreach ($clinic_ids as $clinic_post) {
			$clinic_id = (int) $clinic_post->ID;
            $clinic_view = function_exists('global360_theme_clinic') ? global360_theme_clinic($clinic_id) : null;
            if (! is_array($clinic_view) || ! in_array($state_abbr, (array) ($clinic_view['state_codes'] ?? array()), true)) {
                continue;
            }
            $addresses = $clinic_view['addresses'] ?? array();
            if (! is_array($addresses)) {
                continue;
            }

            foreach ($addresses as $addr) {
                if (! is_array($addr)) {
                    continue;
                }

                $normalized = global360_normalize_clinic_address($addr);
                $address_state = strtoupper(trim((string) $normalized['state']));

                if ($address_state === '') {
                    $address_state = global360_extract_state_from_address((string) $normalized['full_address']);
                }

                if ($address_state === '') {
                    continue;
                }

                if ($address_state !== $state_abbr && ($state_name !== '' && $address_state !== $state_name)) {
                    continue;
                }

                // Visitor requests consume stored coordinates only.
                $coords = global360_get_address_coords($addr, false, '');
                $item = global360_build_location_item($clinic_id, $addr, $coords);

                if ($item !== null) {
                    $locations[] = $item;
                }
            }
        }

        return $locations;
    }
}

if (! function_exists('global360_render_leaflet_map')) {
    /**
     * Render a Leaflet map for a list of standardized locations.
     *
     * @param array $locations
     * @param array $args
     * @return void
     */
    function global360_render_leaflet_map($locations, $args = array())
    {
        $defaults = array(
            'map_id'         => '',
            'height'         => 250,
            'center_lat'     => 0,
            'center_lng'     => 0,
            'zoom'           => 13,
            'max_zoom'       => 15,
            'padding'        => 20,
            'wrapper_class'  => 'global360-map-container',
            'map_class'      => 'global360-map',
            'show_titles'    => false,
        );
        $args = wp_parse_args($args, $defaults);

        $locations = array_values(array_filter((array) $locations, function ($location) {
            return is_array($location)
                && isset($location['coords'][0], $location['coords'][1])
                && is_numeric($location['coords'][0])
                && is_numeric($location['coords'][1]);
        }));

        static $leaflet_assets_loaded = false;
        if (! $leaflet_assets_loaded) {
            $leaflet_assets_loaded = true;
            echo '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />';
            echo '<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>';
        }

        $map_id = ! empty($args['map_id']) ? sanitize_html_class((string) $args['map_id']) : 'global360-map-' . wp_rand(1000, 999999);
        $height = max(120, (int) $args['height']);

        $center_lat = (float) $args['center_lat'];
        $center_lng = (float) $args['center_lng'];
        if ($center_lat === 0.0 && $center_lng === 0.0) {
            $center_lat = 39.8283;
            $center_lng = -98.5795;
        }
        if (! empty($locations)) {
            $first_location = $locations[0];
            $center_lat = (float) $first_location['coords'][0];
            $center_lng = (float) $first_location['coords'][1];
        }

        echo '<div class="' . esc_attr((string) $args['wrapper_class']) . '">';
        echo '<div id="' . esc_attr($map_id) . '" class="' . esc_attr((string) $args['map_class']) . '" style="height:' . esc_attr((string) $height) . 'px;width:100%;border-radius:10px;overflow:hidden;"></div>';
        echo '</div>';

        $js_locations = array();
        foreach ($locations as $location) {
            $js_locations[] = array(
                'coords' => array((float) $location['coords'][0], (float) $location['coords'][1]),
                'name'   => sanitize_text_field((string) ($location['name'] ?? '')),
                'address' => sanitize_text_field((string) ($location['address'] ?? '')),
                'link'   => esc_url_raw((string) ($location['link'] ?? '')),
            );
        }

        $js_config = array(
            'mapId'   => $map_id,
            'lat'     => $center_lat,
            'lng'     => $center_lng,
            'zoom'    => (int) $args['zoom'],
            'maxZoom' => (int) $args['max_zoom'],
            'padding' => (int) $args['padding'],
            'locations' => $js_locations,
        );

?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var config = <?php echo wp_json_encode($js_config); ?>;
                var map = L.map(config.mapId).setView([config.lat, config.lng], config.zoom);

                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                var group = new L.featureGroup();

                config.locations.forEach(function(location) {
                    var marker = L.marker(location.coords).addTo(map);
                    var popupParts = [];

                    if (location.name) {
                        popupParts.push("<strong>" + location.name + "</strong>");
                    }

                    if (location.address) {
                        popupParts.push(location.address);
                    }

                    if (location.link) {
                        popupParts.push("<a href='" + location.link + "' target='_blank' rel='noopener'>View Details</a>");
                    }

                    marker.bindPopup(popupParts.join("<br>"));
                    group.addLayer(marker);
                });

                if (config.locations.length > 1) {
                    var bounds = group.getBounds();
                    if (bounds.isValid()) {
                        map.fitBounds(bounds, {
                            padding: [config.padding, config.padding],
                            maxZoom: config.maxZoom
                        });
                    }
                }
            });
        </script>
<?php
    }
}
