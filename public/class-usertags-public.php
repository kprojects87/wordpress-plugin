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

// Save User Tags.
function utp_save_user_tags($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    $tags = explode(',', sanitize_text_field($_POST['user_tags']));
    wp_set_object_terms($user_id, $tags, 'user_tag', false);
}
add_action('personal_options_update', 'utp_save_user_tags');
add_action('edit_user_profile_update', 'utp_save_user_tags');
function utp_add_user_tags_admin_menu() {
    add_users_page(
        'Manage User Tags',
        'User Tags',
        'manage_options',
        'edit-tags.php?taxonomy=user_tag'
    );
}
add_action('admin_menu', 'utp_add_user_tags_admin_menu');
function utp_add_user_tag_filter($which) {
    $taxonomy = 'user_tag';
    $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
    if (!empty($terms)) {
        ?>
        <select name="user_tag" id="user_tag" class="postform">
            <option value=""><?php _e('Filter by User Tags'); ?></option>
            <?php foreach ($terms as $term) : ?>
                <option value="<?php echo $term->slug; ?>"<?php echo (isset($_GET['user_tag']) && $_GET['user_tag'] === $term->slug) ? ' selected="selected"' : ''; ?>><?php echo $term->name; ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}
add_action('restrict_manage_users', 'utp_add_user_tag_filter');
function utp_filter_users_by_tag($query) {
    global $pagenow;
    if ($pagenow === 'users.php' && isset($_GET['user_tag']) && !empty($_GET['user_tag'])) {
        $term_id = get_term_by('slug', $_GET['user_tag'], 'user_tag')->term_id;
        $user_ids = get_objects_in_term($term_id, 'user_tag');
        $query->query_vars['include'] = $user_ids;
    }
}
add_filter('pre_get_users', 'utp_filter_users_by_tag');
function enqueue_select2_scripts() {
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js', array('jquery'), null, true);
    wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css', array(), null);
    wp_enqueue_script('user-tag-search', plugin_dir_url(__FILE__) . 'user-tag-search.js', array('jquery', 'select2'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_select2_scripts');
