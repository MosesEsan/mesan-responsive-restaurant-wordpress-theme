<?php
/**
 * Created by PhpStorm.
 * User: mosesesan
 * Date: 02/07/2017
 * Time: 15:23
 */


/**
 * Customizer additions.
 */
require get_template_directory() . '/customizer.php';


// WordPress Titles
add_theme_support( 'title-tag' );

// Support Featured Images for Posts
add_theme_support( 'post-thumbnails' );

// Add scripts and stylesheets
function startwordpress_scripts() {
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array());
    wp_enqueue_style( 'index', get_template_directory_uri() . '/css/index.css' );
    wp_enqueue_style( 'bxslider', get_template_directory_uri() . '/jquery.bxslider/jquery.bxslider.css' );

    wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ), false, true );
    wp_enqueue_script( 'main', get_template_directory_uri() . '/js/main.js', array( 'jquery' ), false, true );
    wp_enqueue_script( 'filterizr', get_template_directory_uri() . '/js/filterizr/jquery.filterizr.min.js', array( 'jquery' ), false, true );
}

add_action( 'wp_enqueue_scripts', 'startwordpress_scripts' );



/*=======================
CREATE THE MENU AND ITS PAGE
========================*/
function custom_settings_add_menu() {
    add_theme_page( 'Custom Settings', 'Custom Settings', 'manage_options', 'custom-settings', 'custom_settings_page', null, 99 );
}

function custom_settings_page() { ?>
    <div class="wrap">
        <h1>Custom Settings</h1>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields( 'settings-group' );
            do_settings_sections( 'theme-options' ); //Prints out all settings sections added to a particular settings page
            submit_button();
            ?>
        </form>
    </div>
<?php }

add_action( 'admin_menu', 'custom_settings_add_menu' );




/*=======================
CREATE THE SECTION and FIELDS
========================*/
function custom_settings_page_setup() {
    /*=======================
    HOME
    ========================*/
//    add_settings_section( 'home', 'Home', null, 'theme-options' );
//
//    add_settings_field( "bg_image", "Background Image", "setting_bgimage", "theme-options", "home");
//    add_settings_field( 'tagline', 'Tagline', 'setting_tagline', 'theme-options', 'home' );
//    add_settings_field( 'phone', 'Phone Number', 'setting_phone', 'theme-options', 'home' );
//    add_settings_field( 'opening_hours', 'Opening Hours', 'setting_openinghours', 'theme-options', 'home' );
//
//    register_setting('settings-group', 'bg_image', 'validate_setting');
//    register_setting('settings-group', 'tagline');
//    register_setting('settings-group', 'phone');
//    register_setting('settings-group', 'opening_hours');

    /*=======================
    ABOUT
    ========================*/
//    add_settings_section( 'about', 'About', null, 'theme-options' );
//
//    add_settings_field( 'heading', 'Heading', 'setting_heading', 'theme-options', 'about' );
//    add_settings_field( 'subheading', 'Sub-Heading', 'setting_subheading', 'theme-options', 'about' );
//    add_settings_field( 'about_text', 'About Text', 'setting_about', 'theme-options', 'about' );
//    add_settings_field( "about_image", "Image", "setting_image", 'theme-options', "about");
//
//    register_setting('settings-group', 'heading');
//    register_setting('settings-group', 'subheading');
//    register_setting('settings-group', 'about_text');
//    register_setting('settings-group', 'about_image', 'validate_setting');

    /*=======================
    CONTACT
    ========================*/
//    add_settings_section( 'contact', 'Contact', null, 'theme-options' );
//
//    add_settings_field( 'address', 'Address', 'setting_address', 'theme-options', 'contact' );
//    add_settings_field( 'reservation_number', 'Reservation Number', 'setting_resnumber', 'theme-options', 'contact' );
//    add_settings_field( 'mon_fri', 'Weekday Opening Hours', 'setting_monfri', 'theme-options', 'contact' );
//    add_settings_field( "sat_sun", "Weekend Opening Hours", "setting_weekend", 'theme-options', "contact");
//
//    register_setting('settings-group', 'address');
//    register_setting('settings-group', 'reservation_number');
//    register_setting('settings-group', 'mon_fri');
//    register_setting('settings-group', 'sat_sun');

    /*=======================
    BLOG
    ========================*/
    add_settings_section( 'blog', 'Blog', null, 'theme-options' );

    add_settings_field( 'blog_heading', 'Heading', 'setting_blog_heading', 'theme-options', 'blog' );

    register_setting('settings-group', 'blog_heading');

    /*=======================
    Footer
    ========================*/
//    add_settings_section( 'footer', 'Footer', null, 'theme-options' );
//
//    add_settings_field( 'facebook', 'Facebook', 'setting_facebook', 'theme-options', 'footer' );
//    add_settings_field( 'twitter', 'Twitter', 'setting_twitter', 'theme-options', 'footer' );
//    add_settings_field( 'instagram', 'Instagram', 'setting_instagram', 'theme-options', 'footer' );
//    add_settings_field( "copyright", "Copyright", "setting_copyright", 'theme-options', "footer");
//
//    register_setting('settings-group', 'facebook');
//    register_setting('settings-group', 'twitter');
//    register_setting('settings-group', 'instagram');
//    register_setting('settings-group', 'copyright');
}

//HOME
//function setting_bgimage() {?><!--<input type="file" name="bg_image" /><img src="--><?php //$option = get_option('bg_image');  echo $option?><!--"/>--><?php //}
//function setting_tagline() { ?><!--<input type="text" name="tagline" id="tagline" value="--><?php //echo get_option( 'tagline' ); ?><!--" />--><?php //}
//function setting_phone() { ?><!--<input type="text" name="phone" id="phone" value="--><?php //echo get_option( 'phone' ); ?><!--" />--><?php //}
//function setting_openinghours() { ?><!-- <input type="text" name="opening_hours" id="opening_hours" value="--><?php //echo get_option( 'opening_hours' ); ?><!--" />--><?php //}

// ABOUT
//function setting_heading() { ?><!--<input type="text" name="heading" id="heading" value="--><?php //echo get_option( 'heading' ); ?><!--" />--><?php //}
//function setting_subheading() { ?><!--<input type="text" name="subheading" id="subheading" value="--><?php //echo get_option( 'subheading' ); ?><!--" />--><?php //}
//function setting_about() { ?><!--<textarea type="text" name="about_text" id="about_text">--><?php //echo get_option( 'about_text' ); ?><!--</textarea>--><?php //}
//function setting_image() {?><!--<input type="file" name="about_image" />--><?php //$option = get_option('about_image');  echo $option["about_image"];?><!----><?php //}

// CONTACT
//function setting_address() { ?><!--<textarea type="text" name="address" id="address">--><?php //echo get_option( 'address' ); ?><!--</textarea>--><?php //}
//function setting_resnumber() { ?><!--<input type="text" name="reservation_number" id="reservation_number" value="--><?php //echo get_option( 'reservation_number' ); ?><!--" />--><?php //}
//function setting_monfri() { ?><!--<input type="text" name="mon_fri" id="mon_fri" value="--><?php //echo get_option( 'mon_fri' ); ?><!--" />--><?php //}
//function setting_weekend() { ?><!--<input type="text" name="sat_sun" id="sat_sun" value="--><?php //echo get_option( 'sat_sun' ); ?><!--" />--><?php //}

// BLOG
function setting_blog_heading() { ?><input type="text" name="blog_heading" id="blog_heading" value="<?php echo get_option( 'blog_heading' ); ?>" /><?php }

//FOOTER
//function setting_facebook() { ?><!--<input type="text" name="facebook" id="facebook" value="--><?php //echo get_option( 'facebook' ); ?><!--" />--><?php //}
//function setting_twitter() { ?><!--<input type="text" name="twitter" id="twitter" value="--><?php //echo get_option( 'twitter' ); ?><!--" />--><?php //}
//function setting_instagram() { ?><!--<input type="text" name="instagram" id="instagram" value="--><?php //echo get_option( 'instagram' ); ?><!--" />--><?php //}
//function setting_copyright() { ?><!--<input type="text" name="copyright" id="copyright" value="--><?php //echo get_option( 'copyright' ); ?><!--" />--><?php //}

add_action( 'admin_init', 'custom_settings_page_setup' );









function new_nav_menu_items($items)
{
    $items = "";

    $args = array("post_type" => "page", "order" => "ASC", "orderby" => "menu_order");
    $the_query = new WP_Query($args);

    if($the_query->have_posts()):
        while($the_query->have_posts()):
            $the_query->the_post();
            $items .= '<li><a href="#post-'. get_the_ID() .'">' . get_the_title() . '</a></li>';
        endwhile;
    else:
        echo "";
    endif;
    return $items;
}
add_filter("wp_nav_menu_items", "new_nav_menu_items");








function validate_setting($plugin_options) {
    $keys = array_keys($_FILES);
    $i = 0;

    foreach ( $_FILES as $image ) {
        // if a files was upload
        if ($image['size']) {
            // if it is an image
            if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) {
                $upload_overrides = array( 'test_form' => false );
                // save the file, and store an array, containing its location in $file
                $file = wp_handle_upload( $image, $upload_overrides );

                if ( $file && ! isset( $file['error'] ) ) {
                    $plugin_options[$keys[$i]] = $file['url'];
                } else {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */
                    echo $file['error'];
                }
            } else {
                // Not an image.
                $plugin_options[$keys[$i]] = get_option($keys[$i]);
                // Die and let the user know that they made a mistake.
                wp_die('No image was uploaded.');
            }
        }   // Else, the user didn't upload a file. Retain the image that's already on file.
        else {
            $options = get_option('plugin_options');
            $plugin_options[$keys[$i]] = $options[$keys[$i]];
        }
        $i++;
    }


    return $plugin_options;
}




