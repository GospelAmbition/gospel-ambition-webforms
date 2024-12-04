<?php

add_action( 'dt_webform_field', function ( $key ){
    if ( $key === 'kt-news' ){
        $values = [
            'add_to_mailing_list_24' => 'Sign up for Kingdom.Training news and opportunities, and occasional communication from GospelAmbition.org',
        ];

        go_display_tag_fields( 'tags', $values, true );
    }

});

function send_kt_optin_email( $params ){
    //generate key
    $key = wp_generate_password( 50, false );
    //save params
    update_option( 'go_webform_double_optin_' . $key, $params, false );


    //send email with confirm link
    $subject = 'Confirm subscription to Kingdom.Training News';
    $message = 'Hi ' . ( $params['first_name'] ?? '' ) . ',<br><br>';
    $message .= 'Please confirm your subscription to Kingdom.Training News by clicking the link below:<br><br>';
    $message .= '<a href="' . home_url() . '/wp-json/go-webform/confirm?key=' . $key . '">Confirm Subscription</a>';
    $message .= '<br><br>Thank you for subscribing to Kingdom.Training News.';
    $message .= '<br><br>If you did not request this subscription, please ignore this email.';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $params['email'], $subject, $message, $headers );
}
add_action( 'send_double_optin_email', function ( $params ){
    send_kt_optin_email( $params );
}, 10, 1 );
