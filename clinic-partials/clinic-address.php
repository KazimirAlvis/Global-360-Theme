   <?php

    $addresses = get_post_meta(get_the_ID(), 'clinic_addresses', true);
    if (! empty($addresses)) {
        echo '<ul class="clinic-addresses">';
        foreach ($addresses as $addr) {
            printf(
                '<li><i class="fa-solid fa-location-pin"></i> %s %s %s %s</li>',
                esc_html($addr['street']),
                esc_html($addr['city']),
                esc_html($addr['state']),
                esc_html($addr['zip'])
            );
        }
        echo '</ul>';
    }

    ?>