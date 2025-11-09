<?php

namespace ExportHtmlAdmin\extract_images;

class extract_images
{
    private $admin;

    public function __construct($admin)
    {
        $this->admin = $admin;
    }

    public function process($images = [],$url = "")
    {
        if ($this->admin->is_cancel_command_found()) return;

        $src = $this->admin->site_data;
        $saveAllAssetsToSpecificDir = $this->admin->getSaveAllAssetsToSpecificDir();
        $keepSameName = $this->admin->getKeepSameName();
        $imgExts = $this->admin->getImageExtensions();
        $path_to_dot = $this->admin->rc_path_to_dot($url);

        $image_links = $src->find('a');
        $img_attrs = ['src', 'data-src', 'data-lazyload'];

        if (!empty($images)) {
            foreach ($images as $img) {
                if ($this->admin->is_cancel_command_found()) return;

                foreach ($img_attrs as $attr) {
                    if (array_key_exists($attr, $img->attr)) {
                        $img_src = $img->attr[$attr];
                        if (strpos($img_src, 'data:') !== false || strpos($img_src, 'svg+xml') !== false || strpos($img_src, 'base64') !== false) continue;

                        $img_src = html_entity_decode($img_src, ENT_QUOTES);
                        $img_src = $this->admin->ltrim_and_rtrim($img_src);
                        $img_url = url_to_absolute($url, $img_src);

                        $urlExt = pathinfo($img_url, PATHINFO_EXTENSION);
                        $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $img_url);

                        if (in_array($urlExt, $imgExts) && !$exclude_url) {
                            $this->admin->currently_exporting_url($img_url);
                            $basename = $this->save_image($img_url, $url);

                            $new_url = $saveAllAssetsToSpecificDir
                                ? $path_to_dot . 'images/' . $basename
                                : $path_to_dot . $this->admin->rc_get_url_middle_path_for_assets($img_url) . $basename;

                            $img->setAttribute($attr, $new_url);
                        }
                    }
                }

                if (!empty($img->srcset)) {
                    $img->setAttribute('srcset', $this->process_srcset($img->srcset, $url, $imgExts, $saveAllAssetsToSpecificDir, $path_to_dot));
                }

                if (!empty($img->attr['data-srcset']) && strpos($img->attr['data-srcset'], 'data:') === false && strpos($img->attr['data-srcset'], 'svg+xml') === false && strpos($img->attr['data-srcset'], 'base64') === false) {
                    $img->setAttribute('data-srcset', $this->process_srcset($img->attr['data-srcset'], $url, $imgExts, $saveAllAssetsToSpecificDir, $path_to_dot));
                }
            }
        }

        if (!empty($image_links)) {
            foreach ($image_links as $img) {
                if (!empty($img->href)) {
                    $src_link = html_entity_decode($img->href, ENT_QUOTES);
                    $src_link = $this->admin->ltrim_and_rtrim($src_link);
                    $src_link = url_to_absolute($url, $src_link);

                    $urlExt = pathinfo($src_link, PATHINFO_EXTENSION);
                    $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $src_link);

                    if (in_array($urlExt, $imgExts) && strpos($url, $this->admin->get_host($src_link)) !== false && !$exclude_url) {
                        $basename = $this->save_image($src_link, $url);
                        $new_path = $saveAllAssetsToSpecificDir
                            ? $path_to_dot . 'images/' . $basename
                            : $path_to_dot . $this->admin->rc_get_url_middle_path_for_assets($src_link) . $basename;

                        $img->href = $new_path;
                        $img->src = $new_path;
                    }
                }
            }
        }

        $this->admin->site_data = $src;
    }

    private function process_srcset($srcset, $url, $imgExts, $saveAllAssetsToSpecificDir, $path_to_dot)
    {
        $entries = explode(',', $srcset);
        $result = [];

        foreach ($entries as $entry) {
            $parts = preg_split('/\s+/', trim($entry));
            $img_src = html_entity_decode($parts[0], ENT_QUOTES);
            $img_src = $this->admin->ltrim_and_rtrim($img_src);
            $img_url = url_to_absolute($url, $img_src);

            $urlExt = pathinfo($img_url, PATHINFO_EXTENSION);
            $exclude_url = apply_filters('wp_page_to_html_exclude_urls_settings_only', false, $img_url);

            if (in_array($urlExt, $imgExts) && !$exclude_url) {
                $basename = $this->save_image($img_url, $url);
                $new_url = $saveAllAssetsToSpecificDir
                    ? $path_to_dot . 'images/' . $basename
                    : $path_to_dot . $this->admin->rc_get_url_middle_path_for_assets($img_url) . $basename;

                $result[] = $new_url . (isset($parts[1]) ? ' ' . $parts[1] : '');
            } else {
                $result[] = trim($entry);
            }
        }

        return implode(', ', $result);
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

    private function ensure_dir($dir)
    {
        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
        }
    }
}
