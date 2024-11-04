<?php

add_action( 'dt_webform_field', function ( $key ){
    if ( $key === 'dt-news' ){
        $values = [
            'add_to_mailing_list_18' => 'Sign up for Disciple.Tools news and opportunities, and occasional communication from GospelAmbition.org',
        ];

        go_display_tag_fields( 'tags', $values, false );
    }
});

add_filter( 'dt_webform_fields_before_submit', function ( $fields ){

    if ( isset( $fields['tags']['values'][0]['value'] ) && $fields['tags']['values'][0]['value'] === 'add_to_mailing_list_18' ){
        //double optin
        $fields['tags'] = [];

        $params= [
            'email' => $fields['contact_email'][0]['value'],
            'lists' => [ 'list_18', 'list_22' ],
            'first_name' => $fields['title'] ?? '',
        ];
        send_dt_optin_email( $params );
    }

    return $fields;
} );

function send_dt_optin_email( $params ){
    //generate key
    $key = wp_generate_password( 50, false );
    //save params
    update_option( 'go_webform_double_optin_' . $key, $params, false );


    //send email with confirm link
    $subject = 'Confirm subscription to D.T News';
    $message = 'Hi ' . ( $params['first_name'] ?? '' ) . ',<br><br>';
    $message .= 'Please confirm your subscription to D.T News by clicking the link below:<br><br>';
    $message .= '<a href="' . home_url() . '/wp-json/go-webform/confirm?key=' . $key . '">Confirm Subscription</a>';
    $message .= '<br><br>Thank you for subscribing to D.T News.';
    $message .= '<br><br>If you did not request this subscription, please ignore this email.';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $params['email'], $subject, $message, $headers );
}