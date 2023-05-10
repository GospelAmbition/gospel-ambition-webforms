<?php

add_action( 'dt_webform_field', function ( $key ){
    if ( $key === 'dt-news' ){
        $values = [
            'add_to_mailing_list_18' => 'Sign up for Disciple.Tools news and opportunities, and occasional communication from GospelAmbition.org',
        ];

        go_display_tag_fields( 'tags', $values );
    }

});

add_filter( 'dt_webform_fields_before_submit', function ( $fields ){
    if ( isset( $fields['tags']['values'][0]['value'] ) && $fields['tags']['values'][0]['value'] === 'add_to_mailing_list_18' ){
        $fields['tags']['values'][] = [ 'value' => 'add_to_mailing_list_22' ];
    }
} );