<?php
add_action( 'wp_enqueue_scripts', 'livi_child_enqueue_styles', 100);
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );
// Add support for block styles and patterns
add_theme_support( 'wp-block-styles' );
add_theme_support( 'responsive-embeds' );

// Add support for HTML5
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

// Add support for custom logos, headers, and backgrounds
add_theme_support( 'custom-logo' );
add_theme_support( 'custom-header' );
add_theme_support( 'custom-background' );

// Add support for wide alignment
add_theme_support( 'align-wide' );

// Add editor styling
add_editor_style();

// Add support for post thumbnails
add_theme_support( 'post-thumbnails' );

// Enqueue comment-reply script
if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
	wp_enqueue_script( 'comment-reply' );
}
add_filter( 'woocommerce_cart_item_thumbnail', '__return_false' );

function livi_child_enqueue_styles() {
	wp_enqueue_style( 'livi-parent', get_theme_file_uri('/style.css') );
}

add_action( 'woocommerce_checkout_process', 'custom_handle_user_creation' );

function custom_handle_user_creation() {
    if ( ! is_user_logged_in() ) {
        // Get Parent Info from Step 1
        $parent_email = sanitize_email( $_POST['parent_email'] );
        $parent_password = sanitize_text_field( $_POST['password'] );
        $parent_first_name = sanitize_text_field( $_POST['parent_first_name'] );
        $parent_last_name = sanitize_text_field( $_POST['parent_last_name'] );
        $parent_phone = sanitize_text_field( $_POST['parent_phone'] );

        // Validate email uniqueness
        if ( email_exists( $parent_email ) ) {
            wc_add_notice( __( 'An account with this email already exists.', 'woocommerce' ), 'error' );
        }

        // Validate required fields
        if ( empty( $parent_password ) || empty( $parent_email ) ) {
            wc_add_notice( __( 'Email and Password are required fields.', 'woocommerce' ), 'error' );
        }
        
        // Create the user if no errors
        if ( ! wc_notice_count( 'error' ) ) {
            $user_id = wp_create_user( $parent_email, $parent_password, $parent_email );
            if ( is_wp_error( $user_id ) ) {
                wc_add_notice( __( 'Failed to create an account. Please try again.', 'woocommerce' ), 'error' );
            } else {
                // Update user meta
                wp_update_user( array(
                    'ID'         => $user_id,
                    'first_name' => $parent_first_name,
                    'last_name'  => $parent_last_name,
                ) );
                update_user_meta( $user_id, 'parent_phone', $parent_phone );

                // Log in the user
                wc_set_customer_auth_cookie( $user_id );
            }
        }
    }
}


add_action( 'woocommerce_checkout_update_order_meta', 'save_custom_checkout_fields' );
function save_custom_checkout_fields( $order_id ) {
    // Parent Info
    if ( ! empty( $_POST['parent_first_name'] ) ) {
        update_post_meta( $order_id, '_parent_first_name', sanitize_text_field( $_POST['parent_first_name'] ) );
    }
    if ( ! empty( $_POST['parent_last_name'] ) ) {
        update_post_meta( $order_id, '_parent_last_name', sanitize_text_field( $_POST['parent_last_name'] ) );
    }
    if ( ! empty( $_POST['parent_email'] ) ) {
        update_post_meta( $order_id, '_parent_email', sanitize_email( $_POST['parent_email'] ) );
    }
    if ( ! empty( $_POST['parent_phone'] ) ) {
        update_post_meta( $order_id, '_parent_phone', sanitize_text_field( $_POST['parent_phone'] ) );
    }

    // Student Info
    if ( ! empty( $_POST['student_first_name'] ) ) {
        update_post_meta( $order_id, '_student_first_name', sanitize_text_field( $_POST['student_first_name'] ) );
    }
    if ( ! empty( $_POST['student_last_name'] ) ) {
        update_post_meta( $order_id, '_student_last_name', sanitize_text_field( $_POST['student_last_name'] ) );
    }
    if ( ! empty( $_POST['student_email'] ) ) {
        update_post_meta( $order_id, '_student_email', sanitize_email( $_POST['student_email'] ) );
    }
    if ( ! empty( $_POST['student_phone'] ) ) {
        update_post_meta( $order_id, '_student_phone', sanitize_text_field( $_POST['student_phone'] ) );
    }
    if ( ! empty( $_POST['country'] ) ) {
        update_post_meta( $order_id, '_country', sanitize_text_field( $_POST['country'] ) );
    }
    // if ( ! empty( $_POST['address'] ) ) {
    //     update_post_meta( $order_id, '_address', sanitize_text_field( $_POST['address'] ) );
    // }
    // if ( ! empty( $_POST['address2'] ) ) {
    //     update_post_meta( $order_id, '_address2', sanitize_text_field( $_POST['address2'] ) );
    // }
    // if ( ! empty( $_POST['city'] ) ) {
    //     update_post_meta( $order_id, '_city', sanitize_text_field( $_POST['city'] ) );
    // }
    // if ( ! empty( $_POST['zip'] ) ) {
    //     update_post_meta( $order_id, '_zip', sanitize_text_field( $_POST['zip'] ) );
    // }
}

add_action( 'woocommerce_admin_order_data_after_order_details', 'display_custom_fields_in_admin' );
function display_custom_fields_in_admin( $order ) {
    // Parent Info
    $parent_first_name = get_post_meta( $order->get_id(), '_parent_first_name', true );
    $parent_last_name  = get_post_meta( $order->get_id(), '_parent_last_name', true );
    $parent_email      = get_post_meta( $order->get_id(), '_parent_email', true );
    $parent_phone      = get_post_meta( $order->get_id(), '_parent_phone', true );

    // Student Info
    $student_first_name = get_post_meta( $order->get_id(), '_student_first_name', true );
    $student_last_name  = get_post_meta( $order->get_id(), '_student_last_name', true );
    $student_email      = get_post_meta( $order->get_id(), '_student_email', true );
    $student_phone      = get_post_meta( $order->get_id(), '_student_phone', true );
    $country            = get_post_meta( $order->get_id(), '_country', true );
    // $address            = get_post_meta( $order->get_id(), '_address', true );
    // $address2           = get_post_meta( $order->get_id(), '_address2', true );
    // $city               = get_post_meta( $order->get_id(), '_city', true );
    // $zip                = get_post_meta( $order->get_id(), '_zip', true );

    echo '<h3>' . esc_html__( 'Custom Checkout Fields', 'woocommerce' ) . '</h3>';
    echo '<p><strong>' . esc_html__( 'Parent First Name:', 'woocommerce' ) . '</strong> ' . esc_html( $parent_first_name ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Parent Last Name:', 'woocommerce' ) . '</strong> ' . esc_html( $parent_last_name ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Parent Email:', 'woocommerce' ) . '</strong> ' . esc_html( $parent_email ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Parent Phone:', 'woocommerce' ) . '</strong> ' . esc_html( $parent_phone ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Student First Name:', 'woocommerce' ) . '</strong> ' . esc_html( $student_first_name ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Student Last Name:', 'woocommerce' ) . '</strong> ' . esc_html( $student_last_name ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Student Email:', 'woocommerce' ) . '</strong> ' . esc_html( $student_email ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Student Phone:', 'woocommerce' ) . '</strong> ' . esc_html( $student_phone ) . '</p>';
    echo '<p><strong>' . esc_html__( 'Country:', 'woocommerce' ) . '</strong> ' . esc_html( $country ) . '</p>';
    // echo '<p><strong>' . esc_html__( 'Address:', 'woocommerce' ) . '</strong> ' . esc_html( $address ) . '</p>';
    // echo '<p><strong>' . esc_html__( 'Address 2:', 'woocommerce' ) . '</strong> ' . esc_html( $address2 ) . '</p>';
    // echo '<p><strong>' . esc_html__( 'City:', 'woocommerce' ) . '</strong> ' . esc_html( $city ) . '</p>';
    // echo '<p><strong>' . esc_html__( 'Zip Code:', 'woocommerce' ) . '</strong> ' . esc_html( $zip ) . '</p>';
}

add_filter( 'woocommerce_checkout_fields', 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
    // Set default values for missing fields
    $fields['billing']['billing_first_name']['required'] = false;
    $fields['billing']['billing_last_name']['required'] = false;
    $fields['billing']['billing_email']['required'] = false;
    $fields['billing']['billing_phone']['required'] = false;
    $fields['billing']['billing_country']['required'] = false;
    $fields['billing']['billing_address_1']['required'] = false;
    $fields['billing']['billing_city']['required'] = false;
    $fields['billing']['billing_state']['required'] = false;
    $fields['billing']['billing_postcode']['required'] = false;

    // Set default values
    $fields['billing']['billing_first_name']['default'] = isset($_POST['parent_first_name']) ? sanitize_text_field($_POST['parent_first_name']) : 'N/A';
    $fields['billing']['billing_last_name']['default'] = isset($_POST['parent_last_name']) ? sanitize_text_field($_POST['parent_last_name']) : 'N/A';
    $fields['billing']['billing_email']['default'] = isset($_POST['parent_email']) ? sanitize_email($_POST['parent_email']) : 'noemail@example.com';
    $fields['billing']['billing_phone']['default'] = isset($_POST['parent_phone']) ? sanitize_text_field($_POST['parent_phone']) : '000-000-0000';
    $fields['billing']['billing_country']['default'] = isset($_POST['country']) ? sanitize_text_field($_POST['country']) : 'US';
    $fields['billing']['billing_address_1']['default'] = isset($_POST['address']) ? sanitize_text_field($_POST['address']) : 'Unknown Address';
    $fields['billing']['billing_city']['default'] = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : 'Unknown City';
    $fields['billing']['billing_state']['default'] = 'N/A';
    $fields['billing']['billing_postcode']['default'] = isset($_POST['zip']) ? sanitize_text_field($_POST['zip']) : '00000';

    return $fields;
}

add_action( 'woocommerce_order_status_completed', 'custom_link_courses_to_user' );

function custom_link_courses_to_user( $order_id ) {
    $order = wc_get_order( $order_id );

    // Get the user ID
    $user_id = $order->get_user_id();
    if ( ! $user_id ) {
        return;
    }

    // Get purchased product IDs
    $items = $order->get_items();
    foreach ( $items as $item ) {
        $product_id = $item->get_product_id();

        // Add purchased courses to user meta
        $purchased_courses = get_user_meta( $user_id, 'purchased_courses', true );
        if ( ! is_array( $purchased_courses ) ) {
            $purchased_courses = array();
        }

        if ( ! in_array( $product_id, $purchased_courses ) ) {
            $purchased_courses[] = $product_id;
        }

        update_user_meta( $user_id, 'purchased_courses', $purchased_courses );
    }
}

add_action( 'woocommerce_thankyou', 'custom_redirect_to_dashboard' );

function custom_redirect_to_dashboard( $order_id ) {
    if ( is_user_logged_in() ) {
        wp_redirect(home_url('/my-account/')); // Replace '/dashboard' with your dashboard URL.
        exit;
    }
}


// function redirect_subscribers_to_my_account() {
//     if (is_user_logged_in() && current_user_can('subscriber')) {
//         // Allow access to WooCommerce pages
//         if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
//             return;
//         }

//         // Redirect only if not on allowed pages
//         wp_redirect(home_url('/my-account/'));
//         exit;
//     }
// }
// add_action('template_redirect', 'redirect_subscribers_to_my_account');

// Add custom checkout fields to WooCommerce Admin New Order Email
add_action( 'woocommerce_email_order_details', 'display_custom_fields_in_admin_email', 20, 4 );

function display_custom_fields_in_admin_email( $order, $sent_to_admin, $plain_text, $email ) {
    if ( ! $sent_to_admin ) {
        return;
    }
    
    // Parent Info
    $parent_first_name = get_post_meta( $order->get_id(), '_parent_first_name', true );
    $parent_last_name  = get_post_meta( $order->get_id(), '_parent_last_name', true );
    $parent_email      = get_post_meta( $order->get_id(), '_parent_email', true );
    $parent_phone      = get_post_meta( $order->get_id(), '_parent_phone', true );

    // Student Info
    $student_first_name = get_post_meta( $order->get_id(), '_student_first_name', true );
    $student_last_name  = get_post_meta( $order->get_id(), '_student_last_name', true );
    $student_email      = get_post_meta( $order->get_id(), '_student_email', true );
    $student_phone      = get_post_meta( $order->get_id(), '_student_phone', true );
    $country            = get_post_meta( $order->get_id(), '_country', true );

    // Only display if there are values
    if ( ! empty( $parent_first_name ) || ! empty( $student_first_name ) ) {
        echo '<h2>' . esc_html__( 'Order Details', 'woocommerce' ) . '</h2>';
        echo '<table style="width:100%; border-collapse: collapse;" border="1">';
        echo '<tr><td><strong>' . esc_html__( 'Parent First Name:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $parent_first_name ) . '</td></tr>';
        echo '<tr><td><strong>' . esc_html__( 'Parent Last Name:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $parent_last_name ) . '</td></tr>';
        echo '<tr><td><strong>' . esc_html__( 'Parent Email:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $parent_email ) . '</td></tr>';
        echo '<tr><td><strong>' . esc_html__( 'Parent Phone:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $parent_phone ) . '</td></tr>';
        echo '<tr><td><strong>' . esc_html__( 'Student First Name:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $student_first_name ) . '</td></tr>';
        echo '<tr><td><strong>' . esc_html__( 'Student Last Name:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $student_last_name ) . '</td></tr>';
        echo '<tr><td><strong>' . esc_html__( 'Student Email:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $student_email ) . '</td></tr>';
        echo '<tr><td><strong>' . esc_html__( 'Student Phone:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $student_phone ) . '</td></tr>';
        echo '<tr><td><strong>' . esc_html__( 'Country:', 'woocommerce' ) . '</strong></td><td>' . esc_html( $country ) . '</td></tr>';
        echo '</table>';
    }
}

add_filter('woocommerce_checkout_fields', 'remove_email_password_validation');

function remove_email_password_validation($fields) {
    if (isset($fields['billing']['billing_email'])) {
        $fields['billing']['billing_email']['required'] = false;
    }

    if (isset($fields['account']['account_password'])) {
        $fields['account']['account_password']['required'] = false;
    }

    return $fields;
}

