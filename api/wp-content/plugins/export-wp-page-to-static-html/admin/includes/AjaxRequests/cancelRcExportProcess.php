<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\cancelRcExportProcess;

use function ExportHtmlAdmin\EWPPTH_AjaxRequests\rcCheckNonce;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax cancel_rc_html_export_process*/
        add_action('wp_ajax_cancel_rc_html_export_process', array( $this, 'cancel_rc_html_export_process' ));
    }


    /**
     * Ajax action name: cancel_rc_html_export_process
     * @since    1.0.0
     * @access   public
     * @return json
     */

    public function cancel_rc_html_export_process(){

        \rcCheckNonce();

        //update_option('html_export_cancel', 'yes');
        //$stop_event = wp_schedule_single_event( time() , 'start_export_internal_wp_page_to_html_event', array( array(), array(), array(), array() ) );
        //$this->update_export_log('', 'cancel_export_process');
        $this->setSettings('cancel_command', 1);
        $this->setSettings('task', 'canceled');


        //$this->setSettings('rc_export_pages_as_html_task', 'failed');


        $logs_in_details = true;
        if($this->getSettings('logs_in_details') !== '1'){
            $logs_in_details = false;
        }
        delete_transient( 'doing_cron' );


        echo json_encode(array('success' => 'true', 'status' => 'success', 'logs_in_details' => $logs_in_details));

        die();
    }


}