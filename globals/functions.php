<?php


if ( !function_exists( 'dt_cached_api_call' ) ){
    function dt_cached_api_call( $url, $type = 'GET', $args = [], $duration = HOUR_IN_SECONDS, $use_cache = true ){
        $data = get_transient( 'dt_cached_' . esc_url( $url ) );
        if ( !$use_cache || empty( $data ) ){
            if ( $type === 'GET' ){
                $response = wp_remote_get( $url, $args );
            } else {
                $response = wp_remote_post( $url, $args );
            }
            if ( is_wp_error( $response ) || isset( $response['response']['code'] ) && $response['response']['code'] !== 200 ){
                return false;
            }
            $data = wp_remote_retrieve_body( $response );

            set_transient( 'dt_cached_' . esc_url( $url ), $data, $duration );
        }
        return $data;
    }
}

if ( !function_exists( 'dt_is_rest' ) ) {
    /**
     * Checks if the current request is a WP REST API request.
     *
     * Case #1: After WP_REST_Request initialisation
     * Case #2: Support "plain" permalink settings
     * Case #3: URL Path begins with wp-json/ (your REST prefix)
     *          Also supports WP installations in subfolders
     *
     * @returns boolean
     */
    function dt_is_rest( $namespace = null ) {
        $prefix = rest_get_url_prefix();
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST
            || isset( $_GET['rest_route'] )
            && strpos( trim( sanitize_text_field( wp_unslash( $_GET['rest_route'] ) ), '\\/' ), $prefix, 0 ) === 0 ) {
            return true;
        }
        $rest_url    = wp_parse_url( site_url( $prefix ) );
        $current_url = wp_parse_url( add_query_arg( array() ) );
        $is_rest = strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
        if ( $namespace ){
            return $is_rest && strpos( $current_url['path'], $namespace ) != false;
        } else {
            return $is_rest;
        }
    }
}
