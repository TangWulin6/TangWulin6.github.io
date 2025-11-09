<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\exportLogPercentage;
use ExportHtmlAdmin\extract_stylesheets\extract_stylesheets;


class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax export_log_percentage*/
        add_action('wp_ajax_export_log_percentage', array( $this, 'export_log_percentage' ));
        //include __DIR__ . '/../class-ExtractorHelpers.php';
    }


    // public function ExtractStylesheets() {
    //     return new extract_stylesheets($this);
    // }

    /**
     * Ajax action name: export_log_percentage
     * @since    2.0.0
     * @access   public
     * @return json
     */
    public function export_log_percentage(){
        $id = isset($_POST['id']) ? sanitize_key($_POST['id']) : "0";
        $exportId = isset($_POST['exportId']) ? sanitize_key($_POST['exportId']) : "0";

        \rcCheckNonce();

        
        global $wpdb;
        $totalUrlsToExport = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}export_urls_logs WHERE type = 'url' ");
        $totalExportedUrlsCount = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}export_urls_logs WHERE type = 'url' AND exported='1' ");

        $totalExportedUrlLogs = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}export_urls_logs");
        $totalExportedUrls = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}export_urls_logs WHERE exported='1' ");
        $totalLogs = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}export_page_to_html_logs");
        $timeOutError = (bool) $this->getSettings('timeOutError', false);


        if ($exportId == $this->getSettings('exportId', 0)){


            $response = '';
            $cancel_command = $this->getSettings('cancel_command', false);
            $logs_in_details = $this->getSettings('logs_in_details', false);
            $exportStatus = $this->getSettings('task', 'running');
            $creatingHtmlProcess = $this->getSettings('creating_html_process', 'running');
            $creatingZipStatus = $this->getSettings('creating_zip_process', '');
            $total_zip_files = $this->getSettings('total_zip_files', 0);
            $total_pushed_file_to_zip = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}export_page_to_html_logs WHERE type='added_into_zip_file' ");
            $zipDownloadLink = $this->getSettings('zipDownloadLink');
            $ftp_upload_enabled = $this->getSettings('ftp_upload_enabled');
            $ftp_status = $this->getSettings('ftp_status');
            $lastUpdateTotalLogs = $this->getSettings('lastLogs');
            $lastLogsTime = (int) $this->getSettings('lastLogsTime');
            $paused = (bool) $this->getSettings('paused', false);


            $logs = array();
            //if($logs_in_details == 1){
                if ($id == 0||$id == '0') {
                    $logs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}export_page_to_html_logs ORDER BY id ASC");
                } else {
                    $logs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}export_page_to_html_logs ORDER BY id ASC LIMIT 5000 OFFSET {$id}");
                }
            //}


            $createdLastHtmlFile = "";
            if($creatingHtmlProcess=="completed"){
                $tempUrl = wp_upload_dir()['baseurl'].'/exported_html_files/tmp_files';
                $created_html_file = $wpdb->get_results("SELECT comment FROM {$wpdb->prefix}export_page_to_html_logs WHERE type='created_html_file' ORDER BY ID ASC LIMIT 1");
                $createdLastHtmlFile = isset($created_html_file[0]) ? $created_html_file[0]->comment : '';
                if(!empty($createdLastHtmlFile)){
                    $createdLastHtmlFile = $tempUrl .'/'. $createdLastHtmlFile;
                }
            }

            $total_file_uploaded = 0;
            if($ftp_upload_enabled == "yes"){
                $total_file_uploaded = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}export_page_to_html_logs WHERE type='file_uploaded_to_ftp' ");
            }

            $error = false;
            if(!empty($lastUpdateTotalLogs)&&!empty($lastLogsTime)){
                if ($lastUpdateTotalLogs==$totalLogs){
                    if( ((time()-$lastLogsTime)/60) >= 5 ){
                        $this->remove_user();
                        $error = true;
                        $this->setSettings('timeOutError', true);
                        $this->setSettings('lastLogsTime', time());
                        $this->setSettings('paused', true);
                        $paused = true;
                        
                        do_action('wpptsh_export_error_log', 'timeout_error');
                    }

                }else{
                    $this->setSettings('lastLogsTime', time());
                }
            }
            else{
                $this->setSettings('lastLogsTime', time());
            }
            $this->setSettings('lastLogs', $totalLogs);


            if ($paused){
                $creatingHtmlProcess = 'paused';
            }

            $arrays = array(
                'success' => true,
                'status' => 'success',
                'response' => $response,
                'currently_exporting_url' => $this->getSettings('currently_exporting_url', ''),   
                'cancel_command' => $cancel_command,
                'total_urls_log' => $totalExportedUrlLogs,
                'total_url_exported' => $totalExportedUrls,
                'export_status' => $exportStatus,
                'creating_html_process' => $creatingHtmlProcess,
                'creating_zip_status'=> $creatingZipStatus,
                'total_pushed_file_to_zip'=> $total_pushed_file_to_zip,
                'total_zip_files'=> $total_zip_files,
                'logs_in_details'=> $logs_in_details,
                'total_logs' => $totalLogs,
                'logs' => $logs,
                'zipDownloadLink' => $zipDownloadLink,
                'ftp_upload_enabled' => $ftp_upload_enabled,
                'ftp_status' => $ftp_status,
                'total_file_uploaded' => $total_file_uploaded,
                'createdLastHtmlFile' => $createdLastHtmlFile,
                'error' => $error,
                'exportId' => $exportId,
                'are_all_assets_exported' => $this->are_all_assets_exported(),
                'totalUrlsToExport' => $totalUrlsToExport,
                'totalExportedUrlsCount' => $totalExportedUrlsCount,
                'latest_urls_to_export' =>$this->get_next_export_asset('url', 3),
                'timeOutError' => $timeOutError
                
            );

        }
        else{
            $arrays = array(
                'success' => true,
                'status' => 'success',
                'currently_exporting_url' => $this->getSettings('currently_exporting_url', ''),      
                'response' => '',
                'cancel_command' => 0,
                'total_urls_log' => 0,
                'total_url_exported' => 0,
                'export_status' => 'running',
                'creating_html_process' => 'running',
                'creating_zip_status'=> '',
                'total_pushed_file_to_zip'=> 0,
                'total_zip_files'=> 0,
                'logs_in_details'=> 0,
                'total_logs' => 0,
                'logs' => array(),
                'zipDownloadLink' => '',
                'ftp_upload_enabled' => false,
                'ftp_status' => 'not_activated',
                'total_file_uploaded' => 0,
                'createdLastHtmlFile' => 0,
                'error' => false,
                'exportId' => $exportId,
                'are_all_assets_exported' => $this->are_all_assets_exported(),
                'totalUrlsToExport' => $totalUrlsToExport,
                'totalExportedUrlsCount' => $totalExportedUrlsCount,
                'zip_completed' => $this->getSettings('zip_completed', false),
                'latest_urls_to_export' =>$this-> get_next_export_asset('url', 3),
                'timeOutError' => $timeOutError
            );
        }

        echo json_encode($arrays);

        die();
    }

        
    /**
     * Get next asset(s) to export.
     *
     * @param string|array|null $asset_type One of 'css','js','image', or an array of them. Null = all.
     * @param int               $limit      How many rows to fetch (default 1).
     * @return array|null                   ARRAY_A row when $limit === 1, array of rows when $limit > 1, or null/[] if none.
     */
    public function get_next_export_asset($asset_type = null, $limit = 1) {
        global $wpdb;

        $table   = $wpdb->prefix . 'export_urls_logs';
        $allowed = ['css', 'js', 'url', 'image'];

        // Normalize $asset_type into a validated list of types
        if (is_string($asset_type) && $asset_type !== '') {
            $types = in_array($asset_type, $allowed, true) ? [$asset_type] : [];
        } elseif (is_array($asset_type)) {
            $types = array_values(array_intersect($allowed, array_map('strval', $asset_type)));
        } else {
            $types = $allowed; // default: all
        }

        // If nothing valid remains, return early
        if (empty($types)) {
            return $limit === 1 ? null : [];
        }

        // Sanitize/guard limit
        $limit = max(1, (int) $limit);

        // Build dynamic placeholders for the IN() list
        $in_placeholders = implode(',', array_fill(0, count($types), '%s'));

        // Prepare SQL: filter by type, not yet exported, oldest first, limit N
        $sql = $wpdb->prepare(
            "SELECT * 
            FROM {$table}
            WHERE type IN ($in_placeholders)
            AND exported = %d
            ORDER BY id ASC
            LIMIT %d",
            array_merge($types, [0, $limit])
        );

        // Return one row or many based on $limit
        if ($limit === 1) {
            return $wpdb->get_row($sql, ARRAY_A); // null if no match
        }
        $results = $wpdb->get_results($sql, ARRAY_A);
        if (!empty($results)) {
            foreach ($results as $index => $result) {
                $this->update_asset_url_status($result['url'], 'processing');
            }
        }

        return $results; // [] if no matches
    }

    public function update_asset_url_status($url, $status){
        global $wpdb;
        $table = $wpdb->prefix . 'export_urls_logs';

        // Sanitize inputs
        $sanitized_url = sanitize_text_field($url);
        $sanitized_status = sanitize_text_field($status);

        // Prepare and run the query
        $sql = $wpdb->prepare(
            "UPDATE $table SET status = %s WHERE url LIKE %s",
            $sanitized_status,
            $sanitized_url
        );

        return $wpdb->query($sql); // Returns number of affected rows
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