<?php

if (! defined('ABSPATH')) {
  exit;
}

/**
 * Generate the default RSS feed link for a podcast channel.
 *
 * This is used as the default value for the RSS field in the channel metabox.
 * It builds the URL based on the site URL and the post ID.
 */
function pop_generate_rss_link($field_args, $field)
{
  $object_id = $field->object_id; // Get the current post ID for which the field is rendered
  return get_site_url() . "/podcasts/" . $object_id . "/"; // Return a site-relative RSS link
}

add_action('cmb2_init', 'pop_metaboxes');
function pop_metaboxes()
{
  // Define CMB2 metabox for the "channel" post type (podcast channel settings)
  $pop_podcast = new_cmb2_box([
    'id'            => 'pop_podcast',
    'title'         => esc_html__('Podcast Settings', 'podposter'),
    'object_types'  => ['channel'],
  ]);

  // Add field: Read-only RSS feed link
  $pop_podcast->add_field([
    'name'        => __('RSS Feed Link', 'podposter'),
    'description' => __("Use this RSS link to register your podcast.", 'podposter'),
    'id'          => 'pop_rss',
    'default_cb'  => 'pop_generate_rss_link',
    'type'        => 'text',
    'attributes'  => [
      'disabled' => true,
      'style'    => 'direction: ltr;',
    ],
  ]);

  // Add field: Podcast Description
  $pop_podcast->add_field([
    'name'        => __('Podcast Description', 'podposter'),
    'description' => __('Write a short description for your podcast.', 'podposter'),
    'id'          => 'pop_description',
    'type'        => 'textarea',
    'attributes'  => [
      'required' => 'required',
    ],
  ]);

  // Add field: Full iTunes-style categories with subcategories
  $pop_podcast->add_field([
    'name'     => __('Podcast Category', 'podposter'),
    'desc'     => __('Select a category that best fits your podcast.', 'podposter'),
    'id'       => 'pop_category',
    'type'     => 'select',
    'options'  => [
      'Arts' => __('Arts', 'podposter'),
      'Arts:Design' => __('Arts > Design', 'podposter'),
      'Arts:Fashion & Beauty' => __('Arts > Fashion & Beauty', 'podposter'),
      'Arts:Food' => __('Arts > Food', 'podposter'),
      'Arts:Performing Arts' => __('Arts > Performing Arts', 'podposter'),
      'Arts:Visual Arts' => __('Arts > Visual Arts', 'podposter'),
      'Business' => __('Business', 'podposter'),
      'Business:Careers' => __('Business > Careers', 'podposter'),
      'Business:Entrepreneurship' => __('Business > Entrepreneurship', 'podposter'),
      'Business:Investing' => __('Business > Investing', 'podposter'),
      'Business:Management' => __('Business > Management', 'podposter'),
      'Business:Marketing' => __('Business > Marketing', 'podposter'),
      'Business:Non-Profit' => __('Business > Non-Profit', 'podposter'),
      'Comedy' => __('Comedy', 'podposter'),
      'Education' => __('Education', 'podposter'),
      'Education:Courses' => __('Education > Courses', 'podposter'),
      'Education:How To' => __('Education > How To', 'podposter'),
      'Education:Language Learning' => __('Education > Language Learning', 'podposter'),
      'Education:Self-Improvement' => __('Education > Self-Improvement', 'podposter'),
      'Health & Fitness' => __('Health & Fitness', 'podposter'),
      'Health & Fitness:Fitness' => __('Health & Fitness > Fitness', 'podposter'),
      'Health & Fitness:Medicine' => __('Health & Fitness > Medicine', 'podposter'),
      'Health & Fitness:Mental Health' => __('Health & Fitness > Mental Health', 'podposter'),
      'Health & Fitness:Nutrition' => __('Health & Fitness > Nutrition', 'podposter'),
      'Health & Fitness:Sexuality' => __('Health & Fitness > Sexuality', 'podposter'),
      'History' => __('History', 'podposter'),
      'Kids & Family' => __('Kids & Family', 'podposter'),
      'Leisure' => __('Leisure', 'podposter'),
      'Leisure:Animation & Manga' => __('Leisure > Animation & Manga', 'podposter'),
      'Leisure:Automotive' => __('Leisure > Automotive', 'podposter'),
      'Leisure:Aviation' => __('Leisure > Aviation', 'podposter'),
      'Leisure:Crafts' => __('Leisure > Crafts', 'podposter'),
      'Leisure:Games' => __('Leisure > Games', 'podposter'),
      'Leisure:Hobbies' => __('Leisure > Hobbies', 'podposter'),
      'Leisure:Home & Garden' => __('Leisure > Home & Garden', 'podposter'),
      'Leisure:Video Games' => __('Leisure > Video Games', 'podposter'),
      'Music' => __('Music', 'podposter'),
      'News' => __('News', 'podposter'),
      'Religion & Spirituality' => __('Religion & Spirituality', 'podposter'),
      'Science' => __('Science', 'podposter'),
      'Society & Culture' => __('Society & Culture', 'podposter'),
      'Sports' => __('Sports', 'podposter'),
      'Technology' => __('Technology', 'podposter'),
      'True Crime' => __('True Crime', 'podposter'),
      'TV & Film' => __('TV & Film', 'podposter'),
    ],
    'attributes' => [
      'required' => 'required',
    ],
  ]);

  // Add field: Publisher Name
  $pop_podcast->add_field([
    'name'        => __('Publisher Name', 'podposter'),
    'description' => __('Name of the podcast publisher.', 'podposter'),
    'id'          => 'pop_author',
    'type'        => 'text',
    'attributes'  => [
      'required' => 'required',
    ],
  ]);

  // Add field: Copyright Notice
  $pop_podcast->add_field([
    'name'        => __('Copyright Notice', 'podposter'),
    'description' => __('Copyright notice for your podcast.', 'podposter'),
    'id'          => 'pop_copyright',
    'type'        => 'text',
  ]);

  // Add field: Podcast Language
  $pop_podcast->add_field([
    'name'     => __('Podcast Language', 'podposter'),
    'desc'     => __('Select the language of your podcast.', 'podposter'),
    'id'       => 'pop_language',
    'type'     => 'select',
    'default'  => 'en',
    'options' => [
      'en' => __('English', 'podposter'),
      'es' => __('Spanish', 'podposter'),
      'de' => __('German', 'podposter'),
      'fr' => __('French', 'podposter'),
      'it' => __('Italian', 'podposter'),
      'pt' => __('Portuguese', 'podposter'),
      'fa' => __('Persian', 'podposter'),
      'ar' => __('Arabic', 'podposter'),
      'ru' => __('Russian', 'podposter'),
      'tr' => __('Turkish', 'podposter'),
      'zh' => __('Chinese (Simplified)', 'podposter'),
      'ja' => __('Japanese', 'podposter'),
      'ko' => __('Korean', 'podposter'),
      'nl' => __('Dutch', 'podposter'),
      'sv' => __('Swedish', 'podposter'),
      'hi' => __('Hindi', 'podposter'),
      'pl' => __('Polish', 'podposter'),
      'th' => __('Thai', 'podposter'),
    ],

    'attributes' => [
      'required' => 'required',
    ],
  ]);

  // Add field: Explicit Content Checkbox
  $pop_podcast->add_field([
    'name'        => __('Explicit Content', 'podposter'),
    'description' => __('Mark if the content is explicit.', 'podposter'),
    'id'          => 'pop_explicit',
    'type'        => 'checkbox',
  ]);

  // Add field: Verification Email
  $pop_podcast->add_field([
    'name'        => __('Verification Email', 'podposter'),
    'description' => __('Email used for ownership verification.', 'podposter'),
    'id'          => 'pop_email',
    'type'        => 'text_email',
    'attributes'  => [
      'required' => 'required',
    ],
  ]);

  // Define meta box for 'episode' post type
  $pop_episode = new_cmb2_box([
    'id'            => 'pop_episode',
    'title'         => esc_html__('Episode Settings', 'podposter'),
    'object_types'  => ['episode'],
  ]);

  // Fetch all published podcast channels for the dropdown field
  $channels = get_posts([
    'post_type' => 'channel',
    'post_status' => 'publish',
    'numberposts' => -1,
  ]);
  $channel_options = [];
  foreach ($channels as $channel) {
    $channel_options[$channel->ID] = $channel->post_title;
  }

  // Add field: Select associated podcast channel
  $pop_episode->add_field([
    'name'     => __('Podcast Channel', 'podposter'),
    'desc'     => __('Select the channel this episode belongs to.', 'podposter'),
    'id'       => 'pop_channel',
    'type'     => 'select',
    'options'  => $channel_options,
    'attributes' => [
      'required' => 'required',
    ],
  ]);

  // Add field: Audio File URL
  $pop_episode->add_field([
    'name'     => __('Audio File URL', 'podposter'),
    'id'       => 'pop_audio_url',
    'type'     => 'text_url',
    'attributes' => [
      'required' => 'required',
    ],
  ]);

  // Add field: Audio Duration
  $pop_episode->add_field([
    'name' => __('Audio Duration (mm:ss)', 'podposter'),
    'id'   => 'pop_length',
    'type' => 'text_small',
  ]);

  // Add field: Audio File Size
  $pop_episode->add_field([
    'name' => __('Audio File Size (bytes)', 'podposter'),
    'id'   => 'pop_size',
    'type' => 'text_medium',
  ]);
}
