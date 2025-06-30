<?php

if (! defined('ABSPATH')) {
  exit;
}

/**
 * Adds helpful guidance below the featured image box for custom post types.
 *
 * Provides specific notes for "channel" and "episode" post types to help users understand
 * the importance of cover image dimensions and fallback behavior.
 */
function pop_help_text_for_cover($html)
{
  $screen = get_current_screen();

  if ($screen->post_type === 'channel') {
    $cover_note = __('Recommended dimensions: 1400x1400 to 3000x3000 pixels (as required by podcast platforms).', 'podposter');
    $cover_help = __('This is the main cover for your podcast channel. If no cover is set for an episode, podcast players will use this image for both the channel and its episodes.', 'podposter');

    return $html . "<b style='color: red;'>$cover_note</b><p style='text-align:justify; padding: 10px 10px 0 10px; border-top: dashed 2px #EEE'><b>$cover_help</b></p>";
  }

  if ($screen->post_type === 'episode') {
    $cover_note = __('Recommended dimensions: 1400x1400 to 3000x3000 pixels (as required by podcast platforms).', 'podposter');
    $cover_help = __('You can upload a custom cover for this episode. If none is set, the channel cover will be used as a fallback.', 'podposter');

    return $html . "<b style='color: red;'>$cover_note</b><p style='text-align:justify; padding: 10px 0 0 10px; border-top: dashed 2px #EEE'><b>$cover_help</b></p>";
  }

  return $html;
}
add_filter('admin_post_thumbnail_html', 'pop_help_text_for_cover');


/**
 * Customize the admin footer text for PodPoster-related post types.
 * Adds a friendly message in the admin footer when editing "channel" or "episode" posts.
 */
add_filter('admin_footer_text', function () {
  $screen = get_current_screen();

  if (in_array($screen->post_type, ['channel', 'episode'])) {
    return __('PodPoster helps your voice reach the world. Thank you for trusting us :)', 'podposter');
  }
});
