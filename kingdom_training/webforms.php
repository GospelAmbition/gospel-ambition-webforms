<?php

add_action( 'dt_webform_field', function ( $key ){
    if ( $key === 'kt-news' ){
        $values = [
            'add_to_mailing_list_24' => 'Sign up for Kingdom.Training news and opportunities, and occasional communication from GospelAmbition.org',
        ];

        go_display_tag_fields( 'tags', $values, true );
    }

});