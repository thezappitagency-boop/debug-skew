<?php
/**
 * Plugin Name:  Cool Slide – Skew Slider
 * Plugin URI:   https://github.com/your-repo/cool-slide-skew-widget
 * Description:  Elementor widget for the creative 3-D skew slider from the Agntix / cool-slide theme. Add slides, customise labels and social links — no coding needed.
 * Version:      1.0.0
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Author:       Your Name
 * License:      GPL-2.0-or-later
 *
 * ---- Usage ----
 * 1. Upload this folder to /wp-content/plugins/
 * 2. Activate in WP Admin → Plugins
 * 3. Edit any page with Elementor, search "Skew Slider" in the widget panel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CSKW_VERSION', '1.0.0' );
define( 'CSKW_FILE',    __FILE__ );
define( 'CSKW_DIR',     plugin_dir_path( __FILE__ ) );
define( 'CSKW_URL',     plugin_dir_url( __FILE__ ) );

// ── 1.  Register scripts & styles ────────────────────────────────────

add_action( 'wp_enqueue_scripts',                      'cskw_register_assets' );
add_action( 'elementor/frontend/before_enqueue_styles', 'cskw_register_assets' );

function cskw_register_assets() {

    // GSAP core — use theme's registered copy if present, else load from CDN
    if ( ! wp_script_is( 'gsap', 'registered' ) ) {
        wp_register_script(
            'gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
            [],
            '3.12.5',
            true
        );
    }

    // GSAP Observer plugin (free — fallback when ScrollTrigger unavailable)
    if ( ! wp_script_is( 'gsap-observer', 'registered' ) ) {
        wp_register_script(
            'gsap-observer',
            CSKW_URL . 'assets/js/Observer.min.js',
            [ 'gsap' ],
            '3.12.5',
            true
        );
    }

    // GSAP ScrollTrigger plugin (free — powers the sticky-scroll-through-slides)
    if ( ! wp_script_is( 'gsap-scrolltrigger', 'registered' ) ) {
        wp_register_script(
            'gsap-scrolltrigger',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
            [ 'gsap' ],
            '3.12.5',
            true
        );
    }

    // Slider JS bundle
    wp_register_script(
        'cool-slide-skew-slider',
        CSKW_URL . 'assets/js/skew-slider-bundle.js',
        [ 'gsap', 'gsap-scrolltrigger', 'gsap-observer', 'imagesloaded' ],
        CSKW_VERSION,
        true          // load in footer
    );

    // Slider CSS
    wp_register_style(
        'cool-slide-skew-slider',
        CSKW_URL . 'assets/css/skew-slider.css',
        [],
        CSKW_VERSION
    );
}

// ── 2.  Register Elementor widget ────────────────────────────────────

add_action( 'elementor/widgets/register', 'cskw_register_widget' );

function cskw_register_widget( $widgets_manager ) {
    require_once CSKW_DIR . 'widgets/class-skew-slider-widget.php';
    $widgets_manager->register( new \CoolSlide\SkewSliderWidget() );
}

// ── 3.  Friendly notice if Elementor is not active ───────────────────

add_action( 'admin_notices', 'cskw_admin_notice' );

function cskw_admin_notice() {
    if ( did_action( 'elementor/loaded' ) ) {
        return;
    }
    echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Cool Slide – Skew Slider</strong> requires the <strong>Elementor</strong> page builder to be installed and active.</p>
          </div>';
}
