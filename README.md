# WP Break Test - v1.0.0

**Status:** ✅ Working Version

A controlled test plugin for WordPress that simulates update and rollback scenarios with WPvivid Backup.

## Description

This plugin integrates with GitHub to provide automatic update notifications through WordPress's native update system. Version 1.0.0 works perfectly and serves as the baseline for testing update rollback scenarios.

## Features

- GitHub-based automatic update checking
- Minimal admin interface showing current version
- Front-end marker (HTML comment) to verify plugin is active
- Admin area remains fully functional in all versions

## Installation

1. Download the plugin ZIP file
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Upload the ZIP file and activate
4. Configure your GitHub repository details in `wp-break-test.php`

## Usage

1. Install and activate this v1.0.0
2. Create a full backup using WPvivid
3. Push v2.0.0 to GitHub with a release tag
4. WordPress will show an update notification
5. Update to v2.0.0 (which will break the front-end)
6. Restore using WPvivid backup

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- WPvivid Backup plugin installed

## License

GPL v2 or later
