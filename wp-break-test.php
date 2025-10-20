<?php
/**
 * Plugin Name: WP Break Test
 * Plugin URI: https://github.com/YOUR-USERNAME/wp-break-test
 * Description: A controlled test plugin to simulate updates and rollback scenarios with WPvivid
 * Version: 2.0.0
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
define('WP_BREAK_TEST_VERSION', '2.0.0');
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
            <p><strong>Status:</strong> <span style="color: red;">✗ Broken (v2.0.0)</span></p>
            <hr>
            <p style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;">
                <strong>⚠️ Warning:</strong> This version intentionally breaks the front-end!<br>
                The admin area still works so you can use WPvivid to restore your backup.
            </p>
            <hr>
            <h3>To Restore Your Site:</h3>
            <ol>
                <li>Go to <strong>WPvivid Backup</strong> in the admin menu</li>
                <li>Click on <strong>Backup & Restore</strong> tab</li>
                <li>Find your backup (created before the update)</li>
                <li>Click <strong>Restore</strong> button</li>
                <li>Wait for restoration to complete</li>
            </ol>
        </div>
    </div>
    <?php
}

/**
 * Front-end hook - v2.0.0 INTENTIONALLY BREAKS HERE
 * 
 * This calls a non-existent function to trigger a fatal error
 * on the front-end only. Admin remains accessible for WPvivid.
 */
function wp_break_test_frontend_hook() {
    // v2.0.0: This will cause a fatal error (500)
    // The function does not exist, triggering WordPress critical error handling
    wp_break_test_this_function_does_not_exist();
    
    // This line will never execute
    echo '<!-- WP Break Test v2.0.0 - You should not see this -->';
}

// Only break on front-end, NOT in admin
if (!is_admin()) {
    add_action('wp_head', 'wp_break_test_frontend_hook');
}
