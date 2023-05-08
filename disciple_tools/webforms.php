<?php

add_action( 'dt_webform_field', function ( $key ){
    if ( $key === 'dt-news' ){
        $values = [
            'add_to_mailing_list_18' => 'Sign up for Disciple.Tools news, opportunities, and occasional communication from GospelAmbition.org',
        ];

        go_display_tag_fields( 'tags', $values );
    }

});