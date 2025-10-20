<?php
/**
 * Plugin Name: WP Break Test
 * Plugin URI: https://github.com/YOUR-USERNAME/wp-break-test
 * Description: A controlled test plugin to simulate updates and rollback scenarios with WPvivid
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/YOUR-USERNAME
 * License: GPL v2 or later
 * Text Domain: wp-break-test
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_BREAK_TEST_VERSION', '1.0.0');
define('WP_BREAK_TEST_FILE', __FILE__);
define('WP_BREAK_TEST_PATH', plugin_dir_path(__FILE__));
define('WP_BREAK_TEST_URL', plugin_dir_url(__FILE__));

// GitHub repository settings (UPDATE THESE WITH YOUR REPO INFO)
define('WP_BREAK_TEST_GITHUB_USER', 'nathanonn');
define('WP_BREAK_TEST_GITHUB_REPO', 'wp-break-test');
define('WP_BREAK_TEST_GITHUB_BRANCH', 'main');

/**
 * Include the GitHub updater class
 */
require_once WP_BREAK_TEST_PATH . 'includes/class-github-updater.php';

/**
 * Initialize the GitHub updater
 */
function wp_break_test_init_updater() {
    if (is_admin()) {
        new WP_Break_Test_GitHub_Updater(__FILE__);
    }
}
add_action('init', 'wp_break_test_init_updater');

/**
 * Add admin menu
 */
function wp_break_test_admin_menu() {
    add_menu_page(
        'WP Break Test',
        'WP Break Test',
        'manage_options',
        'wp-break-test',
        'wp_break_test_admin_page',
        'dashicons-admin-tools',
        100
    );
}
add_action('admin_menu', 'wp_break_test_admin_menu');

/**
 * Admin page content
 */
function wp_break_test_admin_page() {
    ?>
    <div class="wrap">
        <h1>WP Break Test</h1>
        <div class="card" style="max-width: 600px; margin-top: 20px;">
            <h2>Plugin Information</h2>
            <p><strong>Current Version:</strong> <?php echo esc_html(WP_BREAK_TEST_VERSION); ?></p>
            <p><strong>Status:</strong> <span style="color: green;">✓ Working (v1.0.0)</span></p>
            <hr>
            <p><strong>GitHub Repository:</strong> <?php echo esc_html(WP_BREAK_TEST_GITHUB_USER . '/' . WP_BREAK_TEST_GITHUB_REPO); ?></p>
            <p><em>Updates are checked automatically through WordPress.</em></p>
            <hr>
            <h3>How to Test Update & Rollback:</h3>
            <ol>
                <li>Create a full backup in WPvivid first</li>
                <li>Go to Dashboard → Updates</li>
                <li>If v2.0.0 is available, click "Update Now"</li>
                <li>v2.0.0 will break the front-end (admin will still work)</li>
                <li>Use WPvivid to restore your backup</li>
            </ol>
        </div>
    </div>
    <?php
}

/**
 * Front-end hook - v1.0.0 works fine
 */
function wp_break_test_frontend_hook() {
    // v1.0.0: This works perfectly
    // Just adds a hidden HTML comment to verify the plugin is active
    echo '<!-- WP Break Test v1.0.0 Active -->';
}
add_action('wp_footer', 'wp_break_test_frontend_hook');
