<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://errorstudio.co.uk
 * @since      1.0.0
 *
 * @package    Justified_Api_Authentication
 * @subpackage Justified_Api_Authentication/public/partials
 */
?>

<?php
$checked = $has_api_key ? 'checked' : null;
?>

<table class="form-table">
    <tr class="api-user">
        <th>API User</th>
        <td>
            <input type="checkbox" name="api_user" <?php echo $checked; ?> />
        </td>
    </tr>

    <?php if($has_api_key && count($keys)):?>
        <?php foreach($keys as $key):?>
            <tr class="api-key">
                <th>
                    Key
                </th>
                <td>
                    <?php echo $key->api_key;?>
                </td>
            </tr>
        <?php endforeach;?>
    <?php endif;?>
</table>
