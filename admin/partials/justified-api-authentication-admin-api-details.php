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

    <p>
        Some info about the API usage goes here <?php echo __FILE__ ?>.
    </p>

    <h3>API Keys</h3>

    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <tr>
                <th>User ID</th>
                <th>User    Email</th>
                <th>API Key</th>
            </tr>
        </thead>
        <?php foreach($api_users as $api_user): ?>
            <tr>
                <td><?php echo $api_user['id'];?></td>
                <td><a href="/wp-admin/user-edit.php?user_id=<?php echo $api_user['id']; ?>"><?php echo $api_user['email'];?></a></td>
                <td><?php echo $api_user['api_key'];?></td>
            </tr>
        <?php endforeach;?>
    </table>
</div>