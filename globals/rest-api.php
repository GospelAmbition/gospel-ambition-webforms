<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class GO_Webforms_Endpoints
{
    public $namespace = 'go-webform';
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct() {
        if ( $this->dt_is_rest() ) {
            add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
//            add_filter( 'dt_allow_rest_access', [ $this, 'authorize_url' ], 10, 1 );
        }
    }
    public function add_api_routes() {
        $namespace = $this->namespace;

        register_rest_route(
            $namespace, '/optin', [
                'methods'  => [ 'POST' ],
                'callback' => [ $this, 'optin' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/double-optin', [
                'methods'  => [ 'POST' ],
                'callback' => [ $this, 'double_optin' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/confirm', [
                'methods'  => [ 'GET' ],
                'callback' => [ $this, 'confirm_optin' ],
                'permission_callback' => '__return_true'
            ]
        );
    }
    public function optin( WP_REST_Request $request ) {
        $params = $request->get_params();
        //can't use nonce as used by campaigns
//        $headers = $request->get_headers();
//        $nonce = $headers['x_wp_nonce'][0] ?? '';
//        if ( !wp_verify_nonce( $nonce, 'wp_rest' ) ){
//            return false;
//        }

        if ( home_url() === 'https://disciple.tools' ){
            $headers = $request->get_headers();
            $nonce = $headers['x_wp_nonce'][0] ?? '';
            if ( !wp_verify_nonce( $nonce, 'wp_rest' ) ){
                return false;
            }
        }

        //set lists from this filter
        $params = apply_filters( 'go_webform_options', $params );

        $keys = Site_Link_System::get_site_keys();
        $crm_link = '';
        foreach ( $keys ?? [] as $key ) {
            if ( $key['dev_key'] === 'crm_link' ) {
                $crm_link = $key;
            }
        }
        if ( empty( $crm_link ) || empty( $params['lists'] ) ){
            return new WP_Error( 'no_crm_link', 'Bad form configuration', [ 'status' => 400 ] );
        }

        $var = Site_Link_System::get_site_connection_vars( $crm_link['post_id'] );

        $subscribe = wp_remote_post(  'https://' . $var['url'] . '/wp-json/crm-email/create', [
            'body' => [
                'email' => $params['email'],
                'lists' => $params['lists'],
                'name' => $params['name'] ?? '',
                'first_name' => $params['first_name'] ?? '',
                'last_name' => $params['last_name'] ?? '',
                'contact_fields' => $params['contact_fields'] ?? [],
                'source' => $params['source'] ?? '',
                'tags' => $params['tags'] ?? [],
                'named_tags' => $params['named_tags'] ?? [],
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $var['transfer_token'],
            ],
        ] );

        if ( is_wp_error( $subscribe ) ) {
            return new WP_Error( 'subscribe_error', 'Something went wrong, please try again.', [ 'status' => 400 ] );
        }
        $response = json_decode( wp_remote_retrieve_body( $subscribe ) );
        if ( !empty( $response->error ) ) {
            return new WP_Error( 'subscribe_error', 'Something went wrong, please try again.', [ 'status' => 400 ] );
        }

        return new WP_REST_Response( 'success', 200 );
    }


    public function double_optin( WP_REST_Request $request ) {
        $params = $request->get_params();
        //can't use nonce as used by campaigns
        $headers = $request->get_headers();
        $nonce = $headers['x_wp_nonce'][0] ?? '';
        if ( !wp_verify_nonce( $nonce, 'wp_rest' ) ){
            return false;
        }

        //set lists from this filter
        $params = apply_filters( 'go_webform_options', $params );
        $params['time'] = time();

        //generate key
        $key = wp_generate_password( 50, false );
        //save params
        update_option( 'go_webform_double_optin_' . $key, $params, false );


        //send email with confirm link
        $subject = 'Confirm subscription to D.T News';
        $message = 'Hi ' . ( $params['first_name'] ?? '' ) . ',<br><br>';
        $message .= 'Please confirm your subscription to D.T News by clicking the link below:<br><br>';
        $message .= '<a href="' . home_url() . '/wp-json/go-webform/confirm?key=' . $key . '">Confirm Subscription</a>';
        $message .= '<br><br>Thank you for subscribing to D.T News';
        $message .= '<br><br>If you did not request this subscription, please ignore this email.';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $params['email'], $subject, $message, $headers );

        return new WP_REST_Response( 'success', 200 );
    }

    public function confirm_optin( WP_REST_Request $request ){
        $params = $request->get_params();
        $optin_key = $params['key'] ?? '';

        $params = get_option( 'go_webform_double_optin_' . $optin_key, false );
        if ( !$params ){
            header('Content-Type: text/html');
            ?>
                <div style="width: 500px; margin-left: auto; margin-right: auto; padding: 2em; margin-top: 200px; background-color: #eee; border-radius: 5px">
                    <h1>Invalid Key</h1>
                    <p>Sorry something went wrong. This confirm link has already been used, or it is invalid. Please try subscribing again.</p>
                </div>
            <?php
            exit();
        }

        $keys = Site_Link_System::get_site_keys();
        $crm_link = '';
        foreach ( $keys ?? [] as $key ) {
            if ( $key['dev_key'] === 'crm_link' ) {
                $crm_link = $key;
            }
        }
        if ( empty( $crm_link ) || empty( $params['lists'] ) ){
            return new WP_Error( 'no_crm_link', 'Bad form configuration', [ 'status' => 400 ] );
        }

        $var = Site_Link_System::get_site_connection_vars( $crm_link['post_id'] );

        $subscribe = wp_remote_post(  'https://' . $var['url'] . '/wp-json/crm-email/create', [
            'body' => [
                'email' => $params['email'],
                'lists' => $params['lists'],
                'name' => $params['name'] ?? '',
                'first_name' => $params['first_name'] ?? '',
                'last_name' => $params['last_name'] ?? '',
                'contact_fields' => $params['contact_fields'] ?? [],
                'source' => $params['source'] ?? '',
                'tags' => $params['tags'] ?? [],
                'named_tags' => $params['named_tags'] ?? [],
            ],
            'headers' => [
                'Authorization' => 'Bearer ' . $var['transfer_token'],
            ],
        ] );

        if ( is_wp_error( $subscribe ) ) {
            header('Content-Type: text/html');
            ?>
            <div style="width: 500px; margin-left: auto; margin-right: auto; padding: 2em; margin-top: 200px; background-color: #eee; border-radius: 5px">
                <h1>Sorry, something went wrong</h1>
                <p>Please try subscribing again.</p>
            </div>
            <?php
            exit();
        }
        $response = json_decode( wp_remote_retrieve_body( $subscribe ) );
        if ( !empty( $response->error ) ) {
            header('Content-Type: text/html');
            ?>
            <div style="width: 500px; margin-left: auto; margin-right: auto; padding: 2em; margin-top: 200px; background-color: #eee; border-radius: 5px">
                <h1>Sorry, something went wrong</h1>
                <p>Please try subscribing again.</p>
            </div>
            <?php
            exit();
        }

        delete_option( 'go_webform_double_optin_' . $optin_key );

        header('Content-Type: text/html');
        ?>
        <div style="width: 500px; margin-left: auto; margin-right: auto; padding: 2em; margin-top: 200px; background-color: #eee; border-radius: 5px">
            <h1>Success!</h1>
            <p>You are now subscribed</p>
        </div>
        <?php
        exit();
    }


    public function dt_is_rest( $namespace = null ) {
        // https://github.com/DiscipleTools/disciple-tools-theme/blob/a6024383e954cec2ac4e7a1a31fb4601c940f485/dt-core/global-functions.php#L60
        // Added here so that in non-dt sites there is no dependency.
        $prefix = rest_get_url_prefix();
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST
            || isset( $_GET['rest_route'] )
            && strpos( trim( sanitize_text_field( wp_unslash( $_GET['rest_route'] ) ), '\\/' ), $prefix, 0 ) === 0 ) {
            return true;
        }
        $rest_url    = wp_parse_url( site_url( $prefix ) );
        $current_url = wp_parse_url( add_query_arg( array() ) );
        $is_rest = strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
        if ( $namespace ){
            return $is_rest && strpos( $current_url['path'], $namespace ) != false;
        } else {
            return $is_rest;
        }
    }
}
GO_Webforms_Endpoints::instance();
