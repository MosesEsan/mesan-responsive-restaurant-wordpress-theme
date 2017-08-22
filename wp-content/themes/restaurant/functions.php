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
//require get_template_directory() . '/menu-plugin.php';


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



/* Navbar */
function register_theme_menu() {
    register_nav_menu( 'primary', 'Main Navigation Menu' );
}
add_action( 'init', 'register_theme_menu' );









// Custom Post Type
//function create_my_custom_post() {
//    register_post_type( 'my-custom-post',
//        array(
//            'labels' => array(
//                'name' => __( 'My Custom Post' ),
//                'singular_name' => __( 'My Custom Post' ),
//            ),
//            'public' => true,
//            'has_archive' => true,
//            'supports' => array(
//                'title',
//                'editor',
//                'thumbnail',
//                'custom-fields'
//            )
//        ));
//}
//add_action( 'init', 'create_my_custom_post' );

