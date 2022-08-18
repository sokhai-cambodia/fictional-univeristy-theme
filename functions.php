<?php
require get_theme_file_path('/includes/rest_api.php');

function the_page_banner($args = null) {
  if(!$args['title']) {
    $args['title'] = get_the_title();
  }

  if(!$args['subtitle']) {
    $args['subtitle'] = get_field('page_banner_subtitle');
  }

  if(!$args['banner_image']) {
    $page_banner_image = get_field('page_banner_background_image');
    if($page_banner_image && !is_home() && !is_archive()) {
      $args['banner_image'] = $page_banner_image['sizes']['page_banner'];
    } else {
      $args['banner_image'] = get_theme_file_uri('/images/ocean.jpg');
    }
  }
?>
  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?= $args['banner_image'] ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?= $args['title'] ?></h1>
      <div class="page-banner__intro">
        <p><?= $args['subtitle'] ?></p>
      </div>
    </div>  
  </div>
<?php
}

function university_files() {
  wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key='.GOOGLE_API_KEY, NULL, '1.0', true);
  wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
  wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

  wp_localize_script('main-university-js', 'universityData', array(
    'root_url' => get_site_url()
  ));
}

add_action('wp_enqueue_scripts', 'university_files');

function university_features() {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_image_size('professor_landscape', 400, 260, true);
  add_image_size('professor_portrait', 480, 650, true);
  add_image_size('page_banner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');

function university_post_types() {

  register_post_type('campus', array(
    'show_in_rest' => true,
    'supports' => array('title', 'editor'),
    'rewrite' => array('slug' => 'campuses'),
    'has_archive' => true,
    'public' => true,
    'labels' => array(
      'name' => 'Campuses',
      'singular_name' => 'Campus',
      'add_new_item' => 'Add Campus',
      'edit_item' => 'Edit Campuses',
      'all_items' => 'All Campuss',
    ),
    'menu_icon' => 'dashicons-location-alt'
  ));

  register_post_type('event', array(
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'excerpt'),
    'rewrite' => array('slug' => 'events'),
    'public' => true,
    'has_archive' => true,
    'labels' => array(
      'name' => 'Events',
      'singular_name' => 'Event',
      'add_new_item' => 'Add Event',
      'edit_item' => 'Edit Event',
      'all_items' => 'All Events',
    ),
    'menu_icon' => 'dashicons-calendar'
  ));

  register_post_type('program', array(
    'show_in_rest' => true,
    'supports' => array('title', 'editor'),
    'rewrite' => array('slug' => 'programs'),
    'public' => true,
    'has_archive' => true,
    'labels' => array(
      'name' => 'Programs',
      'singular_name' => 'Program',
      'add_new_item' => 'Add Program',
      'edit_item' => 'Edit Program',
      'all_items' => 'All Programs',
    ),
    'menu_icon' => 'dashicons-awards'
  ));

  register_post_type('professor', array(
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'thumbnail'),
    'public' => true,
    'labels' => array(
      'name' => 'Professors',
      'singular_name' => 'Professor',
      'add_new_item' => 'Add Professor',
      'edit_item' => 'Edit Professor',
      'all_items' => 'All Professors',
    ),
    'menu_icon' => 'dashicons-welcome-learn-more'
  ));

}

add_action('init', 'university_post_types');


function university_adjust_queries($query) {
  if (!is_admin() && is_post_type_archive('event') && $query->is_main_query()) {
    $today = date('Ymd');
    $query->set('meta_key', 'event_date');
    $query->set('orderby', 'meta_value_num');
    $query->set('order', 'ASC');
    $query->set('meta_query', array(
      array(
        'key' => 'event_date',
        'compare' => '>=',
        'value' => $today,
        'type' => 'numeric'
      )
    ));
  }

  if (!is_admin() && is_post_type_archive('program') && $query->is_main_query()) {
    $query->set('posts_per_page', -1);
    $query->set('orderby', 'title');
    $query->set('order', 'ASC');
  }

  if (!is_admin() && is_post_type_archive('campus') && $query->is_main_query()) {
    $query->set('posts_per_page', -1);
  }
}

add_action('pre_get_posts', 'university_adjust_queries');

function google_map_api($api) {
	$api['key'] = GOOGLE_API_KEY;
	return $api;
}

add_filter('acf/fields/google_map/api', 'google_map_api');