<?php
/**
 * Functions
 *
 * @package     GamiPress\Functions
 * @author      GamiPress <contact@gamipress.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Helper function to get an option value.
 *
 * @since  1.0.1
 *
 * @param string    $option_name
 * @param bool      $default
 *
 * @return mixed Option value or default parameter value if not exists.
 */
function gamipress_get_option( $option_name, $default = false ) {

    if( GamiPress()->settings === null ) {

        // If GamiPress is installed network wide, get settings from network options
        if( gamipress_is_network_wide_active() ) {
            GamiPress()->settings = get_site_option( 'gamipress_settings' );
        } else {
            GamiPress()->settings = get_option( 'gamipress_settings' );
        }

    }

    return isset( GamiPress()->settings[ $option_name ] ) ? GamiPress()->settings[ $option_name ] : $default;

}

/**
 * Helper function to get a transient value.
 *
 * @since  1.4.0
 *
 * @param string    $transient
 *
 * @return mixed Value of transient.
 */
function gamipress_get_transient( $transient ) {

    // If GamiPress is installed network wide, get transient from network
    if( gamipress_is_network_wide_active() ) {
        return get_site_transient( $transient );
    } else {
       return get_transient( $transient );
    }

}

/**
 * Helper function to set a transient value.
 *
 * @since  1.4.0
 *
 * @param string    $transient
 * @param mixed     $value
 * @param integer   $expiration
 */
function gamipress_set_transient( $transient, $value, $expiration = 0 ) {

    // If GamiPress is installed network wide, get transient from network
    if( gamipress_is_network_wide_active() ) {
        set_site_transient( $transient, $value, $expiration );
    } else {
        set_transient( $transient, $value, $expiration );
    }

}

/**
 * Helper function to delete a transient value.
 *
 * @since  1.4.0
 *
 * @param string    $transient
 *
 * @return bool True if successful, false otherwise.
 */
function gamipress_delete_transient( $transient ) {

    // If GamiPress is installed network wide, get transient from network
    if( gamipress_is_network_wide_active() ) {
        return delete_site_transient( $transient );
    } else {
        return delete_transient( $transient );
    }

}

/**
 * Helper function to get an user meta.
 *
 * @since  1.4.0
 *
 * @param int    $user_id   User ID.
 * @param string $meta_key  Optional. The meta key to retrieve. By default, returns data for all keys.
 * @param bool   $single    Whether to return a single value.
 *
 * @return mixed            Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function gamipress_get_user_meta( $user_id, $meta_key = '', $single = true ) {

    if( gamipress_is_network_wide_active() ) {
        return get_user_option( $meta_key, $user_id );
    } else {
        return get_user_meta( $user_id, $meta_key, $single );
    }

}

/**
 * Helper function to update an user meta.
 *
 * @since  1.4.0
 *
 * @param int    $user_id       User ID.
 * @param string $meta_key      Metadata key.
 * @param mixed  $meta_value    Metadata value.
 * @param mixed  $prev_value    Optional. Previous value to check before removing.
 *
 * @return integer|bool         Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function gamipress_update_user_meta( $user_id, $meta_key, $meta_value, $prev_value = '' ) {

    if( gamipress_is_network_wide_active() ) {
        return update_user_option( $user_id, $meta_key, $meta_value, true );
    } else {
        return update_user_meta( $user_id, $meta_key, $meta_value, $prev_value );
    }

}

/**
 * Helper function to delete an user meta.
 *
 * @since  1.4.0
 *
 * @param int    $user_id       User ID.
 * @param string $meta_key      Metadata key.
 * @param mixed  $meta_value    Metadata value.
 *
 * @return bool                 True on success, false on failure.
 */
function gamipress_delete_user_meta( $user_id, $meta_key, $meta_value = '' ) {

    if( gamipress_is_network_wide_active() ) {
        return delete_user_option( $user_id, $meta_key, true );
    } else {
        return delete_user_meta( $user_id, $meta_key, $meta_value );
    }

}

/**
 * Helper function to get a post meta.
 *
 * Important: On network wide installs, this function will return the post meta from main site, so use only for points, achievements and ranks metas
 *
 * @since  1.4.0
 *
 * @param int    $post_id   Post ID.
 * @param string $meta_key  Optional. The meta key to retrieve. By default, returns data for all keys.
 * @param bool   $single    Whether to return a single value.
 *
 * @return mixed            Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function gamipress_get_post_meta( $post_id, $meta_key = '', $single = true ) {

    if( gamipress_is_network_wide_active() && ! is_main_site() ) {

        // Switch to main site
        switch_to_blog( get_main_site_id() );

        // Get the post meta
        $value = get_post_meta( $post_id, $meta_key, $single );

        // Restore current site
        restore_current_blog();

        return $value;

    } else {

        return get_post_meta( $post_id, $meta_key, $single );

    }

}

/**
 * Helper function to update a post meta.
 *
 * Important: On network wide installs, this function will update the post meta from main site, so use only for points, achievements and ranks metas
 *
 * @since  1.4.0
 *
 * @param int    $post_id       Post ID.
 * @param string $meta_key      Metadata key.
 * @param mixed  $meta_value    Metadata value.
 * @param mixed  $prev_value    Optional. Previous value to check before removing.
 *
 * @return integer|bool         Meta ID if the key didn't exist, true on successful update, false on failure.
 */
function gamipress_update_post_meta( $post_id, $meta_key, $meta_value, $prev_value = '' ) {

    if( gamipress_is_network_wide_active() && ! is_main_site() ) {

        // Switch to main site
        switch_to_blog( get_main_site_id() );

        $result = update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );

        // Restore current site
        restore_current_blog();

        return $result;

    } else {

        return update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );

    }

}

/**
 * Helper function to delete a post meta.
 *
 * Important: On network wide installs, this function will delete the post meta from main site, so use only for points, achievements and ranks metas
 *
 * @since  1.4.0
 *
 * @param int    $post_id       Post ID.
 * @param string $meta_key      Metadata key.
 * @param mixed  $meta_value    Metadata value.
 *
 * @return bool                 True on success, false on failure.
 */
function gamipress_delete_post_meta( $post_id, $meta_key, $meta_value = '' ) {

    if( gamipress_is_network_wide_active() && ! is_main_site() ) {

        // Switch to main site
        switch_to_blog( get_main_site_id() );

        $result = delete_post_meta( $post_id, $meta_key, $meta_value );

        // Restore current site
        restore_current_blog();

        return $result;

    } else {
        return delete_post_meta( $post_id, $meta_key, $meta_value );
    }

}

/**
 * Helper function to get a post.
 *
 * Important: On network wide installs, this function will return the post from main site, so use only for points, achievements and ranks posts
 *
 * @since  1.4.0
 *
 * @param int    $post_id       Post ID.
 *
 * @return false|WP_Post        Post object on success, false on failure.
 */
function gamipress_get_post( $post_id ) {

    // if we got a post object, then return their field
    if ( $post_id instanceof WP_Post ) {
        return $post_id;
    }

    global $wpdb;

    if( gamipress_is_network_wide_active() && ! is_main_site() ) {

        // GamiPress post are stored on main site, so if we are not on main site, then we need to get their fields from global table
        $posts = GamiPress()->db->posts;

        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$posts} WHERE ID = %d",
            absint( $post_id )
        ) );

    } else {
        return get_post( $post_id );
    }

}

/**
 * Helper function to get a post status.
 *
 * Important: On network wide installs, this function will return the post status from main site, so use only for points, achievements and ranks post status
 *
 * @since  1.4.0
 *
 * @param int    $post_id       Post ID.
 *
 * @return false|string         Post status on success, false on failure.
 */
function gamipress_get_post_status( $post_id = null ) {

    return gamipress_get_post_field( 'post_status', $post_id );

}

/**
 * Helper function to get a post type.
 *
 * Important: On network wide installs, this function will return the post type from main site, so use only for points, achievements and ranks post type
 *
 * @since  1.4.0
 *
 * @param int    $post_id       Post ID.
 *
 * @return false|string         Post status on success, false on failure.
 */
function gamipress_get_post_type( $post_id = null ) {

    return gamipress_get_post_field( 'post_type', $post_id );

}

/**
 * Helper function to get a post date.
 *
 * Important: On network wide installs, this function will return the post date from main site, so use only for points, achievements and ranks post date
 *
 * @since  1.4.0
 *
 * @param int    $post_id       Post ID.
 *
 * @return false|string         Post status on success, false on failure.
 */
function gamipress_get_post_date( $post_id = null ) {

    return gamipress_get_post_field( 'post_date', $post_id );

}

/**
 * Helper function to get a post field.
 *
 * Important: On network wide installs, this function will return the post field from main site, so use only for points, achievements and ranks posts fields
 *
 * @since  1.4.0
 *
 * @param string                $field      The post field.
 * @param integer|WP_Post|null  $post_id    Post ID.
 *
 * @return false|string                     Post field on success, false on failure.
 */
function gamipress_get_post_field( $field, $post_id = null  ) {

    if ( empty( $post_id ) && isset( $GLOBALS['post'] ) )
        $post_id = $GLOBALS['post'];

    // if we got a post object, then return their field
    if ( $post_id instanceof WP_Post ) {
        return $post_id->$field;
    } else if( is_object( $post_id ) ) {
        return $post_id->$field;
    }

    global $wpdb;

    if( gamipress_is_network_wide_active() && ! is_main_site() ) {
        // GamiPress post are stored on main site, so if we are not on main site, then we need to get their fields from global table
        $posts = GamiPress()->db->posts;

        return $wpdb->get_var( $wpdb->prepare(
            "SELECT {$field} FROM {$posts} WHERE ID = %d",
            absint( $post_id )
        ) );
    } else {
        return get_post_field( $field, $post_id );
    }

}

/**
 * Helper function to check if a post exists.
 *
 * Important: On network wide installs, this function will check the post from main site, so use only for points, achievements and ranks posts
 *
 * @since  1.4.2
 *
 * @param integer  $post_id     Post ID.
 *
 * @return bool                 True if exists, false if not.
 */
function gamipress_post_exists( $post_id  ) {

    $post_id = absint( $post_id );

    if( $post_id === 0 ) {
        return false;
    }

    global $wpdb;

    // GamiPress post are stored on main site, so if we are not on main site, then we need to get their fields from global table
    $posts = GamiPress()->db->posts;

    $found = $wpdb->get_var( $wpdb->prepare(
        "SELECT ID FROM {$posts} WHERE ID = %d",
        $post_id
    ) );

    return absint( $found ) === $post_id;
}

/**
 * Register GamiPress types and flush rewrite rules.
 *
 * @since 1.0.0
 */
function gamipress_flush_rewrite_rules() {

    gamipress_register_post_types();
    gamipress_register_points_types();
    gamipress_register_achievement_types();
    gamipress_register_rank_types();

    flush_rewrite_rules();

}

/**
 * Utility to execute a shortcode passing an array of args
 *
 * @since   1.0.0
 * @updated 1.4.6 Added $content parameter
 * @updated 1.6.2 Sanitize attribute's value to avoid double quotes
 *
 * @param string    $shortcode The shortcode to execute
 * @param array     $args      The args to pass to the shortcode
 * @param string    $content   Content to pass to the shortcode (optional)
 *
 * @return string   $output    Output from the shortcode execution with the given args
 */
function gamipress_do_shortcode( $shortcode, $args, $content = '' ) {

    $shortcode_args = '';

    foreach( $args as $arg => $value ) {

        if( is_array( $value ) ) {

            if( array_keys( $value ) !== range( 0, count($value) - 1 ) ) {

                // Turn associative arrays into json to keep keys
                $value = str_replace( '"', '\'', json_encode( $value ) );
                $value = str_replace( '[', '{', $value );
                $value = str_replace( ']', '}', $value );

            } else {

                $is_multidimensional = false;

                foreach ($value as $value_items) {
                    if ( is_array($value_items) ) {
                        $is_multidimensional = true;
                        break;
                    }
                }

                if( $is_multidimensional ) {
                    // Turn multidimensional arrays into json to keep inherit arrays
                    $value = str_replace( '"', '\'', json_encode( $value ) );
                    $value = str_replace( '[', '{', $value );
                    $value = str_replace( ']', '}', $value );
                } else {
                    // non associative and non multidimensional arrays, set a string of comma separated values
                    $value = implode( ',', $value );
                }

            }
        }

        // Prevent attribute's value to have double quotes
        $value = str_replace( '"', '\'', $value );

        $shortcode_args .= sprintf( ' %s="%s"', $arg, $value);
    }

    if( ! empty( $content ) ) {

        // If content passed, then execute shortcode as [shortcode]content[/shortcode]
        return do_shortcode( sprintf( '[%s %s]%s[/%s]',
            $shortcode,
            $shortcode_args,
            $content,
            $shortcode
        ) );

    }

    return do_shortcode( sprintf( '[%s %s]', $shortcode, $shortcode_args ) );

}

/**
 * Utility to check whether function is disabled.
 *
 * @since 1.3.7
 *
 * @param string $function  Name of the function.
 * @return bool             Whether or not function is disabled.
 */
function gamipress_is_function_disabled( $function ) {
    $disabled = explode( ',',  ini_get( 'disable_functions' ) );

    return in_array( $function, $disabled );
}

/**
 * Sanitize given slug.
 *
 * @since 1.3.9.8
 *
 * @param string $slug  Slug to sanitize.
 *
 * @return string       Sanitized slug.
 */
function gamipress_sanitize_slug( $slug ) {

    // Sanitize slug
    $slug = sanitize_key( $slug );

    // Check slug length
    if( strlen( $slug ) > 20 ) {
        $slug = substr( $slug, 0, 20 );
    }

    return $slug;

}

/**
 * Helper function to recursively check if something is in a multidimensional array
 *
 * @since 1.4.1
 *
 * @param mixed $needle
 * @param array $haystack
 * @param bool  $strict
 *
 * @return bool
 */
function gamipress_in_array( $needle, $haystack, $strict = false ) {

    foreach( $haystack as $item ) {
        if (
            ( $strict ? $item === $needle : $item == $needle )
            || ( is_array( $item ) && gamipress_in_array( $needle, $item, $strict ) )
        ) {
            return true;
        }
    }

    return false;
}

/**
 * Check if a specific action or filter has been hooked
 *
 * @since 1.4.3
 *
 * @param string $filter
 *
 * @return bool
 */
function gamipress_has_filters( $filter ) {

    global $wp_filter;

    return (bool) ( isset( $wp_filter[$filter] ) && count( $wp_filter[$filter] ) > 0 );

}

/**
 * Sum all user meta values of a given meta key
 *
 * @since 1.5.9
 *
 * @param string $meta_key
 *
 * @return integer
 */
function gamipress_get_user_meta_sum( $meta_key ) {

    global $wpdb;

    $sum = $wpdb->get_var( $wpdb->prepare(
        "SELECT SUM( meta_value )
         FROM {$wpdb->usermeta}
         WHERE meta_key = %s",
        $meta_key
    ) );

    return absint( $sum );

}

/**
 * Turn an array into a HTML list of hidden inputs
 *
 * @since 1.6.5
 *
 * @param array $array      Array of elements to render
 * @param array $excluded   Optional, elements excluded for being rendered
 *
 * @return string
 */
function gamipress_array_as_hidden_inputs( $array, $excluded = array() ) {

    $html = '';

    foreach( $array as $key => $value ) {

        // Skip excluded keys
        if( in_array( $key, $excluded ) ) {
            continue;
        }

        // Sanitize value
        $value = is_array( $value ) ? implode(',', $value ) : $value;


        $html .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';

    }

    return $html;

}

/**
 * Gets registered time periods
 *
 * @since 1.6.9
 *
 * @return array
 */
function gamipress_get_time_periods() {

    /**
     * Filter registered time periods
     *
     * @since 1.6.9
     *
     * @param array $time_periods
     *
     * @return array
     */
    return apply_filters( 'gamipress_get_time_periods', array(
        ''              => __( 'None', 'gamipress' ),
        'today'         => __( 'Today', 'gamipress' ),
        'yesterday'     => __( 'Yesterday', 'gamipress' ),
        'this-week'     => __( 'Current Week', 'gamipress' ),
        'past-week'     => __( 'Past Week', 'gamipress' ),
        'this-month'    => __( 'Current Month', 'gamipress' ),
        'past-month'    => __( 'Past Month', 'gamipress' ),
        'this-year'     => __( 'Current Year', 'gamipress' ),
        'past-year'     => __( 'Past Year', 'gamipress' ),
        'custom'        => __( 'Custom', 'gamipress' ),
    ) );

}

/**
 * Get a specific period date range
 *
 * @see gamipress_get_time_periods()
 *
 * @since 1.6.9
 *
 * @param string $period
 *
 * @return array
 */
function gamipress_get_period_range( $period = '' ) {

    // Setup date range var
    $date_range = array(
        'start' => '',
        'end'   => '',
    );

    if( $period !== '' ) {

        switch( $period ) {
            case 'today':
                $date_range = array(
                    'start' => date( 'Y-m-d' ),
                    'end' => '',
                );
                break;
            case 'yesterday':
                $date_range = array(
                    'start' => date( 'Y-m-d', strtotime( '-1 day' ) ),
                    'end' => '',
                );
                break;
            case 'this-week':
                $date_range = gamipress_get_date_range( 'week' );
                break;
            case 'past-week':
                $previous_week = strtotime( '-1 week +1 day' );
                $date_range = gamipress_get_date_range( 'week', $previous_week );
                break;
            case 'this-month':
                $date_range = gamipress_get_date_range( 'month' );
                break;
            case 'past-month':
                $previous_month = strtotime( '-1 month +1 day' );
                $date_range = gamipress_get_date_range( 'month', $previous_month );
                break;
            case 'this-year':
                $date_range = gamipress_get_date_range( 'year' );
                break;
            case 'past-year':
                $previous_year = strtotime( '-1 year +1 day' );
                $date_range = gamipress_get_date_range( 'year', $previous_year );
                break;
            default:
                // For custom ranges use 'gamipress_get_period_range' filter
                break;
        }
    }

    /**
     * Filter the period date range
     *
     * @since 1.6.9
     *
     * @param array     $date_range An array with period date range
     * @param string    $period     Given period, see gamipress_get_time_periods()
     */
    return $date_range = apply_filters( 'gamipress_get_period_range', $date_range, $period );
}

/**
 * Helper function to get a range date based on a given date
 *
 * @since 1.6.9
 *
 * @param string            $range (week|month|year)
 * @param integer|string    $date
 *
 * @return array
 */
function gamipress_get_date_range( $range = '', $date = 0 ) {

    if( gettype( $date ) === 'string' ) {
        $date = strtotime( $date );
    }

    if( ! $date ) {
        $date = current_time( 'timestamp' );
    }

    $start_date = 0;
    $end_date = 0;

    switch( $range ) {
        case 'week':

            // Weekly range
            $start_date    = strtotime( 'last monday', $date );
            $end_date      = strtotime( 'midnight', strtotime( 'next sunday', $date ) );

            break;
        case 'month':

            // Monthly range
            $start_date    = strtotime( date( 'Y-m-01', $date ) );
            $end_date      = strtotime( 'midnight', strtotime( 'last day of this month', $date ) );

            break;
        case 'year':

            // Yearly range
            $start_date    = strtotime( date( 'Y-01-01', $date ) );
            $end_date      = strtotime( date( 'Y-12-31', $date ) );

            break;
    }

    return array(
        'start'    => date( 'Y-m-d', $start_date ),
        'end'      => date( 'Y-m-d', $end_date )
    );

}

/**
 * Helper function to format a given date or timestamp
 *
 * @since 1.6.9
 *
 * @param string    $format Date format
 * @param mixed     $date
 *
 * @return string|false
 */
function gamipress_date( $format, $date = '' ) {

    $date_formatted = false;

    if( strtotime( $date ) ) {
        // Ensure date given is correct
        $date_formatted = date( $format, strtotime( $date ) );
    } else if( absint( $date ) > 0 ) {
        // Support for timestamp value
        $date_formatted = date( $format, absint( $date ) );
    }

    return $date_formatted;

}