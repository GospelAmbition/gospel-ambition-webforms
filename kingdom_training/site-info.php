<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'go_site_info', function( $stats ) {
    $stats['site_name'] = get_bloginfo();
    $stats['icon'] = GO_Context_Switcher::plugin_url( '/assets/icons/kt-circle-logo.png' );

    return $stats;
}, 10, 1 );


add_filter( 'go_webform_options', function ( $params ) {
    $params['lists'] = $params['lists'] ?? [];
    $params['lists'][] = 'list_24'; // K.T News
    $params['lists'][] = 'list_22'; // Go Occasional
    $params['contact_fields'] = $params['contact_fields'] ?? [];
    $params['contact_fields']['projects'] = [ 'values' => [ [ 'value' => 'kingdom_training' ] ] ];
    if ( !empty( $params['source'] ) ){
        $params['contact_fields']['sources'] = [ 'values' => [ [ 'value' => $params['source'] ] ] ];
    } else {
        $params['contact_fields']['sources'] = [ 'values' => [ [ 'value' => 'kingdom.training' ] ] ];
    }

    if ( in_array( 'list_24', $params['lists'] ) ) {
        $params['contact_fields']['steps_taken'] = [ 'values' => [ [ 'value' => 'K.T Newsletter' ] ] ];
        $params['contact_fields']['notes'] = [ 'Signed up for K.T News' ];
    }

    return $params;
}, 10, 1 );