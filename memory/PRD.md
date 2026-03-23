# Cool Slide Skew Slider — Debug Repo PRD

## Original Problem Statement
V3 of the Elementor Skew Slider had multiple critical bugs:
1. Hardcoded black background (background-color:#111) — no transparency
2. Black bars around navigation after setting slide to white — caused by missing chevron SVG files (broken image icons) due to wrong asset paths
3. Content in slide breaks scroll — overflow and event handling issues
4. Cannot scroll to last slide via mouse wheel — snap formula bug
5. Cannot scroll back to slide 1 — activeSnap not properly gated
6. Original "see-through footer" effect lost
7. V3 plugin wouldn't even load (wrong require_once path for widget class)

## Repo
https://github.com/thezappitagency-boop/debug-skew

## Architecture

### Plugin Versions
- **V1** (`cool-slide-skew-widget (2)/cool-slide-skew-widget/`) — Working reference. Full listing slider with title, category, social links, CTA.
- **V2** (`cool-slide-skew-widget-v2/`) — Scrapped. Navigation-only shell with shortcode input.
- **V3** (`cool-slide-skew-widget-v3/`) — **Active development.** Clean slate with template dropdown.

### V3 Plugin Structure (Fixed)
```
cool-slide-skew-widget-v3/
├── cool-slide-skew-widget-v3.php        ← Main plugin file
├── widgets/
│   └── class-skew-slider-v3-widget.php  ← Elementor widget class
├── assets/
│   ├── css/skew-slider.css              ← Scoped styles
│   ├── js/
│   │   ├── skew-slider-bundle.js        ← GSAP slider logic
│   │   └── Observer.min.js              ← GSAP Observer plugin (local)
│   ├── img/
│   │   ├── chevron-up.svg               ← Prev navigation icon
│   │   └── chevron-down.svg             ← Next navigation icon
│   └── fonts/                           ← ClashDisplay + MangoGrotesque
```

## What Was Implemented (v3.1.0 — 2026-02)

### Critical Fixes
1. **Directory structure rebuilt** — all assets now in correct `assets/js/css/img/fonts/` subfolders
2. **Missing chevron SVGs added** — copied from V2; were causing broken image icons ("black around nav")
3. **Removed hardcoded background** — `background-color:#111` fallback eliminated → transparent by default
4. **Wrong require_once path fixed** — `widgets/class-skew-slider-v3-widget.php` (was at root level)

### Scroll / Navigation Fixes
5. **Last slide reachable via scroll** — Snap formula changed from `N` to `N-1` divisions so last slide = progress 1.0 (no dead zone after last slide)
6. **Return to slide 1 works** — Same formula fix; symmetric snap points
7. **Fast scrolling no longer skips slides** — `activeSnap` only updated when `navigateTo()` actually starts; pending navigation queue processes missed targets in `onComplete`
8. **Elementor frontend hook fixed** — Was `cool-slide-skew-slider.default` (V1 name), now `cool-slide-skew-slider-v3.default`

### New Features
9. **Elementor Template Dropdown** — Per slide: SELECT control lists all published `elementor_library` posts. Select a saved template to render it as slide content. Much easier than copy-pasting shortcodes.
10. **Background Color Picker** — Per slide: COLOR control, defaults to transparent. Lets each slide have its own colour independently from the background image.
11. **ScrollTrigger.refresh()** — Called after `window load` to handle shortcode/template JS that modifies DOM layout after GSAP initializes.

### CSS Fixes
12. **Nav visibility on any background** — `filter: drop-shadow()` on chevrons, `text-shadow` on counter, so elements read on both dark and light slides
13. **overflow:hidden on .skew-slider-content** — Prevents shortcode output from overflowing slide and breaking GSAP layout calculations
14. **Transparent by default** — All slide and wrap elements are transparent; page background and footer visible during transitions (the "cool effect" from V1)

## Core Requirements (Static)
- Works as drop-in Elementor widget
- Sticky-scroll: pins to viewport on entry, scrolls through all slides, releases
- Chevron up/down navigation (prev/next), bottom left/right
- Slide counter with decorative lines (right side)
- All 3 plugin versions can be active simultaneously (unique PHP namespaces + prefixes)
- V3 is the "blank canvas" version — no branding chrome
- Container Elementor settings: Full Width + 0 padding + overflow:hidden

## Backlog / Future
- [ ] Keyboard arrow key navigation
- [ ] Touch swipe support (currently via GSAP Observer fallback only)
- [ ] Slide transition direction control (up/down vs scale)
- [ ] Auto-play option with pause on hover
- [ ] Mobile breakpoint: collapse to standard swipe slider
