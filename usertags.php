<?php
/*
Plugin Name: User Tags
Description: A plugin to add custom taxonomies for users.
Version: 1.0
Author: khizer
*/
function register_user_tags_taxonomy() {
    $args = array(
        'hierarchical' => true,
        'label' => 'User Tags',
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'user-tags'),
    );

    register_taxonomy('user_tags', 'user', $args);
}
add_action('init', 'register_user_tags_taxonomy');
function add_user_tags_menu() {
    add_users_page(
        'User Tags',
        'User Tags',
        'manage_options',
        'edit-tags.php?taxonomy=user_tags&post_type=user',
        ''
    );
}
add_action('admin_menu', 'add_user_tags_menu');
function enqueue_user_tag_script() {
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js', array('jquery'), null, true);
    wp_enqueue_script('user-tags-ajax', plugin_dir_url(__FILE__) . 'js/user-tags-ajax.js', array('jquery'), null, true);
    wp_localize_script('user-tags-ajax', 'userTags', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'enqueue_user_tag_script');
function get_user_tags() {
    $tags = get_terms(array(
        'taxonomy' => 'user_tags',
        'name__like' => $_GET['search'],
    ));

    $results = [];
    foreach ($tags as $tag) {
        $results[] = array('id' => $tag->term_id, 'text' => $tag->name);
    }

    echo json_encode($results);
    wp_die(); // Required to terminate AJAX request properly
}
add_action('wp_ajax_get_user_tags', 'get_user_tags');
function filter_users_by_user_tag($query) {
    if (!is_admin() || !$query->is_main_query()) return;

    if (isset($_GET['user_tag_filter']) && !empty($_GET['user_tag_filter'])) {
        $query->set('tax_query', array(
            array(
                'taxonomy' => 'user_tags',
                'field' => 'id',
                'terms' => $_GET['user_tag_filter'],
            )
        ));
    }
}
add_action('pre_get_users', 'filter_users_by_user_tag');
function user_tag_filter_dropdown() {
    $tags = get_terms('user_tags');
    echo '<select name="user_tag_filter">';
    echo '<option value="">Filter by User Tag</option>';
    foreach ($tags as $tag) {
        echo '<option value="' . $tag->term_id . '">' . $tag->name . '</option>';
    }
    echo '</select>';
}
add_action('restrict_manage_users', 'user_tag_filter_dropdown');
