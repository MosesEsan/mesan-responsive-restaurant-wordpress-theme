<?php
/*
Plugin name: Restaurant Menu Plugin
Plugin URI: http://example.com/wordpress-extra-post-info
Description: A simple plugin to add extra info to posts.
Author: Moses Esan
Author URI: http://mosesesan.com
Version: 0.1
*/

/*=======================
1 - CREATE THE MENU AND ITS PAGE
========================*/
add_action('admin_menu', 'add_restaurant_menu');
if (!function_exists("extra_post_info_menu")) {
    function add_restaurant_menu(){
        add_menu_page('Food Menu', 'Food Menu', 'manage_options', 'food-menu', 'food_menu_page', null, 99);
    }
}

/*=======================
2 - CREATE THE MENU PAGE
========================*/
function food_menu_page(){
    $menu_sections = get_option( 'menu_sections' ); ?>
    <div class="wrap">
        <h1>Food Menu</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('food-menu-group'); //This outputs some hidden fields WordPress will use to save your data. //enders the hidden input fields and handles the security aspects
            do_settings_sections('food-menu-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Section 1</th>
                    <td><input type="text" name="menu_sections[1]"
                               value="<?php echo esc_attr($menu_sections["1"]); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Section 2</th>
                    <td><input type="text" name="menu_sections[2]"
                               value="<?php echo esc_attr($menu_sections["2"]); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Section 3</th>
                    <td><input type="text" name="menu_sections[3]"
                               value="<?php echo esc_attr($menu_sections["3"]); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Section 4</th>
                    <td><input type="text" name="menu_sections[4]"
                               value="<?php echo esc_attr($menu_sections["4"]); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Currency</th>
                    <td><input type="text" name="menu_currency"
                               value="<?php echo esc_attr(get_option('menu_currency')); ?>"/></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php }


/*=======================
3 - Link and register the fields
========================*/
add_action('admin_init', 'plugin_settings');
function plugin_settings(){
    register_setting('food-menu-group', 'menu_sections');
    register_setting('food-menu-group', 'menu_currency');
}

/*=======================
4 - CREATE CUSTOM POST TYPE - (OPTIONAL)
========================*/
add_action('init', 'create_custom_post');
function create_custom_post(){
    register_post_type('menu_item',
        array(
            'labels' => array(
                'name' => __('Menu Items'),
                'singular_name' => __('Menu Item'),
            ),
            'public' => true,
            'has_archive' => true
        )
    );
}


/*=======================
5 - ADD META BOX - (OPTIONAL)
========================*/
add_action('add_meta_boxes', 'add_price_meta_box');
function add_price_meta_box(){
    add_meta_box('section_meta_box', 'Menu Section', 'create_section_meta_box', 'menu_item', 'side', 'low');
    add_meta_box('price_meta_box', 'Price', 'create_price_meta_box', 'menu_item', 'side', 'low');
}

/*=======================
6 - CREATE THE META BOX - (OPTIONAL)
========================*/
function create_section_meta_box($post){
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    $section = get_post_meta($post->ID, 'menu_section', true);
    ?>
    <label for="menu_section">Add to Menu Section</label>
    <select name="menu_section" id="menu_section" class="postbox">
        <option value=""></option>

        <?php
        foreach (get_option('menu_sections') as $key => $value):?>
            <option value="<?= $key; ?>" <?php selected($section, $key); ?>><?= $value; ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}

function create_price_meta_box($post){
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    $price = get_post_meta($post->ID, 'price', true); ?>
    <label for="price">Enter Price</label>
    <input type="text" name="price" id="price" value="<?php echo $price; ?>"/>
    <?php
}

/*=======================
7 - SAVE META DATA - (OPTIONAL)
========================*/
add_action("save_post", "save_meta_box_data");
function save_meta_box_data($post_id, $post, $update){
    //verify nonce
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;

    //check autosave
    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;

    // check permission
    if (!current_user_can("edit_post", $post_id))
        return $post_id;

    //update
    if (isset($_POST["price"])) {
        // sanitize array
        $price = sanitize_text_field($_POST["price"]);
        update_post_meta($post_id, "price", $price);
    }

    if (isset($_POST["menu_section"])) {
        // sanitize array
        $menu_section = sanitize_text_field($_POST["menu_section"]);
        update_post_meta($post_id, "menu_section", $menu_section);
    }
}

//
//if (isset($_POST)) {
//// Now we have a final array we can store (or use however you want)!
//// Update option  --no need
//// to do any other formatting here. The values will be automatically serialized.
//    update_option('menu_sections', $arr_store_me);
//
//}