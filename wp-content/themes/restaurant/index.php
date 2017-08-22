<?php get_header(); ?>

<!-- Begin page content -->
<!--Home -->
<section id="home" class="">
    <div class="home_container container-fluid">
        <div class="home_content row" style="background-image: url(<?php echo get_theme_mod( 'background_image' ) ?>)">
            <div class="overlay home-overlay"></div>

            <div class="content">
                <div class="home_text">
                    <p class="welcome">Welcome</p>
                    <h1><?php echo get_theme_mod('tagline'); ?></h1>

                    <div class="info">
                        <p>Book a table directly</p>
                        <p class="phone"><?php echo get_theme_mod('phone'); ?></p>
                        <p class="opening-hours">
                            Opening Hours: <br>
                            <span>
                                Friday - Sunday: <?php echo get_theme_mod('weekday'); ?>
                            </span><br>
                            <span>
                                Monday - Thursday:
                                <?php echo get_theme_mod('weekend'); ?></span>
                        </p>
                    </div>
                </div>
            </div>

        </div>


    </div>
</section>



<!-- About -->
<section id="abouts">
    <div class="container">

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
            </div>
        </div>

        <div class="row abouts">

            <div class="col-sm-6 about-item about-text">
                <div class="content">
                    <h2 class="section-heading"><?php echo get_theme_mod('heading'); ?></h2>
                    <h4><?php echo get_theme_mod('subheading'); ?></h4>
                    <p class="section-text"><?php echo get_theme_mod('about_text'); ?></p>

                    <a class="button-link" href="<?php echo get_theme_mod('button_link'); ?>">
                        <button class="send-btn" type="submit"><?php echo get_theme_mod('button_text'); ?></button>
                    </a>
                </div>
            </div>

            <div class="col-sm-6 about-item">
                <div class="content">
                    <img src="<?php echo get_theme_mod('about_image'); ?>">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Menu -->
<section id="menu" style="background-image: url(<?php echo get_theme_mod( 'menu_background_image' ) ?>)">
    <div class="overlay"></div>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h2 class="section-subheading">
                    <?php echo get_theme_mod( 'menu_heading' ); ?>
                </h2>
            </div>
        </div>

        <?php if (count(get_option('menu_sections')) > 0) {?>

        <!-- Filter Controls -->
        <div class="row simplefilter">
            <?php
            $i = 0;
            foreach (get_option('menu_sections') as $key => $value):?>
                <div class="col-xs-12 col-sm-2 <?php if ($i == 0) {?> col-sm-offset-2 active <?php }?>" data-filter="<?=$key?>">
                    <p><?php echo $value; $i++; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row menu filtr-container">
            <?php
            $args = array('post_type' => 'menu_item', 'posts_per_page' => 50);
            $loop = new WP_Query( $args );

            if ( $loop->have_posts() ) : while ( $loop->have_posts() ) : $loop->the_post();
                $menu_section = get_post_meta( $post->ID, 'menu_section', true);

                if (!empty($menu_section)) :
                ?>
                <div class="col-sm-6 menu-item filtr-item" data-category="<?= $menu_section ?>">
                    <div class="content">
                        <h4><?php the_title() ?></h4>
                        <?php the_content() ?>
                        <?php
                            $price = get_post_meta( $post->ID, 'price', true);
                            if($price){?>
                                <p class="menu-price"><?= get_option('menu_currency').$price; ?></p>
                            <?php } ?>
                    </div>
                </div>
                <?php endif; endwhile;
            endif; wp_reset_postdata();  ?>
        </div>

        <?php } ?>
    </div>
</section>

<!-- Reservation -->
<section id="reservation-contact">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h2 class="section-subheading">
                    Contact Us
                </h2>
            </div>
        </div>

        <div class="row reservation-contact">

            <div class="col-sm-6 reservation">
                <div class="content">
                    <form class="contact-form" action="#" method="POST">
                        <input id="name" name="name" type="text" value="" size="30" placeholder="Name">
                        <input id="email" name="email" type="email" value="" size="30" placeholder="Email">
                        <textarea id="message" name="message" rows="10" cols="30" placeholder="Message"></textarea>
                        <input class="send-btn" type="submit" value="Send">
                    </form>
                </div>
            </div>

            <div class="col-sm-6 contact">
                <div class="content">
                    <div>
                        <h4>Address:</h4>
                        <p class="address"><?php echo get_theme_mod( 'address' ); ?></p>
                    </div>
                    <div>
                        <h4>Reservation:</h4>
                        <p class="tel"><?php echo get_theme_mod( 'reservation_number' ); ?></p>
                    </div>
                    <div>
                        <h4>Opening Hours:</h4>
                        <p class="hours">
                            Friday, Saturday and Sunday:<br>
                            <?php echo get_theme_mod( 'weekend' ); ?>
                        </p>
                        <p class="hours">Monday, Tuesday, Wednesday and Thursday:<br>
                            <?php echo get_theme_mod( 'weekday' ); ?>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Blog -->
<section id="blog">
    <div class="container">

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h2 class="section-subheading"><?php echo get_theme_mod( 'blog_heading' ); ?></h2>
            </div>
        </div>

        <div class="row blog">
            <?php

            if (have_posts() ) : while ( have_posts() ) : the_post();?>

                <div class="col-sm-6 col-md-4 blog-post">
                    <div class="content">
                        <?php if ( has_post_thumbnail() ) { the_post_thumbnail(); }else{?>
                        <img src="http://via.placeholder.com/800x400"/>
                        <?php } ?>
                        <h4><a href='<?php the_permalink() ?>'><?php the_title() ?></a></h4>
                        <!--                        <p class="blog-post-meta">--><?php //the_date(); ?><!-- by <a href="#">--><?php //the_author(); ?><!--</a></p>-->
                        <p><?php the_content(); ?></p>
                    </div>
                </div><!-- /.blog-post -->

            <?php endwhile;
            else: ?>
                <p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
            <?php endif; wp_reset_postdata();  ?>
        </div>
    </div>
</section>


<?php get_footer(); ?>