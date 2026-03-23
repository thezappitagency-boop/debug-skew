<?php
/**
 * Plugin Name:  Cool Slide – Skew Slider V2 (Blank / Shortcode)
 * Plugin URI:   https://github.com/your-repo/cool-slide-skew-widget-v2
 * Description:  Elementor widget — blank-shell version of the creative 3-D skew slider.
 *               Each slide shows only a background image; paste any shortcode or HTML
 *               into the "Slide Content / Shortcode" field to render it as an overlay.
 *               Navigation uses up / down chevron icons instead of Prev / Next text.
 * Version:      1.0.0
 * Requires PHP: 7.4
 * Requires at least: 5.9
 * Author:       Your Name
 * License:      GPL-2.0-or-later
 *
 * ---- Usage ----
 * 1. Upload this folder to /wp-content/plugins/
 * 2. Activate in WP Admin → Plugins
 * 3. Edit any page with Elementor, search "Skew Slider V2" in the widget panel
 *
 * ---- Notes ----
 * • Both V1 (cool-slide-skew-widget) and V2 can be active simultaneously —
 *   all constants and function names are prefixed with CSKW2 to avoid conflicts.
 * • The slide content field supports any shortcode registered on your site.
 *   do_shortcode() is called on save/display, not inside the Elementor editor preview.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CSKW2_VERSION', '1.0.0' );
define( 'CSKW2_FILE',    __FILE__ );
define( 'CSKW2_DIR',     plugin_dir_path( __FILE__ ) );
define( 'CSKW2_URL',     plugin_dir_url( __FILE__ ) );

// ── 1.  Register scripts & styles ────────────────────────────────────

add_action( 'wp_enqueue_scripts',                       'cskw2_register_assets' );
add_action( 'elementor/frontend/before_enqueue_styles', 'cskw2_register_assets' );

function cskw2_register_assets() {

    // GSAP core — reuse the theme's copy if already registered
    if ( ! wp_script_is( 'gsap', 'registered' ) ) {
        wp_register_script(
            'gsap',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',
            [],
            '3.12.5',
            true
        );
    }

    // GSAP Observer plugin (bundled — needed for wheel / touch navigation)
    if ( ! wp_script_is( 'gsap-observer', 'registered' ) ) {
        wp_register_script(
            'gsap-observer',
            CSKW2_URL . 'assets/js/Observer.min.js',
            [ 'gsap' ],
            '3.12.5',
            true
        );
    }

    // GSAP ScrollTrigger plugin (powers the sticky scroll-through-slides effect)
    if ( ! wp_script_is( 'gsap-scrolltrigger', 'registered' ) ) {
        wp_register_script(
            'gsap-scrolltrigger',
            'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',
            [ 'gsap' ],
            '3.12.5',
            true
        );
    }

    // Slider JS bundle (identical to V1 — navigation is handled via button classes)
    wp_register_script(
        'cool-slide-skew-slider-v2',
        CSKW2_URL . 'assets/js/skew-slider-bundle.js',
        [ 'gsap', 'gsap-scrolltrigger', 'gsap-observer', 'imagesloaded' ],
        CSKW2_VERSION,
        true
    );

    // Slider CSS
    wp_register_style(
        'cool-slide-skew-slider-v2',
        CSKW2_URL . 'assets/css/skew-slider.css',
        [],
        CSKW2_VERSION
    );
}

// ── 2.  Register Elementor widget ────────────────────────────────────

add_action( 'elementor/widgets/register', 'cskw2_register_widget' );

function cskw2_register_widget( $widgets_manager ) {
    require_once CSKW2_DIR . 'widgets/class-skew-slider-v2-widget.php';
    $widgets_manager->register( new \CoolSlideV2\SkewSliderV2Widget() );
}

// ── 3.  Friendly notice if Elementor is not active ───────────────────

add_action( 'admin_notices', 'cskw2_admin_notice' );

function cskw2_admin_notice() {
    if ( did_action( 'elementor/loaded' ) ) {
        return;
    }
    echo '<div class="notice notice-warning is-dismissible">
            <p><strong>Cool Slide – Skew Slider V2</strong> requires the
            <strong>Elementor</strong> page builder to be installed and active.</p>
          </div>';
}
