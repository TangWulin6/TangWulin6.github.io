<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\requestForWpPageToStaticHtml;

use function ExportHtmlAdmin\EWPPTH_AjaxRequests\rcCheckNonce;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax rc_export_wp_page_to_static_html*/
        add_action('wp_ajax_rc_export_wp_page_to_static_html', array( $this, 'rc_export_wp_page_to_static_html' ));

    }


    /**
     * Ajax action name: rc_export_wp_page_to_static_html
     * @since    1.0.0
     * @access   public
     * @return json
     */

    public function rc_export_wp_page_to_static_html(){
        $pages = isset($_POST['pages']) ? $_POST['pages'] : "";
        $pages = array_map('sanitize_key', $pages);

        $replace_urls = isset($_POST['replace_urls']) && sanitize_key($_POST['replace_urls']) == "true" ? true : false;
        $image_to_webp = isset($_POST['image_to_webp']) ? sanitize_key($_POST['image_to_webp']) == "true" : false;
        $image_quality = isset($_POST['image_quality']) ? (int) sanitize_key($_POST['image_quality']) : 80;

        $skip_assets_data = isset($_POST['skip_assets']) ? (array) $_POST['skip_assets'] : array();
        $skip_assets_data = array_map('sanitize_key', $skip_assets_data);

        $run_task_in_bg = isset($_POST['run_task_in_bg']) && sanitize_key($_POST['run_task_in_bg']) == "true" ? true : false;
        $receive_email = isset($_POST['receive_email']) && sanitize_key($_POST['receive_email']) == "true" ? true : false;
        $email_lists = isset($_POST['email_lists'] ) ? sanitize_textarea_field($_POST['email_lists']) : "";
        $ftp = isset($_POST['ftp']) ? sanitize_key($_POST['ftp']) : 'no';
        $full_site = isset($_POST['full_site']) && sanitize_key($_POST['full_site']) == "yes" ? true : false;
        $ftpPath = isset($_POST['path']) ? sanitize_text_field($_POST['path']) : '';
        $login_as = isset($_POST['login_as']) ? sanitize_text_field($_POST['login_as']) : '';
        $alt_export = isset($_POST['alt_export']) ? sanitize_key($_POST['alt_export']) == "true" : false;
        $exportId = isset($_POST['exportId']) ? sanitize_key($_POST['exportId']) : 0;

        \rcCheckNonce();

        $settingsKey = 'rcwpptsh__';

        $this->clear_tables_and_files();

        update_option($settingsKey.'task', 'running');
        update_option($settingsKey.'pages_data', $pages);


        $singlePage = false;
        if(count($pages)==1 && !$full_site){
            $singlePage = true;
        }

       /* $this->setSettings('cancel_command', '0');
        $this->setSettings('task', 'running');
        $this->setSettings('creating_html_process', '');
        $this->setSettings('creating_zip_process', '');
        $this->setSettings('total_zip_files', 0);
        $this->setSettings('zipDownloadLink', 'no');
        $this->setSettings('lastLogsTime', '');
        $this->setSettings('export_cancel_complete', 'no');
        $this->setSettings('paused', false);*/

        $settings = array(
            'skipAssetsFiles' => $skip_assets_data,
            'replaceUrlsToHash' => $replace_urls,
            'full_site' => $full_site,
            'login_as' => $login_as,
            'login_pass' => rand(111111, 9999999999),
            'receive_email' => $receive_email,
            'email_lists' => $email_lists,
            'ftp_upload_enabled' => $ftp,
            'image_to_webp' => $image_to_webp,
            'image_quality' => $image_quality,
            'ftp_path' => $ftpPath,
            'alt_export' => $alt_export,
            'singlePage' => $singlePage,
            'export_cancel_complete' => 'no',
            'paused' => false,
            'exportId' => $exportId,

            'cancel_command' => 0,
            'task' => 'running',
            'creating_html_process' => '',
            'creating_zip_process' => '',
            'total_zip_files' => 0,
            'zipDownloadLink' => '',
            'lastLogsTime' => 0,
            'run_task_in_bg' => $run_task_in_bg,
            'nextExportPageId' => 0,
            'lastAjaxExportPageId' => 0,
            'currently_exporting_url' => '',
            'creating_zip' => '',
        );

        foreach ($settings as $key => $setting) {
            $this->setSettings($key, $setting);
        }


        $s=0;
        while (true) {
            $s++;
            $taskStatus = $this->getSettings('task', '');

            $pages = array_slice($pages, 0, 3);
            if ($taskStatus == "" || $taskStatus == "completed" || $taskStatus == "failed" || $s > 5) {
                //$this->create_required_directories();
                //$this->setDefaultSettings();
                if ($run_task_in_bg){
                    wp_schedule_single_event( time() , 'start_export_internal_wp_page_to_html_event', array( $pages, $settings ) );
                }
                else{

                    do_action('run_html_export_task_in_ajax', $pages, $settings);
                }

                echo json_encode(array('success' => 'true', 'status' => 'success', 'response' => $pages));
                break; // Exit the loop once the condition is met
            }

            sleep(1);
        }

        die();

    }

    private function setDefaultSettings()
    {

        $this->setSettings('logs_in_details', 0);
        $this->setSettings('task', 'running');
        $this->setSettings('ftp_upload_enabled', '');
        $this->setSettings('ftp_status', '');
        $this->setSettings('lastLogs', '');
        $this->setSettings('lastLogsTime', '');
        $this->setSettings('timestampError', true);
        $this->setSettings('lastLogs', 0);
        $this->setSettings('lastLogsTime', time());
        $this->setSettings('paused', false);
    }

    public function setSettings( $settings_name="", $value ="")
    {
        $settingsKey = "rcwpptsh__";
        if(!empty($settings_name)){
            $settings_name = $settingsKey . $settings_name;
            update_option($settings_name, $value);
        }
        return true;
    }

}