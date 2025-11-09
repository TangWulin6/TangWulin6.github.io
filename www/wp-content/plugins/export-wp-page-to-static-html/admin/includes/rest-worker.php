<?php
// In your plugin bootstrap (or functions.php)
add_action('rest_api_init', function () {
    register_rest_route('ewptshp/v1', '/run', [
        'methods'  => 'POST',
        'callback' => function (WP_REST_Request $req) {

            // --- simple shared-secret auth ---
            $token = $req->get_param('token');
            if (!$token || $token !== get_option('ewptshp_worker_token')) {
                return new WP_Error('forbidden', 'Bad token', ['status' => 403]);
            }

            // Accept either page_id or url
            $page_id = $req->get_param('page_id');
            $url     = $req->get_param('url');

            if (empty($page_id) && empty($url)) {
                return new WP_Error('bad_request', 'Provide page_id or url', ['status' => 400]);
            }

            // Optional: per-target lock to avoid duplicates (overlapping runs)
            $lockKey = 'ewptshp_lock_' . md5($page_id ? ('id:' . $page_id) : ('url:' . $url));
            if (get_transient($lockKey)) {
                return ['ok' => true, 'skipped' => 'locked'];
            }
            set_transient($lockKey, 1, 30 * MINUTE_IN_SECONDS);

            try {
                // Your existing action + callback will run the actual work
                do_action('next_page_export_from_queue', $page_id ?: $url);
            } finally {
                delete_transient($lockKey);
            }

            // return quickly
            return ['ok' => true];
        },
        
        'permission_callback' => '__return_true',
    ]);
});

