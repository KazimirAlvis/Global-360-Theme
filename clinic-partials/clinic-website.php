<?php
$clinic_view = global360_theme_clinic(get_the_ID());
$website = $clinic_view['website'] ?? '';
if ($website) {
    printf(
        '<a class=" btn btn_green" href="%1$s" target="_blank" rel="noopener">Visit Clinic Website</a>',
        esc_url($website)
    );
}
?>
