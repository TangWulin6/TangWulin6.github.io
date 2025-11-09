<?php

class ExtractorHelpers{
    public $upload_dir;
    public $upload_url;
    public $export_dir;
    public $export_url;
    public $export_temp_dir;
    public $export_temp_url;
    public $css_path;
    public $fonts_path;
    public $js_path;
    public $img_path;
    public $video_path;
    public $audio_path;
    public $docs_path;

    public $site_data = "";
    public $site_data_html = "";
    protected $site_url = "";
    public $queue_event_key = "";
    public $image_extensions;
    public $video_extensions;
    public $audio_extensions;
    public $docs_extensions;
    public $html_extensions;
    public $saveAllAssetsToSpecificDir;
    public $keepSameName;
    public $rcExportHtmlAddContentsToTheHeader;
    public $rcExportHtmlAddContentsToTheFooter;

    public $rcExportHtmlSearchFor;
    public $rcExportHtmlReplaceWith;
    //public  $settingsKey = "rc_export_page_to_html__";
    public  $settingsKey = "rcwpptsh__";

    /*Extract methods*/
    public $extract_stylesheets;
    public $extract_scripts;
    public $extract_images;
    public $extract_html;
    public $inline_css;
    public $extract_meta_images;
    public $extract_videos;
    public $extract_audios;
    public $extract_docs;

    /*Ftp functions*/
    public $ftpFunctions;

    public $exportId;


    public function __construct()
    {
        
        $this->upload_dir = wp_upload_dir()['basedir'];
        $this->upload_url = wp_upload_dir()['baseurl'];
        $this->export_dir = $this->upload_dir . '/exported_html_files';
        $this->export_url = $this->upload_url . '/exported_html_files';
        $this->export_temp_dir = $this->upload_dir . '/exported_html_files/tmp_files';
        $this->export_temp_url = $this->upload_url . '/exported_html_files/tmp_files';

        $this->css_path = $this->export_temp_dir . '/css/';
        
        $this->fonts_path = $this->export_temp_dir . '/fonts/';
        $this->js_path = $this->export_temp_dir . '/js/';
        $this->img_path = $this->export_temp_dir . '/images/';
        $this->video_path = $this->export_temp_dir . '/videos/';
        $this->audio_path = $this->export_temp_dir . '/audios/';
        $this->docs_path = $this->export_temp_dir . '/documents/';

        $this->image_extensions = array("gif", "jpg", "jpeg", "png", "tiff", "tif", "bmp", "svg", "ico", "php", "webp");
        $this->video_extensions = array("flv", "3gp", "mp4", "m3u8", "ts", "gp", "mov", "avi", "wmv", "webm", "mpg", "mpv", "ogg", "mpv", "m4p", "m4v", "swf", "avchd");
        $this->audio_extensions = array("m4a", "aa", "aac", "aax", "amr", "m4b", "mp3", "mpc", "ogg", "tta", "wav", "wv", "webm", "cda");
        $this->docs_extensions = array("doc", "docx", "odt", "pdf", "xls", "xlsx", "ods", "ppt", "pptx", "txt");
        $this->html_extensions = array("html", "htm");

        $this->saveAllAssetsToSpecificDir = "on"; //get_option('rcExportHtmlSaveAllAssetsToSpecificDir', 'on') == "on"
        $this->keepSameName = get_option('rcExportHtmlKeepSameName', 'off') == "on";
        $this->rcExportHtmlAddContentsToTheHeader = get_option('rcExportHtmlAddContentsToTheHeader', "");
        $this->rcExportHtmlAddContentsToTheFooter = get_option('rcExportHtmlAddContentsToTheFooter', "");

        $this->rcExportHtmlSearchFor = get_option('rcExportHtmlSearchFor', "");
        $this->rcExportHtmlReplaceWith = get_option('rcExportHtmlReplaceWith', "");

        $this->exportId = "";

    } 


    
    /**
     * @return string
     */
    public function getFontsPath()
    {
        return $this->fonts_path;
    }


    /**
     * @return string
     */
    public function getCssPath()
    {
        return $this->css_path;
    }

    /**
     * @return string
     */
    public function getJsPath()
    {
        return $this->js_path;
    }
    /**
     * @return string
     */
    public function getImgPath()
    {
        return $this->img_path;
    }

    /**
     * @return string
     */
    public function getVideosPath()
    {
        return $this->video_path;
    }

    /**
     * @return string
     */
    public function getAudiosPath()
    {
        return $this->audio_path;
    }

    /**
     * @return string
     */
    public function getDocsPath()
    {
        return $this->docs_path;
    }

    public function is_link_exists($url="", $found_on = false, $found_on_url = "")
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'export_urls_logs';

        $url = $this->escape_quotations($this->ltrim_and_rtrim($url));

        if(!$found_on){
            $found = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE url LIKE '$url'");
        }else{
            $found = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE url LIKE '$url' AND found_on LIKE '$found_on_url' ");
        }
        if ($found){
            return true;
        }

        return false;
    }

    public function get_host($url='', $isScheme=true)
    {
        $url = parse_url($url);
        $scheme = isset($url['scheme']) ? $url['scheme'] : '';

        if($isScheme){
            $host = isset($url['host']) ? $scheme.'://'.$url['host'] : '';
        }else{
            $host = isset($url['host']) ? $url['host'] : '';
        }
        return $host;
    }

    public function getSaveAllAssetsToSpecificDir()
    {
        return $this->saveAllAssetsToSpecificDir;
    }

    /**
     * @return extract_videos
     */
    public function getExportDir()
    {
        return $this->export_dir;
    }
    /**
     * @return export_temp_dir
     */
    public function getExportTempDir()
    {
        return $this->export_temp_dir;
    }

    public function getKeepSameName()
    {
        return $this->keepSameName;
    }


    /**
     * Removes a query parameter from a URL.
     */
    public function removeParam($url, $param)
    {
        $parsedUrl = parse_url($url);

        // Parse query string into an array
        if (!isset($parsedUrl['query'])) return $url;

        parse_str($parsedUrl['query'], $queryParams);
        unset($queryParams[$param]);

        // Build query string again
        $newQuery = http_build_query($queryParams);

        // Rebuild full URL
        $scheme   = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host     = $parsedUrl['host'] ?? '';
        $port     = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $path     = $parsedUrl['path'] ?? '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';

        $finalUrl = $scheme . $host . $port . $path;
        if ($newQuery) {
            $finalUrl .= '?' . $newQuery;
        }
        $finalUrl .= $fragment;

        return $finalUrl;
    }

    /**
     * Checks if the URL has already been logged in the DB (ver param removed, protocol stripped).
     */
    public function rc_is_link_already_generated($url = '')
    {
        global $wpdb;

        // Normalize URL (remove protocol, decode, remove ?ver= param)
        $url = str_replace(['http:', 'https:'], '', urldecode($url));
        $url = $this->removeParam($url, 'ver');

        // Sanitize for SQL LIKE query
        $likeUrl = esc_sql($url);

        $table = $wpdb->prefix . 'export_page_to_html_logs';

        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table WHERE path LIKE %s", '%' . $likeUrl . '%')
        );

        return !empty($results);
    }

    /**
     * Inserts a new log entry into the export_page_to_html_logs table.
     *
     * @param string $path    The path or URL that is being logged.
     * @param string $type    The type of export operation (e.g., 'copying', 'generating').
     * @param string $comment Optional comment or note related to the export.
     * @return bool           Always returns true after inserting.
     */
    public function update_export_log($path="", $type="copying", $comment="")
    {
        global $wpdb; $table = $wpdb->prefix.'export_page_to_html_logs';

        $id = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE path LIKE %s LIMIT 1", $path)
        );
        wpptsh_error_log('======='.$path);
        wpptsh_error_log('========id: '.$id);

        if (!$id) {
            $wpdb->insert($table, ['path'=>$path,'type'=>$type,'comment'=>$comment], ['%s','%s','%s']);
        }
        return true;
    }


    /**
     * Retrieves the contents of a given URL using WordPress's HTTP API.
     *
     * @param string $url The URL to fetch.
     * @return string|array Returns the response body if successful,
     *                      or an array with 'error' and 'response_code' on failure.
     */
    public function get_url_data($url = "")
    {
        // Normalize the URL (replace spaces with %20, etc.)
        $url = $this->url_basename_space_to_percent20($url);

        // Make an HTTP GET request using WordPress's wp_remote_get()
        $response = wp_remote_get($url, array(
            'timeout'     => 300,     // Allow up to 5 minutes (useful for slow servers)
            'httpversion' => '1.1',   // Use HTTP/1.1
            'sslverify'   => true,    // Verify SSL certificate (recommended for security)
        ));

        // Check for errors during the request
        if (is_wp_error($response)) {
            wpptsh_error_log('Error retrieving URL: ' . $response->get_error_message());
            return [
                'error' => true,
                'response_code' => 0
            ];
        }

        // Get the HTTP response code and body
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        // If the response code is 200 (OK), return the content
        if (200 === $response_code) {
            return $body;
        } else {
            // Otherwise, log the response code and return an error array
            wpptsh_error_log('Non-200 response code: ' . $response_code . " --> " . $url);
            return [
                'error' => true,
                'response_code' => $response_code
            ];
        }
    }

    /**
     * Checks whether the 'cancel_command' setting is enabled.
     *
     * @return bool True if cancel command is found/enabled, false otherwise.
     */
    public function is_cancel_command_found()
    {
        // Retrieve the 'cancel_command' setting, defaulting to 0 if not set
        $result = $this->getSettings('cancel_command', 0);

        // Return true if setting is truthy, otherwise false
        return (bool) $result;
    }
    /**
     * Trims unwanted characters from both ends of a string.
     *
     * If no specific symbol is given, it removes common URL artifacts like quotes, spaces, etc.
     *
     * @param string $string The input string to clean.
     * @param string $sym    Optional: Specific symbol(s) to trim.
     * @return string        The cleaned string.
     */
    public function ltrim_and_rtrim($string = '', $sym = '')
    {
        if (empty($sym)) {
            // Decode URL-encoded characters first
            $string = urldecode($string);

            // Remove common unwanted characters from both ends
            $trimChars = " '\""; // space, single quote, double quote
            return trim($string, $trimChars);
        }

        // Trim specific symbol(s) from both ends
        return trim($string, $sym);
    }

    /**
     * Cleans and normalizes a URL: decodes entities, trims, and resolves to absolute.
     *
     * @param string $url       The (possibly relative or messy) URL.
     * @param string $base_url  Base URL to resolve relative URLs.
     * @return string           The cleaned absolute URL.
     */
    public function clean_url($url, $base_url = '')
    {
        // Decode HTML entities (e.g., &amp; → &)
        $url = html_entity_decode($url, ENT_QUOTES);

        // Trim unwanted characters and clean up
        $url = $this->ltrim_and_rtrim($url);

        // Convert to absolute URL using base, if relative
        return \url_to_absolute($base_url, $url);
    }

    /**
     * Stores the currently exporting item's URL in the options table.
     *
     * @param string $url The URL of the item currently being exported.
     * @return void
     */
    public function currently_exporting_item($url)
    {
        // Construct the full option key
        $optionKey = $this->settingsKey . 'currently_exporting_item';

        // Store the value in the WordPress options table
        update_option($optionKey, $url);
    }

    /**
     * Adds a URL log entry to the database if not already present and not a data/base64/svg URL.
     *
     * @param string $url        The URL to log.
     * @param string $found_on   Where the URL was found.
     * @param string $type       The type/category of the URL.
     * @param int    $exported   Whether the URL has been exported (0 or 1).
     * @param string $new_url    New file name or URL after processing.
     * @return int|false         Number of rows inserted or false on failure.
     */
    public function add_urls_log($url = "", $found_on = "", $type = "", $exported = 0, $new_url = "")
    {
        $url = (string) $url;
        $url = rtrim($url, '/');

        // Skip logging if URL is data URI, svg+xml, or base64 encoded (common inline assets)
        if (strpos($url, 'data:') === false && strpos($url, 'svg+xml') === false && strpos($url, 'base64') === false) {

            global $wpdb;
            $table_name = $wpdb->prefix . 'export_urls_logs';

            // Clean URL (trim + escape quotes)
            $url = $this->escape_quotations($this->ltrim_and_rtrim($url));

            // Use $wpdb->prepare to safely query
            $found = $wpdb->get_var(
                $wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE url = %s", $url)
            );

            if (!$found) {
                // Insert new record safely using $wpdb->insert()
                $res = $wpdb->insert(
                    $table_name,
                    [
                        'url'           => $url,
                        'new_file_name' => $new_url,
                        'found_on'      => $found_on,
                        'type'          => $type,
                        'exported'      => (int) $exported,
                    ],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                    ]
                );

                return $res; // Returns number of rows inserted (1) or false on failure
            }
        }

        return 0; // URL was not inserted (already exists or filtered out)
    }

    /**
     * Converts a URL to its basename with optional type suffix and parameter handling.
     *
     * @param string $url   The URL to process.
     * @param bool   $parm  Whether to include URL fragment (#anchor) in the result.
     * @param string $type  Optional suffix to append to the basename (e.g., file extension).
     * @return string       The basename derived from the URL.
     */
    public function url_to_basename($url = "", $parm = false, $type = "")
    {
        // Return default if input is empty or not a string
        if (!is_string($url) || trim($url) === "") {
            return "index" . $type;
        }

        // Remove custom URL base if enabled in settings
        if ($this->getSettings('customUrl') && $this->getSettings('full_site')) {
            $customBase = $this->getSettings('customUrlAddress');
            if ($customBase) {
                $url = str_replace($customBase, '', $url);
            }
        }

        // Remove home URL and host part from URL to get relative path
        $url = str_replace([home_url(), $this->get_host($url)], ['', ''], $url);

        // Trim leading and trailing slashes
        $url = $this->ltrim_and_rtrim($url, '/');

        // Extract the path part of the URL (without query string or fragment)
        $cleanUrl = parse_url($url, PHP_URL_PATH);

        // Get the basename of the path (last segment), with optional type trimmed
        $basename = basename($cleanUrl ?: '', $type);

        // Default to 'index' if basename is empty
        if ($basename === '') {
            $basename = 'index';
        }

        // If parameters/fragments should not be included, return basename + type
        if (!$parm) {
            return $basename . $type;
        }

        // Otherwise, add URL fragment (#anchor) if it exists
        $fragment = parse_url($url, PHP_URL_FRAGMENT);
        return $basename . $type . ($fragment ? '#' . $fragment : '');
    }

    /**
     * Converts a URL path to a relative path prefix with dots (e.g., "./", "../", "../../").
     *
     * @param string $url         The URL to convert.
     * @param bool   $isSpecific  Optional flag for specific path handling (currently treated same).
     * @return string             The relative path prefix.
     */
    public function rc_path_to_dot($url, $isSpecific = false)
    {
        // Remove custom URL base if 'customUrl' and 'full_site' settings are enabled
        if ($this->getSettings('customUrl') && $this->getSettings('full_site')) {
            $customBase = $this->getSettings('customUrlAddress');
            if ($customBase) {
                $url = str_replace($customBase, '', $url);
            }
        }

        // Remove home URL and host from URL to get relative path
        $middle_path = str_replace([home_url(), $this->get_host($url)], ['', ''], $url);
        $middle_path = $this->ltrim_and_rtrim($middle_path, '/');

        // If single page mode enabled with custom URL, clear the path
        if ($this->getSettings('customUrl') && $this->getSettings('singlePage')) {
            $middle_path = "";
        }

        $relativePath = './';

        // If there's a middle path, calculate how many "../" to add based on its segments
        if (!empty($middle_path)) {
            $segments = explode('/', $middle_path);
            // We add "../" for each segment except the first one (starts from 1)
            $levels = count($segments) - 1;
            if ($levels > 0) {
                $relativePath .= str_repeat('../', $levels);
            }
        }

        // If neither full site nor custom URL mode, just return "./"
        if (!$this->getSettings('full_site') && !$this->getSettings('customUrl')) {
            $relativePath = "./";
        }

        return $relativePath;
    }

    /**
     * Generates a dash-separated sub-path string from the middle part of a URL.
     *
     * Intended for use in filenames based on URL structure.
     * It selectively includes parts of the path, skipping the first 1 or 2 segments.
     *
     * @param string $url The URL to process.
     * @return string     The resulting path portion with segments separated by dashes.
     */
    public function middle_path_for_filename($url = '')
    {
        // Get the middle path from the full URL
        $middle_path = $this->rc_get_url_middle_path($url);

        // Remove trailing slash
        $middle_path_slash_cut = rtrim($middle_path, '/');

        // Split path into directory segments
        $path_dir = explode('/', $middle_path_slash_cut);

        // Final string to build from segments
        $path_dir_dash = '';

        // If URL contains '-child', skip first segment
        if (strpos($url, '-child') !== false) {
            if (count($path_dir) > 2) {
                for ($i = 1; $i < count($path_dir); $i++) {
                    $path_dir_dash .= $path_dir[$i] . '-';
                }
            }
        } else {
            // If not child, skip first two segments
            if (count($path_dir) > 2) {
                for ($i = 2; $i < count($path_dir); $i++) {
                    $path_dir_dash .= $path_dir[$i] . '-';
                }
            }
        }

        // Optional: Generate a random string if empty
        // if (empty($path_dir_dash)) {
        //     $path_dir_dash = $this->generate_string(3) . '-';
        // }

        // Optional: Remove trailing dash (uncomment if needed)
        // $path_dir_dash = rtrim($path_dir_dash, '-');

        return $path_dir_dash;
    }

    
    private function as_dir_base($url) {
        $parts = parse_url($url);
        $path  = $parts['path'] ?? '/';
        // If path has no extension and doesn't end with '/', treat as directory
        if (!preg_match('/\.[a-z0-9]+$/i', $path) && substr($path, -1) !== '/') {
            $parts['path'] = $path . '/';
            // Rebuild URL
            $scheme = $parts['scheme'] ?? 'http';
            $host   = $parts['host'] ?? '';
            $port   = isset($parts['port']) ? ':' . $parts['port'] : '';
            $user   = $parts['user'] ?? '';
            $pass   = isset($parts['pass']) ? ':' . $parts['pass']  : '';
            $auth   = $user ? ($user . $pass . '@') : '';
            $query  = isset($parts['query']) ? '?' . $parts['query'] : '';
            return $scheme . '://' . $auth . $host . $port . $parts['path'] . $query;
        }
        return $url;
    }

    private function normalize_url($u) {
        $parts = parse_url($u);
        if (!$parts) return $u;
        $host = isset($parts['host']) ? strtolower($parts['host']) : '';
        $path = isset($parts['path']) ? preg_replace('#/+#','/',$parts['path']) : '';
        // strip common tracking
        if (isset($parts['query'])) {
            parse_str($parts['query'], $q);
            foreach ($q as $k => $v) {
                if (preg_match('/^(utm_|fbclid|gclid|mc_eid|mc_cid)/i', $k)) unset($q[$k]);
            }
            $parts['query'] = http_build_query($q);
            if ($parts['query'] === '') unset($parts['query']);
        }
        // rebuild
        $scheme = $parts['scheme'] ?? 'https';
        $port   = isset($parts['port']) ? ':' . $parts['port'] : '';
        $query  = isset($parts['query']) ? '?' . $parts['query'] : '';
        $frag   = ''; // we already removed hash earlier
        return $scheme . '://' . $host . $port . $path . $query . $frag;
    }

    /**
     * @return string[]
     */
    public function getImageExtensions()
    {
        return $this->image_extensions;
    }


    /**
     * Saves a file from a given URL to a specified local path.
     *
     * - If the URL is from the same site and exists locally, it copies the file directly.
     * - Otherwise, it fetches the data via HTTP and saves it to the path.
     *
     * @param string $url      The source URL.
     * @param string $savePath The destination file path.
     */
    public function saveFile($url, $savePath)
    {
        $localPath = $this->abs_url_to_path($url);

        // Check if the file is local and exists
        if (strpos($url, home_url()) !== false && file_exists($localPath)) {
            wpptsh_error_log("Copying local file: $localPath -> $savePath");

            if (!@copy($localPath, $savePath)) {
                wpptsh_error_log("❌ Failed to copy local file: $localPath");
            } else {
                wpptsh_error_log("✅ Local file copied successfully.");
            }

            return;
        }

        // Handle remote file saving
        $directory = dirname($savePath);

        // Ensure directory exists
        if (!file_exists($directory)) {
            if (@mkdir($directory, 0777, true)) {
                wpptsh_error_log("📁 Created directory: $directory");
            } else {
                wpptsh_error_log("❌ Failed to create directory: $directory");
                return;
            }
        }

        // Fetch remote data
        $data = $this->get_url_data($url);

        if (empty($data)) {
            wpptsh_error_log("❌ No data fetched from: $url");
            return;
        }

        if (is_array($data) && $data['error']) {
            
            $this->update_asset_url_status($url, 'error');
            return;
        }

        // Attempt to write data to file
        if (!$handle = @fopen($savePath, 'w')) {
            wpptsh_error_log("❌ Cannot open file for writing: $savePath");
            return;
        }

        $bytes = @fwrite($handle, $data);
        @fclose($handle);

        if ($bytes !== false) {
            wpptsh_error_log("✅ Successfully saved file to: $savePath");
        } else {
            wpptsh_error_log("❌ Failed to write data to file: $savePath");
        }
    }

    public function update_urls_log($url = "", $value = "", $by = 'exported', $type = "cssItem")
    {
        global $wpdb;

        $table = $wpdb->prefix . 'export_urls_logs';
        $url = $this->escape_quotations($this->ltrim_and_rtrim($url));
        $url = rtrim($url, '/');
        // Check if record exists
        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE url = %s", $url)
        );

        // Update logic
        if ($exists) {
            $field = ($by === 'new_file_name') ? 'new_file_name' : 'exported';
            $format = ($field === 'exported') ? '%d' : '%s';

            return $wpdb->update(
                $table,
                [ $field => $value ],
                [ 'url' => $url ],
                [ $format ],
                [ '%s' ]
            );
        }

        // Insert logic
        $data = [
            'url'       => $url,
            'found_on'  => $url,
            'type'      => $type,
        ];

        $formats = [ '%s', '%s', '%s' ];

        if ($by === 'new_file_name') {
            $data['new_file_name'] = $value;
            $formats[] = '%s';
        } else {
            $data['exported'] = $value;
            $formats[] = '%d';
        }

        return $wpdb->insert($table, $data, $formats);
    }

    public function setSettings( $settings_name="", $value ="")
    {
        if(!empty($settings_name)){
            $settings_name = $this->settingsKey . $settings_name;
            update_option($settings_name, $value);
        }
        return true;
    }

    public function getSettings( $settings_name="", $default = "")
    {
        $settings_name = $this->settingsKey . $settings_name;
        $rc_ewppth_settings = get_option($settings_name);

        if(empty($rc_ewppth_settings) && !empty($default)){
            return $default;
        }

        return $rc_ewppth_settings;
    }

    public function removeSettings( $settings_name="")
    {
        $settings_name = $this->settingsKey . $settings_name;
        $rc_ewppth_settings = delete_option($settings_name);

        if ($rc_ewppth_settings) {
            return true;
        }
        return false;
    }

    public function removeAllSettings($key="")
    {
        global $wpdb;
        if (!empty($key)){
            $removefromdb = $wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '{$key}%'");
        }
        else{
            //$removefromdb = $wpdb->query("UPDATE {$wpdb->prefix}options SET option_value = '' WHERE option_name LIKE '{$this->settingsKey}%'");
            $removefromdb = $wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '{$this->settingsKey}%'");
        }


        if ($removefromdb) {
            return true;
        }
        return false;
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

    return $wpdb->get_results($sql, ARRAY_A); // [] if no matches
}

    public function rc_get_url_middle_path($url, $custom_url = false, $full_site = false)
    {
        // Remove query string and fragment
        $url = strtok($url, '?#');

        // Remove host and home_url
        $middle_path = str_replace(
            [home_url(), $this->get_host($url)],
            ['', ''],
            $url
        );

        // Clean slashes
        $middle_path = $this->ltrim_and_rtrim($middle_path, '/');

        // Remove filename (e.g., index.html, page.php, etc.)
        $basename = basename($url);
        if (strpos($middle_path, $basename) !== false) {
            $middle_path = str_replace($basename, '', $middle_path);
            $middle_path = rtrim($middle_path, '/');
        }

        return $middle_path;
    }
    public function url_basename_space_to_percent20($url = "")
    {
        if (empty($url)) return '';
        
        $parts = explode('/', $url);
        $basename = array_pop($parts);
        $basename = str_replace(' ', '%20', $basename);
        
        return implode('/', $parts) . '/' . $basename;
    }
    public function escape_quotations($content = '')
    {
        return str_replace(array("'", '"'), '', $content);
    }

    public function filter_filename($name) {
        // Remove illegal filesystem characters
        $name = str_replace(array_merge(
            array_map('chr', range(0, 31)),
            array('<', '>', ':', '"', '/', '\\', '|', '?', '*')
        ), '', $name);

        $name = trim($name);

        // Detect encoding safely
        $encoding = mb_detect_encoding($name) ?: 'UTF-8';

        // Preserve extension while cutting
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $filename = mb_strcut(pathinfo($name, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), $encoding);
        $name = $filename . ($ext ? '.' . $ext : '');

        // Decode URL-encoded chars, remove '=' for extra safety
        return str_replace('=', '', urldecode($name));
    }

    public function abs_url_to_path( $path = '' ) {
        return str_replace(
            site_url(),
            wp_normalize_path( untrailingslashit( ABSPATH ) ),
            wp_normalize_path( $path )
        );
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




    
    /**
     * @since 2.0.0
     * @param string $stylesheet_url
     * @param string $found_on
     * @return false|string
     */
    public function save_stylesheet($stylesheet_url = "", $found_on = "", $file_name = "")
    {

        wpptsh_error_log('\n\n=== Starting save_stylesheet ===\n');
        wpptsh_error_log('Original stylesheet URL: $stylesheet_url\n');
        $pathname_fonts = $this->getFontsPath();

        $pathname_css = $this->getCssPath();
        $pathname_images = $this->getImgPath();
        $host = $this->get_host($found_on);
        $saveAllAssetsToSpecificDir = $this->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->getExportTempDir();
        $keepSameName = $this->getKeepSameName();

        $m_basename = $this->middle_path_for_filename($stylesheet_url);
        $basename = $this->url_to_basename($stylesheet_url);

        if (!$this->rc_is_link_already_generated($stylesheet_url)) {

            $this->update_export_log($stylesheet_url, 'copying', '');
            $data = $this->get_url_data($stylesheet_url);
            if(is_array($data)){
                $this->update_urls_log($stylesheet_url, 1);
                $this->update_asset_url_status($stylesheet_url, 'exported');
                return;
            }
            
            wpptsh_error_log("\nBefore preg match");
            preg_match_all("/(?<=url\().*?(?=\))/", $data, $images_links);

            foreach ($images_links as $key => $images) {
        
                wpptsh_error_log("\Inside foreach preg match");
                $replacements_from = [];
                $replacements_to = [];

                foreach ($images as $image) {
                    $image_url = trim($image, "'\"");

                    if (strpos($image_url, 'data:') !== false || strpos($image_url, 'base64') !== false) {
                        continue;
                    }
                    // wpptsh_error_log("Memory used: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB");

                    if ($this->is_cancel_command_found()) {
                        wpptsh_error_log("Cancel command found while processing image: $image");
                        exit;
                    }

                    $newImageUrl = $this->clean_url($image_url, $stylesheet_url);
                    wpptsh_error_log("Resolved image URL: $newImageUrl");

                    //$this->currently_exporting_item($image_url);

                    $item_url = $newImageUrl;
                    //$this->add_urls_log($item_url, $stylesheet_url, 'cssItem');


                    $url_basename = $this->url_to_basename($item_url);
                    $url_basename = $this->filter_filename($url_basename);

                    $path_to_dot = './../';
                    // if (!$saveAllAssetsToSpecificDir) {
                    //     $path_to_dot = $this->rc_path_to_dot($item_url);
                    // } else {
                    //     if ($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)) {
                    //         $path_to_dot = $this->urlToDot($this->middle_path_for_filename($item_url));
                    //     } else {
                    //         $path_to_dot = './../';
                    //     }
                    // }

                    if (strpos($item_url, $host) !== false) {
                        $urlExt = \pathinfo($url_basename, PATHINFO_EXTENSION);
                        $fontExt = array("eot", "woff", "woff2", "ttf", "otf");

                        $my_file = null;
                        $replacement_path = '';

                        if (in_array($urlExt, $fontExt)) {
                            wpptsh_error_log("Font detected: $item_url | Extension: $urlExt");

                            if (!file_exists($pathname_fonts)) {
                                @mkdir($pathname_fonts, 0777, true);
                                wpptsh_error_log("Font directory created: $pathname_fonts");
                            }

                            $my_file = $pathname_fonts . $url_basename;
                            $replacement_path = $path_to_dot . 'fonts/' . $url_basename;
                        } elseif (in_array($urlExt, $this->getImageExtensions())) {
                            wpptsh_error_log("Image detected: $item_url");

                            if (!file_exists($pathname_images)) {
                                @mkdir($pathname_images, 0777, true);
                            }

                            $my_file = $pathname_images . $url_basename;
                            $replacement_path = $path_to_dot . 'images/' . $url_basename;
                        } elseif (strpos($item_url, 'css') !== false) {
                            $my_file = $pathname_css . $url_basename;
                            $replacement_path = $path_to_dot . 'css/' . $url_basename;
                        }

                        // Add replacement to arrays
                        if (!empty($replacement_path)) {
                            $replacements_from[] = $image_url;
                            $replacements_to[] = $replacement_path;
                        }
                        
                        wpptsh_error_log("\nBefore Save asset file if not already saved");
                        // Save asset file if not already saved
                        if (isset($my_file) && !file_exists($my_file)) {
                            wpptsh_error_log("Saving new asset file: $my_file");
                            $this->update_export_log($item_url, 'copying', '');
                            $this->currently_exporting_item($item_url);

                            
                             wpptsh_error_log("\nBefore save file method");
                            $this->saveFile($item_url, $my_file);
                             wpptsh_error_log("\After save file method");
                            
                            $this->update_urls_log($item_url, 1);
                        } else {
                            $this->currently_exporting_item($item_url);
                            //$this->update_urls_log($item_url, $url_basename, 'new_file_name', 'cssItem');
                            $this->update_urls_log($item_url, 1);
                        }
                    }

                    // Replace all in one go
                    $data = \str_replace($replacements_from, $replacements_to, $data);

                }
            }


            $my_file = $pathname_css . $file_name;

            wpptsh_error_log("Before saving new asset file: $my_file");
            if (!file_exists($my_file)) {
                wpptsh_error_log("Saving new asset file: $my_file");

                $this->update_export_log($stylesheet_url, 'copying', '');
                $this->update_asset_url_status($stylesheet_url, 'exported');
                $handle = @fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
                @fwrite($handle, $data);
                fclose($handle);
                
                wpptsh_error_log("File written: $my_file");
                $this->update_urls_log($stylesheet_url, 1);
            }
        }
        
        $this->update_urls_log($stylesheet_url, 1); 
    }

    
    /**
     * Save JS file locally and return the new basename path.
     */
    public function save_scripts($script_url_prev = '', $found_on = '', $file_name = "")
    {
        wpptsh_error_log("=== Starting save_scripts ===");
        wpptsh_error_log("Original script URL: $script_url_prev");
        wpptsh_error_log("Found on: $found_on");
        wpptsh_error_log("Target file name: $file_name");

        $script_url = $this->clean_url($script_url_prev, $found_on);
        wpptsh_error_log("Cleaned script URL: $script_url");

        $pathname_js = $this->getJsPath();
        wpptsh_error_log("JS path: $pathname_js");

        $m_basename = $this->middle_path_for_filename($script_url);
        $basename = $this->url_to_basename($script_url);
        $basename = $this->filter_filename($basename);
        wpptsh_error_log("Middle path: $m_basename");
        wpptsh_error_log("Filtered base name: $basename");

        $keepSameName = $this->getKeepSameName();
        $saveAllAssetsToSpecificDir = $this->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->getExportTempDir();

        if ($this->rc_is_link_already_generated($script_url_prev)) {
            wpptsh_error_log("Script already saved: $script_url_prev");
            return $m_basename . $file_name;
        }

        wpptsh_error_log("Script is new, adding to logs");
        $this->add_urls_log($script_url, $found_on, 'js');
        $this->update_export_log($script_url);

        // if (!(strpos($basename, '.') !== false)) {
        //     $basename = rand(5000, 9999) . ".js";
        //     wpptsh_error_log("Generated random basename: $basename");
        //     //$this->update_urls_log($script_url_prev, $basename, 'new_file_name');
        // }

        $full_path = $pathname_js . $file_name;

        wpptsh_error_log("Full path to save JS: $full_path");

        $this->ensure_dir(dirname($full_path));
        wpptsh_error_log("Directory ensured: " . dirname($full_path));

        if (!file_exists($full_path)) {
            wpptsh_error_log("File does not exist, proceeding to save: $full_path");

            if (
                strpos($script_url, 'elementor-pro/assets/js/webpack-pro.runtime') !== false ||
                strpos($script_url, 'elementor/assets/js/webpack.runtime') !== false
            ) {
                wpptsh_error_log("Elementor Webpack runtime script detected");

                $scriptData = $this->get_url_data($script_url);
                wpptsh_error_log("Fetched script data: " . strlen($scriptData) . " bytes");

                $file_url = dirname($script_url);
                $jsContents = $this->replaceTheJsContents($scriptData, $file_url);
                wpptsh_error_log("Replaced content length: " . strlen($jsContents) . " bytes");

                file_put_contents($full_path, $jsContents);
                wpptsh_error_log("Elementor script saved to: $full_path");
            } else {
                wpptsh_error_log("Standard JS file saving...");
                
                $this->update_asset_url_status($script_url_prev, 'exported');
                $this->saveFile($script_url, $full_path);
                wpptsh_error_log("JS file saved to: $full_path");
            }

            $this->update_urls_log($script_url_prev, 1);
            wpptsh_error_log("Updated URL log status to 1 for: $script_url_prev");
        } else {
            wpptsh_error_log("File already exists: $full_path — skipping download.");
        }

        wpptsh_error_log("=== Finished save_scripts ===");
    }


    /**
     * Ensure directory exists.
     */
    private function ensure_dir($dir)
    {
        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }
    }
    
    public function replaceTheJsContents($js_content, $file_url)
    {
        // Define a regular expression pattern to match JavaScript file names
        $pattern = '/(["\'])([^"\']+\.js)\1/';

        // Match all JavaScript file names using the defined pattern
        if (preg_match_all($pattern, $js_content, $matches)) {
            // Extracted JavaScript file names are stored in $matches[2]
            $js_files = $matches[2];

            // Initialize a variable to store modified JavaScript content
            $modified_js_content = $js_content;

            // Arrays for search and replace
            $search  = [];
            $replace = [];

            // Iterate over each matched JavaScript file name
            foreach ($js_files as $file) {

                $generated_file_url = $file_url.'/'.$file;
                $pathname_js = $this->getJsPath();
                $middle_p = $this->middle_path_for_filename($generated_file_url);
                $basename = $this->url_to_basename($generated_file_url);
                $file_save_path = $pathname_js . $middle_p;

                $elementor = 'elementor';
                if (strpos($file_url, 'elementor-pro')!==false){
                    $elementor = 'elementor-pro';
                }

                $uploadDir = $file_save_path;
                // if (!file_exists($uploadDir)) {
                //     mkdir($uploadDir, 0777, true);
                // }

                $this->saveFile($generated_file_url, $uploadDir.$basename);

                // Prepare replacement
                $modified_name = '../../../../../js/'.$middle_p . $basename;

                // Collect search and replace values
                $search[]  = $file;
                $replace[] = $modified_name;
            }

            // Do replacement in one go
            $modified_js_content = str_replace($search, $replace, $modified_js_content);

            // Return the modified JavaScript contents as a string
            return $modified_js_content;

        } else {
            // If no JavaScript file names found, return the original content
            return $js_content;
        }
    }  

}

