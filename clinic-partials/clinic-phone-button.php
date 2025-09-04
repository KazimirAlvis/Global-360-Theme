
            <?php
            // 3) Clinic phone
            $phone = get_post_meta(get_the_ID(), '_cpt360_clinic_phone', true);
            if ($phone) {
                // wrap in a tel: link
                printf(
                    '<a class="btn btn_green_ol" href="tel:%1$s">Call Us</a>',
                    esc_attr(preg_replace('/\D+/', '', $phone)), // strip non-digits for tel:
                    esc_html($phone)
                );
            }
            ?>
      