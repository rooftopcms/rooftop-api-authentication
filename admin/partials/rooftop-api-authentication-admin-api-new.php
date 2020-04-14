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
    <h1>Preview API key</h1>

    <form action="?page=api-keys" method="post">
        <table class="form-table">
            <tr>
                <th scope="row">
                    API Key name
                </th>
                <td>
                    <input type="text" name="key_name" placeholder="Content Preview API Key" value="<?php defined('$new_key_name') ? $new_key_name : '' ?>"/>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Full API Access
                </th>
                <td>
                    <input type="checkbox" name="key_full_access" />
                    <span class="hint">This will create a key for a user that will have read/write access via the API</span>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field( 'rooftop-api-authentication-api-add-key', 'api-field-token' ); ?>

        <p class="submit">
            <input type="submit" value="Add API Key" class="button button-primary" />
        </p>

    </form>
</div>
