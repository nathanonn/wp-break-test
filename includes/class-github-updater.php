<?php
/**
 * GitHub Updater Class
 * Integrates GitHub releases with WordPress's native update system
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Break_Test_GitHub_Updater {
    
    private $file;
    private $plugin;
    private $basename;
    private $active;
    private $github_user;
    private $github_repo;
    private $github_response;

    public function __construct($file) {
        $this->file = $file;
        $this->plugin = plugin_basename($this->file);
        $this->basename = plugin_basename($this->file);
        $this->active = is_plugin_active($this->basename);
        
        $this->github_user = WP_BREAK_TEST_GITHUB_USER;
        $this->github_repo = WP_BREAK_TEST_GITHUB_REPO;
        
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
        add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
    }

    /**
     * Get information from GitHub API
     */
    private function get_github_data() {
        if (!empty($this->github_response)) {
            return;
        }

        // Get latest release from GitHub API
        $request_uri = sprintf('https://api.github.com/repos/%s/%s/releases/latest', 
            $this->github_user, 
            $this->github_repo
        );

        $response = wp_remote_get($request_uri, array(
            'timeout' => 15,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json'
            )
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $this->github_response = json_decode($body);
        
        return true;
    }

    /**
     * Check for plugin updates
     */
    public function check_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        $this->get_github_data();

        if (!$this->github_response || !isset($this->github_response->tag_name)) {
            return $transient;
        }

        // Remove 'v' prefix from tag if present
        $github_version = ltrim($this->github_response->tag_name, 'v');
        $current_version = WP_BREAK_TEST_VERSION;

        // Compare versions
        if (version_compare($github_version, $current_version, '>')) {
            $plugin_data = get_plugin_data($this->file);
            
            $package_url = sprintf(
                'https://github.com/%s/%s/archive/refs/tags/%s.zip',
                $this->github_user,
                $this->github_repo,
                $this->github_response->tag_name
            );

            $obj = new stdClass();
            $obj->slug = dirname($this->basename);
            $obj->new_version = $github_version;
            $obj->url = $plugin_data['PluginURI'];
            $obj->package = $package_url;
            $obj->tested = get_bloginfo('version');
            
            $transient->response[$this->basename] = $obj;
        }

        return $transient;
    }

    /**
     * Show plugin information popup
     */
    public function plugin_popup($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if (!isset($args->slug) || $args->slug !== dirname($this->basename)) {
            return $result;
        }

        $this->get_github_data();

        if (!$this->github_response) {
            return $result;
        }

        $plugin_data = get_plugin_data($this->file);
        $github_version = ltrim($this->github_response->tag_name, 'v');

        $obj = new stdClass();
        $obj->name = $plugin_data['Name'];
        $obj->slug = dirname($this->basename);
        $obj->version = $github_version;
        $obj->author = $plugin_data['Author'];
        $obj->homepage = $plugin_data['PluginURI'];
        $obj->requires = '5.0';
        $obj->tested = get_bloginfo('version');
        $obj->downloaded = 0;
        $obj->last_updated = $this->github_response->published_at;
        $obj->sections = array(
            'description' => $plugin_data['Description'],
            'changelog' => isset($this->github_response->body) ? $this->github_response->body : 'No changelog available.'
        );
        $obj->download_link = sprintf(
            'https://github.com/%s/%s/archive/refs/tags/%s.zip',
            $this->github_user,
            $this->github_repo,
            $this->github_response->tag_name
        );

        return $obj;
    }

    /**
     * Handle post-install
     */
    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;

        $install_directory = plugin_dir_path($this->file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;

        if ($this->active) {
            activate_plugin($this->basename);
        }

        return $result;
    }
}
