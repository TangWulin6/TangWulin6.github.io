<?php

namespace ExportHtmlAdmin\extract_scripts;

class extract_scripts
{
    private $admin;

    public function __construct($admin)
    {
       $this->admin = $admin;
    }

    /**
     * Process array of script elements and rewrite their src attributes
     */
public function process($script_elements = [], $url = '')
{
    wpptsh_error_log("=== process(): start, page URL={$url} ===");

    $saveAllAssetsToSpecificDir = $this->admin->getSaveAllAssetsToSpecificDir();
    $path_to_dot = $this->admin->rc_path_to_dot($url, true);

    $pageHost  = $this->admin->get_host($url);
    $imgPath   = rtrim($this->admin->getImgPath(), '/\\') . DIRECTORY_SEPARATOR; // absolute
    $jsPath    = rtrim($this->admin->getJsPath(),  '/\\') . DIRECTORY_SEPARATOR; // absolute

    if (!is_dir($imgPath)) { @mkdir($imgPath, 0777, true); }
    if (!is_dir($jsPath))  { @mkdir($jsPath,  0777, true); }

    foreach ($script_elements as $script) {

        // =========================
        // CASE 1: External <script src="...">
        // =========================
        if (!empty($script->src)) {
            $src_link = $this->admin->clean_url($script->src, $url);
            $host     = $this->admin->get_host($src_link);

            $exclude = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $src_link);

            if (!$exclude && strpos($src_link, '.js') !== false && strpos($url, $host) !== false) {
                $this->admin->currently_exporting_url($src_link);

                $middle_p = $this->admin->middle_path_for_filename($src_link);
                $basename = $this->admin->url_to_basename($src_link, false, '.js');

                $this->admin->add_urls_log($src_link, $url, 'js', 0, $middle_p . $basename);

                // point the DOM to the local JS path (relative to this HTML)
                $script->src = $path_to_dot . 'js/' . $middle_p . $basename;

                wpptsh_error_log("External JS rewritten: {$src_link} -> {$script->src}");
            } else {
                wpptsh_error_log("External JS skipped: src={$src_link}, exclude=" . var_export($exclude, true));
            }

            continue; // done with this <script>
        }

        //Working with CDATA
        $raw = isset($script->innertext) ? $script->innertext : '';
        if ($raw === '') continue;
        if (strpos($raw, 'var ') === false) continue;

        if (strpos($raw, '<![CDATA[') == false) {
            continue;
        }

        // Match all .js URLs (http or https)
        preg_match_all('#(?:https?:\/\/|https?:\\\/\\\/)[^\s"\']+\.js(?:\?[^\s"\']*)?#i', $raw, $jsMatches);
        
        // Match all image URLs (http or https)
        preg_match_all('#(?:https?:\/\/|https?:\\\/\\\/)[^\s"\']+\.(?:png|jpe?g|gif|svg|webp)(?:\?[^\s"\']*)?#i', $raw, $imgMatches);

        if (!empty($jsMatches)) {
            
            foreach ($jsMatches as $jsUrls) {
                $replacements_from = [];
                $replacements_to = [];
                foreach ($jsUrls as $jsUrl) {
                    $middle_p = $this->admin->middle_path_for_filename($jsUrl);
                    $basename = $this->admin->url_to_basename($jsUrl);

                    $fileName = $this->save_scripts($this->normalize_url($jsUrl), $url);
                    $replace = $path_to_dot . 'js/' . $fileName;
                    $replacements_from[] = $jsUrl;
                    $replacements_to[] = ($path_to_dot . $replace) . '?tttttttttttttttttttt';
                }

                $new_replaced_string = str_replace($replacements_from, $replacements_to, $raw);

                // error_log('replacements_from'.json_encode($replacements_from));
                // error_log('replacements_to'.json_encode($replacements_to));
                // error_log('=======>'.$jsUrl);

                $script->outertext = "<script>{$this->strip_cdata_wrappers($new_replaced_string)}</script>";
            }
        }

        if (!empty($imgMatches)) {

            require_once __DIR__ . '/../class-ExtractorHelpers.php';
            $extractorHelpers = new \ExtractorHelpers();

            foreach ($imgMatches as $imgUrls) {
                $replacements_from = [];
                $replacements_to = [];
                foreach ($imgUrls as $imgUrl) {
                    
                    $middle_p = $this->admin->middle_path_for_filename($imgUrl);
                    $basename = $this->admin->url_to_basename($imgUrl);
                
                    $fileName = $this->save_image($this->normalize_url($imgUrl), $url);
                    $replace = $path_to_dot . 'images/' . $fileName;
                    $replacements_from[] = $imgUrl;
                    $replacements_to[] = $this->url_to_json_escaped($path_to_dot . $replace);
                }

                $new_replaced_string = str_replace($replacements_from, $replacements_to, $raw);


                $script->outertext = "<script>{$this->strip_cdata_wrappers($new_replaced_string)}</script>";
            }
        }


    }

    
}

public function save_image($img_src = "", $found_on = "")
{
    $pathname_images = $this->admin->getImgPath();
    $saveAllAssetsToSpecificDir = $this->admin->getSaveAllAssetsToSpecificDir();
    $exportTempDir = $this->admin->getExportTempDir();
    $keepSameName = $this->admin->getKeepSameName();

    if (strpos($img_src, 'data:') !== false) return false;

    $img_src = html_entity_decode($img_src, ENT_QUOTES);
    $img_url = url_to_absolute($found_on, $img_src);
    $basename = $this->admin->url_to_basename($img_url);
    $basename = $this->admin->filter_filename($basename);
    $m_basename = $this->admin->middle_path_for_filename($img_url);

    if (($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)) || !$saveAllAssetsToSpecificDir) {
        $m_basename = str_replace('-', '/', $m_basename);
    }

    if (!$this->admin->is_link_exists($img_url)) {
        $this->admin->update_export_log($img_url);
        $this->admin->add_urls_log($img_url, $found_on, 'image');

        if (strpos($basename, '.') === false) {
            $basename = rand(5000, 9999) . ".jpg";
            $this->admin->update_urls_log($img_url, $basename, 'new_file_name');
        }

        $basename = $this->admin->filter_filename($basename);

        if ($this->admin->getSettings('image_to_webp')) {
            $basename = preg_replace('/\.(jpg|jpeg|png|bmp)$/i', '.webp', $basename);
        }

        $img_path = $saveAllAssetsToSpecificDir
            ? $pathname_images . $m_basename . $basename
            : $exportTempDir . '/' . $this->admin->rc_get_url_middle_path_for_assets($img_url) . '/' . $basename;

        if (!file_exists($img_path)) {
            $this->ensure_dir(dirname($img_path));

            $urlExt = pathinfo($basename, PATHINFO_EXTENSION);
            if (in_array($urlExt, ['webp']) && $this->admin->getSettings('image_to_webp')) {
                $this->admin->saveImageToWebp($img_url, $img_path);
            } else {
                $this->admin->saveFile($img_url, $img_path);
            }

            $this->admin->update_urls_log($img_url, 1);
        }
    }

    return ($saveAllAssetsToSpecificDir && !empty($m_basename))
        ? $m_basename . $basename
        : $basename;
}

function strip_cdata_wrappers($code) {
    // Remove JS-style CDATA markers
    $code = preg_replace('#^\s*//<!\[CDATA\[\s*#m', '', $code);
    $code = preg_replace('#\s*//\]\]>\s*$#m', '', $code);

    // Remove XML-style CDATA markers
    $code = preg_replace('#^\s*<!\[CDATA\[\s*#m', '', $code);
    $code = preg_replace('#\s*\]\]>\s*$#m', '', $code);

    // Remove the exact CDATA comment open/close lines
    $code = str_replace('/* <![CDATA[ */', '', $code);
    $code = str_replace('/* ]]> */',   '', $code);

    return trim($code);
}

public function url_to_json_escaped($url) {
    // Ensure no surrounding quotes
    $url = trim($url, '"\'');

    // Escape forward slashes for JSON
    $url = str_replace('/', '\\/', $url);

    // Wrap in quotes (JSON strings must be quoted)
    return $url;
}
public function normalize_url($url) {
    // Remove JSON-style escaping of slashes
    $url = str_replace('\\/', '/', $url);
    $url = str_replace('\\\\/', '/', $url); // in case of double escaping
    
    // Remove surrounding quotes if present
    $url = trim($url, '"\'');
    
    return $url;
}


    /**
     * Save JS file locally and return the new basename path.
     */
    public function save_scripts($script_url_prev = '', $found_on = '')
    {
        //error_log('[]'.$script_url_prev);
        $script_url = \url_to_absolute($found_on, $script_url_prev);
        $pathname_js = $this->admin->getJsPath();
        $m_basename = $this->admin->middle_path_for_filename($script_url);
        $basename = $this->admin->url_to_basename($script_url);
        $basename = $this->admin->filter_filename($basename);
        $keepSameName = $this->admin->getKeepSameName();
        $saveAllAssetsToSpecificDir = $this->admin->getSaveAllAssetsToSpecificDir();
        $exportTempDir = $this->admin->getExportTempDir();

        if ($saveAllAssetsToSpecificDir && $keepSameName && !empty($m_basename)) {
            $m_basename = str_replace('-', '/', $m_basename);
        }

        // Already saved?
        if ($this->admin->is_link_exists($script_url_prev)) {
            $existing = $this->admin->get_newly_created_basename_by_url($script_url);
            if (!(strpos($basename, '.') !== false) && $existing) {
                return $m_basename . $existing;
            }
            return $m_basename . $basename;
        }

        // Not saved yet
        $this->admin->add_urls_log($script_url, $found_on, 'js');
        $this->admin->update_export_log($script_url);

        if (!(strpos($basename, '.') !== false)) {
            $basename = rand(5000, 9999) . ".js";
            $this->admin->update_urls_log($script_url_prev, $basename, 'new_file_name');
        }

        $full_path = $saveAllAssetsToSpecificDir
            ? $pathname_js . $m_basename . $basename
            : $exportTempDir . '/' . $this->admin->rc_get_url_middle_path_for_assets($script_url) . '/' . $basename;

        $this->ensure_dir(dirname($full_path));

        // Save file
        if (!file_exists($full_path)) {
            if (strpos($script_url, 'elementor-pro/assets/js/webpack-pro.runtime') !== false || strpos($script_url, 'elementor/assets/js/webpack.runtime') !== false) {
                $scriptData = $this->admin->get_url_data($script_url);
                $file_url = dirname($script_url);
                $jsContents = $this->admin->replaceTheJsContents($scriptData, $file_url);
                file_put_contents($full_path, $jsContents);
            } else {
                $this->admin->saveFile($script_url, $full_path);
            }

            $this->admin->update_urls_log($script_url_prev, 1);
        }

        return ($saveAllAssetsToSpecificDir && !empty($m_basename))
            ? $m_basename . $basename
            : $basename;
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
}
