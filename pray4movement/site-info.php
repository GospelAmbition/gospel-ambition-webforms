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

    if ( !empty( $params['source'] ) ){
        $params['contact_fields']['sources'] = [ 'values' => [ [ 'value' => $params['source'] ] ] ];
    } else {
        $params['contact_fields']['sources'] = [ 'values' => [ [ 'value' => 'pray4movement' ] ] ];
    }
    if ( in_array( 'list_23', $params['lists'] ) ) {
        $params['contact_fields']['steps_taken'] = [ 'values' => [ [ 'value' => 'P4M Newsletter' ] ] ];
        $note = 'Signed up for P4M News';
        if ( isset( $params['named_tags']['values'][0]['type'] ) && 'P4M Newsletter' === $params['named_tags']['values'][0]['type'] ) {
            $note .= ' on ' . $params['named_tags']['values'][0]['value'];
        }
        $params['contact_fields']['notes'] = [ $note ];
    }

    return $params;
}, 10, 1 );