<?php

namespace ExportHtmlAdmin\FtpFunctions;
/**
 * Class name: FtpFunctions
 */
class FtpFunctions  extends \ExportHtmlAdmin\Export_Wp_Page_To_Static_Html_Admin
{
    private $export_Wp_Page_To_Static_Html_Admin;
    private $host;
    private $user;
    private $pass;
    private $path;

    private $ftpConn;
    private $isCdn;

    public function __construct($export_Wp_Page_To_Static_Html_Admin)
    {
        $this->export_Wp_Page_To_Static_Html_Admin = $export_Wp_Page_To_Static_Html_Admin;
        $this->setFtpData();
        $this->ftpLogin();

    }

    public function setFtpData()
    {
        $status = get_option('rc_export_html_ftp_connection_status');
        $ftp_data = get_option('rc_export_html_ftp_data');

        if ($status == 'connected') {
            $this->host = $this->user = $this->pass = $this->path = "";
            if (isset($ftp_data->host)) {
                $this->host = $ftp_data->host;
            }
            if (isset($ftp_data->user)) {
                $this->user = $ftp_data->user;
            }
            if (isset($ftp_data->pass)) {
                $this->pass = $ftp_data->pass;
            }
            if (isset($ftp_data->path)) {
                $this->path = $ftp_data->path;
            }

            if (!empty($p_path)) {
                $this->path = $p_path;
            }
        }
    }

    public function ftpLogin()
    {
        if (function_exists('ftp_connect') && function_exists('ftp_login')) {

            if (!empty($this->host) && !empty($this->user) && !empty($this->pass)) {
                $ftpConn = ftp_connect($this->host);
                $login = ftp_login($ftpConn,$this->user,$this->pass);

                if ($ftpConn && $login) {
                    $this->ftpConn = $ftpConn;
                }
                else{
                    $this->ftpConn = false;
                    $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'failed');
                }
            }
            else{
                $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'failed');
            }
        }
    }

    public function closeFtp()
    {
        ftp_close($this->ftpConn);
    }


    public function testUpload()
    {
        if ($this->ftpConn!==false){
            $this->export_Wp_Page_To_Static_Html_Admin->update_export_log( __('Checking ftp server environment', 'export-wp-page-to-static-html'), 'file_uploaded_to_ftp');
            ftp_pasv($this->ftpConn, true);
            if(@ftp_put($this->ftpConn, 'test-uplaod/test-upload.txt', plugin_dir_path( __FILE__ ) . 'test-upload.txt', FTP_BINARY)){
                @ftp_rmdir($this->ftpConn, 'test_upload/test-upload.txt');
                $this->isCdn = true;
            }
            else{
                $this->isCdn = false;
            }
        }
    }

    public function ftp_rrmdir($conn_id, $directory){
        $lists = ftp_mlsd($conn_id, $directory);

        foreach($lists as $list){
            $full = $directory . '/' . $list['name'];

            if ($list['type']=='dir'||$list['type']=='file') {
                if($list['type'] == 'dir'){
                    $this->ftp_rrmdir($conn_id, $full);
                }else{
                    ftp_delete($conn_id, $full);
                }
            }
        }

        ftp_rmdir($conn_id, $directory);
        return true;
    }

    public function ftp_putAll($conn_id, $src_dir, $dst_dir) {
        if ($this->export_Wp_Page_To_Static_Html_Admin->is_cancel_command_found()) {
            return false;
        }
        $d = dir($src_dir);
        while($file = $d->read()) { // do this for each file in the directory
            if ($file != "." && $file != "..") { // to prevent an infinite loop
                if (is_dir($src_dir."/".$file)) { // do the following if it is a directory
                    if (!$this->isCdn){
                        if (!@ftp_chdir($conn_id, $dst_dir."/".$file)) {
                            @ftp_mkdir($conn_id, $dst_dir."/".$file); // create directories that do not yet exist
                        }
                    }
                    $this->ftp_putAll($conn_id, $src_dir."/".$file, $dst_dir."/".$file); // recursive part
                } else {
                    if(!$this->export_Wp_Page_To_Static_Html_Admin->is_cancel_command_found()){
                        $upload = @ftp_put($conn_id, $dst_dir."/".$file, $src_dir."/".$file, FTP_BINARY); // put the files
                        if($upload){
                            $this->export_Wp_Page_To_Static_Html_Admin->update_export_log($src_dir."/".$file, 'file_uploaded_to_ftp', $dst_dir."/".$file);
                        }
                        else{
                            $this->export_Wp_Page_To_Static_Html_Admin->update_export_log('uploading failed!', 'file_uploaded_to_ftp', $dst_dir."/".$file);
                        }
                    }
                }
            }
        }
        $d->close();
    }

    public function ftp_mksubdirs($ftpcon,$ftpbasedir,$ftpath){
        @ftp_chdir($ftpcon, $ftpbasedir); // /var/www/uploads
        $parts = array_filter(explode('/',$ftpath)); // 2013/06/11/username
        foreach($parts as $part){
            if(!@ftp_chdir($ftpcon, $part)){
                ftp_mkdir($ftpcon, $part);
                //ftp_chmod($ftpcon, 0775, $part);
                ftp_chdir($ftpcon, $part);
            }
        }
    }

    public function override_ftp_upload_files($ftpConn, $path = ''){

        $upload_dir = wp_upload_dir()['basedir'] . '/exported_html_files/tmp_files';

        $all_files = $this->export_Wp_Page_To_Static_Html_Admin->get_all_files_as_array2($upload_dir);

        if (!empty($all_files)) {
            foreach ($all_files as $key => $file) {
                $file2 = str_replace($upload_dir, $path, $file);
                //@ftp_delete($ftpConn, $file2 );

                if (!$this->export_Wp_Page_To_Static_Html_Admin->is_cancel_command_found()) {
                    $upload = @ftp_put($ftpConn, $file2, $file, FTP_BINARY);
                    if($upload){
                        $this->export_Wp_Page_To_Static_Html_Admin->update_export_log($file, 'file_uploaded_to_ftp', $file2);
                    }
                    else{
                        $this->export_Wp_Page_To_Static_Html_Admin->update_export_log('uploading failed!', 'file_uploaded_to_ftp', $file2);
                    }
                }

            }
        }
    }


    public function rc_if_images_directory_found($ftpConn='', $directory='')
    {
        $lists = ftp_mlsd($ftpConn, $directory);
        if (!empty($lists)) {
            foreach ($lists as $key => $file) {
                if ($file['type']=='dir'&&$file['name']=='images') {
                    return true;
                    break;
                }
            }
        }
        return false;
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

                    if (!$ftpConn || !$login) {
                        $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'failed');
                    }
                }
                else{
                    $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'failed');
                }
            }
            else{
                $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'failed');
            }
        }
    }


    public function uploadToFtp()
    {
        $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'running');

        //Testing ftp environment by uploading a test file.
        $this->testUpload();

        $this->export_Wp_Page_To_Static_Html_Admin->update_export_log('', 'uploading_to_ftp', '');

        $all_files = $this->export_Wp_Page_To_Static_Html_Admin->export_temp_dir;

        $path = $this->path;

        if (!empty($this->export_Wp_Page_To_Static_Html_Admin->getSettings('ftp_path'))) {
            $path = $this->export_Wp_Page_To_Static_Html_Admin->getSettings('ftp_path');
        }

        if (function_exists('ftp_connect') && function_exists('ftp_login')){
            if ($this->ftpConn!==false) {
                if (substr(ftp_pwd($this->ftpConn), -1) == "/"){
                    $path = ltrim($path, "/");
                }

                ftp_pasv($this->ftpConn, true);

                if (!$this->isCdn){
                    if (!@ftp_nlist($this->ftpConn, $path)) {
                        //ftp_mkdir($ftpConn, $path);
                        $this->ftp_mksubdirs($this->ftpConn, '/', $path);
                        @ftp_chdir($this->ftpConn, '/');
                    }

                    if ($this->rc_if_images_directory_found($this->ftpConn, $path)) {

                        $this->override_ftp_upload_files($this->ftpConn, $path);
                    }
                    else{
                        $this->ftp_putAll($this->ftpConn, $all_files, $path);
                    }
                }
                else{
                    $this->ftp_putAll($this->ftpConn, $all_files, $path);
                }

                ftp_close($this->ftpConn);

                $this->export_Wp_Page_To_Static_Html_Admin->update_export_log('', 'uploaded_to_ftp', '');
                $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'completed');

            }
            else{
                $this->export_Wp_Page_To_Static_Html_Admin->update_export_log('', 'login_failed_to_ftp', '');
                $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'failed');
            }
        }
        else{
            $this->export_Wp_Page_To_Static_Html_Admin->setSettings('ftp_status', 'failed');
        }
    }


}