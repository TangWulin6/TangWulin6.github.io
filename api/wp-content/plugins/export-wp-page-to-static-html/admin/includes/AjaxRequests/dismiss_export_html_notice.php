<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\dismissExportHtmlNotice;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax dismiss_export_html_notice*/
        add_action('wp_ajax_dismiss_export_html_notice', array( $this, 'dismiss_export_html_notice' ));
    }


    /**
     * Ajax action name: dismiss_export_html_notice
     * @since    1.0.0
     * @access   public
     * @return json
     */

    public function dismiss_export_html_notice(){

        \rcCheckNonce();


        //update_option('html_export_cancel', 'yes');
        //$stop_event = wp_schedule_single_event( time() , 'start_export_internal_wp_page_to_html_event', array( array(), array(), array(), array() ) );
        //$this->update_export_log('', 'cancel_export_process');
        $this->setSettings('dismiss_notice', true);


        echo json_encode(array('success' => 'true', 'status' => 'success'));

        die();
    }


}