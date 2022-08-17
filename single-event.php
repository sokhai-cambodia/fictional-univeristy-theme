<?php
  
  get_header();

  while(have_posts()) {
    the_post(); 
    the_page_banner(); ?>

    <div class="container container--narrow page-section">
          <div class="metabox metabox--position-up metabox--with-home-link">
        <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('event'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Events Home</a> <span class="metabox__main"><?php the_title(); ?></span></p>
      </div>

      <div class="generic-content"><?php the_content(); ?></div>
      <?php 
        $related_programs = get_field('related_programs');
        if($related_programs) {
          echo "<h3>Related Programs</h3>";
          echo "<ul class='link-list min-list'>";
          foreach($related_programs as $related_program) { ?>
            <li>
                <a href="<?php echo get_the_permalink($related_program); ?>"><?php echo get_the_title($related_program); ?></a>
            </li>
        <?php 
          } 
          echo "</ul>";
        }
      ?>
    </div>
    
  <?php }

  get_footer();

?>