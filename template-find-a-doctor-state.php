<?php
$state_slug = sanitize_title(strtolower((string) get_query_var('find_a_doctor_state')));
$states = [
    'AL' => 'Alabama',
    'AK' => 'Alaska',
    'AZ' => 'Arizona',
    'AR' => 'Arkansas',
    'CA' => 'California',
    'CO' => 'Colorado',
    'CT' => 'Connecticut',
    'DE' => 'Delaware',
    'FL' => 'Florida',
    'GA' => 'Georgia',
    'HI' => 'Hawaii',
    'ID' => 'Idaho',
    'IL' => 'Illinois',
    'IN' => 'Indiana',
    'IA' => 'Iowa',
    'KS' => 'Kansas',
    'KY' => 'Kentucky',
    'LA' => 'Louisiana',
    'ME' => 'Maine',
    'MD' => 'Maryland',
    'MA' => 'Massachusetts',
    'MI' => 'Michigan',
    'MN' => 'Minnesota',
    'MS' => 'Mississippi',
    'MO' => 'Missouri',
    'MT' => 'Montana',
    'NE' => 'Nebraska',
    'NV' => 'Nevada',
    'NH' => 'New Hampshire',
    'NJ' => 'New Jersey',
    'NM' => 'New Mexico',
    'NY' => 'New York',
    'NC' => 'North Carolina',
    'ND' => 'North Dakota',
    'OH' => 'Ohio',
    'OK' => 'Oklahoma',
    'OR' => 'Oregon',
    'PA' => 'Pennsylvania',
    'RI' => 'Rhode Island',
    'SC' => 'South Carolina',
    'SD' => 'South Dakota',
    'TN' => 'Tennessee',
    'TX' => 'Texas',
    'UT' => 'Utah',
    'VT' => 'Vermont',
    'VA' => 'Virginia',
    'WA' => 'Washington',
    'WV' => 'West Virginia',
    'WI' => 'Wisconsin',
    'WY' => 'Wyoming',
];

$state_slug_map = function_exists('global360_get_valid_state_slug_map')
    ? global360_get_valid_state_slug_map()
    : [];

if (empty($state_slug) || ! isset($state_slug_map[$state_slug])) {
    global $wp_query;
    if ($wp_query) {
        $wp_query->set_404();
    }
    status_header(404);
    nocache_headers();
    include get_query_template('404');
    exit;
}

get_header();

if (! function_exists('global360_render_leaflet_map')) {
    $map_utils_path = get_template_directory() . '/inc/map-utils.php';
    if (file_exists($map_utils_path)) {
        require_once $map_utils_path;
    }
}

$state_centers = [
    'AL' => ['lat' => 32.8067, 'lng' => -86.7911, 'zoom' => 7],
    'AK' => ['lat' => 61.2176, 'lng' => -149.8997, 'zoom' => 5],
    'AZ' => ['lat' => 33.4484, 'lng' => -112.0740, 'zoom' => 7],
    'AR' => ['lat' => 34.9697, 'lng' => -92.3731, 'zoom' => 7],
    'CA' => ['lat' => 36.7783, 'lng' => -119.4179, 'zoom' => 6],
    'CO' => ['lat' => 39.0598, 'lng' => -105.3111, 'zoom' => 7],
    'CT' => ['lat' => 41.5978, 'lng' => -72.7554, 'zoom' => 8],
    'DE' => ['lat' => 39.3185, 'lng' => -75.5071, 'zoom' => 9],
    'FL' => ['lat' => 27.7663, 'lng' => -81.6868, 'zoom' => 7],
    'GA' => ['lat' => 33.0406, 'lng' => -83.6431, 'zoom' => 7],
    'HI' => ['lat' => 21.0943, 'lng' => -157.4983, 'zoom' => 8],
    'ID' => ['lat' => 44.2405, 'lng' => -114.4788, 'zoom' => 6],
    'IL' => ['lat' => 40.3495, 'lng' => -88.9861, 'zoom' => 7],
    'IN' => ['lat' => 39.8494, 'lng' => -86.2583, 'zoom' => 7],
    'IA' => ['lat' => 42.0115, 'lng' => -93.2105, 'zoom' => 7],
    'KS' => ['lat' => 38.5266, 'lng' => -96.7265, 'zoom' => 7],
    'KY' => ['lat' => 37.6681, 'lng' => -84.6701, 'zoom' => 7],
    'LA' => ['lat' => 31.1695, 'lng' => -91.8678, 'zoom' => 7],
    'ME' => ['lat' => 44.6939, 'lng' => -69.3819, 'zoom' => 7],
    'MD' => ['lat' => 39.0639, 'lng' => -76.8021, 'zoom' => 8],
    'MA' => ['lat' => 42.2352, 'lng' => -71.0275, 'zoom' => 8],
    'MI' => ['lat' => 43.3266, 'lng' => -84.5361, 'zoom' => 7],
    'MN' => ['lat' => 45.6945, 'lng' => -93.9002, 'zoom' => 6],
    'MS' => ['lat' => 32.7673, 'lng' => -89.6812, 'zoom' => 7],
    'MO' => ['lat' => 38.4561, 'lng' => -92.2884, 'zoom' => 7],
    'MT' => ['lat' => 47.0527, 'lng' => -110.2148, 'zoom' => 6],
    'NE' => ['lat' => 41.1254, 'lng' => -98.2681, 'zoom' => 7],
    'NV' => ['lat' => 38.9517, 'lng' => -117.1542, 'zoom' => 6],
    'NH' => ['lat' => 43.4525, 'lng' => -71.5639, 'zoom' => 8],
    'NJ' => ['lat' => 40.3573, 'lng' => -74.4057, 'zoom' => 8],
    'NM' => ['lat' => 34.8405, 'lng' => -106.2485, 'zoom' => 7],
    'NY' => ['lat' => 42.1657, 'lng' => -74.9481, 'zoom' => 7],
    'NC' => ['lat' => 35.6301, 'lng' => -79.8064, 'zoom' => 7],
    'ND' => ['lat' => 47.5289, 'lng' => -99.7840, 'zoom' => 7],
    'OH' => ['lat' => 40.3888, 'lng' => -82.7649, 'zoom' => 7],
    'OK' => ['lat' => 35.5653, 'lng' => -96.9289, 'zoom' => 7],
    'OR' => ['lat' => 44.5672, 'lng' => -122.1269, 'zoom' => 7],
    'PA' => ['lat' => 40.5908, 'lng' => -77.2098, 'zoom' => 7],
    'RI' => ['lat' => 41.6809, 'lng' => -71.5118, 'zoom' => 9],
    'SC' => ['lat' => 33.8569, 'lng' => -80.9450, 'zoom' => 8],
    'SD' => ['lat' => 44.2998, 'lng' => -99.4388, 'zoom' => 7],
    'TN' => ['lat' => 35.7478, 'lng' => -86.7923, 'zoom' => 7],
    'TX' => ['lat' => 31.9686, 'lng' => -99.9018, 'zoom' => 6],
    'UT' => ['lat' => 40.1135, 'lng' => -111.8535, 'zoom' => 7],
    'VT' => ['lat' => 44.0459, 'lng' => -72.7107, 'zoom' => 8],
    'VA' => ['lat' => 37.7693, 'lng' => -78.2057, 'zoom' => 7],
    'WA' => ['lat' => 47.4009, 'lng' => -121.4905, 'zoom' => 7],
    'WV' => ['lat' => 38.4912, 'lng' => -80.9540, 'zoom' => 8],
    'WI' => ['lat' => 44.2619, 'lng' => -89.6165, 'zoom' => 7],
    'WY' => ['lat' => 42.7559, 'lng' => -107.3025, 'zoom' => 7],
];

$state_abbr = $state_slug_map[$state_slug];
$state_name = $states[$state_abbr];
$map_config = isset($state_centers[$state_abbr]) ? $state_centers[$state_abbr] : $state_centers['TX'];
$locations = function_exists('global360_get_state_locations')
    ? global360_get_state_locations($state_abbr, $state_name, array('allow_geocode' => true))
    : array();
?>
<main id="primary" class="site-main">
    <div class="sm_hero">
        <h1>Find a Doctor <?php echo esc_html($state_name); ?></h1>
    </div>
    <div class="max_width_content_body">
        <div class="map_holder">
            <style>
                .map-container {
                    max-width: 2500px;
                    margin: 0 auto;
                    padding: 0;
                    position: relative;
                    z-index: 1;
                }

                #map {
                    height: 500px;
                    width: 100%;
                    border-radius: 10px;
                    overflow: hidden;
                }
            </style>
            <?php
            if (function_exists('global360_render_leaflet_map')) {
                global360_render_leaflet_map($locations, array(
                    'map_id'        => 'map',
                    'height'        => 500,
                    'zoom'          => $map_config['zoom'],
                    'center_lat'    => $map_config['lat'],
                    'center_lng'    => $map_config['lng'],
                    'max_zoom'      => 10,
                    'padding'       => 20,
                    'wrapper_class' => 'map-container',
                    'map_class'     => 'map',
                ));
            } else {
                echo '<p>Map is temporarily unavailable.</p>';
            }
            ?>
        </div>
        <div class="body_heading">
            <h2>Practices in <?php echo esc_html($state_name); ?></h2>
        </div>
        <?php
        if ($state_abbr) {
            echo do_shortcode('[cpt360_state_clinics state="' . esc_attr($state_abbr) . '"]');
        } else {
            echo '<p>No clinics found for this state.</p>';
        }
        ?>
    </div>
</main><!-- #main -->
<?php get_footer(); ?>