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

// Find abbreviation by matching the slug to the state name
$state_abbr = array_search(ucwords(str_replace('-', ' ', $state_slug)), $states);
$state_name = $state_abbr ? $states[$state_abbr] : $state_slug;
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
        $clinics = get_posts([
                'post_type'      => 'clinic',
                'posts_per_page' => -1,
                'meta_key'       => '_cpt360_clinic_state',
                'meta_value'     => $state_abbr,
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
                            $google_api_key = 'AIzaSyAbNsO6_Txl5OfJzlnDqm8yfS1XwMijfmE';
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
    var map = L.map("map").setView([31.9686, -99.9018], 6); // Center of Texas (default)
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);
    var locations = <?php echo json_encode($locations); ?>;
        locations.forEach(function (location) {
            var marker = L.marker(location.coords)
                .addTo(map)
                .bindPopup(
                    "<b>" + location.name + "</b><br>" + location.address + "<br>Click marker for more info"
                );
            marker.on("mouseover", function () {
                this.openPopup();
            });
            marker.on("mouseout", function () {
                this.closePopup();
            });
            marker.on("click", function () {
                window.open(location.link, "_blank");
            });
        });
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