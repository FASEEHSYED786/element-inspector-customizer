<?php
/*
Plugin Name: Element Inspector and Customizer
Plugin URI: https://wordpress.org/plugins/element-inspector-customizer
Description: Allows users to inspect elements on their WordPress site, hide elements, and add custom CSS to any element.
Version: 1.0
Author: Syed Faseeh Ul Hassan
Author URI: https://syedfaseeh.com
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';

// Enqueue styles and scripts
function eic_enqueue_assets() {
    wp_enqueue_style('eic-styles', plugin_dir_url(__FILE__) . 'assets/css/styles.css');
    wp_enqueue_script('eic-inspector', plugin_dir_url(__FILE__) . 'assets/js/inspector.js', array('jquery'), null, true);
    wp_enqueue_script('eic-customizer', plugin_dir_url(__FILE__) . 'assets/js/customizer.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'eic_enqueue_assets');
add_action('wp_enqueue_scripts', 'eic_enqueue_assets');

// Add custom CSS from settings
function eic_custom_css() {
    $custom_css = get_option('eic_custom_css', '');
    if (!empty($custom_css)) {
        echo '<style type="text/css">' . esc_html($custom_css) . '</style>';
    }
}
add_action('wp_head', 'eic_custom_css');
add_action('admin_head', 'eic_custom_css');

// Add frontend toolbar button
function eic_add_toolbar_button($wp_admin_bar) {
    if (current_user_can('manage_options')) {
        $wp_admin_bar->add_node(array(
            'id'    => 'eic-inspect',
            'title' => 'Inspect Elements',
            'href'  => '#',
            'meta'  => array(
                'onclick' => 'toggleInspection(); return false;',
            ),
        ));
    }
}
add_action('admin_bar_menu', 'eic_add_toolbar_button', 100);

// Add frontend script for toggling inspection mode
function eic_frontend_scripts() {
    if (current_user_can('manage_options')) {
        ?>
        <script type="text/javascript">
            function toggleInspection() {
                if (!window.isInspecting) {
                    document.getElementById('eic-inspect-button').innerText = 'Stop Inspecting';
                    jQuery('body').on('mouseover.eic', '*', function(e) {
                        e.stopPropagation();
                        jQuery(this).addClass('eic-highlight');
                    }).on('mouseout.eic', '*', function(e) {
                        e.stopPropagation();
                        jQuery(this).removeClass('eic-highlight');
                    }).on('click.eic', '*', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var element = jQuery(this);
                        var action = prompt('Choose action: hide, color, bg-color, custom-css:', '');
                        if (action === 'hide') {
                            element.addClass('eic-hidden');
                            var customCSS = jQuery('#eic_custom_css').val();
                            customCSS += '\n' + element.prop('tagName').toLowerCase() + '.' + element.attr('class').split(' ').join('.') + ' { display: none !important; }';
                            jQuery('#eic_custom_css').val(customCSS);
                        } else if (action === 'color') {
                            var color = prompt('Enter text color:', '');
                            if (color) {
                                element.css('color', color).addClass('eic-custom-style');
                                var customCSS = jQuery('#eic_custom_css').val();
                                customCSS += '\n' + element.prop('tagName').toLowerCase() + '.' + element.attr('class').split(' ').join('.') + ' { color: ' + color + ' !important; }';
                                jQuery('#eic_custom_css').val(customCSS);
                            }
                        } else if (action === 'bg-color') {
                            var bgColor = prompt('Enter background color:', '');
                            if (bgColor) {
                                element.css('background-color', bgColor).addClass('eic-custom-style');
                                var customCSS = jQuery('#eic_custom_css').val();
                                customCSS += '\n' + element.prop('tagName').toLowerCase() + '.' + element.attr('class').split(' ').join('.') + ' { background-color: ' + bgColor + ' !important; }';
                                jQuery('#eic_custom_css').val(customCSS);
                            }
                        } else if (action === 'custom-css') {
                            var css = prompt('Enter custom CSS for this element:', '');
                            if (css) {
                                element.attr('style', css).addClass('eic-custom-style');
                                var customCSS = jQuery('#eic_custom_css').val();
                                customCSS += '\n' + element.prop('tagName').toLowerCase() + '.' + element.attr('class').split(' ').join('.') + ' { ' + css + ' }';
                                jQuery('#eic_custom_css').val(customCSS);
                            }
                        }
                        return false;
                    });
                } else {
                    document.getElementById('eic-inspect-button').innerText = 'Inspect Elements';
                    jQuery('body').off('mouseover.eic mouseout.eic click.eic');
                }
                window.isInspecting = !window.isInspecting;
            }

            jQuery(document).ready(function($) {
                $('body').append('<textarea id="eic_custom_css" style="display:none;">' + <?php echo json_encode(get_option('eic_custom_css', '')); ?> + '</textarea>');
                $('body').append('<button id="eic-inspect-button" class="button button-primary" style="display:none;">Inspect Elements</button>');
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'eic_frontend_scripts');
?>
