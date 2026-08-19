
            <?php
            // 3) Clinic phone
            $clinic_view = global360_theme_clinic(get_the_ID());
            $phone = $clinic_view['phone'] ?? '';
            if ($phone) {
                // wrap in a tel: link
                printf(
                    '<a class="btn btn_green_ol" href="tel:%1$s">Call Us</a>',
                    esc_attr(preg_replace('/\D+/', '', $phone)), // strip non-digits for tel:
                    esc_html($phone)
                );
            }
            ?>
