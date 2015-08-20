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

<div class="wrap">
    <h2>API Overview</h2>

    <?php if(count($api_keys)):?>
        <h3>API Keys</h3>
        <p>
            <a href="/wp-admin/options-general.php?page=justified-api-authentication-api-add-key">Add a new API Key</a>.
        </p>

        <table class="wp-list-table widefat fixed striped pages">
            <thead>
            <tr>
                <th>Key Name</th>
                <th>API Key</th>
            </tr>
            </thead>
            <?php foreach($api_keys as $api_key): ?>
                <tr>
                    <td><a href="/wp-admin/options-general.php?page=justified-api-authentication-api-view-key&id=<?php echo $api_key['id'];?>"><?php echo $api_key['key_name'];?></a></td>
                    <td><?php echo $api_key['api_key'];?></td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php else: ?>
        <p>
            You haven't added any API keys yet. <a href="/wp-admin/options-general.php?page=justified-api-authentication-api-add-key">Add a new API Key</a>.
        </p>
    <?php endif;?>
</div>