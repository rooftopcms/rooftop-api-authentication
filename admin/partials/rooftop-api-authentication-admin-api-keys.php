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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <h2>
        API Keys
        <a href="/wp-admin/options-general.php?page=rooftop-api-authentication-api-add-key" class="page-title-action">Add New</a>
    </h2>

    <?php if(count($api_keys)):?>
        <table class="wp-list-table widefat fixed striped pages">
            <thead>
            <tr>
                <th>Key Name</th>
                <th>API Key</th>
                <th>Roles</th>
            </tr>
            </thead>
            <?php foreach($api_keys as $api_key): ?>
                <tr>
                    <?php
                    $r = get_role('api-read-write');
                    $f = 1;
                    ?>
                    <td><a href="/wp-admin/options-general.php?page=rooftop-api-authentication-api-view-key&id=<?php echo $api_key['id'];?>"><?php echo $api_key['key_name'];?></a></td>
                    <td><?php echo $api_key['api_key'];?></td>
                    <td>
                        <?php
                        global $wp_roles;

                        $roles_and_caps = array_map(function($r) use($wp_roles) {
                            $rc = array();
                            $rc['role'] = $wp_roles->roles[$r]['name'];
                            $role_capabilities = array_filter($wp_roles->roles[$r]['capabilities']);
                            $rc['caps'] = implode(", ", array_keys($role_capabilities));
                            return $rc;
                        }, $api_key['user']->roles);

                        foreach($roles_and_caps as $rc) {
                            echo "<strong>".$rc['role']."</strong><br/> ".$rc['caps'] . "<br/><br/>";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php else: ?>
        <p>
            You haven't added any API keys yet. <a href="/wp-admin/options-general.php?page=rooftop-api-authentication-api-add-key">Add a new API Key</a>.
        </p>
    <?php endif;?>
</div>