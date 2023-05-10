<?php
/**
 * Plugin Name: Gospel Ambition - Webforms
 * Plugin URI: https://github.com/GospelAmbition/gospel-ambition-webforms
 * Description: Gospel Ambition Webforms
 * Text Domain: gospel-ambition-webforms
 * Version:  2023.05.08.4
 * Author URI: https://github.com/GospelAmbition/gospel-ambition-webforms
 * GitHub Plugin URI: https://github.com/GospelAmbition/gospel-ambition-webforms
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 6.3
 *
 * @package Disciple_Tools
 * @link    https://github.com/DiscipleTools
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

class GO_Webform_Context_Switcher {
    private static $instance = null;
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public static function plugin_url( $path = '' ) {
        return plugins_url( $path, __FILE__ );
    }
    public function __construct(){
        $site = get_bloginfo();

        require_once( 'globals/loader.php' );
        require_once( 'globals/site-link-post-type.php' );
        Site_Link_System::instance( 100, 'dashicons-admin-links' );

//        require_once( 'assets/enqueue.php' );

        switch ( $site ) {

            case 'Prayer Global':
                require_once( 'prayer_global/loader.php' );
                break;

            case 'Vision':
            case 'Zúme Training':
                require_once( 'zume_vision/loader.php' );
                break;

            case 'Pray4Movement':
                require_once( 'pray4movement/loader.php' );
                break;

            case 'Kingdom Training':
                require_once( 'kingdom_training/loader.php' );
                break;

            case 'Disciple.Tools':
                require_once( 'disciple_tools/loader.php' );
                break;

            case 'Gospel Ambition':
                require_once( 'gospel_ambition/loader.php' );
                break;

            default:
                return false;
        }
        return false;
    }
}

add_action( 'after_setup_theme', [ 'GO_Webform_Context_Switcher', 'instance' ], 10 );
