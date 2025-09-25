<?php
$website = get_post_meta(get_the_ID(), '_clinic_website_url', true);
if ($website) {
    printf(
        '<a class=" btn btn_green" href="%1$s" target="_blank" rel="noopener">Visit Clinic Website</a>',
        esc_url($website)
    );
}
?>