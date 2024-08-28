<?php

add_action( 'dt_webform_field', function ( $key ){
    if ( $key === 'p4m-news' ){
        $values = [
            'add_to_mailing_list_23' => 'Sign up for Prayer.Tools news and opportunities, and occasional communication from GospelAmbition.org',
        ];

        go_display_tag_fields( 'tags', $values, true );
    }

});