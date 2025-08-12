<?php

/**
 * Plugin Name: RT Celebs REST API
 * Description: Adds a REST route to fetch rt-celebs CPT posts with pagination. Includes optional CORS / origin restriction examples.
 * Version: 0.1
 * Author: Vinay
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Optional: register the CPT if you don't already have it.
 * If your theme/plugin already registers 'rt-celebs', remove or comment out this function.
 */
add_action('init', function () {
    if (post_type_exists('rt-celebs')) {
        return;
    }

    register_post_type('rt-celebs', [
        'labels' => [
            'name' => 'RT Celebs',
            'singular_name' => 'RT Celeb',
        ],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'rewrite' => ['slug' => 'rt-celebs'],
    ]);
});

/**
 * Register REST route
 */
add_action('rest_api_init', function () {
    register_rest_route('rt/v1', '/celebs', [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => 'rt_get_celebs',
        'args'     => [
            'page' => [
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ],
            'per_page' => [
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ],
        ],
        // For public read: allow all. For protected endpoints, change this.
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Callback: return paginated rt-celebs posts
 *
 * Returns JSON with: items[], total, total_pages, page, per_page
 * Also sets X-WP-Total and X-WP-TotalPages headers (like core WP endpoints).
 */
function rt_get_celebs(WP_REST_Request $request)
{
    $page = max(1, (int) $request->get_param('page'));
    $per_page = max(1, min(100, (int) $request->get_param('per_page'))); // limit to 100

    $q = new WP_Query([
        'post_type'      => 'rt-celebs',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
    ]);

    $items = [];
    foreach ($q->posts as $post) {
        $items[] = [
            'id'      => $post->ID,
            'title'   => get_the_title($post),
            'excerpt' => get_the_excerpt($post),
            'date'    => get_the_date('c', $post),
            'link'    => get_permalink($post),
        ];
    }

    $data = [
        'items'       => $items,
        'total'       => (int) $q->found_posts,
        'total_pages' => (int) $q->max_num_pages,
        'page'        => (int) $page,
        'per_page'    => (int) $per_page,
    ];

    $response = rest_ensure_response($data);
    $response->header('X-WP-Total', (int) $q->found_posts);
    $response->header('X-WP-TotalPages', (int) $q->max_num_pages);

    return $response;
}

/**
 * CORS: Add headers for allowed origins.
 * Replace https://your-allowed-origin.example with the origin you want to allow.
 * If you prefer to allow all origins for local dev: use '*' (not recommended for production).
 */
$site_origin = get_site_url(null, '', 'http'); // or 'https' if SSL
$site_origin = preg_replace('#/$#', '', $site_origin); // remove trailing slash

add_filter('rest_pre_serve_request', function ($value) use ($site_origin) {
    if (isset($_SERVER['HTTP_ORIGIN']) && rtrim($_SERVER['HTTP_ORIGIN'], '/') === $site_origin) {
        header('Access-Control-Allow-Origin: ' . $site_origin);
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce');
    }
    return $value;
});
