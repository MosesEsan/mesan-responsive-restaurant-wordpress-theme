<?php
/**
 * Created by PhpStorm.
 * User: mosesesan
 * Date: 02/07/2017
 * Time: 23:14
 */

/**
 * Create Logo Setting and Upload Control
 */
add_action('customize_register', 'your_theme_new_customizer_settings');
function your_theme_new_customizer_settings($wp_customize) {


    /*=======================
    WEBSITE INFO
    ========================*/
    // Add Home Section
    $wp_customize->add_section('website_settings', array(
        'title' => 'Website Settings',
        'description' => '',
        'priority' => 200,
    ));

    //Website Name
    $wp_customize->add_setting('restaurant_name', array("default" => "Restaurant Name"));
    $wp_customize->add_control('restaurant_name', array(
        'label'   => 'Restaurant Name',
        'section' => 'website_settings',
        'type'    => 'text',
    ));

    /*=======================
    HOME
    ========================*/
    // Add Home Section
    $wp_customize->add_section('home_settings', array(
        'title' => 'Home',
        'description' => '',
        'priority' => 201,
    ));

    //Background Image
    $wp_customize->add_setting('background_image', array("default" => get_bloginfo('template_directory')."/img/bg1.jpg"));
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'background_image',
        array(
            'label' => 'Background Image',
            'section' => 'home_settings',
            'settings' => 'background_image',
        ) ) );

    //Tagline
    $wp_customize->add_setting('tagline', array("default" => "Fine Mediterranean Cuisine"));
    $wp_customize->add_control('tagline', array(
        'label'   => 'Tagline',
        'section' => 'home_settings',
        'type'    => 'text',
    ));

    //Phone
    $wp_customize->add_setting('phone', array("default" => "(042) 435-1111"));
    $wp_customize->add_control('phone', array(
        'label'   => 'Phone',
        'section' => 'home_settings',
        'type'    => 'text',
    ));

    /*=======================
    ABOUT
    ========================*/
    // Add About Section
    $wp_customize->add_section('about_settings', array(
        'title' => 'About',
        'description' => '',
        'priority' => 202,
    ));

    //Heading
    $wp_customize->add_setting('heading', array("default" => "Our Story"));
    $wp_customize->add_control('heading', array(
        'label'   => 'Heading',
        'section' => 'about_settings',
        'type'    => 'text',
    ));

    //SubHeading
    $wp_customize->add_setting('subheading', array("default" => "Cooking Is The Art Of Adjustment"));
    $wp_customize->add_control('subheading', array(
        'label'   => 'Sub-Heading',
        'section' => 'about_settings',
        'type'    => 'text',
    ));

    //About Text
    $wp_customize->add_setting('about_text', array("default" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi."));
    $wp_customize->add_control('about_text', array(
        'label'   => 'About Text',
        'section' => 'about_settings',
        'type'    => 'textarea',
    ));

    //About Image
    $wp_customize->add_setting('about_image', array("default" => get_bloginfo('template_directory')."/img/grilled-oysters.jpg"));
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'about_image',
        array(
            'label' => 'Image',
            'section' => 'about_settings',
            'settings' => 'about_image',
        )));

    //Button Text
    $wp_customize->add_setting('button_text', array("default" => "Make A Reservation"));
    $wp_customize->add_control('button_text', array(
        'label'   => 'Button Text',
        'section' => 'about_settings',
        'type'    => 'text',
    ));

    //Button Link
    $wp_customize->add_setting('button_link', array("default" => "#"));
    $wp_customize->add_control('button_link', array(
        'label'   => 'Button Link',
        'section' => 'about_settings',
        'type'    => 'text',
    ));

    /*=======================
    OUR MENU
    ========================*/
    // Add Home Section
    $wp_customize->add_section('menu_settings', array(
        'title' => 'Menu',
        'description' => '',
        'priority' => 203,
    ));

    //Background Image
    $wp_customize->add_setting('menu_background_image', array("default" => get_bloginfo('template_directory')."/img/bg2.jpg"));
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'menu_background_image',
        array(
            'label' => 'Background Image',
            'section' => 'menu_settings',
            'settings' => 'menu_background_image',
        ) ) );

    //Heading
    $wp_customize->add_setting('menu_heading', array("default" => "Our Menu"));
    $wp_customize->add_control('menu_heading', array(
        'label'   => 'Heading',
        'section' => 'menu_settings',
        'type'    => 'text',
    ));


    /*=======================
    CONTACT
    ========================*/
    // Add Contact Section
    $wp_customize->add_section('contact_settings', array(
        'title' => 'Contact',
        'description' => '',
        'priority' => 204,
    ));

    //Address
    $wp_customize->add_setting('address', array("default" => "1600 Pennsylvania Ave NW, Washington, DC 20500, USA"));
    $wp_customize->add_control('address', array(
        'label'   => 'Address',
        'section' => 'contact_settings',
        'type'    => 'text',
    ));

    //Reservation Number
    $wp_customize->add_setting('reservation_number', array("default" => "(202) 456-1111"));
    $wp_customize->add_control('reservation_number', array(
        'label'   => 'Reservation Number',
        'section' => 'contact_settings',
        'type'    => 'text',
    ));

    //Weekday Opening Hours
    $wp_customize->add_setting('weekday', array("default" => "09:00 - 20:00"));
    $wp_customize->add_control('weekday', array(
        'label'   => 'Weekday Opening Hours',
        'section' => 'contact_settings',
        'type'    => 'text',
    ));

    //Weekend Opening Hours
    $wp_customize->add_setting('weekend', array("default" => "09:00 - 22:00"));
    $wp_customize->add_control('weekend', array(
        'label'   => 'Weekend Opening Hours',
        'section' => 'contact_settings',
        'type'    => 'text',
    ));

    /*=======================
    OUR Blog
    ========================*/
    // Add Home Section
    $wp_customize->add_section('blog_settings', array(
        'title' => 'Blog',
        'description' => '',
        'priority' => 205,
    ));

    //Heading
    $wp_customize->add_setting('blog_heading', array("default" => "Our Blog"));
    $wp_customize->add_control('blog_heading', array(
        'label'   => 'Heading',
        'section' => 'blog_settings',
        'type'    => 'text',
    ));

    /*=======================
    FOOTER
    ========================*/
    // Add Footer Section
    $wp_customize->add_section('footer_settings_section', array(
        'title' => 'Footer',
        'description' => '',
        'priority' => 206,
    ));

    //Social Links
    $wp_customize->add_setting('facebook');
    $wp_customize->add_control('facebook', array(
        'label'   => 'Facebook',
        'section' => 'footer_settings_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('twitter');
    $wp_customize->add_control('twitter', array(
        'label'   => 'Twitter',
        'section' => 'footer_settings_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('instagram');
    $wp_customize->add_control('instagram', array(
        'label'   => 'Instagram',
        'section' => 'footer_settings_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('instagram');
    $wp_customize->add_control('instagram', array(
        'label'   => 'Instagram',
        'section' => 'footer_settings_section',
        'type'    => 'text',
    ));

    $wp_customize->add_setting('copyright');
    $wp_customize->add_control('copyright', array(
        'label'   => 'Copyright',
        'section' => 'footer_settings_section',
        'type'    => 'text',
    ));

    //Background Color
    $wp_customize->add_setting('background_color', array('default'=> 'rgb(25,27,29)') );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
        'label'   => 'Footer Color Setting',
        'section' => 'footer_settings_section',
        'settings'   => 'background_color',
    )));

    //Image
    $wp_customize->add_setting('footer_logo');
    $wp_customize->add_control(new WP_Customize_Upload_Control($wp_customize,'footer_logo',array(
        'label'      => __('Footer Logo', 'mytheme'),
        'section'    => 'footer_settings_section',
        'settings'   => 'footer_logo',
    )));

}

