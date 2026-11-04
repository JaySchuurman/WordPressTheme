
<?php

require_once get_template_directory() . '/dg-extras.php';

  wp_enqueue_style(
        'deathgrips-google-fonts',
        'https://fonts.googleapis.com/css2?family=Anton&family=Space+Mono:wght@400;700&display=swap',
        array(),
        null
    );
    
function deathgrips_enqueue_styles() {
    wp_enqueue_style(
        'deathgrips-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get('Version')
    );
}

add_action('wp_enqueue_scripts', 'deathgrips_enqueue_styles');
