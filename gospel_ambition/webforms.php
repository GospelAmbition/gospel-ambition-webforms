<?php

add_action( 'dt_webform_field', function ( $key ){
    if ( $key === 'partner-news' ){
        $values = [
            'News and testimonies' => 'News and testimonies',
            'Prayer opportunities and resources' => 'Prayer opportunities and resources',
            'Being a disciple and making disciples' => 'Being a disciple and making disciples',
            'Using media to accelerate disciple making' => 'Using media to accelerate disciple making',
        ];

        go_display_tag_fields( 'tags', $values );
    }
    if ( $key === 'partner-skills' ){
        $values = [
            'Technology and coding' => 'Technology and coding',
            'Administration' => 'Administration',
            'Translation' => 'Translation',
            'Marketing' => 'Marketing',
            'User testing' => 'User testing',
            'Fund raising' => 'Fund raising',
        ];
        go_display_tag_fields( 'skills', $values );
    }

    //kk
    if ( $key === "kk-partner-news" ){
        $values = [
            'K-family news and testimonies' => 'K-family news and testimonies',
            'Gospel Ambition news and testimonies' => 'Gospel Ambition news and testimonies',
            'Prayer opportunities and resources' => 'Prayer opportunities and resources',
            'Being a disciple and making disciples' => 'Being a disciple and making disciples',
            'Using media to accelerate disciple making' => 'Using media to accelerate disciple making',
        ];
        go_display_tag_fields( 'tags', $values );
    }
    if ( $key === 'kk-partner-skills' ){
        $values = [
            'Technology and coding' => 'Technology and coding',
            'Administration' => 'Administration',
            'Translation' => 'Translation',
            'Marketing' => 'Marketing',
            'User testing' => 'User testing',
            'Fund raising' => 'Fund raising',
            'Hospitality (KK)' => 'Hospitality (when there is an event near me)',
            'Home improvement (KK)' => 'Home improvement (if the K’s need some help on a project)',
        ];
        go_display_tag_fields( 'skills', $values );
    }
});