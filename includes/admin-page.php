<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
function eic_add_admin_menu() {
    add_menu_page('Element Inspector', 'Element Inspector', 'manage_options', 'element-inspector', 'eic_admin_page', 'dashicons-search', 100);
}
add_action('admin_menu', 'eic_add_admin_menu');

// Admin page content
function eic_admin_page() {
    if (isset($_POST['eic_save_custom_css'])) {
        check_admin_referer('eic_save_custom_css');
        $custom_css = sanitize_textarea_field($_POST['eic_custom_css']);
        update_option('eic_custom_css', $custom_css);
        echo '<div class="updated"><p>Custom CSS saved.</p></div>';
    }
    $custom_css = get_option('eic_custom_css', '');
    ?>
    <div class="wrap">
        <h1>Element Inspector and Customizer</h1>
        <p>Click the "Inspect Elements" button below to start inspecting elements on your site. You can hide elements or add custom CSS.</p>
        <h2>Custom CSS</h2>
        <form method="post" action="">
            <?php wp_nonce_field('eic_save_custom_css'); ?>
            <textarea id="eic_custom_css" name="eic_custom_css" rows="10" cols="50" class="large-text"><?php echo esc_textarea($custom_css); ?></textarea>
            <?php submit_button('Save Custom CSS'); ?>
        </form>
    </div>
    <?php
}
?>
