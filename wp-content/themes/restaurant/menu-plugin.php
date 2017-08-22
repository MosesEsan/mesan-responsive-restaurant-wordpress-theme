<?php
/**
 * Created by PhpStorm.
 * User: mosesesan
 * Date: 06/07/2017
 * Time: 01:53
 */

function custom_settings_add_menu() {
    add_theme_page( 'Restaurant Menu', 'Restaurant Menu', 'manage_options', 'restaurant-menu', 'restaurant_menu_page', null, 99 );
}

function restaurant_menu_page() { ?>
    <div class="wrap">
        <h1>Custom Settings</h1>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields( 'settings-group' );
            do_settings_sections( 'theme-options' );
            submit_button();
            ?>
        </form>
    </div>
<?php }

add_action( 'admin_menu', 'custom_settings_add_menu' );

