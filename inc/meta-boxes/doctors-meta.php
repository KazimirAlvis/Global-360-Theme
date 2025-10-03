<?php
/**
 * includes/meta-boxes/doctors-meta.php
 * Meta-boxes and admin customizations for the Doctor CPT.
 */

// 1) Add a “Clinic” column to the Doctor list table
add_filter( 'manage_doctor_posts_columns', function( $cols ) {
    $cols['clinic'] = __( 'Clinic', 'cpt360' );
    return $cols;
}, 20 );

add_action( 'manage_doctor_posts_custom_column', function( $column, $post_id ) {
    if ( 'clinic' !== $column ) {
        return;
    }
    $clinic_ids = (array) get_post_meta( $post_id, 'clinic_id', true );
    if ( $clinic_ids ) {
        $links = [];
        foreach ( $clinic_ids as $cid ) {
            $links[] = sprintf(
                '<a href="%1$s">%2$s</a>',
                esc_url( get_edit_post_link( $cid ) ),
                esc_html( get_the_title( $cid ) )
            );
        }
        echo implode( ', ', $links );
    } else {
        echo '&mdash;';
    }
}, 10, 2 );

// Make “Clinic” sortable
add_filter( 'manage_edit-doctor_sortable_columns', function( $cols ) {
    $cols['clinic'] = 'clinic';
    return $cols;
} );

/**
 * 2) Register clinic_id post_meta
 */
add_action( 'init', function() {
    register_post_meta( 'doctor', 'clinic_id', [
        'type'         => 'array',
        'single'       => false,
        'show_in_rest' => false,
    ] );
} );

/**
 * 3) Clinic dropdown metabox
 */
# Doctor Clinic Selection Metabox
add_action( 'add_meta_boxes', function() {
	add_meta_box(
		'doctor-clinic-selector', 
		'Clinic', 
		'cpt360_render_doctor_clinic_metabox', 
		'doctor', 
		'side', 
		'default'
	);
} );

if (!function_exists('cpt360_render_doctor_clinic_metabox')) {
	function cpt360_render_doctor_clinic_metabox( $post ) {
		wp_nonce_field( 'save_doctor_clinic', 'doctor_clinic_nonce' );
		$selected = (array) get_post_meta( $post->ID, 'clinic_id', true );
		$clinics  = get_posts([
			'post_type'   => 'clinic',
			'numberposts' => -1,
			'orderby'     => 'title',
			'order'       => 'ASC',
		]);

		echo '<select name="clinic_id[]" multiple style="width:100%; min-height:100px;">';
		echo '<option value="">— ' . esc_html__( 'Select a Clinic', 'cpt360' ) . ' —</option>';
		foreach ( $clinics as $c ) {
			printf(
				'<option value="%1$d"%2$s>%3$s</option>',
				$c->ID,
				in_array( $c->ID, $selected ) ? ' selected' : '',
				esc_html( $c->post_title )
			);
		}
		echo '</select>';
	}
}

/**
 * 4) Save clinic_id
 */
add_action( 'save_post_doctor', function( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( empty( $_POST['doctor_clinic_nonce'] ) ||
         ! wp_verify_nonce( $_POST['doctor_clinic_nonce'], 'save_doctor_clinic' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( isset( $_POST['clinic_id'] ) && is_array( $_POST['clinic_id'] ) ) {
        update_post_meta( $post_id, 'clinic_id', array_map( 'intval', $_POST['clinic_id'] ) );
    } else {
        delete_post_meta( $post_id, 'clinic_id' );
    }
} );

/**
 * 5) Doctor Details metabox (name, title, bio, photo)
 */
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'doctor_details',
        __( 'Doctor Details', 'cpt360' ),
        'cpt360_render_doctor_details_metabox',
        'doctor',
        'normal',
        'high'
    );
} );

if (!function_exists('cpt360_render_doctor_details_metabox')) {
	function cpt360_render_doctor_details_metabox( $post ) {
		wp_nonce_field( 'save_doctor_details', 'doctor_details_nonce' );

		// Photo fallback logic
		$photo_id = get_post_meta( $post->ID, '_doctor_photo_id', true );
		if ( $photo_id ) {
			$photo_url = wp_get_attachment_image_url( $photo_id, 'medium' );
		} else {
			$slug      = $post->post_name;
			$photo_url = plugin_dir_url( __FILE__ )
					   . '../assets/images/doctor-images/'
					   . $slug
					   . '.jpg';
		}

		$name  = get_post_meta( $post->ID, 'doctor_name', true );
		$title = get_post_meta( $post->ID, 'doctor_title', true );
    $bio   = get_post_meta( $post->ID, 'doctor_bio', true );
    ?>
    <div id="doctor-details-metabox">
      <p>
        <label for="doctor_name"><?php esc_html_e( 'Name', 'cpt360' ); ?></label><br/>
        <input type="text"
               id="doctor_name"
               name="doctor_name"
               value="<?php echo esc_attr( $name ); ?>"
               style="width:100%;" />
      </p>
      <p>
        <label for="doctor_title"><?php esc_html_e( 'Title', 'cpt360' ); ?></label><br/>
        <input type="text"
               id="doctor_title"
               name="doctor_title"
               value="<?php echo esc_attr( $title ); ?>"
               style="width:100%;" />
      </p>
      <p>
        <label for="doctor_bio"><?php esc_html_e( 'Bio', 'cpt360' ); ?></label><br/>
        <textarea name="doctor_bio"
                  id="doctor_bio"
                  rows="5"
                  style="width:100%;"><?php echo esc_textarea( $bio ); ?></textarea>
      </p>
      <p>
        <label><?php esc_html_e( 'Photo', 'cpt360' ); ?></label><br/>
        <div id="doctor-photo-container">
          <?php if ( $photo_url ): ?>
            <img src="<?php echo esc_url( $photo_url ); ?>"
                 style="max-width:150px; display:block; margin-bottom:10px;" />
          <?php endif; ?>
        </div>
        <input type="hidden"
               id="doctor_photo_field"
               name="doctor_photo_field"
               value="<?php echo esc_attr( $photo_id ); ?>" />
        <button type="button"
                class="button"
                id="doctor_photo_button">
          <?php echo $photo_id
                 ? esc_html__( 'Change Photo', 'cpt360' )
                 : esc_html__( 'Select Photo', 'cpt360' ); ?>
        </button>
        <button type="button"
                class="button"
                id="doctor_photo_remove"
                style="<?php echo $photo_id ? '' : 'display:none;'; ?>">
          <?php esc_html_e( 'Remove Photo', 'cpt360' ); ?>
        </button>
      </p>
    </div>
    <?php
	}
}

/**
 * 6) Enqueue your single media-uploader JS
 */
add_action( 'admin_enqueue_scripts', function( $hook ) {
    $screen = get_current_screen();
    if ( $screen && $screen->post_type === 'doctor' ) {
        wp_enqueue_media();
        wp_enqueue_script(
            'cpt360-media-meta',
            plugin_dir_url( __FILE__ ) . 'media-meta-boxes.js',
            [ 'jquery' ],
            '1.0',
            true
        );
    }
} );

/**
 * 7) Save Doctor Details
 */
add_action( 'save_post_doctor', function( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( empty( $_POST['doctor_details_nonce'] ) ||
         ! wp_verify_nonce( $_POST['doctor_details_nonce'], 'save_doctor_details' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Name
    if ( isset( $_POST['doctor_name'] ) ) {
        update_post_meta( $post_id,
                          'doctor_name',
                          sanitize_text_field( $_POST['doctor_name'] ) );
    }

    // Title
    if ( isset( $_POST['doctor_title'] ) ) {
        update_post_meta( $post_id,
                          'doctor_title',
                          sanitize_text_field( $_POST['doctor_title'] ) );
    }

    // Bio
    if ( isset( $_POST['doctor_bio'] ) ) {
        update_post_meta( $post_id,
                          'doctor_bio',
                          sanitize_textarea_field( $_POST['doctor_bio'] ) );
    }

    // Photo ID
    $new_photo = isset( $_POST['doctor_photo_field'] )
               ? intval( $_POST['doctor_photo_field'] )
               : '';
    if ( $new_photo ) {
        update_post_meta( $post_id, '_doctor_photo_id', $new_photo );
    } else {
        delete_post_meta( $post_id, '_doctor_photo_id' );
    }
} );
