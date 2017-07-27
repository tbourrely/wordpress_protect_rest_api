<?php
/**
 * Plugin Name: Protect Rest Api
 * Plugin URI: https://tbourrely.github.io/
 * Description: Restrict requests to rest API to the same IP
 * Version: 0.1
 * Author: Thomas Bourrely
 * Author URI: https://tbourrely.github.io/
 * License: GPL2+
 */

require_once __DIR__ . 'views/Admin.php';

$blog_version = get_bloginfo('version');


if ( version_compare( $blog_version, "4.7", ">=" ) )
{
    new Rest_Protect();
}


/**
 * Class Rest_Protect
 * @todo whitelist
 */
class Rest_Protect {

    /**
     * Rest_Protect constructor.
     */
    public function __construct()
    {
        add_action( 'init', array( $this, 'add_hooks' ) );
    }

    /**
     * add hooks
     */
    public function add_hooks()
    {
        add_filter( 'rest_authentication_errors', array( $this, 'rest_authentication' ) );

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    /**
     * rest authentication method
     * @param $access
     * @return WP_Error
     */
    public function rest_authentication( $access )
    {
        $whitelist = get_option( 'protect_rest_api_whitelist' );

        if ( !empty( $whitelist ) ) {
            $whitelist = array_map( 'trim', explode( ',', $whitelist ) );
        }

        $server_ip = gethostbyname($_SERVER['SERVER_NAME']);

        $request_ip = gethostbyname($_SERVER['REMOTE_ADDR']);

        if (
            $_SERVER['SERVER_ADDR'] === $_SERVER['REMOTE_ADDR'] ||
            $server_ip === $request_ip ||
            in_array( $server_ip, $whitelist ) ||
            in_array( $_SERVER['SERVER_ADDR'], $whitelist )
        ) {
            return $access;
        }

        return new WP_Error( 'Unauthorized', 'This wordpress blog is not the origin of this request', array( 'status' => rest_authorization_required_code() ) );
    }

    /**
     * add menu page
     */
    public function admin_menu()
    {
        add_menu_page( 'Protect Rest Api', 'Protect Rest Api', 'manage_options', 'protect_rest_api_admin', array( Admin::class, 'render' ) );
    }

}