<?php
/**
 * Elementor Widget: Skew Slider
 *
 * @package CoolSlide
 */

namespace CoolSlide;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SkewSliderWidget extends \Elementor\Widget_Base {

    // ── Identity ─────────────────────────────────────────────────────

    public function get_name()  { return 'cool-slide-skew-slider'; }
    public function get_title() { return esc_html__( 'Skew Slider', 'cool-slide' ); }
    public function get_icon()  { return 'eicon-slider-full-screen'; }

    public function get_categories() {
        return [ 'basic', 'general' ];
    }

    // ── Asset dependencies ────────────────────────────────────────────

    public function get_script_depends() {
        return [ 'cool-slide-skew-slider' ];
    }
    public function get_style_depends() {
        return [ 'cool-slide-skew-slider' ];
    }

    // ── Controls (panel UI) ───────────────────────────────────────────

    protected function register_controls() {

        /* ── SECTION: Slides ── */
        $this->start_controls_section( 'section_slides', [
            'label' => esc_html__( 'Slides', 'cool-slide' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control( 'image', [
            'label'   => esc_html__( 'Background Image', 'cool-slide' ),
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'default' => [
                'url' => 'https://picsum.photos/seed/csl1/1920/1080',
            ],
        ] );

        $repeater->add_control( 'category', [
            'label'       => esc_html__( 'Category / Label', 'cool-slide' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'Digital Platform',
            'placeholder' => 'e.g. Branding',
        ] );

        $repeater->add_control( 'title_line1', [
            'label'       => esc_html__( 'Title – Line 1', 'cool-slide' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'Project',
            'placeholder' => 'First word(s)',
        ] );

        $repeater->add_control( 'title_line2', [
            'label'       => esc_html__( 'Title – Line 2 (optional)', 'cool-slide' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'placeholder' => 'Leave blank for single-line title',
        ] );

        $repeater->add_control( 'url', [
            'label'         => esc_html__( 'Link', 'cool-slide' ),
            'type'          => \Elementor\Controls_Manager::URL,
            'placeholder'   => 'https://your-portfolio.com/project',
            'show_external' => true,
            'default'       => [ 'url' => '' ],
        ] );

        $this->add_control( 'slides', [
            'label'       => esc_html__( 'Slides', 'cool-slide' ),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [
                    'category'    => 'Digital Platform',
                    'title_line1' => 'Simple',
                    'title_line2' => 'Logistics',
                    'image'       => [ 'url' => 'https://picsum.photos/seed/csl-s1/1920/1080' ],
                ],
                [
                    'category'    => 'Digital Platform',
                    'title_line1' => 'Smart',
                    'title_line2' => 'Platform',
                    'image'       => [ 'url' => 'https://picsum.photos/seed/csl-s2/1920/1080' ],
                ],
                [
                    'category'    => 'Branding',
                    'title_line1' => 'Royal',
                    'title_line2' => 'Benz',
                    'image'       => [ 'url' => 'https://picsum.photos/seed/csl-s3/1920/1080' ],
                ],
                [
                    'category'    => 'Digital Platform',
                    'title_line1' => "World's",
                    'title_line2' => 'Relays',
                    'image'       => [ 'url' => 'https://picsum.photos/seed/csl-s4/1920/1080' ],
                ],
                [
                    'category'    => 'Design',
                    'title_line1' => 'Bright',
                    'title_line2' => 'Captive',
                    'image'       => [ 'url' => 'https://picsum.photos/seed/csl-s5/1920/1080' ],
                ],
            ],
            'title_field' => '{{{ title_line1 }}} {{{ title_line2 }}}',
        ] );

        $this->end_controls_section();

        /* ── SECTION: Footer / Social ── */
        $this->start_controls_section( 'section_footer', [
            'label' => esc_html__( 'Footer & Social', 'cool-slide' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'copyright_text', [
            'label'       => esc_html__( 'Copyright / CTA line (supports HTML)', 'cool-slide' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'Have a project in mind? <a href="#">Let\'s Talk.</a>',
            'label_block' => true,
        ] );

        $social_repeater = new \Elementor\Repeater();
        $social_repeater->add_control( 'label', [
            'label'   => esc_html__( 'Label', 'cool-slide' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Fb',
        ] );
        $social_repeater->add_control( 'url', [
            'label'   => esc_html__( 'URL', 'cool-slide' ),
            'type'    => \Elementor\Controls_Manager::URL,
            'default' => [ 'url' => '#' ],
        ] );

        $this->add_control( 'social_links', [
            'label'       => esc_html__( 'Social Links (displayed vertically on left)', 'cool-slide' ),
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $social_repeater->get_controls(),
            'default'     => [
                [ 'label' => 'Fb', 'url' => [ 'url' => '#' ] ],
                [ 'label' => 'In', 'url' => [ 'url' => '#' ] ],
                [ 'label' => 'Be', 'url' => [ 'url' => '#' ] ],
            ],
            'title_field' => '{{{ label }}}',
        ] );

        $this->add_control( 'prev_text', [
            'label'   => esc_html__( 'Prev Button Label', 'cool-slide' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Prev',
        ] );
        $this->add_control( 'next_text', [
            'label'   => esc_html__( 'Next Button Label', 'cool-slide' ),
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Next',
        ] );

        $this->add_control( 'cta_text', [
            'label'       => esc_html__( 'CTA Button Text (upper right)', 'cool-slide' ),
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'View All Listings',
            'placeholder' => 'e.g. View All Listings',
            'separator'   => 'before',
        ] );
        $this->add_control( 'cta_url', [
            'label'         => esc_html__( 'CTA Button URL', 'cool-slide' ),
            'type'          => \Elementor\Controls_Manager::URL,
            'placeholder'   => 'https://yoursite.com/listings',
            'show_external' => true,
            'default'       => [ 'url' => '' ],
        ] );

        $this->end_controls_section();
    }

    // ── Render (front-end & editor) ───────────────────────────────────

    protected function render() {
        $settings     = $this->get_settings_for_display();
        $slides       = $settings['slides'] ?? [];
        $slides_count = count( $slides );
        $copyright    = $settings['copyright_text'] ?? '';
        $socials      = $settings['social_links']   ?? [];
        $prev_text    = ! empty( $settings['prev_text'] ) ? $settings['prev_text'] : 'Prev';
        $next_text    = ! empty( $settings['next_text'] ) ? $settings['next_text'] : 'Next';
        $cta_text     = $settings['cta_text']  ?? '';
        $cta_href     = ! empty( $settings['cta_url']['url'] ) ? esc_url( $settings['cta_url']['url'] ) : '#';
        $cta_target   = ! empty( $settings['cta_url']['is_external'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
        ?>

        <div class="skew-slider-area">

            <!-- Slides -->
            <div class="skew-slider-wrap">
                <?php foreach ( $slides as $slide ) :
                    $img_url  = ! empty( $slide['image']['url'] ) ? esc_url( $slide['image']['url'] ) : '';
                    $category = ! empty( $slide['category'] )    ? esc_html( $slide['category'] )    : '';
                    $line1    = ! empty( $slide['title_line1'] )  ? esc_html( $slide['title_line1'] ) : '';
                    $line2    = ! empty( $slide['title_line2'] )  ? esc_html( $slide['title_line2'] ) : '';
                    $link_url = ! empty( $slide['url']['url'] )   ? esc_url( $slide['url']['url'] )   : '';
                    $target   = ! empty( $slide['url']['is_external'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
                ?>
                <div class="skew-slider-item slide">
                    <div class="slide__img"
                         style="background-image: url('<?php echo $img_url; ?>')">
                    </div>
                    <div class="skew-slider-content">
                        <span><?php echo $category; ?></span>
                        <h4>
                            <?php if ( $link_url ) : ?>
                                <a href="<?php echo $link_url; ?>"<?php echo $target; ?>>
                            <?php endif; ?>
                            <?php echo $line1; ?>
                            <?php if ( $line2 ) : ?><br><?php echo $line2; ?><?php endif; ?>
                            <?php if ( $link_url ) : ?></a><?php endif; ?>
                        </h4>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Copyright / CTA (top centre) -->
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

            <!-- Social links (left, vertical) -->
            <?php if ( ! empty( $socials ) ) : ?>
            <div class="tp-portfolio-slider__social tp-el-social">
                <?php foreach ( $socials as $link ) :
                    $href    = ! empty( $link['url']['url'] )         ? esc_url( $link['url']['url'] )  : '#';
                    $ext     = ! empty( $link['url']['is_external'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
                    $label   = ! empty( $link['label'] )              ? esc_html( $link['label'] )       : '';
                ?>
                <a href="<?php echo $href; ?>"<?php echo $ext; ?>><?php echo $label; ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Prev / Next arrows (bottom) -->
            <div class="skew-slider-arrow slides-nav">
                <button class="skew-slider-prev d-flex align-items-center">
                    <span class="icon-1">
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M7 1L1 7L7 13" stroke="white" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="slider-nav-text tp-el-prev"><?php echo esc_html( $prev_text ); ?></span>
                </button>

                <button class="skew-slider-next d-flex align-items-center">
                    <span class="slider-nav-text tp-el-next"><?php echo esc_html( $next_text ); ?></span>
                    <span class="icon-2">
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 13L7 7L1 1" stroke="white" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </button>
            </div>

            <!-- Slide counter (right, with decorative lines) -->
            <div class="slides-numbers-wrap">
                <div class="slides-numbers">
                    <span class="active text-1">01</span>
                    <span class="text-2">/</span>
                    <span class="text-3"><?php echo str_pad( $slides_count, 2, '0', STR_PAD_LEFT ); ?></span>
                </div>
            </div>

        </div><!-- /.skew-slider-area -->

        <?php
    }

    // ── Editor placeholder (shows widget name while panel loads) ─────

    protected function content_template() {
        ?>
        <#
        var slides       = settings.slides || [];
        var slidesCount  = slides.length;
        var copyright    = settings.copyright_text || '';
        var socials      = settings.social_links || [];
        var prevText     = settings.prev_text || 'Prev';
        var nextText     = settings.next_text || 'Next';
        #>

        <div class="skew-slider-area">
            <div class="skew-slider-wrap">
                <# _.each( slides, function( slide, i ) { #>
                <div class="skew-slider-item slide <# if (i===0) { #>slide--current<# } #>">
                    <div class="slide__img"
                         style="background-image: url('{{ slide.image.url }}')">
                    </div>
                    <div class="skew-slider-content">
                        <span>{{ slide.category }}</span>
                        <h4>
                            <# if ( slide.url.url ) { #><a href="{{ slide.url.url }}"><# } #>
                            {{ slide.title_line1 }}
                            <# if ( slide.title_line2 ) { #><br>{{ slide.title_line2 }}<# } #>
                            <# if ( slide.url.url ) { #></a><# } #>
                        </h4>
                    </div>
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
                    <span class="icon-1">
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none"><path d="M7 1L1 7L7 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span class="slider-nav-text">{{ prevText }}</span>
                </button>
                <button class="skew-slider-next d-flex align-items-center">
                    <span class="slider-nav-text">{{ nextText }}</span>
                    <span class="icon-2">
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none"><path d="M1 13L7 7L1 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
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
