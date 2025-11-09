<?php
if (!class_exists('WPPTSH_DataCollector')) {
    class WPPTSH_DataCollector {
        protected $plugin_slug;
        protected $api_url;

        public function __construct($plugin_slug, $api_url) {
            $this->plugin_slug = $plugin_slug;
            $this->api_url     = $api_url;

            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
            add_action('admin_footer-plugins.php', [$this, 'render_modal']);
            add_action('wp_ajax_wpptsh_save_deactivation_feedback', [$this, 'handle_ajax']);
            add_action('wp_ajax_wpptsh_click_go_pro', [$this, 'click_go_pro']);
            add_action('wpptsh_export_error_log', [$this, 'export_error_log'], 10, 1);
            
            add_action('wp_ajax_wpptsh_save_review', [$this, 'save_review_handler']);
            add_action('wp_ajax_wpptsh_hide_review', [$this, 'hide_review']);
        }

        public function enqueue_scripts($hook) {
            if ($hook !== 'plugins.php' && $hook !== 'toplevel_page_export-wp-page-to-html') return;

            wp_enqueue_script('data-js', plugin_dir_url(__FILE__) . 'data.js', ['jquery'], null, true);

            wp_localize_script('data-js', 'wpptshData', [
                'pluginSlug' => $this->plugin_slug,
                'ajaxUrl'    => admin_url('admin-ajax.php'),
            ]);
        }


        public function render_modal() {
            ?>

            <div id="wpptsh-backdrop" style="
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(3px);
                z-index: 9998;
                display: none;
                "></div>

            <div id="wpptsh-feedback-modal" class="wpptsh-feedback-modal">
                <h2>We're sad to see you go 😢</h2>
                <p>If you have a moment, please let us know why you’re deactivating.</p>

                <div class="feedback-reasons">
                    <label><input type="radio" name="deactivate_reason" value="couldnt_understand"> Couldn't understand</label>
                    <label><input type="radio" name="deactivate_reason" value="found_better"> Found a better plugin</label>
                    <label><input type="radio" name="deactivate_reason" value="missing_feature"> Missing a specific feature</label>
                    <label><input type="radio" name="deactivate_reason" value="not_working"> Not working</label>
                    <label><input type="radio" name="deactivate_reason" value="not_needed"> Not what I was looking for</label>
                    <label><input type="radio" name="deactivate_reason" value="other"> Other</label>
                </div>

                <textarea id="wpptsh-feedback-text" placeholder="Could you tell us more?" style="width:100%; height:80px; margin-top:10px;"></textarea>

                <div class="modal-actions" style="margin-top: 20px; text-align: right;">
                    <button id="wpptsh-cancel" class="button">Cancel</button>
                    <button id="wpptsh-submit" class="button button-primary">Submit & Deactivate</button>
                </div>
            </div>

            <style>
                .wpptsh-feedback-modal {
                    background: #fff;
                    border-radius: 10px;
                    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
                    max-width: 500px;
                    padding: 30px;
                    font-family: sans-serif;
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    z-index: 9999;
                    transform: translate(-50%, -50%);
                    display: none;
                }

                .feedback-reasons label {
                    display: block;
                    margin-bottom: 10px;
                    font-weight: 500;
                }
            </style>
            <?php
        }

        public function handle_ajax() {
            $data = [
                'site_url'    => get_site_url(),
                'reason_key'  => sanitize_text_field($_POST['reason_key']),
                'feedback'    => sanitize_textarea_field($_POST['feedback']),
                'wp_version'  => get_bloginfo('version'),
                'plugin_version' => EXPORT_WP_PAGE_TO_STATIC_HTML_VERSION
            ];

            $response = wp_remote_post($this->api_url . '?type=deactivation', [
                'timeout' => 15,
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => json_encode($data),
            ]);

            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Failed to send feedback.', 'response' => wp_remote_retrieve_body($response)]);
            } else {
                wp_send_json_success(['message' => 'Feedback sent successfully.', 'response' => wp_remote_retrieve_body($response)]);
            }
        }

        public function click_go_pro() {
            $data = [
                'site_url'    => get_site_url(),
                'button'    => sanitize_text_field($_POST['button'])
            ];

            $response = wp_remote_post($this->api_url . '?type=go_pro', [
                'timeout' => 15,
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => json_encode($data),
            ]);

            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Failed to send feedback.', 'response' => wp_remote_retrieve_body($response)]);
            } else {
                wp_send_json_success(['message' => 'Feedback sent successfully.', 'response' => wp_remote_retrieve_body($response)]);
            }
        }

        public function export_error_log($status='error') {
            $data = [
                'site_url'    => get_site_url(),
                'status'    => $status,
                'plugin_version' => EXPORT_WP_PAGE_TO_STATIC_HTML_VERSION,
                'wp_version'  => get_bloginfo('version'),
            ];

            $response = wp_remote_post($this->api_url . '?type=error_log', [
                'timeout' => 15,
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => json_encode($data),
            ]);
        }


        function save_review_handler() {
            check_ajax_referer('rc-nonce', 'rc_nonce');
            // Sanitize and fetch data
            $rating  = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
            $comment = isset($_POST['comment']) ? sanitize_text_field($_POST['comment']) : '';

            // Validate
            if ($rating < 1 || $rating > 5) {
                wp_send_json_error(['message' => 'Invalid rating']);
            }

            // Prepare data to send to remote server
            $remote_data = [
                'site_url'       => get_site_url(),
                'plugin_slug'    => 'wpptsh',
                'rating'         => $rating,
                'feedback'       => $comment,
                'plugin_version' => defined('EXPORT_WP_PAGE_TO_STATIC_HTML_VERSION') ? EXPORT_WP_PAGE_TO_STATIC_HTML_VERSION : 'unknown',
                'wp_version'     => get_bloginfo('version'),
            ];

            // Send to remote server
            $response = wp_remote_post($this->api_url . '?type=review', [
                'timeout'     => 100,
                'body'        => json_encode($remote_data),
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Failed to send review.']);
            }

            wp_send_json_success(['message' => 'Review submitted successfully.']);
        }

        public function hide_review() {
            check_ajax_referer('rc-nonce', 'rc_nonce');
            update_option('wpptsh_hide_review', true);
            
            wp_send_json_success(['message' => 'Review hided successfully.']);
        }

    }

    // Initialize with plugin slug and your remote API URL
    new WPPTSH_DataCollector(
        'export-wp-page-to-static-html/export-wp-page-to-static-html.php',
        'https://api.myrecorp.com/wpptsh-report.php' // ← replace with your real endpoint
    );
}
