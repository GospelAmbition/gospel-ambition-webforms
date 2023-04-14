<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'go_site_info', function( $stats ) {
    $stats['site_name'] = get_bloginfo();
    $stats['icon'] = GO_Context_Switcher::plugin_url( '/assets/icons/dt-circle-logo.png' );

    return $stats;
}, 10, 1 );

add_filter( 'go_webform_options', function ( $params ) {
    $params['lists'] = $params['lists'] ?? [];
    $params['lists'][] = 'list_18';

    return $params;
}, 10, 1 );