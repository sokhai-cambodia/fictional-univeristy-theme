<?php
  
  get_header();
  
  while(have_posts()) {
    the_post(); 
    the_page_banner();?>

    <div class="container container--narrow page-section">
          <div class="metabox metabox--position-up metabox--with-home-link">
        <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Programs Home</a> <span class="metabox__main"><?php the_title(); ?></span></p>
      </div>

      <div class="generic-content"><?php the_content(); ?></div>
        <?php
          $professors = new WP_Query(array(
            'posts_per_page' => 5,
            'post_type' => 'professor',
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => array(
              array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => strval(get_the_ID()),
              )
            )
          ));
          if($professors->have_posts()) {
            echo '<hr class="section-break">';
            echo '<h2 class="headline headline--medium">' . get_the_title() . ' Professors</h2>';
    
            echo '<ul class="professor-cards">';
            while ( $professors->have_posts() ) {
              $professors->the_post();?>
              <li class="professor-card__list-item">
                <a class="professor-card" href="<?php the_permalink(); ?>">
                  <img class="professor-card__image" src="<?php the_post_thumbnail_url('professor_landscape') ?>">
                  <span class="professor-card__name"><?php the_title(); ?></span>
                </a>
              </li>
            <?php
            }
            echo '</ul>';
          }

          wp_reset_postdata();

          $today = date('Ymd');
          $upcomming_events = new WP_Query(array(
            'posts_per_page' => 2,
            'post_type' => 'event',
            'meta_key' => 'event_date',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
            'meta_query' => array(
              array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
              ),
              array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => strval(get_the_ID()),
              )
            )
          ));
          if($upcomming_events->have_posts()) {
            echo '<hr class="section-break">';
            echo '<h2 class="headline headline--small-plus t-center">Upcoming Events</h2>';
            while ( $upcomming_events->have_posts() ) {
              $upcomming_events->the_post();
              get_template_part('template-parts/content-event');
            }
          }

          wp_reset_postdata();
          $related_campuses = get_field('related_campus');

          if ($related_campuses) {
            echo '<hr class="section-break">';
            echo '<h2 class="headline headline--medium">' . get_the_title() . ' is Available At These Campuses:</h2>';

            echo '<ul class="min-list link-list">';
            foreach($related_campuses as $campus) {
              ?> <li><a href="<?php echo get_the_permalink($campus); ?>"><?php echo get_the_title($campus) ?></a></li> <?php
            }
            echo '</ul>';

          }
        ?>
    </div>
    
  <?php }

  get_footer();

?>