<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\deleteExportedZipFile;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax delete_exported_zip_file*/
        add_action('wp_ajax_delete_exported_zip_file', array( $this, 'delete_exported_zip_file' ));

    }


    /**
     * Ajax action name: delete_exported_zip_file
     * @since    1.0.0
     * @access   public
     * @return json
     */

    public function delete_exported_zip_file() {
        \rcCheckNonce(); // nonce + role check (your helper already enforces capability)

        $file_name = isset($_POST['file_name']) ? sanitize_file_name($_POST['file_name']) : '';
        if ($file_name === '') {
            wp_send_json_error(['message' => 'Invalid file name']);
        }

        $base = wp_upload_dir()['basedir'] . '/exported_html_files/';
        $candidate = wp_normalize_path($base . $file_name);

        // Resolve & enforce directory boundary
        $realBase = realpath($base);
        $realPath = realpath($candidate);
        if (!$realPath || strpos($realPath, $realBase) !== 0) {
            wp_send_json_error(['message' => 'Path not allowed']);
        }

        $ok = is_file($realPath) ? @unlink($realPath) : false;
        $ok ? wp_send_json_success() : wp_send_json_error(['message' => 'Delete failed']);
    }



}