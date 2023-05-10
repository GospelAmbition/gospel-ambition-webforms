<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'go_site_info', function( $stats ) {
    $stats['site_name'] = get_bloginfo();
    $stats['icon'] = GO_Context_Switcher::plugin_url( '/assets/icons/dt-circle-logo.png' );

    return $stats;
}, 10, 1 );

add_filter( 'go_webform_options', function ( $params ) {
    $params['lists'] = $params['lists'] ?? [];
    $params['lists'][] = 'list_18'; // D.T News
    $params['lists'][] = 'list_22'; // Go Occasional
    $params['contact_fields'] = $params['contact_fields'] ?? [];
    $params['contact_fields']['projects'] = [ 'values' => [ [ 'value' => 'disciple_tools' ] ] ];
    if ( !empty( $params['source'] ) ){
        $params['contact_fields']['sources'] = [ 'values' => [ [ 'value' => $params['source'] ] ] ];
    } else {
        $params['contact_fields']['sources'] = [ 'values' => [ [ 'value' => 'disciple_tools' ] ] ];
    }

    if ( in_array( 'list_18', $params['lists'] ) ) {
        $params['contact_fields']['steps_taken'] = [ 'values' => [ [ 'value' => 'D.T Newsletter' ] ] ];
        $params['contact_fields']['notes'] = [ 'Signed up for D.T News' ];
    }

    return $params;
}, 10, 1 );