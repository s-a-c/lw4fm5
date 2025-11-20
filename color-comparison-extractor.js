// Color Comparison Extractor Script
// Run this in browser console on both index-copy and index-flux pages

(function() {
  'use strict';

  // Helper function to convert RGB to OKLCH
  function rgbToOklch(r, g, b) {
    // First convert RGB to sRGB (0-1)
    r = r / 255;
    g = g / 255;
    b = b / 255;

    // Gamma correction to linear RGB
    function toLinear(c) {
      return c <= 0.04045 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
    }
    r = toLinear(r);
    g = toLinear(g);
    b = toLinear(b);

    // Convert to XYZ (sRGB matrix)
    let x = r * 0.4124564 + g * 0.3575761 + b * 0.1804375;
    let y = r * 0.2126729 + g * 0.7151522 + b * 0.0721750;
    let z = r * 0.0193339 + g * 0.1191920 + b * 0.9503041;

    // Normalize by D65 white point
    x /= 0.95047;
    y /= 1.00000;
    z /= 1.08883;

    // Convert to LAB
    function f(t) {
      return t > 0.008856451679035631 ? Math.cbrt(t) : (t * 7.787037037037035 + 0.13793103448275862);
    }
    const fx = f(x);
    const fy = f(y);
    const fz = f(z);

    const l = 116 * fy - 16;
    const a = 500 * (fx - fy);
    const b_lab = 200 * (fy - fz);

    // Convert LAB to LCH
    const c = Math.sqrt(a * a + b_lab * b_lab);
    let h = Math.atan2(b_lab, a) * (180 / Math.PI);
    if (h < 0) h += 360;

    // Convert to OKLCH approximation (simplified)
    // For more accurate conversion, we'd need OKLab library
    const okl = l / 100; // Approximate lightness 0-1
    const okc = c / 150; // Approximate chroma (normalized)
    const okh = h; // Hue 0-360

    return { l: okl, c: okc, h: okh };
  }

  // Extract RGB from computed style
  function extractRGB(element, property = 'background-color') {
    const style = window.getComputedStyle(element);
    const color = style[property] || style.getPropertyValue(property);
    
    if (!color || color === 'transparent' || color === 'rgba(0, 0, 0, 0)') {
      return null;
    }

    // Parse rgb/rgba
    const match = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
    if (match) {
      return {
        r: parseInt(match[1]),
        g: parseInt(match[2]),
        b: parseInt(match[3]),
        alpha: match[4] ? parseFloat(match[4]) : 1,
        raw: color
      };
    }

    // Parse hex
    const hexMatch = color.match(/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i);
    if (hexMatch) {
      return {
        r: parseInt(hexMatch[1], 16),
        g: parseInt(hexMatch[2], 16),
        b: parseInt(hexMatch[3], 16),
        alpha: 1,
        raw: color
      };
    }

    return null;
  }

  // Extract gradient colors
  function extractGradientColors(element) {
    const style = window.getComputedStyle(element);
    const bgImage = style.backgroundImage || '';
    
    if (!bgImage.includes('gradient')) {
      return null;
    }

    // Extract gradient stops (simplified)
    const matches = bgImage.match(/(rgba?\([^)]+\))/g);
    if (matches) {
      return matches.map(colorStr => {
        const match = colorStr.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
        if (match) {
          return {
            r: parseInt(match[1]),
            g: parseInt(match[2]),
            b: parseInt(match[3]),
            alpha: match[4] ? parseFloat(match[4]) : 1,
            raw: colorStr
          };
        }
        return null;
      }).filter(c => c !== null);
    }

    return null;
  }

  // Calculate color difference
  function calculateColorDifference(rgb1, rgb2) {
    if (!rgb1 || !rgb2) return null;

    const rDelta = rgb2.r - rgb1.r;
    const gDelta = rgb2.g - rgb1.g;
    const bDelta = rgb2.b - rgb1.b;

    // RGB distance
    const rgbDistance = Math.sqrt(rDelta * rDelta + gDelta * gDelta + bDelta * bDelta);
    const rgbPercentage = (rgbDistance / 441.67) * 100;

    // Convert to OKLCH
    const oklch1 = rgbToOklch(rgb1.r, rgb1.g, rgb1.b);
    const oklch2 = rgbToOklch(rgb2.r, rgb2.g, rgb2.b);

    const lDelta = oklch2.l - oklch1.l;
    const cDelta = oklch2.c - oklch1.c;
    let hDelta = oklch2.h - oklch1.h;
    
    // Handle hue circularity
    if (hDelta > 180) hDelta -= 360;
    if (hDelta < -180) hDelta += 360;

    // Delta E (CIE76) approximation
    const lab1 = {
      l: oklch1.l * 100,
      a: oklch1.c * Math.cos(oklch1.h * Math.PI / 180) * 150,
      b: oklch1.c * Math.sin(oklch1.h * Math.PI / 180) * 150
    };
    const lab2 = {
      l: oklch2.l * 100,
      a: oklch2.c * Math.cos(oklch2.h * Math.PI / 180) * 150,
      b: oklch2.c * Math.sin(oklch2.h * Math.PI / 180) * 150
    };

    const deltaE = Math.sqrt(
      Math.pow(lab2.l - lab1.l, 2) +
      Math.pow(lab2.a - lab1.a, 2) +
      Math.pow(lab2.b - lab1.b, 2)
    );
    const deltaEPercentage = (deltaE / 100) * 100;

    return {
      rgb: {
        r: rDelta,
        g: gDelta,
        b: bDelta,
        distance: rgbDistance,
        percentage: rgbPercentage
      },
      oklch: {
        l: lDelta,
        c: cDelta,
        h: hDelta
      },
      deltaE: deltaE,
      deltaEPercentage: deltaEPercentage
    };
  }

  // Extract color data for an element
  function extractElementColors(selector, description, theme) {
    const elements = document.querySelectorAll(selector);
    if (elements.length === 0) {
      return { selector, description, theme, found: false };
    }

    const results = [];
    elements.forEach((el, index) => {
      const bgColor = extractRGB(el, 'background-color');
      const textColor = extractRGB(el, 'color');
      const borderColor = extractRGB(el, 'border-color');
      const fillColor = el.tagName === 'path' || el.tagName === 'svg' || el.tagName === 'rect' 
        ? extractRGB(el, 'fill') 
        : null;
      const gradientColors = extractGradientColors(el);

      results.push({
        index,
        selector,
        description,
        theme,
        backgroundColor: bgColor,
        textColor: textColor,
        borderColor: borderColor,
        fillColor: fillColor,
        gradientColors: gradientColors,
        computedStyle: {
          backgroundImage: window.getComputedStyle(el).backgroundImage,
          cssVariables: {
            primary: getComputedStyle(el).getPropertyValue('--color-ctp-primary') || 
                     getComputedStyle(document.documentElement).getPropertyValue('--color-ctp-primary'),
            secondary: getComputedStyle(el).getPropertyValue('--color-ctp-secondary') ||
                      getComputedStyle(document.documentElement).getPropertyValue('--color-ctp-secondary'),
            primary900: getComputedStyle(document.documentElement).getPropertyValue('--color-ctp-primary-900'),
          }
        }
      });
    });

    return { selector, description, theme, found: true, results };
  }

  // Main extraction function
  function extractAllColors(theme) {
    // Switch theme if needed
    if (document.documentElement.className !== theme) {
      document.documentElement.className = theme;
      localStorage.setItem('theme', theme);
      // Wait for theme to apply
      return new Promise(resolve => {
        setTimeout(() => {
          const colors = performExtraction(theme);
          resolve(colors);
        }, 100);
      });
    }

    return performExtraction(theme);
  }

  function performExtraction(theme) {
    const colors = {
      page: window.location.pathname,
      theme: theme,
      timestamp: new Date().toISOString(),
      elements: {}
    };

    // Background SVG paths
    const svgPaths = document.querySelectorAll('svg path[class*="fill-ctp"]');
    if (svgPaths.length > 0) {
      colors.elements.backgroundSVG = extractElementColors(
        'svg path[class*="fill-ctp-primary-900"], svg path[class*="fill-ctp-secondary"]',
        'Background SVG Wave Paths',
        theme
      );
    }

    // H1 Heading
    colors.elements.h1Heading = extractElementColors(
      'h1.bg-linear-35',
      'H1 Heading Gradient',
      theme
    );

    // Theme switcher buttons - checked
    colors.elements.themeButtonChecked = extractElementColors(
      '#flavour-switcher input[type="radio"]:checked + label, #flavour-switcher [data-checked]',
      'Theme Switcher Button (Checked)',
      theme
    );

    // Theme switcher buttons - unchecked
    colors.elements.themeButtonUnchecked = extractElementColors(
      '#flavour-switcher input[type="radio"]:not(:checked) + label, #flavour-switcher ui-radio:not([data-checked])',
      'Theme Switcher Button (Unchecked)',
      theme
    );

    // Calendar highlighted week
    colors.elements.calendarWeek = extractElementColors(
      '.week.has-\\(.today\\):bg-ctp-primary-50, .week[class*="today"]',
      'Calendar Highlighted Week',
      theme
    );

    // Calendar today cell
    colors.elements.calendarToday = extractElementColors(
      '.today, [class*="today"]',
      'Calendar Today Cell',
      theme
    );

    // Progress bar
    colors.elements.progressBar = extractElementColors(
      '[class*="bg-ctp-primary"][class*="bg-linear"]',
      'Progress Bar',
      theme
    );

    return colors;
  }

  // Export function
  window.extractAllColors = extractAllColors;
  window.extractAllColorsSync = performExtraction;
  window.calculateColorDifference = calculateColorDifference;

  // Auto-run if theme specified
  const urlParams = new URLSearchParams(window.location.search);
  const themeParam = urlParams.get('theme');
  
  if (themeParam) {
    extractAllColors(themeParam).then(colors => {
      console.log('Color extraction complete:', colors);
      window.colorExtractionResults = colors;
    });
  } else {
    console.log('Color extraction script loaded. Use extractAllColors(theme) to extract colors.');
    console.log('Example: extractAllColors("latte").then(colors => console.log(colors));');
  }
})();

