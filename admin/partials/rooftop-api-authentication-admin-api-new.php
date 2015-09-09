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
    <h1>Add new API key</h1>

    <form action="?page=rooftop-api-authentication-overview" method="post">
        <table class="form-table">
            <tr>
                <th scope="row">
                    API Key name
                </th>
                <td>
                    <input type="text" name="key_name" placeholder="iOS API Key" value="<?php defined('$new_key_name') ? $new_key_name : '' ?>"/>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    Read-only
                </th>
                <td>
                    <input type="checkbox" name="key_read_only" />
                    <p class="description">
                        Something about the difference between a read-only key and a regular one
                    </p>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field( 'rooftop-api-authentication-api-add-key', 'api-field-token' ); ?>

        <p class="submit">
            <input type="submit" value="Add API Key" class="button button-primary" />
        </p>

    </form>
</div>
