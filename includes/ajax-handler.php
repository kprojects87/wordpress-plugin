<?php
// Handle AJAX requests for fetching user tags dynamically.
function utp_fetch_user_tags() {
    $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
    $terms = get_terms(array(
        'taxonomy' => 'user_tag',
        'name__like' => $search,
        'hide_empty' => false,
    ));
    $results = array();
    foreach ($terms as $term) {
        $results[] = array('id' => $term->slug, 'text' => $term->name);
    }
    wp_send_json($results);
}
add_action('wp_ajax_fetch_user_tags', 'utp_fetch_user_tags');
function enqueue_select2_scripts() {
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js', array('jquery'), null, true);
    wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css', array(), null);
    wp_enqueue_script('user-tag-search', plugin_dir_url(__FILE__) . 'user-tag-search.js', array('jquery', 'select2'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_select2_scripts');
