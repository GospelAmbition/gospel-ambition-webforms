<?php

add_action( 'dt_webform_field', function ( $key ){
    if ( $key === 'go-news' ){
        $values = [
            'add_to_mailing_list_22' => 'Sign me up for news and testimonies from GospelAmbition.org',
        ];

        go_display_tag_fields( 'tags', $values, true );
    }

    if ( $key === 'partner-news' ){
        $values = [
            'Gospel Ambition news and testimonies' => 'News and testimonies',
            'Prayer opportunities and resources' => 'Prayer opportunities and resources',
            'Being a disciple and making disciples' => 'Being a disciple and making disciples',
            'Using media to accelerate disciple making' => 'Using media to accelerate disciple making',
        ];

        go_display_tag_fields( 'skills', $values, true );
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
    if ( $key === 'kk-partner-news' ){
        $values = [
            'K-family news and testimonies' => 'K-family news and testimonies',
            'Gospel Ambition news and testimonies' => 'Gospel Ambition news and testimonies',
            'Prayer opportunities and resources' => 'Prayer opportunities and resources',
            'Being a disciple and making disciples' => 'Being a disciple and making disciples',
            'Using media to accelerate disciple making' => 'Using media to accelerate disciple making',
        ];
        go_display_tag_fields( 'skills', $values );
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

add_filter( 'dt_webform_fields_before_submit', function ( $fields ){
    $tags_to_add = [];
    if ( isset( $fields['skills']['values'] ) ){
        foreach ( $fields['skills']['values'] as $tag ){
            if ( isset( $tag['value'] ) && $tag['value'] === 'Gospel Ambition news and testimonies' ){
                $tags_to_add[] = [ 'value' => 'add_to_mailing_list_22' ]; //Go
            }
            if ( isset( $tag['value'] ) && $tag['value'] === 'Prayer opportunities and resources' ){
                $tags_to_add[] = [ 'value' => 'add_to_mailing_list_23' ]; //P4M
            }
            if ( isset( $tag['value'] ) && $tag['value'] === 'Using media to accelerate disciple making' ){
                $tags_to_add[] = [ 'value' => 'add_to_mailing_list_24' ]; //KT
            }
            //if ( isset( $tag['value'] ) && $tag['value'] === 'Being a disciple and making disciples' ){
            //    $tags_to_add[] = [ 'value' => 'add_to_mailing_list_24' ];
            //}
        }
    }
    if ( !empty( $tags_to_add ) ){
        if ( empty( $fields['tags'] ) ){
            $fields['tags'] = [ 'values' => [] ];
        }
        $fields['tags']['values'] = array_merge( $fields['tags']['values'], $tags_to_add );
    }

    return $fields;
} );

function send_dt_optin_email( $params ){
    //generate key
    $key = wp_generate_password( 50, false );
    //save params
    update_option( 'go_webform_double_optin_' . $key, $params, false );


    //send email with confirm link
    $subject = 'Confirm subscription to Gospel Ambition News';
    $message = 'Hi ' . ( $params['first_name'] ?? '' ) . ',<br><br>';
    $message .= 'Please confirm your subscription to Gospel Ambition News by clicking the link below:<br><br>';
    $message .= '<a href="' . home_url() . '/wp-json/go-webform/confirm?key=' . $key . '">Confirm Subscription</a>';
    $message .= '<br><br>Thank you for subscribing to Gospel Ambition News.';
    $message .= '<br><br>If you did not request this subscription, please ignore this email.';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $params['email'], $subject, $message, $headers );
}

add_action( 'send_double_optin_email', function ( $params ){
    send_dt_optin_email( $params );
}, 10, 1 );