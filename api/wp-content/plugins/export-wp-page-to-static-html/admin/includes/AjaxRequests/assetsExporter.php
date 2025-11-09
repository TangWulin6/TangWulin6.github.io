<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\assetsExporter;

use function ExportHtmlAdmin\EWPPTH_AjaxRequests\rcCheckNonce;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax assets_exporter*/
        add_action('wp_ajax_wpptsh_assets_exporter', array( $this, 'assets_exporter' ));
    }


    /**
     * Ajax action name: assets_exporter
     * @since    1.0.0
     * @access   public
     * @return json
     */

    public function assets_exporter() {

    \rcCheckNonce();

    $asset_type = isset($_POST['asset_type']) ? \sanitize_text_field($_POST['asset_type']) : "";

    include __DIR__ . '/../class-ExtractorHelpers.php';
    $extractorHelpers = new \ExtractorHelpers();

    $asset_type = isset($_POST['asset_type']) ? sanitize_text_field($_POST['asset_type']) : null;
    $limit      = isset($_POST['limit']) ? (int) $_POST['limit'] : 1;

    $assets = $extractorHelpers->get_next_export_asset($asset_type, $limit);

    // Normalize to array
    if ($limit === 1) {
        $assets = $assets ? [$assets] : [];
    }

    $results = [
        'css'     => [],
        'js'      => [],
        'image'   => [],
        'url'     => [],
        'skipped' => [],
    ];

    foreach ($assets as $asset) {

        $id       = isset($asset['id']) ? (int) $asset['id'] : 0;
        $url      = isset($asset['url']) ? $asset['url'] : '';
        $found_on = isset($asset['found_on']) ? $asset['found_on'] : '';
        $type     = isset($asset['type']) ? $asset['type'] : '';
        $status   = isset($asset['status']) ? $asset['status'] : '';
        $new_name = isset($asset['new_file_name']) ? $asset['new_file_name'] : '';

        if (!$id || !$url || !$type) {
            $results['skipped'][] = ['id' => $id, 'reason' => 'missing_required_fields'];
            continue;
        }

        if ($status === 'processing') {
            $results[$type][] = [
                'id'           => $id,
                'url'          => $url,
                'asset_status' => $status,
                'handled'      => false,
                'message'      => 'Already processing',
            ];
            continue;
        }

        $extractorHelpers->update_asset_url_status($url, 'processing');

        try {
            switch ($type) {
                case 'css':
                    $extractorHelpers->save_stylesheet($url, $found_on, $new_name);
                    break;

                case 'js':
                    $extractorHelpers->save_scripts($url, $found_on, $new_name);
                    break;

                case 'url':
                    $endpoint = rest_url('ewptshp/v1/run');
                    $token    = get_option('ewptshp_worker_token');

                    wp_remote_post($endpoint, [
                        'timeout'   => 5,
                        'blocking'  => false,
                        'sslverify' => false,
                        'body'      => [
                            'token' => $token,
                            'url'   => $url,
                        ],
                    ]);
                    
                    error_log('[URL DOne] onAssetsExporter'. $url);
                    break;

                case 'image':
                    if (method_exists($extractorHelpers, 'save_image')) {
                        $extractorHelpers->save_image($url, $found_on, $new_name);
                    } else {
                    }
                    break;

                default:
                    $results['skipped'][] = ['id' => $id, 'reason' => 'unknown_type', 'type' => $type];
                    continue 2;
            }

            $results[$type][] = [
                'id'           => $id,
                'url'          => $url,
                'asset_status' => 'processed',
                'handled'      => true,
            ];

        } catch (Exception $e) {
            $extractorHelpers->update_asset_url_status($url, 'failed');

            $results[$type][] = [
                'id'           => $id,
                'url'          => $url,
                'asset_status' => 'failed',
                'handled'      => false,
                'error'        => $e->getMessage(),
            ];
        }
    }


    echo wp_json_encode([
        'success' => true,
        'status'  => 'success',
        'fetched' => count($assets),
        'grouped' => [
            'css'   => $results['css'],
            'js'    => $results['js'],
            'image' => $results['image'],
            'url'   => $results['url'],
        ],
        'skipped' => $results['skipped'],
    ], JSON_UNESCAPED_SLASHES);

    $status = (string) $this->getSettings('creating_zip');
    $proc   = (string) $this->getSettings('creating_zip_process');
    $creatingHtmlProcess = $this->getSettings('creating_html_process', 'running');


    if ($creatingHtmlProcess === 'completed' && $this->are_all_assets_exported() && $status !== 'running') {
        $this->setSettings('creating_zip', 'running');
    }
    elseif ($status === 'running' && $proc !== 'running' && $proc !== 'completed') {
        do_action('assets_files_exporting_completed');
    }


    wp_die();
}


    public function are_all_assets_exported() {
        global $wpdb;
        $table = $wpdb->prefix . 'export_urls_logs';

        // Count total css/js assets
        $total = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$table} 
            WHERE type IN ('css', 'js')
        ");

        // If no css/js assets exist, return false (or change this to true if preferred)
        if ((int) $total === 0) {
            return false;
        }

        // Count how many of those have been exported
        $exported = $wpdb->get_var("
            SELECT COUNT(*) 
            FROM {$table} 
            WHERE type IN ('css', 'js') AND exported = 1
        ");

        // Return true only if all css/js assets are exported
        return ((int) $total === (int) $exported);
    }


}