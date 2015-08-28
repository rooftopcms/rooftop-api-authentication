<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Rooftop_Api_Authentication
 * @subpackage Rooftop_Api_Authentication/admin/partials
 */
?>


<div class="wrap">
    <h1>API key <strong><?php echo $api_key->key_name ?></strong></h1>

    <form method="post">

        <?php wp_nonce_field( 'rooftop-api-authentication-api-view-key', 'api-field-token' ); ?>

        <p>
            <strong><?php echo $api_key->key_name;?></strong>: <?php echo $api_key->api_key;?>.
        </p>

        <p class="submit">
            <?php submit_button( 'Delete', 'delete button-primary' );?>
        </p>

    </form>
</div>
