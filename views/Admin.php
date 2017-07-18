<?php
/**
 * File "admin.php"
 * @author Thomas Bourrely
 * 18/07/2017
 */


class Admin
{

    public static function render()
    {

        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        if ($_POST['whitelist']) {
            $whitelist = sanitize_text_field( $_POST['whitelist'] );

            $elements = explode( ',', $whitelist );

            $errors = [];

            foreach ( $elements as $element ) {
                $element = trim( $element );
                if ( !filter_var( $element, FILTER_VALIDATE_IP ) ) {
                    $errors[$element] = 'Is not a valid address';
                }
            }

            if ( empty( $errors ) ) {
                update_option( 'protect_rest_api_whitelist', $whitelist );
            }
        }

        ?>

        <div class="wrap">

            <h1 class="h1">Protect Rest API settings page</h1>

            <?php if ( !empty( $errors ) ) :  ?>
                <div class="notice notice-error is-dismissible">
                    <h2>These errors occured : </h2>
                    <?php foreach ( $errors as $key => $error ) : ?>
                        <h4><?php echo $key . ' ' . $error ?></h4>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post">
                <label for="whitelist" size="200">IP addresses to whitelist (comma separated) </label><br>
                <input type="text" name="whitelist" size="102" value="<?php echo (get_option( 'protect_rest_api_whitelist' )) ? get_option( 'protect_rest_api_whitelist' ) : ''; ?>">
                <br>
                <br>
                <input type="submit" class="button button-primary">
            </form>

        </div>

        <?php

    }
}