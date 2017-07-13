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
    }

    /**
     * rest authentication method
     * @param $access
     * @return WP_Error
     */
    public function rest_authentication( $access )
    {
        if ( $_SERVER['SERVER_ADDR'] !== $_SERVER['REMOTE_ADDR'] ) {
            return new WP_Error( 'Unauthorized', 'This wordpress blog is not the origin of this request', array( 'status' => rest_authorization_required_code() ) );
        }

        return $access;
    }
}