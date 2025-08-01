<?php

if (! defined('ABSPATH')) {
  exit;
}

// Register custom post types and taxonomy during WordPress 'init' action
add_action("init", function () {

  // Register the 'channel' post type – represents podcast channels
  register_post_type("channel", [
    'label'                 => __('Podcasts', 'podposter'),
    'description'           => __('Podcast channels', 'podposter'),
    'labels'                => [
      'name'                  => _x('Podcasts', 'Post Type General Name', 'podposter'),
      'singular_name'         => _x('Podcast', 'Post Type Singular Name', 'podposter'),
      'menu_name'             => __('Podcasts', 'podposter'),
      'name_admin_bar'        => __('Podcast', 'podposter'),
      'archives'              => __('Podcast Archives', 'podposter'),
      'attributes'            => __('Podcast Attributes', 'podposter'),
      'parent_item_colon'     => __('Parent Podcast:', 'podposter'),
      'all_items'             => __('All Podcasts', 'podposter'),
      'add_new_item'          => __('Add New Podcast', 'podposter'),
      'add_new'               => __('Add Podcast', 'podposter'),
      'new_item'              => __('New Podcast', 'podposter'),
      'edit_item'             => __('Edit Podcast', 'podposter'),
      'update_item'           => __('Update Podcast', 'podposter'),
      'view_item'             => __('View Podcast', 'podposter'),
      'view_items'            => __('View Podcasts', 'podposter'),
      'search_items'          => __('Search Podcasts', 'podposter'),
      'not_found'             => __('No podcasts found.', 'podposter'),
      'not_found_in_trash'    => __('No podcasts found in Trash.', 'podposter'),
      'featured_image'        => __('Podcast Cover Image', 'podposter'),
      'set_featured_image'    => __('Set Cover Image', 'podposter'),
      'remove_featured_image' => __('Remove Cover Image', 'podposter'),
      'use_featured_image'    => __('Use as Cover Image', 'podposter'),
      'insert_into_item'      => __('Insert into podcast', 'podposter'),
      'uploaded_to_this_item' => __('Uploaded to this podcast', 'podposter'),
      'items_list'            => __('Podcasts list', 'podposter'),
      'items_list_navigation' => __('Podcasts list navigation', 'podposter'),
      'filter_items_list'     => __('Filter podcasts list', 'podposter'),
    ],
    'supports'              => ['title', 'editor', 'thumbnail'],
    'taxonomies'            => ['podcast_category'],
    'hierarchical'          => false,
    'public'                => false,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 31,
    'menu_icon'             => "dashicons-microphone",
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => false,
    'capability_type'       => 'page',
  ]);

  // Register the 'episode' post type – represents podcast episodes
  register_post_type("episode", [
    'label'                 => __('Episodes', 'podposter'),
    'description'           => __('Podcast episodes', 'podposter'),
    'labels'                => [
      'name'                  => _x('Episodes', 'Post Type General Name', 'podposter'),
      'singular_name'         => _x('Episode', 'Post Type Singular Name', 'podposter'),
      'menu_name'             => __('Episodes', 'podposter'),
      'name_admin_bar'        => __('Episode', 'podposter'),
      'archives'              => __('Episode Archives', 'podposter'),
      'attributes'            => __('Episode Attributes', 'podposter'),
      'parent_item_colon'     => __('Parent Episode:', 'podposter'),
      'all_items'             => __('All Episodes', 'podposter'),
      'add_new_item'          => __('Add New Episode', 'podposter'),
      'add_new'               => __('Add Episode', 'podposter'),
      'new_item'              => __('New Episode', 'podposter'),
      'edit_item'             => __('Edit Episode', 'podposter'),
      'update_item'           => __('Update Episode', 'podposter'),
      'view_item'             => __('View Episode', 'podposter'),
      'view_items'            => __('View Episodes', 'podposter'),
      'search_items'          => __('Search Episodes', 'podposter'),
      'not_found'             => __('No episodes found.', 'podposter'),
      'not_found_in_trash'    => __('No episodes found in Trash.', 'podposter'),
      'featured_image'        => __('Episode Cover Image', 'podposter'),
      'set_featured_image'    => __('Set Cover Image', 'podposter'),
      'remove_featured_image' => __('Remove Cover Image', 'podposter'),
      'use_featured_image'    => __('Use as Cover Image', 'podposter'),
      'insert_into_item'      => __('Insert into episode', 'podposter'),
      'uploaded_to_this_item' => __('Uploaded to this episode', 'podposter'),
      'items_list'            => __('Episodes list', 'podposter'),
      'items_list_navigation' => __('Episodes list navigation', 'podposter'),
      'filter_items_list'     => __('Filter episodes list', 'podposter'),
    ],
    'supports'              => ['title', 'editor', 'thumbnail'],
    'taxonomies'            => [],
    'hierarchical'          => false,
    'public'                => false,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 32,
    'menu_icon'             => "dashicons-playlist-audio",
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => false,
    'capability_type'       => 'page',
  ]);

  // Register the 'podcast_category' taxonomy for organizing podcast channels
  register_taxonomy('podcast_category', ['channel'], [
    "hierarchical" => true,
    "labels" => [
      'name'                       => _x('Categories', 'Taxonomy General Name', 'podposter'),
      'singular_name'              => _x('Category', 'Taxonomy Singular Name', 'podposter'),
      'menu_name'                  => __('Categories', 'podposter'),
      'all_items'                  => __('All Categories', 'podposter'),
      'new_item_name'              => __('New Category Name', 'podposter'),
      'add_new_item'               => __('Add New Category', 'podposter'),
      'edit_item'                  => __('Edit Category', 'podposter'),
      'update_item'                => __('Update Category', 'podposter'),
      'view_item'                  => __('View Category', 'podposter'),
      'separate_items_with_commas' => __('Separate categories with commas', 'podposter'),
      'add_or_remove_items'        => __('Add or remove categories', 'podposter'),
      'choose_from_most_used'      => __('Choose from the most used categories', 'podposter'),
      'popular_items'              => __('Popular Categories', 'podposter'),
      'search_items'               => __('Search Categories', 'podposter'),
      'not_found'                  => __('No categories found.', 'podposter'),
      'no_terms'                   => __('No categories', 'podposter'),
      'items_list'                 => __('Categories list', 'podposter'),
      'items_list_navigation'      => __('Categories list navigation', 'podposter'),
    ],
  ]);
});
