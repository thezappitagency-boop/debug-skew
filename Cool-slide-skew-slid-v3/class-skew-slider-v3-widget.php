
<?php
/**
 * Elementor Widget: Skew Slider V3
 *
 * Clean slate — no copyright, no social links, no CTA button chrome.
 * Just: background images + chevron navigation + slide counter.
 * Optional per-slide content/shortcode overlay (centered on both axes).
 *
 * @package CoolSlideV3
 */

namespace CoolSlideV3;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SkewSliderV3Widget extends \Elementor\Widget_Base {

    // ── Identity ─────────────────────────────────────────────────────

    public function get_name()  { return 'cool-slide-skew-slider-v3'; }
    public function get_title() { return esc_html__( 'Skew Slider V3', 'cool-slide-v3' ); }
    public function get_icon()  { return 'eicon-slider-full-screen'; }

    public function get_categories() {
        return [ 'basic', 'general' ];
    }

    // ── Asset dependencies ────────────────────────────────────────────

    public function get_script_depends() {
        return [ 'cool-slide-skew-slider-v3' ];
    }
    public function get_style_depends() {
        return [ 'cool-slide-skew-slider-v3' ];
    }

    // ── Controls ─────────────────────────────────────────────────────

    protected function register_controls() {

        $this->start_controls_section( 'section_slides', [
            'label' => esc_html__( 'Slides', 'cool-slide-v3' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control( 'image', [
            'label'       => esc_html__( 'Background Image', 'cool-slide-v3' ),
            'type'        => \Elementor\Controls_Manager::MEDIA,
            'default'     => [ 'url' => '' ],  // blank — no default image
            'description' => esc_html__( 'Full-bleed background for this slide.', 'cool-slide-v3' ),
        ] );

        $repeater->add_control( 'slide_content', [
            'label'       => esc_html__( 'Slide Content / Shortcode (optional)', 'cool-slide-v3' ),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 5,
            'default'     => '',
            'placeholder' => 'e.g.  [property_carousel]  or any HTML',
            'description' => esc_html__(
                'Leave blank for a fully image-only slide. Paste HTML or a shortcode — it is rendered centered over the slide on the front end. Shortcodes do NOT preview inside the Elementor editor; that is expected.',
                'cool-slide-v3'
            ),
        ] );

        $this->add_control( 'slides', [
            'label'       => esc_html__( 'Slides', 'cool-slide-v3' ),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'image' => [ 'url' => '' ], 'slide_content' => '' ],
                [ 'image' => [ 'url' => '' ], 'slide_content' => '' ],
                [ 'image' => [ 'url' => '' ], 'slide_content' => '' ],
            ],
            'title_field' => 'Slide',
        ] );

        $this->end_controls_section();
    }

    // ── Render (front end) ────────────────────────────────────────────

    protected function render() {
        $settings     = $this->get_settings_for_display();
        $slides       = $settings['slides'] ?? [];
        $slides_count = count( $slides );

        $chevron_up   = CSKW3_URL . 'assets/img/chevron-up.svg';
        $chevron_down = CSKW3_URL . 'assets/img/chevron-down.svg';
        ?>

        <div class=\"skew-slider-area cool-slide-v3\">

            <!-- Slides -->
            <div class=\"skew-slider-wrap\">
                <?php foreach ( $slides as $slide ) :
                    $img_url = ! empty( $slide['image']['url'] ) ? esc_url( $slide['image']['url'] ) : '';
                    $content = ! empty( $slide['slide_content'] ) ? $slide['slide_content'] : '';
                ?>
                <div class=\"skew-slider-item slide\">
                    <div class=\"slide__img\"
                         style=\"<?php echo $img_url ? 'background-image:url(' . $img_url . ')' : 'background-color:#111'; ?>\">
                    </div>
                    <?php if ( $content ) : ?>
                    <div class=\"skew-slider-content\">
                        <?php
                        // do_shortcode processes [shortcodes]. No wp_kses_post wrapper —
                        // that can corrupt attribute quotes inside shortcode strings.
                        echo do_shortcode( $content );
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation: chevron-up = Prev | chevron-down = Next -->
            <div class=\"skew-slider-arrow slides-nav\">
                <button class=\"skew-slider-prev d-flex align-items-center\"
                        aria-label=\"<?php esc_attr_e( 'Previous slide', 'cool-slide-v3' ); ?>\">
                    <img src=\"<?php echo esc_url( $chevron_up ); ?>\"
                         alt=\"\"
                         class=\"nav-chevron\"
                         aria-hidden=\"true\">
                </button>
                <button class=\"skew-slider-next d-flex align-items-center\"
                        aria-label=\"<?php esc_attr_e( 'Next slide', 'cool-slide-v3' ); ?>\">
                    <img src=\"<?php echo esc_url( $chevron_down ); ?>\"
                         alt=\"\"
                         class=\"nav-chevron\"
                         aria-hidden=\"true\">
                </button>
            </div>

            <!-- Slide counter -->
            <div class=\"slides-numbers-wrap\">
                <div class=\"slides-numbers\">
                    <span class=\"active text-1\">01</span>
                    <span class=\"text-2\">/</span>
                    <span class=\"text-3\"><?php echo str_pad( $slides_count, 2, '0', STR_PAD_LEFT ); ?></span>
                </div>
            </div>

        </div><!-- /.skew-slider-area.cool-slide-v3 -->

        <?php
    }

    // ── Editor preview ────────────────────────────────────────────────
    // Shortcodes are NOT executed in the editor — shown as plain text label.
    // Background images ARE shown so you can compose slide order visually.

    protected function content_template() {
        ?>
        <#
        var slides      = settings.slides || [];
        var slidesCount = slides.length;
        #>

        <div class=\"skew-slider-area cool-slide-v3\">
            <div class=\"skew-slider-wrap\">
                <# _.each( slides, function( slide, i ) {
                    var bgStyle = slide.image && slide.image.url
                        ? 'background-image:url(' + slide.image.url + ')'
                        : 'background-color:#111';
                #>
                <div class=\"skew-slider-item slide <# if (i===0) { #>slide--current<# } #>\">
                    <div class=\"slide__img\" style=\"{{ bgStyle }}\"></div>
                    <# if ( slide.slide_content ) { #>
                    <div class=\"skew-slider-content\" style=\"opacity:1;visibility:visible;\">
                        <p style=\"background:rgba(0,0,0,.5);color:#fff;font-size:12px;padding:8px 16px;display:inline-block;border-radius:3px;margin:0;\">
                            [Content preview: {{ slide.slide_content }}]
                        </p>
                    </div>
                    <# } #>
                </div>
                <# } ); #>
            </div>

            <div class=\"skew-slider-arrow slides-nav\">
                <button class=\"skew-slider-prev d-flex align-items-center\">
                    <span style=\"display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.15);\">&#8593;</span>
                </button>
                <button class=\"skew-slider-next d-flex align-items-center\">
                    <span style=\"display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:50%;background:rgba(255,255,255,.15);\">&#8595;</span>
                </button>
            </div>

            <div class=\"slides-numbers-wrap\">
                <div class=\"slides-numbers\">
                    <span class=\"active text-1\">01</span>
                    <span class=\"text-2\">/</span>
                    <span class=\"text-3\">0{{ slidesCount }}</span>
                </div>
            </div>
        </div>
        <?php
    }
}
