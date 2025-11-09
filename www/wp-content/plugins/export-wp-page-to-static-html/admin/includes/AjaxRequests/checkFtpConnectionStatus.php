<?php


namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\checkFtpConnectionStatus;

use function ExportHtmlAdmin\EWPPTH_AjaxRequests\rcCheckNonce;

class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax rc_check_ftp_connection_status*/
        add_action('wp_ajax_rc_check_ftp_connection_status', array( $this, 'rc_check_ftp_connection_status' ));

    }


    /**
     * Ajax action name: rc_check_ftp_connection_status
     * @since    1.0.0
     * @access   public
     * @return json
     */

    
    public function rc_check_ftp_connection_status(){
        //$post = $_POST['post'];
        $ftp_data = isset($_POST['ftp_data']) ? $_POST['ftp_data'] : "";

        \rcCheckNonce();

        $ftp_data = isset($_POST['ftp_data']) ? $_POST['ftp_data'] : "";
        $ftp_data = stripcslashes($ftp_data);
        $ftp_data = json_decode($ftp_data);

        $host = $user = $pass = $path = "";
        if (isset($ftp_data->host)) {
            $host = $ftp_data->host;
        }
        if (isset($ftp_data->user)) {
            $user = $ftp_data->user;
        }
        if (isset($ftp_data->pass)) {
            $pass = $ftp_data->pass;
        }
        if (isset($ftp_data->path)) {
            $path = $ftp_data->path;
        }

        $connected = false;

        if (function_exists('ftp_connect') && function_exists('ftp_login')) {

            if (!empty($host) && !empty($user) && !empty($pass)) {
                $ftpConn = ftp_connect($host);
                $login = ftp_login($ftpConn,$user,$pass);

                if ($ftpConn && $login) {
                    $connected = true;

                    update_option('rc_export_html_ftp_connection_status', 'connected');
                    update_option('rc_export_html_ftp_data', $ftp_data);
                }
                else{
                    update_option('rc_export_html_ftp_connection_status', 'not_connected');
                    //$this->setSettings('rc_export_html_ftp_data', $ftp_data);
                }
            }
        }

        $response = $connected;


        echo json_encode(array('success' => 'true', 'status' => 'success', 'response' => $response));

        die();
    }


}