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
    }
    public function optin( WP_REST_Request $request ) {
        $params = $request->get_params();

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
                'first_name' => $params['first_name'] ?? '',
                'last_name' => $params['last_name'] ?? '',
                'contact_fields' => $params['contact_fields'] ?? [],
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
