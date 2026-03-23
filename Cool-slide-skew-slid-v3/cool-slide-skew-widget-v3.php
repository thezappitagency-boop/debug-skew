
<?php
/**
 * Plugin Name:  Cool Slide – Skew Slider V3 (Clean / Navigation Only)
 * Plugin URI:   https://github.com/your-repo/cool-slide-skew-widget-v3
 * Description:  Elementor widget — completely blank skew slider. Each slide shows only
 *               a background image. No title, no social links, no CTA button chrome.
 *               Chevron navigation + slide counter included. Optionally add a centered
 *               content/shortcode overlay per slide via the \"Slide Content\" field.
 * Version:      3.0.0
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Author:       InmuebleLife
 * License:      GPL-2.0-or-later
 *
 * ---- Usage ----
 * 1. Upload this folder to /wp-content/plugins/ and activate
 * 2. In Elementor, search \"Skew Slider V3\" in the widget panel
 * 3. Add it to a FULL-WIDTH container with these settings:
 *      Layout  → Content Width: Full Width
 *      Layout  → Padding: 0 (all sides)
 *      Advanced → Overflow: Hidden  ← IMPORTANT
 *      (Do NOT set a min-height on the container — the widget sets 100vh itself)
 *
 * ---- Notes ----
 * • V1, V2, and V3 can all be active simultaneously — prefixes are unique.
 * • Shortcodes in the \"Slide Content\" field are processed with do_shortcode().
 * • Content is centered on both axes over the slide image.
 * • Leave \"Slide Content\" blank for a fully image-only slide (default).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CSKW3_VERSION', '3.0.0' );
define( 'CSKW3_FILE',    __FILE__ );
define( 'CSKW3_DIR',     plugin_dir_path( __FILE__ ) );
define( 'CSKW3_URL',     plugin_dir_url( __FILE__ ) );

// ── 1. Register scripts & styles ─────────────────────────────────────

add_action( 'wp_enqueue_scripts',                       'cskw3_register_assets' );
add_action( 'elementor/frontend/before_enqueue_styles', 'cskw3_register_assets' );

function cskw3_register_assets() {

    // GSAP core — reuse the theme's copy if already registered
    if ( ! wp_script_is( 'gsap', 'registered' ) ) {
        wp_register_script(
            'gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js',
            [],
            '3.12.2',
            true
        );
    }

    // GSAP Observer plugin
    if ( ! wp_script_is( 'gsap-observer', 'registered' ) ) {
        wp_register_script(
            'gsap-observer',
            CSKW3_URL . 'assets/js/Observer.min.js',
            [ 'gsap' ],
            '3.12.2',
            true
        );
    }

    // GSAP ScrollTrigger
    if ( ! wp_script_is( 'gsap-scrolltrigger', 'registered' ) ) {
        wp_register_script(
            'gsap-scrolltrigger',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js',
            [ 'gsap' ],
            '3.12.2',
            true
        );
    }

    // Slider JS bundle
    wp_register_script(
        'cool-slide-skew-slider-v3',
        CSKW3_URL . 'assets/js/skew-slider-bundle.js',
        [ 'gsap', 'gsap-scrolltrigger', 'gsap-observer', 'imagesloaded' ],
        CSKW3_VERSION,
        true
    );

    // Slider CSS
    wp_register_style(
        'cool-slide-skew-slider-v3',
        CSKW3_URL . 'assets/css/skew-slider.css',
        [],
        CSKW3_VERSION
    );
}

// ── 2. Register Elementor widget ──────────────────────────────────────

add_action( 'elementor/widgets/register', 'cskw3_register_widget' );

function cskw3_register_widget( $widgets_manager ) {
    require_once CSKW3_DIR . 'widgets/class-skew-slider-v3-widget.php';
    $widgets_manager->register( new \CoolSlideV3\SkewSliderV3Widget() );
}

// ── 3. Admin notice if Elementor is not active ────────────────────────

add_action( 'admin_notices', 'cskw3_admin_notice' );

function cskw3_admin_notice() {
    if ( did_action( 'elementor/loaded' ) ) {
        return;
    }
    echo '<div class=\"notice notice-warning is-dismissible\">
            <p><strong>Cool Slide – Skew Slider V3</strong> requires the
            <strong>Elementor</strong> page builder to be installed and active.</p>
          </div>';
}
