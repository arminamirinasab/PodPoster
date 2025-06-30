<?php

if (! defined('ABSPATH')) {
  exit;
}

// Regenerate RSS feed when a channel or episode is created or updated
add_action("wp_after_insert_post", function ($post_id, $post, $update) {

  // Only proceed for relevant post types
  if (!in_array($post->post_type, ['channel', 'episode'])) return;

  // Update RSS for the channel itself
  if ($post->post_type === 'channel') {
    updateRSSlink($post_id, $post);
  }

  // Update related channel's RSS when an episode is saved
  if ($post->post_type === 'episode') {
    $channel_id = get_post_meta($post_id, "pop_channel", true);
    $channel_post = $channel_id ? get_post($channel_id) : null;

    if ($channel_post && $channel_post->post_type === 'channel') {
      updateRSSlink($channel_id, $channel_post);
    }
  }
}, 20, 3);


// When a podcast channel is deleted, remove its RSS file from the server
add_action("delete_post_channel", function ($post_id, $post) {
  if (file_exists($rss_file)) {
    wp_delete_file($rss_file);
  }
}, 20, 2);

// When an episode is deleted, regenerate the RSS feed for its parent channel
add_action("delete_post_episode", function ($post_id, $post) {
  $channel_id = get_post_meta($post_id, "pop_channel", true);

  if ($channel_id) {
    $channel_post = get_post($channel_id);
    if ($channel_post && $channel_post->post_type === 'channel') {
      updateRSSlink($channel_id, $channel_post);
    }
  }
}, 20, 2);

/**
 * Generates and updates the RSS feed file for a podcast channel.
 *
 * This function is triggered with the given `$post_id` and `$post` (representing a podcast channel),
 * and uses the `shutdown` hook to defer RSS generation until the end of the current request.
 * It collects the channel's metadata and all related published episodes, then constructs
 * a compliant RSS 2.0 feed with iTunes and Atom extensions and saves it to a file.
 *
 * @param int     $post_id The ID of the podcast channel post.
 * @param WP_Post $post    The full post object for the podcast channel.
 */
function updateRSSlink($post_id, $post)
{
  // Ensure valid input
  if (!$post_id || !$post) return;

  // Defer RSS file generation until shutdown to avoid interfering with post save
  add_action("shutdown", function () use ($post_id, $post) {
    // Fetch all published episodes assigned to this channel
    $popEpisodes = get_posts([
      'post_type'   => 'episode',
      'post_status' => 'publish',
      'numberposts' => -1,
      'meta_key'    => 'pop_channel',
      'meta_value'  => $post_id
    ]);

    // Define RSS file path and URL
    $rss_path = WP_CONTENT_DIR . "/uploads/rss/{$post_id}.xml";
    $rss_url  = content_url("uploads/rss/{$post_id}.xml");

    // Create the directory if it doesn't exist
    if (!file_exists(dirname($rss_path))) {
      wp_mkdir_p(dirname($rss_path));
    }

    // Collect podcast metadata from the channel post
    $popPodcast = [
      'link'       => $rss_url,
      'title'      => $post->post_title,
      'description' => get_post_meta($post_id, 'pop_description', true),
      'date'       => $post->post_date,
      'category'   => get_post_meta($post_id, 'pop_category', true),
      'email'      => get_post_meta($post_id, 'pop_email', true),
      'image'      => get_the_post_thumbnail_url($post),
      'author'     => get_post_meta($post_id, 'pop_author', true),
      'copyright'  => get_post_meta($post_id, 'pop_copyright', true),
      'language'   => get_post_meta($post_id, 'pop_language', true),
      'explicit'   => get_post_meta($post_id, 'pop_explicit', true) ? "yes" : "no",
    ];

    // Initialize the RSS document
    $rss = new DOMDocument('1.0', 'UTF-8');
    $rss->formatOutput = true;

    // Create <rss> root element with required namespaces
    $rss_el = $rss->createElement('rss');
    $rss_el->setAttribute('version', '2.0');
    $rss_el->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
    $rss_el->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
    $rss->appendChild($rss_el);

    $channel = $rss->createElement('channel');
    $rss_el->appendChild($channel);

    // Add core RSS elements
    $elements = [
      'title'       => $popPodcast['title'],
      'link'        => $popPodcast['link'],
      'description' => $popPodcast['description'],
      'language'    => $popPodcast['language'],
      'pubDate'     => !empty($popPodcast['date']) ? gmdate(DATE_RSS, strtotime($popPodcast['date'])) : null,
    ];

    foreach ($elements as $tag => $value) {
      if (!empty($value)) {
        $el = $rss->createElement($tag);
        $el->appendChild($rss->createCDATASection($value));
        $channel->appendChild($el);
      }
    }

    // Add iTunes-specific tags
    $itunes_tags = [
      'itunes:author'   => $popPodcast['author'],
      'itunes:explicit' => $popPodcast['explicit'],
    ];

    foreach ($itunes_tags as $tag => $value) {
      if (!empty($value)) {
        $el = $rss->createElement($tag);
        $el->appendChild($rss->createCDATASection($value));
        $channel->appendChild($el);
      }
    }

    // Add podcast cover image if available
    if (!empty($popPodcast['image'])) {
      $image = $rss->createElement('itunes:image');
      $image->setAttribute('href', $popPodcast['image']);
      $channel->appendChild($image);
    }

    // Add copyright notice if provided
    if (!empty($popPodcast['copyright'])) {
      $copyright = $rss->createElement('copyright');
      $copyright->appendChild($rss->createCDATASection($popPodcast['copyright']));
      $channel->appendChild($copyright);
    }

    // Add podcast category (iTunes)
    if (!empty($popPodcast['category'])) {
      $cat = $rss->createElement('itunes:category');
      $cat->setAttribute('text', $popPodcast['category']);
      $channel->appendChild($cat);
    }

    // Add Atom self-referencing link
    $atom = $rss->createElement('atom:link');
    $atom->setAttribute('href', $popPodcast['link']);
    $atom->setAttribute('rel', 'self');
    $atom->setAttribute('type', 'application/rss+xml');
    $channel->appendChild($atom);

    // Add iTunes owner email
    if (!empty($popPodcast['email'])) {
      $owner = $rss->createElement('itunes:owner');
      $email = $rss->createElement('itunes:email');
      $email->appendChild($rss->createCDATASection($popPodcast['email']));
      $owner->appendChild($email);
      $channel->appendChild($owner);
    }

    // Loop through each episode and add as RSS <item>
    foreach ($popEpisodes as $episode) {
      $item = $rss->createElement('item');

      $fields = [
        'title'            => $episode->post_title,
        'link'             => get_permalink($episode),
        'description'      => $episode->post_content,
        'pubDate'          => gmdate(DATE_RSS, strtotime($episode->post_date)),
        'guid'             => get_post_meta($episode->ID, 'pop_audio_url', true),
        'itunes:duration'  => get_post_meta($episode->ID, 'pop_length', true),
      ];

      foreach ($fields as $tag => $value) {
        if (!empty($value)) {
          $el = $rss->createElement($tag);
          $el->appendChild($rss->createCDATASection($value));
          $item->appendChild($el);
        }
      }

      // Add audio file enclosure for the episode
      $audio_url = get_post_meta($episode->ID, 'pop_audio_url', true);
      if (!empty($audio_url)) {
        $enclosure = $rss->createElement('enclosure');
        $enclosure->setAttribute('url', $audio_url);
        $enclosure->setAttribute('length', get_post_meta($episode->ID, 'pop_size', true) ?: 0);
        $enclosure->setAttribute('type', 'audio/mpeg');
        $item->appendChild($enclosure);
      }

      // Add episode-specific image or fallback to channel image
      $image_url = get_the_post_thumbnail_url($episode) ?: $popPodcast['image'];
      if (!empty($image_url)) {
        $image_el = $rss->createElement('itunes:image');
        $image_el->setAttribute('href', $image_url);
        $item->appendChild($image_el);
      }

      $channel->appendChild($item);
    }

    // Add last build date to the feed
    $lastBuildDate = $rss->createElement('lastBuildDate');
    $lastBuildDate->appendChild($rss->createCDATASection(gmdate(DATE_RSS)));
    $channel->appendChild($lastBuildDate);

    // Attempt to save RSS file to disk
    $current_user_id = get_current_user_id();
    if ($rss->save($rss_path)) {
      // Store a transient to show an admin success notice to the user
      set_transient("pop_notice_user_{$current_user_id}", [
        'message' => __('RSS feed updated successfully.', 'podposter'),
        'type'    => 'success',
      ], 30);
    } else {
      // Store a transient to show an admin error notice to the user
      set_transient("pop_notice_user_{$current_user_id}", [
        'message' => __('An error occurred while updating the RSS feed.', 'podposter'),
        'type'    => 'error',
      ], 30);
    }
  });

  flush_rewrite_rules();
}

/**
 * Register a custom rewrite rule for podcast RSS feeds.
 * Example:
 *  /podcasts/123/ → index.php?pop_rss_feed=123
 */
function pop_add_rewrite_rules()
{
  add_rewrite_rule(
    '^podcasts/([0-9]+)/?$',
    'index.php?pop_rss_feed=$matches[1]',
    'top'
  );
}
add_action('init', 'pop_add_rewrite_rules');


// Register custom query variables for podcast RSS routing.
function pop_add_query_vars($vars)
{
  $vars[] = 'pop_rss_feed';
  return $vars;
}
add_filter('query_vars', 'pop_add_query_vars');


/** 
 * Intercepts requests to serve a custom RSS feed file based on the 'pop_rss_feed' query variable.
 * If the RSS file exists, it sends the XML content with appropriate headers.
 * If not found, it returns a 404 error with a helpful message.
 */
add_action('template_redirect', 'pop_template_redirect', 0);
function pop_template_redirect()
{
  $rss_id = get_query_var('pop_rss_feed');

  if ($rss_id) {
    $rss_path = WP_CONTENT_DIR . "/uploads/rss/{$rss_id}.xml";

    // If the RSS file exists, serve it with proper headers
    if (file_exists($rss_path)) {
      header('Content-Type: application/rss+xml; charset=UTF-8');
      header('Content-Disposition: inline; filename="rss.xml"');
      header('Cache-Control: no-cache');

      global $wp_filesystem;
      // Check if the filesystem is initialized; if not, initialize it
      if (empty($wp_filesystem)) {
        require_once ABSPATH . '/wp-admin/includes/file.php';
        WP_Filesystem();
      }
      // Read the contents of the RSS file from the given path
      $content = $wp_filesystem->get_contents($rss_path);
      if ($content !== false) {
        echo $content;
      } else {
        wp_die(esc_html__('RSS file could not be read.', 'podposter'));
      }
    } else {
      // If file not found, show a 404 with a helpful message
      status_header(404);
      wp_die(
        esc_html__("The requested RSS feed was not found. If you are confident about the registered address, please update the permalinks.", "podposter"),
        esc_html__("Not Found", "podposter")
      );
    }

    exit;
  }
}
