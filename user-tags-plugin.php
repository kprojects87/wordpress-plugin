// Show User Tags on User Profile.
function utp_show_user_tags_in_profile($user) {
    $terms = wp_get_object_terms($user->ID, 'user_tag', array('fields' => 'names'));
    ?>
    <h3>User Tags</h3>
    <table class="form-table">
        <tr>
            <th><label for="user_tags">User Tags</label></th>
            <td>
                <input type="text" name="user_tags" id="user_tags" value="<?php echo esc_attr(join(', ', $terms)); ?>" class="regular-text" /><br>
                <span class="description">Add tags separated by commas.</span>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'utp_show_user_tags_in_profile');
add_action('edit_user_profile', 'utp_show_user_tags_in_profile');

// Save User Tags when updating user profile.
function utp_save_user_tags($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    $tags = explode(',', sanitize_text_field($_POST['user_tags']));
    wp_set_object_terms($user_id, $tags, 'user_tag', false);
}
add_action('personal_options_update', 'utp_save_user_tags');
add_action('edit_user_profile_update', 'utp_save_user_tags');
// Add a "User Tags" menu under Users.
function utp_add_user_tags_admin_menu() {
    add_users_page(
        'Manage User Tags',    // Page title.
        'User Tags',           // Menu title.
        'manage_options',      // Capability required.
        'edit-tags.php?taxonomy=user_tag' // URL to manage tags.
    );
}
add_action('admin_menu', 'utp_add_user_tags_admin_menu');
function utp_add_user_tags_admin_menu() {
    add_users_page(
        'Manage User Tags',    // Page title.
        'User Tags',           // Menu title.
        'manage_options',      // Capability required.
        'edit-tags.php?taxonomy=user_tag' // URL to manage tags.
    );
}
add_action('admin_menu', 'utp_add_user_tags_admin_menu');
