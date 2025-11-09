<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\rcExportSetPause;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax see_logs_in_details*/
        add_action('wp_ajax_rc_export_set_pause', array( $this, 'rc_export_set_pause' ));

    }

    /**
     * Ajax action name: see_logs_in_details
     * @since    2.0.0
     * @access   public
     * @return json
     */
    public function rc_export_set_pause(){
        \rcCheckNonce();

        if (isset($_POST['paused']) && $_POST['paused'] == 'paused'){
            $this->setSettings('paused', true);
            echo json_encode(array('success' => true, 'status' => 'success', 'paused' => true));
            //$this->update_export_log('', 'reading', '');
        }
        else{
            $this->setSettings('paused', false);
            echo json_encode(array('success' => true, 'status' => 'success', 'paused' => false));
            //$this->update_export_log('', 'reading', '');

            $pageNow = $this->getSettings('pageNow');

            if (isset($pageNow['id'])&&isset($pageNow['url'])&&isset($pageNow['filename'])){
                $this->export_wp_page_as_static_html_by_page_id($pageNow['url'], $pageNow['filename'], $pageNow['id']);
            }

        }

        die();
    }


}