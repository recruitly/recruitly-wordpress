<?php
add_action('admin_menu', 'recruitly_wordpress_setup_menu');

function recruitly_wordpress_setup_menu()
{
    add_menu_page( 'Recruitly Jobs Admin', 'Recruitly', 'manage_options', 'recruitly/settings.php', 'recruitly_wordpress_settings', '', 2);

}