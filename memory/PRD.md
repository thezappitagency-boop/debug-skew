# Cool Slide Skew Slider — Debug Repo PRD

## Original Problem Statement
V3 of the Elementor Skew Slider had multiple critical bugs:
1. Hardcoded black background (background-color:#111) — no transparency
2. Black bars around navigation — caused by missing chevron SVG files (broken image icons)
3. Content in slide breaks scroll — overflow and event handling issues
4. Cannot scroll to last slide via mouse wheel — snap formula bug
5. Cannot scroll back to slide 1 — activeSnap race condition
6. Original "see-through footer" effect lost
7. V3 plugin wouldn't even load (wrong require_once + asset paths)

## Repo
https://github.com/thezappitagency-boop/debug-skew

## Architecture

### Plugin Versions
- **V1** (`cool-slide-skew-widget (2)/cool-slide-skew-widget/`) — Working reference. Full listing slider.
- **V2** (`cool-slide-skew-widget-v2/`) — Scrapped.
- **V3** (`cool-slide-skew-widget-v3/`) — **Active development.** Clean slate with template dropdown.

### V3 Plugin Structure
```
cool-slide-skew-widget-v3/
├── cool-slide-skew-widget-v3.php        ← Main plugin file
├── widgets/
│   └── class-skew-slider-v3-widget.php  ← Elementor widget class
├── assets/
│   ├── css/skew-slider.css
│   ├── js/
│   │   ├── skew-slider-bundle.js
│   │   └── Observer.min.js
│   ├── img/chevron-up.svg + chevron-down.svg
│   └── fonts/ (ClashDisplay + MangoGrotesque)
```

## What Was Implemented

### Session 1 (2026-02) — v3.1.0 Initial Build
1. Directory structure rebuilt — all assets in correct subdirs
2. Missing chevron SVGs added (was causing broken img "black" around nav)
3. Removed hardcoded `background-color:#111` → transparent
4. Wrong require_once path fixed
5. Elementor frontend hook fixed to V3 widget name
6. Per-slide background color picker (COLOR control, transparent default)
7. Elementor Template Dropdown per slide (lists `elementor_library` posts)
8. Custom HTML/shortcode textarea fallback
9. ScrollTrigger.refresh() on window load
10. nav drop-shadow/text-shadow for visibility on all backgrounds
11. overflow:hidden on .skew-slider-content
12. activeSnap gating (bug fix: only update when navigateTo() starts)
13. Pending navigation mechanism (handles fast scroll / isAnimating block)

### Session 1 — v3.1.1 Animation Fix
14. **Restored V1 exact scroll formula** (was changed to (N-1)*innerHeight — broke feel)
    - `end = N * innerHeight`  (intentional pause on last slide)
    - `snapTo = min((N-1)/N, round(v*N)/N)`
    - `ease: power1.inOut`, `duration: {min:0.2, max:0.45}`
    - `onUpdate snap = min(N-1, round(progress * numSlides))`
15. `.skew-slider-content--template` → full height/width (top:0 left:0 height:100%)
    so 100vh Elementor templates fill the slide correctly

## Core Requirements (Static)
- V3 is "blank canvas" — identical animation/scroll to V1, different content approach
- Per-slide: background image, background color, Elementor template OR shortcode
- Sticky-scroll: pins to viewport, scrolls through all N slides, releases
- Chevron up/down navigation (prev/next), bottom left/right
- Slide counter + decorative lines (right side)
- Container Elementor settings: Full Width + 0 padding + overflow:hidden + (no min-height)

## Backlog
- [ ] Keyboard arrow key navigation
- [ ] Auto-play with configurable interval + pause on hover
- [ ] Mobile swipe (currently Observer fallback)
- [ ] Slide transition speed/ease control in Elementor
