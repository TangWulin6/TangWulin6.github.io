<?php
namespace ExportHtmlAdmin\EWPPTH_AjaxRequests\exploreFtpDirectory;


class initAjax extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{

    public function __construct()
    {
        /*Initialize Ajax get_ftp_dir_file_list*/
        add_action('wp_ajax_rc_html_export_get_dir_path', array( $this, 'rc_html_export_get_dir_path' ));


    }

    /**
     * Ajax action name: get_ftp_dir_file_list
     * @since    1.0.0
     * @access   public
     * @return json
     */

    public function rc_html_export_get_dir_path(){
        //$post = $_POST['post'];
        $path = isset($_POST['path']) ? $_POST['path'] : "";

        \rcCheckNonce();

        $dirs = $this->get_ftp_path_directory($path);
        $lists = '<span><span style="font-weight: bold;">Current path: </span><span class="ftp_current_path">/' . $this->get_absolute_path($dirs['path']).'</span></span><ul class="list-group">';
        if(isset($dirs['lists'])&&!empty($dirs['lists'])){
            foreach ($dirs['lists'] as $key => $dir) {
                if (strpos($dir, '..')!==false) {
                    $lists .= '<li class="list-group-item" dir_path="'.$dirs['path'].'/'.$dir.'">'.$dir.'</li>';
                }
                else{
                    $lists .= '<li class="list-group-item" dir_path="'.$dirs['path'].'/'.$dir.'"><span class="dir_png"></span>'.$dir.'</li>';
                }

            }
        }

        $lists .= '</ul>';

        $response = $lists;


        echo json_encode(array('success' => 'true', 'status' => 'success', 'response' => $response));

        die();
    }

    public function get_ftp_path_directory($p_path='')
    {
        $status = get_option('rc_export_html_ftp_connection_status');
        $ftp_data = get_option('rc_export_html_ftp_data');

        if ($status == 'connected') {
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

            if (!empty($p_path)) {
                $path = $p_path;
            }

            if (function_exists('ftp_connect') && function_exists('ftp_login')) {

                if (!empty($host) && !empty($user) && !empty($pass)) {
                    $ftpConn = ftp_connect($host);
                    $login = ftp_login($ftpConn,$user,$pass);

                    if ($ftpConn && $login) {
                        //@ftp_chdir($ftpConn, $path);
                        $u['path'] = $this->normalizePath($path);//ftp_pwd($ftpConn);

                        $lists = ftp_mlsd($ftpConn, $path);
                        $list = array();
                        $list_d = array();
                        if (!empty($lists)) {
                            foreach ($lists as $value) {
                                if ($value['type'] == 'dir') {
                                    $list[] = $value['name'];
                                }
                                if ($value['type'] == 'pdir') {
                                    $list_d[] = $value['name'];
                                }
                            }
                        }

                        $all_lists = array_merge($list_d, $list);
                        $u['lists'] = $all_lists;
                        return $u;
                    }
                    else{
                        $this->setSettings('ftp_status', 'failed');
                    }
                }
                else{
                    $this->setSettings('ftp_status', 'failed');
                }
            }
            else{
                $this->setSettings('ftp_status', 'failed');
            }
        }
    }

    public function get_absolute_path($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode('/', $absolutes);
    }

}

