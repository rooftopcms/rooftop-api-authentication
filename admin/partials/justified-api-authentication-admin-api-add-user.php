<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<form action="/wp-admin/options-general.php?page=justified-api-authentication-api-add-user" method="post">
    <input type="text" name="name"/>


    <?php wp_nonce_field( 'justified-api-authentication-api-add-user', 'api-field-token' ); ?>

    <input type="submit" value="Add" />
</form>