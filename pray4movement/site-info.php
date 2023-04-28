<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'go_site_info', function( $stats ) {
    $stats['site_name'] = get_bloginfo();
    $stats['icon'] = GO_Context_Switcher::plugin_url( '/assets/icons/p4m-circle-logo.png' );

    return $stats;
}, 10, 1 );


add_filter( 'go_webform_options', function ( $params ) {
    $params['lists'] = $params['lists'] ?? [];
    $params['contact_fields'] = $params['contact_fields'] ?? [];
    $params['contact_fields']['projects'] = [ 'values' => [ [ 'value' => 'pray4movement' ] ] ];
    $params['contact_fields']['sources'] = [ 'values' => [ [ 'value' => 'pray4movement' ] ] ];
    if ( in_array( 'list_23', $params['lists'] ) ) {
        $params['contact_fields']['steps_taken'] = [ 'values' => [ [ 'value' => 'P4M Newsletter' ] ] ];
        $params['contact_fields']['notes'] = [ 'Signed up for P4M News' ];
    }

    return $params;
}, 10, 1 );