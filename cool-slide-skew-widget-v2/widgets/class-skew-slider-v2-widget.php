<?php
/**
 * Elementor Widget: Skew Slider V2 (Blank / Shortcode Shell)
 *
 * Differences from V1:
 *  • Repeater has only "Background Image" + "Slide Content / Shortcode" fields
 *    (no category, title lines, or link per slide)
 *  • Navigation uses up / down chevron icons instead of Prev / Next text
 *  • Slide content is rendered via do_shortcode() on the front end
 *
 * @package CoolSlideV2
 */

namespace CoolSlideV2;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SkewSliderV2Widget extends \Elementor\Widget_Base {

    // ── Identity ─────────────────────────────────────────────────────

    public function get_name()  { return 'cool-slide-skew-slider-v2'; }
    public function get_title() { return esc_html__( 'Skew Slider V2', 'cool-slide-v2' ); }
    public function get_icon()  { return 'eicon-slider-full-screen'; }

    public function get_categories() {
        return [ 'basic', 'general' ];
    }

    // ── Asset dependencies ────────────────────────────────────────────

    public function get_script_depends() {
        return [ 'cool-slide-skew-slider-v2' ];
    }
    public function get_style_depends() {
        return [ 'cool-slide-skew-slider-v2' ];
    }

    // ── Controls (panel UI) ───────────────────────────────────────────

    protected function register_controls() {

        /* ── SECTION: Slides ── */
        $this->start_controls_section( 'section_slides', [
            'label' => esc_html__( 'Slides', 'cool-slide-v2' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control( 'image', [
            'label'   => esc_html__( 'Background Image', 'cool-slide-v2' ),
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => 'https://picsum.photos/seed/csl1/1920/1080',
            ],
        ] );

        $repeater->add_control( 'shortcode', [
            'label'       => esc_html__( 'Slide Content / Shortcode', 'cool-slide-v2' ),
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'rows'        => 4,
            'default'     => '',
            'placeholder' => 'e.g. [my_shortcode param="value"]',
            'description' => esc_html__(
                'Paste any shortcode or raw HTML. It will be rendered as an overlay inside the slide on the front end. Leave blank for a fully image-only slide.',
                'cool-slide-v2'
            ),
        ] );

        $this->add_control( 'slides', [
            'label'       => esc_html__( 'Slides', 'cool-slide-v2' ),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'image' => [ 'url' => 'https://picsum.photos/seed/csl-s1/1920/1080' ], 'shortcode' => '' ],
                [ 'image' => [ 'url' => 'https://picsum.photos/seed/csl-s2/1920/1080' ], 'shortcode' => '' ],
                [ 'image' => [ 'url' => 'https://picsum.photos/seed/csl-s3/1920/1080' ], 'shortcode' => '' ],
                [ 'image' => [ 'url' => 'https://picsum.photos/seed/csl-s4/1920/1080' ], 'shortcode' => '' ],
                [ 'image' => [ 'url' => 'https://picsum.photos/seed/csl-s5/1920/1080' ], 'shortcode' => '' ],
            ],
            'title_field' => 'Slide',
        ] );

        $this->end_controls_section();

        /* ── SECTION: Footer / Social / CTA ── */
        $this->start_controls_section( 'section_footer', [
            'label' => esc_html__( 'Footer & Social', 'cool-slide-v2' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'copyright_text', [
            'label'       => esc_html__( 'Copyright / CTA line (supports HTML)', 'cool-slide-v2' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'Have a project in mind? <a href="#">Let\'s Talk.</a>',
            'label_block' => true,
        ] );

        $social_repeater = new \Elementor\Repeater();
        $social_repeater->add_control( 'label', [
            'label'   => esc_html__( 'Label', 'cool-slide-v2' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Fb',
        ] );
        $social_repeater->add_control( 'url', [
            'label'   => esc_html__( 'URL', 'cool-slide-v2' ),
            'type'    => \Elementor\Controls_Manager::URL,
            'default' => [ 'url' => '#' ],
        ] );

        $this->add_control( 'social_links', [
            'label'       => esc_html__( 'Social Links (displayed vertically on left)', 'cool-slide-v2' ),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $social_repeater->get_controls(),
            'default'     => [
                [ 'label' => 'Fb', 'url' => [ 'url' => '#' ] ],
                [ 'label' => 'In', 'url' => [ 'url' => '#' ] ],
                [ 'label' => 'Be', 'url' => [ 'url' => '#' ] ],
            ],
            'title_field' => '{{{ label }}}',
        ] );

        $this->add_control( 'cta_text', [
            'label'       => esc_html__( 'CTA Button Text (upper right)', 'cool-slide-v2' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'View All Listings',
            'placeholder' => 'e.g. View All Listings',
            'separator'   => 'before',
        ] );

        $this->add_control( 'cta_url', [
            'label'         => esc_html__( 'CTA Button URL', 'cool-slide-v2' ),
            'type'          => \Elementor\Controls_Manager::URL,
            'placeholder'   => 'https://yoursite.com/listings',
            'show_external' => true,
            'default'       => [ 'url' => '' ],
        ] );

        $this->end_controls_section();
    }

    // ── Render (front-end) ────────────────────────────────────────────

    protected function render() {
        $settings     = $this->get_settings_for_display();
        $slides       = $settings['slides'] ?? [];
        $slides_count = count( $slides );
        $copyright    = $settings['copyright_text'] ?? '';
        $socials      = $settings['social_links']   ?? [];
        $cta_text     = $settings['cta_text']        ?? '';
        $cta_href     = ! empty( $settings['cta_url']['url'] )         ? esc_url( $settings['cta_url']['url'] ) : '#';
        $cta_target   = ! empty( $settings['cta_url']['is_external'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
        $chevron_up   = CSKW2_URL . 'assets/img/chevron-up.svg';
        $chevron_down = CSKW2_URL . 'assets/img/chevron-down.svg';
        ?>

        <style>
            /* V2 chevron icon nav overrides (scoped to this widget instance) */
            .cool-slide-v2 .skew-slider-arrow button { gap: 0; }
            .cool-slide-v2 .nav-chevron {
                width: 44px;
                height: 44px;
                display: block;
                transition: transform 0.35s ease;
                filter: brightness(0) invert(1);
            }
            .cool-slide-v2 .skew-slider-prev:hover .nav-chevron { transform: translateY(-6px); }
            .cool-slide-v2 .skew-slider-next:hover .nav-chevron { transform: translateY( 6px); }
        </style>

        <div class="skew-slider-area cool-slide-v2">

            <!-- Slides -->
            <div class="skew-slider-wrap">
                <?php foreach ( $slides as $slide ) :
                    $img_url   = ! empty( $slide['image']['url'] ) ? esc_url( $slide['image']['url'] ) : '';
                    $shortcode = ! empty( $slide['shortcode'] )    ? $slide['shortcode']               : '';
                ?>
                <div class="skew-slider-item slide">
                    <div class="slide__img"
                         style="background-image: url('<?php echo $img_url; ?>')">
                    </div>
                    <?php if ( $shortcode ) : ?>
                    <div class="skew-slider-content">
                        <?php echo do_shortcode( wp_kses_post( $shortcode ) ); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Copyright / CTA line (top centre) -->
            <?php if ( $copyright ) : ?>
            <div class="tp-portfolio-slider__copyright">
                <p class="tp-el-sec-text"><?php echo wp_kses_post( $copyright ); ?></p>
            </div>
            <?php endif; ?>

            <!-- CTA Button (upper right) -->
            <?php if ( $cta_text ) : ?>
            <div class="skew-slider-cta">
                <a href="<?php echo $cta_href; ?>"<?php echo $cta_target; ?> class="skew-slider-cta__btn">
                    <?php echo esc_html( $cta_text ); ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- Social links (left side, reads bottom-to-top) -->
            <?php if ( ! empty( $socials ) ) : ?>
            <div class="tp-portfolio-slider__social tp-el-social">
                <?php foreach ( $socials as $link ) :
                    $href  = ! empty( $link['url']['url'] )         ? esc_url( $link['url']['url'] ) : '#';
                    $ext   = ! empty( $link['url']['is_external'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
                    $label = ! empty( $link['label'] )              ? esc_html( $link['label'] )     : '';
                ?>
                <a href="<?php echo $href; ?>"<?php echo $ext; ?>><?php echo $label; ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Navigation: chevron-up = Prev (bottom-left)
                             chevron-down = Next (bottom-right) -->
            <div class="skew-slider-arrow slides-nav">
                <button class="skew-slider-prev d-flex align-items-center"
                        aria-label="<?php esc_attr_e( 'Previous slide', 'cool-slide-v2' ); ?>">
                    <img src="<?php echo esc_url( $chevron_up ); ?>"
                         alt="<?php esc_attr_e( 'Previous slide', 'cool-slide-v2' ); ?>"
                         class="nav-chevron">
                </button>
                <button class="skew-slider-next d-flex align-items-center"
                        aria-label="<?php esc_attr_e( 'Next slide', 'cool-slide-v2' ); ?>">
                    <img src="<?php echo esc_url( $chevron_down ); ?>"
                         alt="<?php esc_attr_e( 'Next slide', 'cool-slide-v2' ); ?>"
                         class="nav-chevron">
                </button>
            </div>

            <!-- Slide counter (right side, with decorative lines) -->
            <div class="slides-numbers-wrap">
                <div class="slides-numbers">
                    <span class="active text-1">01</span>
                    <span class="text-2">/</span>
                    <span class="text-3"><?php echo str_pad( $slides_count, 2, '0', STR_PAD_LEFT ); ?></span>
                </div>
            </div>

        </div><!-- /.skew-slider-area.cool-slide-v2 -->

        <?php
    }

    // ── Editor placeholder (Elementor live preview) ───────────────────
    // Note: shortcodes are not executed in the editor preview (expected behaviour).
    // The shortcode string is shown as plain text so editors can verify the value.

    protected function content_template() {
        ?>
        <#
        var slides       = settings.slides || [];
        var slidesCount  = slides.length;
        var copyright    = settings.copyright_text || '';
        var socials      = settings.social_links || [];
        #>

        <div class="skew-slider-area cool-slide-v2">
            <div class="skew-slider-wrap">
                <# _.each( slides, function( slide, i ) { #>
                <div class="skew-slider-item slide <# if (i===0) { #>slide--current<# } #>">
                    <div class="slide__img"
                         style="background-image: url('{{ slide.image.url }}')">
                    </div>
                    <# if ( slide.shortcode ) { #>
                    <div class="skew-slider-content">
                        <p style="color:#fff;font-size:12px;padding:8px 12px;background:rgba(0,0,0,.45);display:inline-block;">
                            [shortcode preview: {{ slide.shortcode }}]
                        </p>
                    </div>
                    <# } #>
                </div>
                <# } ); #>
            </div>

            <# if ( copyright ) { #>
            <div class="tp-portfolio-slider__copyright">
                <p>{{{ copyright }}}</p>
            </div>
            <# } #>

            <div class="tp-portfolio-slider__social">
                <# _.each( socials, function( link ) { #>
                <a href="{{ link.url.url }}">{{ link.label }}</a>
                <# } ); #>
            </div>

            <div class="skew-slider-arrow slides-nav">
                <button class="skew-slider-prev d-flex align-items-center">
                    <span style="display:inline-block;width:44px;height:44px;background:#fff;opacity:.7;border-radius:50%;"></span>
                </button>
                <button class="skew-slider-next d-flex align-items-center">
                    <span style="display:inline-block;width:44px;height:44px;background:#fff;opacity:.7;border-radius:50%;"></span>
                </button>
            </div>

            <div class="slides-numbers-wrap">
                <div class="slides-numbers">
                    <span class="active text-1">01</span>
                    <span class="text-2">/</span>
                    <span class="text-3">0{{ slidesCount }}</span>
                </div>
            </div>
        </div>
        <?php
    }
}
