<?php


/*--------------------------------------------------------------
# Add state meta box to clinic CPT 
--------------------------------------------------------------*/ 

// 1) Move State box into the main column
add_action( 'add_meta_boxes', 'cpt360_move_state_metabox', 5 );
function cpt360_move_state_metabox() {
    // remove any WP‐core taxonomy box named "state" (hierarchical or tag style)
    remove_meta_box( 'statediv',    'clinic', 'side' ); // hierarchical
    remove_meta_box( 'tagsdiv-state','clinic', 'side' ); // non-hierarchical

    // now add our own metabox into the main column
    add_meta_box(
        'cpt360_clinic_state',
        __( 'State', 'cpt360' ),
        'cpt360_render_clinic_state_metabox',
        'clinic',
        'normal',
        'high'
    );
}

// 2) Render our custom State dropdown
function cpt360_render_clinic_state_metabox( $post ) {
    wp_nonce_field( 'cpt360_save_clinic_state', 'cpt360_clinic_state_nonce' );
    $current = get_post_meta( $post->ID, '_cpt360_clinic_state', true );

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

    echo '<label for="cpt360_clinic_state">' . __( 'Select State:', 'cpt360' ) . '</label><br>';
    echo '<select name="cpt360_clinic_state" id="cpt360_clinic_state" style="width:100%;max-width:300px">';
    echo '<option value="">' . __( '— None —', 'cpt360' ) . '</option>';
    foreach ( $states as $abbr => $name ) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr( $abbr ),
            selected( $current, $abbr, false ),
            esc_html( $name )
        );
    }
    echo '</select>';
}

// 3) Save the selected state
add_action( 'save_post_clinic', 'cpt360_save_clinic_state' );
function cpt360_save_clinic_state( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['cpt360_clinic_state_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['cpt360_clinic_state_nonce'], 'cpt360_save_clinic_state' ) ) return;
    if ( get_post_type( $post_id ) !== 'clinic' ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $new = isset( $_POST['cpt360_clinic_state'] )
         ? sanitize_text_field( $_POST['cpt360_clinic_state'] )
         : '';
    if ( $new ) {
        update_post_meta( $post_id, '_cpt360_clinic_state', $new );
    } else {
        delete_post_meta( $post_id, '_cpt360_clinic_state' );
    }
}





/*--------------------------------------------------------------
# Clinic Thumbnail Meta Box
--------------------------------------------------------------*/

// 1) Register the meta box
add_action( 'add_meta_boxes', function() {
  add_meta_box(
    'clinic_thumbnail_meta',                      // meta box ID
    __( 'Clinic Thumbnail', 'clinic-thumbnail' ), // title
    'clinic_thumbnail_render_metabox',            // callback
    'clinic',                                     // CPT slug
    'normal',
    'high'
  );
} );

// 2) Render the box
function clinic_thumbnail_render_metabox( $post ) {
  // nonce for security
  wp_nonce_field( 'clinic_thumbnail_nonce', 'clinic_thumbnail_nonce_field' );

  // get current attachment ID + URL
  $thumb_id  = get_post_meta( $post->ID, '_clinic_thumbnail_id', true );
  $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
  ?>
  <div id="clinic-thumbnail-container">
    <?php if ( $thumb_url ): ?>
      <p>
        <img src="<?php echo esc_url( $thumb_url ); ?>"
             style="max-width:200px; display:block; margin-bottom:10px;" />
      </p>
    <?php endif; ?>

    <input type="hidden"
           id="clinic_thumbnail_field"
           name="clinic_thumbnail_field"
           value="<?php echo esc_attr( $thumb_id ); ?>" />

    <button type="button"
            class="button"
            id="clinic_thumbnail_button">
      <?php echo $thumb_id ? 'Change Thumbnail' : 'Select Thumbnail'; ?>
    </button>
    <button type="button"
            class="button"
            id="clinic_thumbnail_remove"
            style="<?php echo $thumb_id ? '' : 'display:none;'; ?>">
      Remove
    </button>
  </div>
  <?php
}




// 4) Save the attachment ID
add_action( 'save_post', function( $post_id, $post ) {
  // a) Bail on autosave/revisions
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
  if ( wp_is_post_revision( $post_id ) )         return;

  // b) Only our CPT
  if ( $post->post_type !== 'clinic' ) return;

  // c) Nonce check
  if ( empty( $_POST['clinic_thumbnail_nonce_field'] )
    || ! wp_verify_nonce( $_POST['clinic_thumbnail_nonce_field'], 'clinic_thumbnail_nonce' )
  ) {
    return;
  }

  // d) Capability check
  if ( ! current_user_can( 'edit_post', $post_id ) ) return;

  // e) Save or delete meta
  $new_id = isset( $_POST['clinic_thumbnail_field'] ) && is_numeric( $_POST['clinic_thumbnail_field'] )
            ? intval( $_POST['clinic_thumbnail_field'] )
            : '';

  if ( $new_id ) {
    update_post_meta( $post_id, '_clinic_thumbnail_id', $new_id );
  } else {
    delete_post_meta( $post_id, '_clinic_thumbnail_id' );
  }
}, 10, 2 );





/*--------------------------------------------------------------
# Phone Number Meta Box
--------------------------------------------------------------*/

/**
 * 1) Add Phone Number meta box below logo (normal context, high priority)
 */
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'cpt360_clinic_phone',
        __( 'Clinic Phone Number', 'cpt360' ),
        'cpt360_render_clinic_phone_metabox',
        'clinic',
        'normal',
        'high'
    );
} );

/**
 * 2) Render the phone field
 */
function cpt360_render_clinic_phone_metabox( $post ) {
    wp_nonce_field( 'cpt360_save_clinic_phone', 'cpt360_clinic_phone_nonce' );

    $phone = get_post_meta( $post->ID, '_cpt360_clinic_phone', true );
    ?>
    <p>
        <label for="cpt360_clinic_phone"><?php esc_html_e( 'Phone Number:', 'cpt360' ); ?></label><br>
        <input
            type="tel"
            name="cpt360_clinic_phone"
            id="cpt360_clinic_phone"
            value="<?php echo esc_attr( $phone ); ?>"
            style="width:100%; max-width:300px;"
            placeholder="e.g. (555) 123-4567"
        >
    </p>
    <?php
}

/**
 * 3) Save the phone number
 */
add_action( 'save_post', function( $post_id ) {
    if ( ! isset( $_POST['cpt360_clinic_phone_nonce'] )
         || ! wp_verify_nonce( $_POST['cpt360_clinic_phone_nonce'], 'cpt360_save_clinic_phone' )
    ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( get_post_type( $post_id ) !== 'clinic' ) {
        return;
    }

    if ( isset( $_POST['cpt360_clinic_phone'] ) ) {
        // sanitize: allow numbers, spaces, parentheses, dashes, plus
        $clean = preg_replace( '/[^\d\+\-\(\)\s]/', '', $_POST['cpt360_clinic_phone'] );
        update_post_meta( $post_id, '_cpt360_clinic_phone', $clean );
    } else {
        delete_post_meta( $post_id, '_cpt360_clinic_phone' );
    }
} );

/*--------------------------------------------------------------
# Address Repeater Meta Box
--------------------------------------------------------------*/

// 1) Register the box
add_action( 'add_meta_boxes', function() {
  add_meta_box( 'clinic_addresses', 'Clinic Addresses',
                'render_clinic_addresses_box', 'clinic', 'normal', 'high' );
} );

// 2) Render the box (in cpt360-plugin.php)
function render_clinic_addresses_box( $post ) {
  wp_nonce_field( 'save_clinic_addresses', 'clinic_addresses_nonce' );
  $addresses = get_post_meta( $post->ID, 'clinic_addresses', true ) ?: [];
  ?>
  <div id="clinic-addresses-container">
    <?php foreach ( $addresses as $i => $addr ): ?>
      <div class="clinic-address-row" data-index="<?php echo $i; ?>">
        <input type="text"
               name="clinic_addresses[<?php echo $i; ?>][street]"
               value="<?php echo esc_attr( $addr['street'] ); ?>"
               placeholder="Street" />
        <!-- add other fields: city, state, zip -->
        <button class="remove-address button">–</button>
      </div>
    <?php endforeach; ?>
  </div>
  <button id="add-address" class="button">Add Address</button>

  <script>
  jQuery(function($){
    $('#add-address').on('click', function(e){
      e.preventDefault();
      var container = $('#clinic-addresses-container');
      var index     = container.find('.clinic-address-row').length;
      var row = $(
        '<div class="clinic-address-row" data-index="'+index+'">'+
          '<input type="text" name="clinic_addresses['+index+'][street]" placeholder="Street" />'+
          '<button class="remove-address button">–</button>'+
        '</div>'
      );
      container.append(row);
    });

    $(document).on('click', '.remove-address', function(e){
      e.preventDefault();
      $(this).closest('.clinic-address-row').remove();
      // optionally re-index the remaining rows…
    });
  });
  </script>
  <?php
}
add_action( 'save_post', function( $post_id, $post ) {
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
  if ( $post->post_type !== 'clinic' )    return;
  if ( empty($_POST['clinic_addresses_nonce']) ||
       ! wp_verify_nonce( $_POST['clinic_addresses_nonce'], 'save_clinic_addresses' )
  ) return;

  $raw = $_POST['clinic_addresses'] ?? [];
  $clean = [];

  foreach ( $raw as $addr ) {
    $clean[] = [
      'street' => sanitize_text_field( $addr['street'] ?? '' ),
      'city'   => sanitize_text_field( $addr['city']   ?? '' ),
      'state'  => sanitize_text_field( $addr['state']  ?? '' ),
      'zip'    => sanitize_text_field( $addr['zip']    ?? '' ),
    ];
  }

  update_post_meta( $post_id, 'clinic_addresses', $clean );
}, 10, 2 );


/*--------------------------------------------------------------
# Clinic ID Meta Box
--------------------------------------------------------------*/

/**
 * Clinic Meta: per‐Clinic Assessment ID meta box + helper
 */

/**
 * 1) Add the “Clinic Assessment ID” meta box to the Clinic CPT.
 */
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'cpt360_clinic_assessment_id',        // ID
        __( 'Clinic Assessment ID', 'cpt360' ),// Title
        'cpt360_render_assessment_id_metabox', // Callback
        'clinic',                             // Post Type
        'normal',                             // Context
        'default'                             // Priority
    );
} );

/**
 * 2) Render the meta box form, falling back to the global setting.
 *
 * @param WP_Post $post
 */
function cpt360_render_assessment_id_metabox( $post ) {
    wp_nonce_field( 'cpt360_save_assessment_id', 'cpt360_assessment_id_nonce' );

    // Per-clinic saved value
    $saved = get_post_meta( $post->ID, '_cpt360_assessment_id', true );

    // Global fallback from our single settings array
    $globals       = get_option( '360_global_settings', [] );
    $global_default = $globals['assessment_id'] ?? '';

    // Decide what to show in the input
    $value = ( $saved !== '' ) ? $saved : $global_default;

    echo '<p><label for="cpt360_assessment_id_field">'
       .  __( 'Clinic Assessment ID:', 'cpt360' )
       .  '</label></p>';
    echo '<input type="text" '
       .  'id="cpt360_assessment_id_field" '
       .  'name="cpt360_assessment_id_field" '
       .  'value="' . esc_attr( $value ) . '" '
       .  'class="widefat" />';
    
    if ( $saved === '' && $global_default ) {
        echo '<p class="description">'
           . esc_html__( 'Using global default since this field is empty.', 'cpt360' )
           . '</p>';
    }
}

/**
 * 3) Save the Clinic Assessment ID when the post is saved.
 */
add_action( 'save_post', function( $post_id, $post ) {
    // Bail on autosave, wrong CPT, or invalid nonce/cap
    if (
        ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        || $post->post_type !== 'clinic'
        || empty( $_POST['cpt360_assessment_id_nonce'] )
        || ! wp_verify_nonce( $_POST['cpt360_assessment_id_nonce'], 'cpt360_save_assessment_id' )
        || ! current_user_can( 'edit_post', $post_id )
    ) {
        return;
    }

    $new = isset( $_POST['cpt360_assessment_id_field'] )
         ? sanitize_text_field( wp_unslash( $_POST['cpt360_assessment_id_field'] ) )
         : '';

    if ( $new ) {
        update_post_meta( $post_id, '_cpt360_assessment_id', $new );
    } else {
        delete_post_meta( $post_id, '_cpt360_assessment_id' );
    }
}, 10, 2 );

/**
 * 4) Helper: get the effective Assessment ID
 *
 * @param int|null $post_id Defaults to current post ID.
 * @return string
 */
if ( ! function_exists( 'cpt360_get_assessment_id' ) ) {
    function cpt360_get_assessment_id( $post_id = null ) {
        $post_id = $post_id ?: get_the_ID();

        // 1) Per-clinic override
        $meta = get_post_meta( $post_id, '_cpt360_assessment_id', true );
        if ( $meta ) {
            return $meta;
        }

        // 2) Fallback to global setting
        $globals = get_option( '360_global_settings', [] );
        return $globals['assessment_id'] ?? '';
    }
}



/*--------------------------------------------------------------
# Clinic Bio Meta Box
--------------------------------------------------------------*/

add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'cpt360_clinic_bio',
        __( 'Clinic Bio', 'cpt360' ),
        'cpt360_render_clinic_bio_metabox',
        'clinic',
        'normal',   // main column
        'default'   // default priority
    );
} );

/**
 * Render the Clinic Bio meta box.
 *
 * @param WP_Post $post
 */
function cpt360_render_clinic_bio_metabox( $post ) {
    // nonce for security
    wp_nonce_field( 'cpt360_save_clinic_bio', 'cpt360_clinic_bio_nonce' );

    // load any saved bio
    $bio = get_post_meta( $post->ID, '_cpt360_clinic_bio', true );

    // render the editor
    wp_editor(
        $bio,                              // initial content
        'cpt360_clinic_bio_field',        // <textarea> ID
        [
            'textarea_name' => 'cpt360_clinic_bio_field',
            'media_buttons' => false,
            'teeny'         => false,     // set to true for a simpler toolbar
            'textarea_rows' => 8,
        ]
    );
}

/**
 * Save the Clinic Bio when the post is saved.
 */
add_action( 'save_post', function( $post_id, $post ) {
    // bail on autosave, wrong CPT, or missing/invalid nonce
    if (
        ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        || $post->post_type !== 'clinic'
        || empty( $_POST['cpt360_clinic_bio_nonce'] )
        || ! wp_verify_nonce( $_POST['cpt360_clinic_bio_nonce'], 'cpt360_save_clinic_bio' )
    ) {
        return;
    }

    // sanitize and save (or delete if empty)
    $new = isset( $_POST['cpt360_clinic_bio_field'] )
           ? wp_kses_post( wp_unslash( $_POST['cpt360_clinic_bio_field'] ) )
           : '';

    if ( $new ) {
        update_post_meta( $post_id, '_cpt360_clinic_bio', $new );
    } else {
        delete_post_meta( $post_id, '_cpt360_clinic_bio' );
    }
}, 10, 2 );

/**
 * Front-end helper: echo the saved Clinic Bio.
 *
 * @param array $args {
 *   Optional. Arguments.
 *
 *   @type bool   $apply_filters Whether to run the bio through 'the_content' filters.
 *   @type string $before        HTML to echo before the bio.
 *   @type string $after         HTML to echo after the bio.
 * }
 */
if ( ! function_exists( 'the_clinic_bio' ) ) {
    function the_clinic_bio( $args = [] ) {
        $args = wp_parse_args( $args, [
            'apply_filters' => true,
            'before'        => '<div class="clinic-bio">',
            'after'         => '</div>',
        ] );

        $bio = get_post_meta( get_the_ID(), '_cpt360_clinic_bio', true );
        if ( ! $bio ) {
            return;
        }

        echo $args['before'];
        if ( $args['apply_filters'] ) {
            echo apply_filters( 'the_content', $bio );
        } else {
            echo wp_kses_post( $bio );
        }
        echo $args['after'];
    }
}
/*--------------------------------------------------------------
# Clinic Info Repeater Meta Box
--------------------------------------------------------------*/

// 1) Register the box
add_action( 'add_meta_boxes', function() {
  add_meta_box(
    'clinic_info_meta',
    'Clinic Info',
    'render_clinic_info_box',
    'clinic',
    'normal',
    'high'
  );
});

// 2) Render the box
function render_clinic_info_box( $post ) {
  wp_nonce_field( 'save_clinic_info', 'clinic_info_nonce' );
  $items = get_post_meta( $post->ID, 'clinic_info', true ) ?: [];
  ?>
  <div id="clinic-info-container">
    <?php foreach ( $items as $i => $item ): ?>
      <div class="clinic-info-row" data-index="<?php echo esc_attr($i); ?>">
        <input type="text"
               name="clinic_info[<?php echo $i; ?>][title]"
               value="<?php echo esc_attr( $item['title'] ); ?>"
               placeholder="Title"
               style="width:30%; margin-right:10px;" />
        <textarea
               name="clinic_info[<?php echo $i; ?>][description]"
               placeholder="Description"
               rows="2"
               style="width:65%;"><?php echo esc_textarea( $item['description'] ); ?></textarea>
        <button class="remove-info button" style="">–</button>
      </div>
    <?php endforeach; ?>
  </div>
  <button id="add-clinic-info" class="button">Add Info</button>

  <script>
  jQuery(function($){
    $('#add-clinic-info').on('click', function(e){
      e.preventDefault();
      var container = $('#clinic-info-container');
      var index     = container.find('.clinic-info-row').length;
      var row = $(
        '<div class="clinic-info-row" data-index="'+index+'">'+
          '<input type="text" name="clinic_info['+index+'][title]" placeholder="Title"  />'+
          '<textarea name="clinic_info['+index+'][description]" placeholder="Description" rows="2" ></textarea>'+
          '<button class="remove-info button" style="margin-left:10px;">–</button>'+
        '</div>'
      );
      container.append(row);
    });

    $(document).on('click', '.remove-info', function(e){
      e.preventDefault();
      $(this).closest('.clinic-info-row').remove();
      // you could re-index here if you need strict sequential indexes
    });
  });
  </script>
  <?php
}

// 3) Save the data
add_action( 'save_post', function( $post_id, $post ) {
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
  if ( $post->post_type !== 'clinic' )    return;
  if ( empty($_POST['clinic_info_nonce']) ||
       ! wp_verify_nonce( $_POST['clinic_info_nonce'], 'save_clinic_info' )
  ) return;
  if ( ! current_user_can( 'edit_post', $post_id ) ) return;

  $raw = $_POST['clinic_info'] ?? [];
  $clean = [];

  foreach ( $raw as $item ) {
    $title       = sanitize_text_field( $item['title']       ?? '' );
    $description = sanitize_textarea_field( $item['description'] ?? '' );
    if ( $title || $description ) {
      $clean[] = [ 'title' => $title, 'description' => $description ];
    }
  }

  if ( ! empty( $clean ) ) {
    update_post_meta( $post_id, 'clinic_info', $clean );
  } else {
    delete_post_meta( $post_id, 'clinic_info' );
  }
}, 10, 2 );


/*--------------------------------------------------------------
# Clinic Reviews Repeater Meta Box
--------------------------------------------------------------*/

// 1) Register the box
add_action( 'add_meta_boxes', function() {
  add_meta_box(
    'clinic_reviews',
    __( 'Clinic Reviews', 'clinic-reviews' ),
    'render_clinic_reviews_box',
    'clinic',
    'normal',
    'high'
  );
} );

// 2) Render the box
function render_clinic_reviews_box( $post ) {
  wp_nonce_field( 'save_clinic_reviews', 'clinic_reviews_nonce' );
  $reviews = get_post_meta( $post->ID, 'clinic_reviews', true ) ?: [];
  ?>
  <div id="clinic-reviews-container">
    <?php foreach ( $reviews as $i => $r ): ?>
      <div class="clinic-review-row" data-index="<?php echo $i; ?>">
        <input
          type="text"
          name="clinic_reviews[<?php echo $i; ?>][reviewer]"
          value="<?php echo esc_attr( $r['reviewer'] ); ?>"
          placeholder="<?php esc_attr_e( 'Reviewer Name', 'clinic-reviews' ); ?>"
          style="width:30%;"
        />
        <textarea
          name="clinic_reviews[<?php echo $i; ?>][review]"
          placeholder="<?php esc_attr_e( 'Review Text', 'clinic-reviews' ); ?>"
          style="width:65%;"
          rows="2"
        ><?php echo esc_textarea( $r['review'] ); ?></textarea>
        <button class="remove-review button">–</button>
      </div>
    <?php endforeach; ?>
  </div>
  <p><button id="add-review" class="button">+ Add Review</button></p>

  <script>
  jQuery(function($){
    var container = $('#clinic-reviews-container');

    $('#add-review').on('click', function(e){
      e.preventDefault();
      var index = container.find('.clinic-review-row').length;
      var row = $(
        '<div class="clinic-review-row" data-index="'+index+'">'+
          '<input type="text" name="clinic_reviews['+index+'][reviewer]" placeholder="Reviewer Name" style="width:30%;" />'+
          '<textarea name="clinic_reviews['+index+'][review]" placeholder="Review Text" style="width:65%; height:4em;"></textarea>'+
          '<button class="remove-review button">–</button>'+
        '</div>'
      );
      container.append(row);
    });

    $(document).on('click', '.remove-review', function(e){
      e.preventDefault();
      $(this).closest('.clinic-review-row').remove();
      // optional: re-index rows here if you need sequential keys
    });
  });
  </script>
  <?php
}

// 3) Save the data
add_action( 'save_post', function( $post_id, $post ) {
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
  if ( $post->post_type !== 'clinic' )    return;
  if ( empty($_POST['clinic_reviews_nonce']) ||
       ! wp_verify_nonce( $_POST['clinic_reviews_nonce'], 'save_clinic_reviews' )
  ) return;
  if ( ! current_user_can( 'edit_post', $post_id ) ) return;

  $raw = $_POST['clinic_reviews'] ?? [];
  $clean = [];

  foreach ( $raw as $r ) {
    $reviewer = sanitize_text_field( $r['reviewer'] ?? '' );
    $review   = sanitize_textarea_field( $r['review'] ?? '' );
    if ( $reviewer || $review ) {
      $clean[] = [
        'reviewer' => $reviewer,
        'review'   => $review,
      ];
    }
  }

  if ( $clean ) {
    update_post_meta( $post_id, 'clinic_reviews', $clean );
  } else {
    delete_post_meta( $post_id, 'clinic_reviews' );
  }
}, 10, 2 );


/*--------------------------------------------------------------
# Clinic website Meta Box
--------------------------------------------------------------*/

/**
 * 1) Register the meta box
 */
add_action( 'add_meta_boxes', function() {
    add_meta_box(
        'clinic_website_meta',
        __( 'Clinic Website', 'your-text-domain' ),
        'clinic_website_render_metabox',
        'clinic',     // CPT slug
        'normal',
        'high'
    );
});

/**
 * 2) Render the box
 */
function clinic_website_render_metabox( $post ) {
    wp_nonce_field( 'clinic_website_nonce', 'clinic_website_nonce_field' );

    // Retrieve existing value, if any
    $url = get_post_meta( $post->ID, '_clinic_website_url', true );
    ?>
    <p>
      <label for="clinic_website_field">
        <?php esc_html_e( 'Website Address (full URL):', 'your-text-domain' ); ?>
      </label><br/>
      <input
        type="url"
        id="clinic_website_field"
        name="clinic_website_field"
        value="<?php echo esc_attr( $url ); ?>"
        style="width:100%; max-width:400px;"
        placeholder="https://example.com"
      />
    </p>
    <?php
}

/**
 * 3) Save the value on post save
 */
add_action( 'save_post', function( $post_id, $post ) {
    // Bail on autosave or revision
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) )          return;

    // Only for our CPT
    if ( $post->post_type !== 'clinic' )            return;

    // Check our nonce
    if ( empty( $_POST['clinic_website_nonce_field'] )
      || ! wp_verify_nonce( $_POST['clinic_website_nonce_field'], 'clinic_website_nonce' )
    ) {
        return;
    }

    // Capability check
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Sanitize & save (or delete if empty)
    $new = ! empty( $_POST['clinic_website_field'] )
         ? esc_url_raw( trim( $_POST['clinic_website_field'] ) )
         : '';

    if ( $new ) {
        update_post_meta( $post_id, '_clinic_website_url', $new );
    } else {
        delete_post_meta( $post_id, '_clinic_website_url' );
    }
}, 10, 2 );

/*--------------------------------------------------------------
# upload images logos
--------------------------------------------------------------*/
// includes/clinic-meta.php

// 1) Register the meta box
add_action( 'add_meta_boxes', 'cpt360_register_clinic_logo_box' );
function cpt360_register_clinic_logo_box() {
    add_meta_box(
        'clinic_logo_meta',
        __( 'Clinic Logo', 'cpt360' ),
        'cpt360_render_clinic_logo_metabox',
        'clinic',
        'normal',
        'high'
    );
}

// 2) Render the meta box
function cpt360_render_clinic_logo_metabox( $post ) {
    wp_nonce_field( 'cpt360_clinic_logo_nonce', 'cpt360_clinic_logo_nonce_field' );
    $logo_id  = get_post_meta( $post->ID, '_clinic_logo_id', true );
    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
    ?>
    <div id="clinic-logo-container">
      <?php if ( $logo_url ): ?>
        <p><img src="<?php echo esc_url( $logo_url ); ?>" style="max-width:200px; display:block; margin-bottom:10px;" /></p>
      <?php endif; ?>

      <input type="hidden"
             id="clinic_logo_field"
             name="clinic_logo_field"
             value="<?php echo esc_attr( $logo_id ); ?>" />

      <button type="button"
              class="button"
              id="clinic_logo_button">
        <?php echo $logo_id ? 'Change Logo' : 'Select Logo'; ?>
      </button>
      <button type="button"
              class="button"
              id="clinic_logo_remove"
              style="<?php echo $logo_id ? '' : 'display:none;'; ?>">
        Remove
      </button>
    </div>
    <?php
}

// 3) Save the attachment ID
add_action( 'save_post', 'cpt360_save_clinic_logo', 10, 2 );
function cpt360_save_clinic_logo( $post_id, $post ) {
    // bail on autosave & wrong post type
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( $post->post_type !== 'clinic' ) return;

    // nonce check
    if ( empty( $_POST['cpt360_clinic_logo_nonce_field'] ) ||
         ! wp_verify_nonce( $_POST['cpt360_clinic_logo_nonce_field'], 'cpt360_clinic_logo_nonce' ) ) {
      return;
    }

    // user permission
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // save or delete
    $new = isset( $_POST['clinic_logo_field'] ) && is_numeric( $_POST['clinic_logo_field'] )
           ? intval( $_POST['clinic_logo_field'] )
           : '';
    if ( $new ) {
      update_post_meta( $post_id, '_clinic_logo_id', $new );
    } else {
      delete_post_meta( $post_id, '_clinic_logo_id' );
    }
}

// 4) Enqueue the media uploader script
add_action( 'admin_enqueue_scripts', 'cpt360_enqueue_logo_uploader' );
function cpt360_enqueue_logo_uploader( $hook ) {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'clinic' ) return;

    wp_enqueue_media();
    wp_enqueue_script(
      'cpt360-logo-upload',
      plugin_dir_url( __DIR__ ) . 'assets/js/logo-upload.js',
      [ 'jquery' ],
      '1.0',
      true
    );
}


/*--------------------------------------------------------------
# Auto fetch clinic logos
--------------------------------------------------------------*/
/**
 * Get the URL of the clinic logo.
 * 1) If you’ve uploaded one via the meta‐box, return that.
 * 2) Otherwise look in assets/images/clinic-images/{slug}-logo.{svg,png,jpg}
 *
 * @param int|null $post_id
 * @return string|false URL or false if none found
 */
function cpt360_get_clinic_logo_url( $post_id = null ) {
    $post_id = $post_id ?: get_the_ID();
    if ( ! $post_id ) {
        return false;
    }

    // 1) uploaded via meta‐box?
    $logo_id = get_post_meta( $post_id, '_clinic_logo_id', true );
    if ( $logo_id ) {
        $url = wp_get_attachment_image_url( $logo_id, 'full' );
        if ( $url ) {
            return $url;
        }
    }


  // 2) fallback to theme assets folder
  $slug       = get_post_field( 'post_name', $post_id );
  $base_dir   = get_template_directory() . '/assets/clinic-images/';
  $base_url   = get_template_directory_uri() . '/assets/clinic-images/';
  $extensions = [ 'svg', 'png', 'jpg', 'jpeg', 'webp' ];

  foreach ( $extensions as $ext ) {
    $file = $base_dir . "{$slug}-logo.{$ext}";
    if ( file_exists( $file ) ) {
      return $base_url . "{$slug}-logo.{$ext}";
    }
  }

    return false;
}

/*--------------------------------------------------------------
# Show clinic assessment IDs in the admin list
--------------------------------------------------------------*/

/**
 * 1) Register a new “Assessment ID” column for the clinic CPT.
 */
add_filter( 'manage_clinic_posts_columns', 'cpt360_add_assessment_id_column', 10 );
function cpt360_add_assessment_id_column( $columns ) {
    $new = [];
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( 'title' === $key ) {
            $new['clinic_assessment_id'] = __( 'Assessment ID', 'cpt360' );
        }
    }
    return $new;
}

/**
 * 2) Output the assessment ID in our new column.
 *
 *    Replace '_cpt360_assessment_id' with whatever meta-key
 *    you’re using to store the assessment ID.
 */
add_action( 'manage_clinic_posts_custom_column', 'cpt360_render_assessment_id_column', 10, 2 );
function cpt360_render_assessment_id_column( $column, $post_id ) {
    if ( 'clinic_assessment_id' !== $column ) {
        return;
    }

    // If you store the assessment ID in post meta:
    $assessment_id = get_post_meta( $post_id, '_cpt360_assessment_id', true );

    // If you instead use a relationship (e.g. a linked CPT), you might do:
    // $connected = get_posts([
    //     'post_type'      => 'clinic_assessment',
    //     'meta_key'       => 'linked_clinic_id',
    //     'meta_value'     => $post_id,
    //     'fields'         => 'ids',
    //     'posts_per_page' => 1,
    // ]);
    // $assessment_id = $connected ? $connected[0] : '';

    echo $assessment_id ? esc_html( $assessment_id ) : '—';
}

/**
 * 3) (Optional) Make the Assessment ID column sortable.
 */
add_filter( 'manage_edit-clinic_sortable_columns', 'cpt360_sortable_assessment_id_column' );
function cpt360_sortable_assessment_id_column( $sortable ) {
    $sortable['clinic_assessment_id'] = 'clinic_assessment_id';
    return $sortable;
}

/**
 * 4) (Optional) Tell WP how to sort by our Assessment ID column.
 */
add_action( 'pre_get_posts', 'cpt360_assessment_id_orderby' );
function cpt360_assessment_id_orderby( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if (
        'clinic' === $query->get( 'post_type' )
        && 'clinic_assessment_id' === $query->get( 'orderby' )
    ) {
        // sort by meta value (numeric)
        $query->set( 'meta_key',   '_cpt360_assessment_id' );
        $query->set( 'orderby',    'meta_value_num' );
    }
}



/*--------------------------------------------------------------
# Add "State" Column to Clinic Admin List
--------------------------------------------------------------*/

// 1. Add a new "State" column after the title
add_filter( 'manage_clinic_posts_columns', 'cpt360_add_state_column' );
function cpt360_add_state_column( $columns ) {
    $new_columns = [];

    foreach ( $columns as $key => $label ) {
        $new_columns[ $key ] = $label;
        if ( $key === 'title' ) {
            $new_columns['clinic_state'] = __( 'State', 'cpt360' );
        }
    }

    return $new_columns;
}

// 2. Populate the "State" column with full state name
add_action( 'manage_clinic_posts_custom_column', 'cpt360_render_state_column', 10, 2 );
function cpt360_render_state_column( $column, $post_id ) {
    if ( $column !== 'clinic_state' ) return;

    $abbr = get_post_meta( $post_id, '_cpt360_clinic_state', true );

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

    echo $abbr && isset( $states[ $abbr ] ) ? esc_html( $states[ $abbr ] ) : '—';
}
// 3. Make the State column sortable
add_filter( 'manage_edit-clinic_sortable_columns', function( $columns ) {
    $columns['clinic_state'] = 'clinic_state';
    return $columns;
});

add_action( 'pre_get_posts', function( $query ) {
    if (
        is_admin() &&
        $query->is_main_query() &&
        $query->get('orderby') === 'clinic_state'
    ) {
        $query->set('meta_key', '_cpt360_clinic_state');
        $query->set('orderby', 'meta_value');
    }
});


/**
 * Shortcode: [cpt360_state_clinics state="DE"]
 * Displays clinics in the given state (by 2-letter abbreviation).
 */
add_shortcode('cpt360_state_clinics', function($atts) {
    $atts = shortcode_atts([
        'state' => '',
    ], $atts, 'cpt360_state_clinics');

    $state = strtoupper(trim($atts['state']));
    if (!$state) return '';

    $clinics = get_posts([
        'post_type'      => 'clinic',
        'posts_per_page' => -1,
        'meta_key'       => '_cpt360_clinic_state',
        'meta_value'     => $state,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ]);

    if (!$clinics) return '<p>No clinics found in this state.</p>';

    ob_start();
    echo '<div class="state-clinics-grid">';
    foreach ($clinics as $clinic) {
        $logo_url = function_exists('cpt360_get_clinic_logo_url')
            ? cpt360_get_clinic_logo_url($clinic->ID)
            : '';
        $title = get_the_title($clinic->ID);
        $link  = get_permalink($clinic->ID);

        echo '<div class="state-clinic">';
        if ($logo_url) {
            echo '<div class="row-1"><a href="' . esc_url($link) . '"><div class="clinic-logo"><img src="' . esc_url($logo_url) . '" alt="' . esc_attr($title) . '" /></div></a></div>';
        }
        echo '<div class="row-2"><h3 class="clinic-title"><a href="' . esc_url($link) . '">' . esc_html($title) . '</a></h3></div>';
        echo '</div>';
    }
    echo '</div>';
    return ob_get_clean();
});

/**
 * Shortcode: [cpt360_clinic_by_name name="Clinic Name"]
 * Displays a specific clinic by its exact name.
 * 
 * Usage examples:
 * [cpt360_clinic_by_name name="New York Clinic"]
 * [cpt360_clinic_by_name name="Main Street Medical Center"]
 */
add_shortcode('cpt360_clinic_by_name', function($atts) {
    $atts = shortcode_atts([
        'name' => '',
    ], $atts, 'cpt360_clinic_by_name');

    $clinic_name = trim($atts['name']);
    if (!$clinic_name) return '<p>Please specify a clinic name.</p>';

    // Search for clinic by exact title match
    $clinics = get_posts([
        'post_type'      => 'clinic',
        'posts_per_page' => 1,
        'title'          => $clinic_name,
        'post_status'    => 'publish',
    ]);

    // If exact match not found, try partial match
    if (!$clinics) {
        $clinics = get_posts([
            'post_type'      => 'clinic',
            'posts_per_page' => 1,
            's'              => $clinic_name,
            'post_status'    => 'publish',
        ]);
    }

    if (!$clinics) {
        return '<p>No clinic found with the name "' . esc_html($clinic_name) . '".</p>';
    }

    $clinic = $clinics[0];
    $logo_url = function_exists('cpt360_get_clinic_logo_url')
        ? cpt360_get_clinic_logo_url($clinic->ID)
        : '';
    $title = get_the_title($clinic->ID);
    $link  = get_permalink($clinic->ID);

    ob_start();
    echo '<div class="clinic-by-name-display">';
    echo '<div class="single-clinic">';
    if ($logo_url) {
        echo '<div class="row-1"><a href="' . esc_url($link) . '"><div class="clinic-logo"><img src="' . esc_url($logo_url) . '" alt="' . esc_attr($title) . '" /></div></a></div>';
    }
    echo '<div class="row-2"><h3 class="clinic-title"><a href="' . esc_url($link) . '">' . esc_html($title) . '</a></h3></div>';
    echo '</div>';
    echo '</div>';
    return ob_get_clean();
});

/**
 * Shortcode: [cpt360_clinics_by_names names="Clinic 1, Clinic 2, Clinic 3"]
 * Displays multiple clinics by their names (comma-separated).
 * 
 * Usage example:
 * [cpt360_clinics_by_names names="New York Clinic, Boston Medical, Chicago Center"]
 */
add_shortcode('cpt360_clinics_by_names', function($atts) {
    $atts = shortcode_atts([
        'names' => '',
    ], $atts, 'cpt360_clinics_by_names');

    $clinic_names = trim($atts['names']);
    if (!$clinic_names) return '<p>Please specify clinic names separated by commas.</p>';

    // Split names by comma and clean them up
    $names_array = array_map('trim', explode(',', $clinic_names));
    $found_clinics = [];

    foreach ($names_array as $clinic_name) {
        if (empty($clinic_name)) continue;

        // Search for clinic by exact title match first
        $clinics = get_posts([
            'post_type'      => 'clinic',
            'posts_per_page' => 1,
            'title'          => $clinic_name,
            'post_status'    => 'publish',
        ]);

        // If exact match not found, try partial match
        if (!$clinics) {
            $clinics = get_posts([
                'post_type'      => 'clinic',
                'posts_per_page' => 1,
                's'              => $clinic_name,
                'post_status'    => 'publish',
            ]);
        }

        if ($clinics) {
            $found_clinics[] = $clinics[0];
        }
    }

    if (!$found_clinics) {
        return '<p>No clinics found with the specified names.</p>';
    }

    ob_start();
    echo '<div class="clinics-by-names-grid">';
    foreach ($found_clinics as $clinic) {
        $logo_url = function_exists('cpt360_get_clinic_logo_url')
            ? cpt360_get_clinic_logo_url($clinic->ID)
            : '';
        $title = get_the_title($clinic->ID);
        $link  = get_permalink($clinic->ID);

        echo '<div class="named-clinic">';
        if ($logo_url) {
            echo '<div class="row-1"><a href="' . esc_url($link) . '"><div class="clinic-logo"><img src="' . esc_url($logo_url) . '" alt="' . esc_attr($title) . '" /></div></a></div>';
        }
        echo '<div class="row-2"><h3 class="clinic-title"><a href="' . esc_url($link) . '">' . esc_html($title) . '</a></h3></div>';
        echo '</div>';
    }
    echo '</div>';
    return ob_get_clean();
});
