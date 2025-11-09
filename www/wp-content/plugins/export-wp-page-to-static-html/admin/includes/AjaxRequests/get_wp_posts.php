<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\getWpPosts;

use function ExportHtmlAdmin\EWPPTH_AjaxRequests\rcCheckNonce;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax cancel_rc_html_export_process*/
        add_action('wp_ajax_rcewpp_get_wp_posts', array( $this, 'get_wp_posts' ));
    }


    /**
     * Ajax action name: cancel_rc_html_export_process
     * @since    1.0.0
     * @access   public
     * @return json
     */

    public function get_wp_posts(){

        $nonce = isset($_REQUEST['rc_nonce']) ? sanitize_text_field($_REQUEST['rc_nonce']) : '';
        if (!wp_verify_nonce( $nonce, "rc-nonce" )) {
            wp_send_json_error();
        }

        //check_ajax_referer('ajax_post_nonce', 'security');

        $paged = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $search = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
        $post_status = isset($_GET['post_status']) ? sanitize_text_field($_GET['post_status']) : '';

        $args = array(
            'post_type' => 'post',
            'posts_per_page' => 10,
            'paged' => $paged,
            's' => $search,
            'post_status' => array('publish', 'pending', 'draft', 'future', 'private'),
        );

        if (!empty($post_status)){
            $args['post_status'] = $post_status;
        }

        $query = new \WP_Query($args);
        $results = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                if (get_post_status() == "trash"){
                    continue;
                }
                $results[] = array(
                    'id' => get_the_ID(),
                    'text' => get_the_title() . ' (' . get_post_status() . ')',
                );
            }
        }

        wp_reset_postdata();

        wp_send_json(array(
            'results' => $results,
            'post_status' => $post_status,
            'pagination' => array('more' => $query->max_num_pages > $paged),
        ));
    }


}

