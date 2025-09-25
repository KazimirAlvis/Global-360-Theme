<?php
get_header();

$state_slug = strtolower( get_query_var('find_a_doctor_state') );
$states = [
    'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas',
    'CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware',
    'FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho',
    'IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas',
    'KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland',
    'MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi',
    'MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada',
    'NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York',
    'NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma',
    'OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina',
    'SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah',
    'VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia',
    'WI'=>'Wisconsin','WY'=>'Wyoming',
];

// State center coordinates and zoom levels for map initialization
$state_centers = [
    'AL' => ['lat' => 32.8067, 'lng' => -86.7911, 'zoom' => 7],  // Alabama
    'AK' => ['lat' => 61.2176, 'lng' => -149.8997, 'zoom' => 5], // Alaska
    'AZ' => ['lat' => 33.4484, 'lng' => -112.0740, 'zoom' => 7], // Arizona
    'AR' => ['lat' => 34.9697, 'lng' => -92.3731, 'zoom' => 7],  // Arkansas
    'CA' => ['lat' => 36.7783, 'lng' => -119.4179, 'zoom' => 6], // California
    'CO' => ['lat' => 39.0598, 'lng' => -105.3111, 'zoom' => 7], // Colorado
    'CT' => ['lat' => 41.5978, 'lng' => -72.7554, 'zoom' => 8],  // Connecticut
    'DE' => ['lat' => 39.3185, 'lng' => -75.5071, 'zoom' => 9],  // Delaware
    'FL' => ['lat' => 27.7663, 'lng' => -81.6868, 'zoom' => 7],  // Florida
    'GA' => ['lat' => 33.0406, 'lng' => -83.6431, 'zoom' => 7],  // Georgia
    'HI' => ['lat' => 21.0943, 'lng' => -157.4983, 'zoom' => 8], // Hawaii
    'ID' => ['lat' => 44.2405, 'lng' => -114.4788, 'zoom' => 6], // Idaho
    'IL' => ['lat' => 40.3495, 'lng' => -88.9861, 'zoom' => 7],  // Illinois
    'IN' => ['lat' => 39.8494, 'lng' => -86.2583, 'zoom' => 7],  // Indiana
    'IA' => ['lat' => 42.0115, 'lng' => -93.2105, 'zoom' => 7],  // Iowa
    'KS' => ['lat' => 38.5266, 'lng' => -96.7265, 'zoom' => 7],  // Kansas
    'KY' => ['lat' => 37.6681, 'lng' => -84.6701, 'zoom' => 7],  // Kentucky
    'LA' => ['lat' => 31.1695, 'lng' => -91.8678, 'zoom' => 7],  // Louisiana
    'ME' => ['lat' => 44.6939, 'lng' => -69.3819, 'zoom' => 7],  // Maine
    'MD' => ['lat' => 39.0639, 'lng' => -76.8021, 'zoom' => 8],  // Maryland
    'MA' => ['lat' => 42.2352, 'lng' => -71.0275, 'zoom' => 8],  // Massachusetts
    'MI' => ['lat' => 43.3266, 'lng' => -84.5361, 'zoom' => 7],  // Michigan
    'MN' => ['lat' => 45.6945, 'lng' => -93.9002, 'zoom' => 6],  // Minnesota
    'MS' => ['lat' => 32.7673, 'lng' => -89.6812, 'zoom' => 7],  // Mississippi
    'MO' => ['lat' => 38.4561, 'lng' => -92.2884, 'zoom' => 7],  // Missouri
    'MT' => ['lat' => 47.0527, 'lng' => -110.2148, 'zoom' => 6], // Montana
    'NE' => ['lat' => 41.1254, 'lng' => -98.2681, 'zoom' => 7],  // Nebraska
    'NV' => ['lat' => 38.9517, 'lng' => -117.1542, 'zoom' => 6], // Nevada
    'NH' => ['lat' => 43.4525, 'lng' => -71.5639, 'zoom' => 8],  // New Hampshire
    'NJ' => ['lat' => 40.3573, 'lng' => -74.4057, 'zoom' => 8],  // New Jersey
    'NM' => ['lat' => 34.8405, 'lng' => -106.2485, 'zoom' => 7], // New Mexico
    'NY' => ['lat' => 42.1657, 'lng' => -74.9481, 'zoom' => 7],  // New York
    'NC' => ['lat' => 35.6301, 'lng' => -79.8064, 'zoom' => 7],  // North Carolina
    'ND' => ['lat' => 47.5289, 'lng' => -99.7840, 'zoom' => 7],  // North Dakota
    'OH' => ['lat' => 40.3888, 'lng' => -82.7649, 'zoom' => 7],  // Ohio
    'OK' => ['lat' => 35.5653, 'lng' => -96.9289, 'zoom' => 7],  // Oklahoma
    'OR' => ['lat' => 44.5672, 'lng' => -122.1269, 'zoom' => 7], // Oregon
    'PA' => ['lat' => 40.5908, 'lng' => -77.2098, 'zoom' => 7],  // Pennsylvania
    'RI' => ['lat' => 41.6809, 'lng' => -71.5118, 'zoom' => 9],  // Rhode Island
    'SC' => ['lat' => 33.8569, 'lng' => -80.9450, 'zoom' => 8],  // South Carolina
    'SD' => ['lat' => 44.2998, 'lng' => -99.4388, 'zoom' => 7],  // South Dakota
    'TN' => ['lat' => 35.7478, 'lng' => -86.7923, 'zoom' => 7],  // Tennessee
    'TX' => ['lat' => 31.9686, 'lng' => -99.9018, 'zoom' => 6],  // Texas
    'UT' => ['lat' => 40.1135, 'lng' => -111.8535, 'zoom' => 7], // Utah
    'VT' => ['lat' => 44.0459, 'lng' => -72.7107, 'zoom' => 8],  // Vermont
    'VA' => ['lat' => 37.7693, 'lng' => -78.2057, 'zoom' => 7],  // Virginia
    'WA' => ['lat' => 47.4009, 'lng' => -121.4905, 'zoom' => 7], // Washington
    'WV' => ['lat' => 38.4912, 'lng' => -80.9540, 'zoom' => 8],  // West Virginia
    'WI' => ['lat' => 44.2619, 'lng' => -89.6165, 'zoom' => 7],  // Wisconsin
    'WY' => ['lat' => 42.7559, 'lng' => -107.3025, 'zoom' => 7], // Wyoming
];

// Find abbreviation by matching the slug to the state name
$state_abbr = array_search(ucwords(str_replace('-', ' ', $state_slug)), $states);
$state_name = $state_abbr ? $states[$state_abbr] : $state_slug;

// Get map center and zoom for this state (fallback to Texas if not found)
$map_config = isset($state_centers[$state_abbr]) ? $state_centers[$state_abbr] : $state_centers['TX'];
?>
<main id="primary" class="site-main">
    <div class="sm_hero">
        <h1>Find a Doctor <?php echo esc_html($state_name); ?></h1>
    </div>
<div class="max_width_content_body">
<div class="map_holder">




<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .map-container { max-width: 2500px; margin: 0 auto; padding: 0; position: relative; z-index: 1; }
    #map { height: 500px; width: 100%; border-radius: 10px; overflow: hidden; }
</style>
<div class="map-container"><div id="map"></div></div>

<?php
$locations = [];
if ($state_abbr) {
    // Updated query to work with both multi-state and single-state clinics
    $clinics = get_posts([
        'post_type'      => 'clinic',
        'posts_per_page' => -1,
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'clinic_states',
                'value'   => $state_abbr,
                'compare' => 'LIKE'
            ],
            [
                'key'     => '_cpt360_clinic_state',
                'value'   => $state_abbr,
                'compare' => '='
            ]
        ],
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);
        foreach ($clinics as $clinic) {
                $addresses = get_post_meta($clinic->ID, 'clinic_addresses', true);
                if (!is_array($addresses)) continue;
                foreach ($addresses as $addr) {
                    $full_address = $addr['street'];
                    // Extract state abbreviation or full name before the zip, anywhere in the address
                    $address_state = '';
                    $matches = [];
                    // Find all matches for ', STATE ZIP'
                    preg_match_all('/,\s*([A-Za-z]{2})\s*\d{5}/', $full_address, $matches);
                    if (!empty($matches[1])) {
                        $address_state = strtoupper(end($matches[1]));
                    } else {
                        // Find all matches for ', StateName ZIP'
                        preg_match_all('/,\s*([A-Za-z ]+)\s*\d{5}/', $full_address, $matches);
                        if (!empty($matches[1])) {
                            $address_state = ucwords(strtolower(trim(end($matches[1]))));
                        }
                    }
                    // Only add if state matches current state (abbr or full name)
                    if ($address_state && ($address_state === $state_abbr || $address_state === $state_name)) {
                        $lat = $addr['lat'] ?? '';
                        $lng = $addr['lng'] ?? '';
                        if (!$lat || !$lng) {
                            // Clean address: remove suite/unit patterns
                            $clean_address = preg_replace('/(Suite|Ste|STE|#|Unit|Apt|Apartment)\s*\d+[A-Za-z]?/i', '', $full_address);
                            $clean_address = preg_replace('/\s{2,}/', ' ', $clean_address); // Remove double spaces
                            $query = urlencode(trim($clean_address));
                            $google_api_key = _360_Global_Settings::get_google_maps_api_key();
                            if (!empty($google_api_key)) {
                                $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$query&key=$google_api_key";
                                $response = wp_remote_get($url, [ 'timeout' => 10 ]);
                                if (!is_wp_error($response)) {
                                $body = wp_remote_retrieve_body($response);
                                $data = json_decode($body, true);
                                if (!empty($data['results'][0]['geometry']['location']['lat']) && !empty($data['results'][0]['geometry']['location']['lng'])) {
                                    $lat = $data['results'][0]['geometry']['location']['lat'];
                                    $lng = $data['results'][0]['geometry']['location']['lng'];
                                    // Optionally cache in post meta for future use
                                    // $addr['lat'] = $lat; $addr['lng'] = $lng;
                                    // update_post_meta($clinic->ID, 'clinic_addresses', $addresses);
                                }
                            }
                            // If still no lat/lng, try less specific address (street, city, state, zip)
                            if (!$lat || !$lng) {
                                if (preg_match('/^([^,]+),\s*([^,]+),\s*([A-Za-z ]{2,}),?\s*(\d{5})$/', $clean_address, $parts)) {
                                    $less_specific = $parts[1] . ', ' . $parts[2] . ', ' . $parts[3] . ' ' . $parts[4];
                                    $query2 = urlencode(trim($less_specific));
                                    $url2 = "https://maps.googleapis.com/maps/api/geocode/json?address=$query2&key=$google_api_key";
                                    $response2 = wp_remote_get($url2, [ 'timeout' => 10 ]);
                                    if (!is_wp_error($response2)) {
                                        $body2 = wp_remote_retrieve_body($response2);
                                        $data2 = json_decode($body2, true);
                                        if (!empty($data2['results'][0]['geometry']['location']['lat']) && !empty($data2['results'][0]['geometry']['location']['lng'])) {
                                            $lat = $data2['results'][0]['geometry']['location']['lat'];
                                            $lng = $data2['results'][0]['geometry']['location']['lng'];
                                            // Optionally cache in post meta for future use
                                            // $addr['lat'] = $lat; $addr['lng'] = $lng;
                                            // update_post_meta($clinic->ID, 'clinic_addresses', $addresses);
                                        }
                                    }
                                }
                            }
                            } // End of API key check
                        }
                        if ($lat && $lng) {
                            $locations[] = [
                                'coords'  => [ floatval($lat), floatval($lng) ],
                                'name'    => get_the_title($clinic->ID),
                                'address' => $full_address,
                                'link'    => get_permalink($clinic->ID),
                            ];
                        }
                    }
                }
        }
}
?>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Dynamic map center and zoom based on current state
    var mapConfig = <?php echo json_encode($map_config); ?>;
    var map = L.map("map").setView([mapConfig.lat, mapConfig.lng], mapConfig.zoom);
    
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);
    
    var locations = <?php echo json_encode($locations); ?>;
    
    // If we have locations, auto-fit the map to show all markers with some padding
    if (locations.length > 0) {
        var group = new L.featureGroup();
        
        locations.forEach(function (location) {
            var marker = L.marker(location.coords)
                .addTo(map)
                .bindPopup(
                    "<b>" + location.name + "</b><br>" + location.address + "<br><a href='" + location.link + "' target='_blank'>View Details</a>"
                );
            
            // Add hover effects
            marker.on("mouseover", function () {
                this.openPopup();
            });
            marker.on("mouseout", function () {
                this.closePopup();
            });
            marker.on("click", function () {
                window.open(location.link, "_blank");
            });
            
            // Add to group for auto-fitting
            group.addLayer(marker);
        });
        
        // Auto-fit map to show all markers, but don't zoom in too much
        var bounds = group.getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds, {
                padding: [20, 20],
                maxZoom: Math.min(mapConfig.zoom + 1, 10) // Don't zoom in too much
            });
        }
    }
});
</script>
<!-- <pre><?php print_r($locations); ?></pre> -->
</div>
<div class="body_heading">
    <h2>Practices in <?php echo esc_html($state_name); ?></h2>
</div>
    <?php 
    // clincs grid
    if ($state_abbr) {
        echo do_shortcode('[cpt360_state_clinics state="' . esc_attr($state_abbr) . '"]');
    } else {
        echo '<p>No clinics found for this state.</p>';
    }
    ?>
      </div>

    
</main><!-- #main -->
<?php get_footer(); ?>