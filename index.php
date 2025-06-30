<?php

/**
 * Plugin Name: PodPoster
 * Description: PodPoster is Podcast Publishing Plugin for WordPress. it transforms your site into a podcast hosting service—simple, standard, and free.
 * Version: 1.0.0
 * Author: Armin Amiri Nasab
 * Author URI: https://amirinasab.vercel.app/
 * License: GPLv2 or later
 * Text Domain: podposter
 * Requires Plugins: cmb2
 */

if (! defined('ABSPATH')) {
  exit;
}

// Check if the CMB2 plugin exists
add_action('admin_init', 'pop_check_for_cmb2_dependency');
function pop_check_for_cmb2_dependency()
{
  if (! class_exists('CMB2')) {
    add_action('admin_notices', function () {
      echo '<div class="notice notice-error"><p>';
      echo esc_html__('CMB2 plugin is required for PodPoster to work.', 'podposter');
      echo '</p></div>';
    });
  }
}


// Define constants for plugin directory path and URL
define("POP_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
define("POP_PLUGIN_DIR_URL", plugin_dir_url(__FILE__));

// Include necessary plugin files
include_once POP_PLUGIN_DIR_PATH . "options.php";             // Plugin settings and options
include_once POP_PLUGIN_DIR_PATH . "inc/post_types.php";      // Custom post types (e.g. episode)
include_once POP_PLUGIN_DIR_PATH . "inc/admin.php";           // Admin panel integrations
include_once POP_PLUGIN_DIR_PATH . "inc/generator.php";       // RSS feed generator logic

// It loads the text domain for the 'podposter' plugin, allowing for translation of plugin strings.
add_action('plugins_loaded', 'podposter_load_textdomain');
function podposter_load_textdomain()
{
  load_plugin_textdomain(
    'podposter',
    false,
    dirname(plugin_basename(__FILE__)) . '/languages/'
  );
}

// Enqueue admin styles
add_action("admin_enqueue_scripts", function () {
  wp_enqueue_style(
    "podposter_style",
    POP_PLUGIN_DIR_URL . "assets/css/style.css",
    [],
    "1.0.0"
  );
});

// Plugin activation hook
register_activation_hook(__FILE__, function () {
  $upload_dir = wp_upload_dir();
  $rss_dir = trailingslashit($upload_dir['basedir']) . 'rss';

  $message = '';
  $type = 'success';

  // Check if the /rss directory exists or try to create it
  if (is_dir($rss_dir)) {
    $message = __('Welcome back to PodPoster!', 'podposter');
  } else {
    if (wp_mkdir_p($rss_dir)) {
      $message = __('PodPoster activated successfully.', 'podposter');
    } else {
      $message = __('Could not create RSS directory during activation.', 'podposter');
      $type = 'error';
    }
  }

  // Store the activation notice as a transient
  set_transient('pop_activation_notice', [
    'message' => $message,
    'type'    => $type,
  ], 30);

  // Flush rewrite rules to register custom URLs (e.g. /podcasts/{id})
  flush_rewrite_rules();
});

// Display admin notices set via transients
add_action('admin_notices', function () {
  $current_user_id = get_current_user_id();

  // Check both user-specific and global notices
  $transients = [
    "pop_notice_user_{$current_user_id}",
    "pop_activation_notice",
  ];

  foreach ($transients as $transient_key) {
    $notice = get_transient($transient_key);

    if ($notice) {
      $class = $notice['type'] === 'success' ? 'notice-success' : 'notice-error';

      // Output the notice with correct styling
      echo '<div class="notice ' . esc_attr($class) . ' is-dismissible">';
      echo '<p><b>' . esc_html($notice['message']) . '</b></p>';
      echo '</div>';

      // Remove the transient after displaying
      delete_transient($transient_key);
    }
  }
});

// Disable the block editor (Gutenberg) for specific custom post types.
// This helps prevent WordPress from creating auto-drafts when opening the editor for these post types.
add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
  // Disable for 'channel' and 'episode' post types
  if ($post_type === 'channel' || $post_type === 'episode') {
    return false;
  }
  return $use_block_editor;
}, 10, 2);
