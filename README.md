# Bears FAQ Module

A modern, accessible Joomla module that displays FAQ articles grouped as tabs and accordions based on article tags. Built with Bootstrap 5 and comprehensive accessibility features. Compatible with Joomla 4 and Joomla 5.

## Features

### Core Functionality
- Tag-based Organization: Automatically groups FAQ articles by their assigned tags
- Multiple Navigation Styles: Tabs, Pills, or List
- Flexible Layouts: Horizontal or vertical tab orientations
- Accordion Content: Each tab contains an accordion of FAQ questions and answers
- Smart Fallback: Untagged articles are grouped under a "General" category

### Styling and Layout
- Tab Styles: Tabs, Pills, or List
- Color Customization: Configure colors for active/inactive tabs, borders, text, and the active tab underline
- Layout Options: Alignment presets (left, right, centered, justified, equal width), tab gap, and rounded corners
- Responsive Design: Works seamlessly across device sizes
- Professional Appearance: Enhanced styling with borders, shadows, and smooth transitions

### Accessibility
- WCAG 2.1 AA oriented patterns
- Keyboard Navigation: Arrow keys, Home/End, Enter/Space
- Screen Reader Optimized: Proper ARIA roles/labels and live announcements
- Focus Management: Logical tab order and visible focus states
- High Contrast and Reduced Motion support

### Access Control (ACL)
- Respects Tag Access Levels: Tabs for tags the viewer cannot access (e.g., Special) are not rendered
- Example: Set a tag's Access to "Special" and only logged-in users in that view level will see that tab and its FAQs

## Requirements
- Joomla 4.x or 5.x
- PHP 7.4+
- Bootstrap 5 (loaded automatically by the module)

## Installation
1. Download the module package
2. In Joomla Administrator, go to Extensions > Manage > Install
3. Upload the module package file
4. Go to Extensions > Modules to configure and assign the module

## Content Setup
1. Create a Category for FAQs (e.g., "FAQ") under Content > Categories
2. Create Articles in that category for each question/answer
3. Create/Assign Tags to group related questions (articles can have multiple tags)
4. Optionally, set Tag Access to control who can see each group (e.g., set a tag to "Special")

## Configuration

### Basic Settings
- FAQ Category (required): The Joomla content category containing your FAQ articles
- Max Articles: Maximum number of FAQ articles to display (default 100)
- Tab Orientation: Horizontal or Vertical (default Horizontal)
- Tab Style: Tabs, Pills, or List (default Tabs)
- Tab Alignment: Left, Right, Centered, Space Between, Space Around, Justified, Equal Width (default Left)
- Tab Gap (px): Space between tabs (default 2, 0–64)

### Styling
- Active Tab Background Color
- Active Tab Underline Color (RGBA supported)
- Inactive Tab Background Color
- Tab Font Color
- Question Color (accordion question links)
- Border Color
- Border Radius (px)

### Advanced
- Module Class Suffix
- Caching: Use Global or No Caching
- Cache Time (seconds)

## Behavior Details

### Tabs vs Pills vs List
- Tabs: Traditional Bootstrap tabs with underline and borders
- Pills: Pill-shaped navigation with consistent active background
- List: Best suited for vertical orientation; shows bullets for inactive items and highlights the active item using a primary color accent

### Vertical Orientation
- Uses Bootstrap flex-column for vertical tab/pill/list navigation
- Tab pane content is shown to the right under typical site templates

### ACL and Hidden Tabs
- If a tag is set to a restricted Access level (e.g., "Special"), its tab will not render for users who do not belong to that view level
- Logged-in users with the appropriate view level will see the tab normally

## CSS Variables
Adjust presentation via CSS custom properties (override in your template if desired):

```css
.bearsfaq-tabs {
  --bfq-accent: var(--bs-primary);
  --bfq-active-tab-color: inherit;           /* active tab background */
  --bfq-inactive-tab-color: transparent;     /* inactive tab background */
  --bfq-tab-font-color: inherit;             /* tab/pill font color */
  --bfq-question-color: inherit;             /* accordion question links */
  --bfq-border-color: rgba(0,0,0,0.275);     /* tab borders */
  --bfq-active-underline-color: rgba(13,110,253,1); /* active underline color */
  --bfq-border-radius: 8px;                  /* top radius for tab corners */
  --bfq-tab-gap: 2px;                        /* gap between tabs */
  --bfq-tab-alignment: flex-start;           /* alignment within container */
}
```

Note: Underline height defaults to a subtle value. You can override it via CSS if desired.

## JavaScript Enhancements
- Keyboard navigation across tabs (Left/Right, Home/End)
- Live announcements for screen readers on tab changes
- Focus management and tabindex adjustments
- Syncs active state in List style

## Troubleshooting
- No FAQs Displayed: Ensure the selected category exists and has published articles
- Tags Not Grouping: Verify articles have tags; ensure tags are published
- Styling Issues: Clear Joomla cache; check for template CSS conflicts; ensure Bootstrap 5 is loading
- Accessibility: Test keyboard navigation and screen reader output; check console for JS errors

## File Structure
```
mod_bearsfaq/
├── mod_bearsfaq.php          # Main module file
├── mod_bearsfaq.xml          # Module manifest
├── README.md                 # Documentation (this file)
├── LICENSE                   # GPL v3 license
├── language/
│   └── en-GB/
│       ├── en-GB.mod_bearsfaq.ini
│       └── en-GB.mod_bearsfaq.sys.ini
├── media/
│   ├── css/
│   │   └── mod_bearsfaq.css  # Styles
│   └── js/
│       └── mod_bearsfaq.js   # Accessibility enhancements
└── index.html                # Standalone Joomla 5 documentation article (see below)
```

## Joomla 5 Documentation Article
A standalone HTML documentation article is provided at index.html. You can:
- Open it directly to read the docs, or
- Copy/paste its contents into a Joomla Article (Editor > Code view) to publish it on your site

## Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test accessibility and ACL behavior
5. Submit a pull request

### Development Guidelines
- Follow Joomla coding standards
- Maintain WCAG 2.1 AA-oriented practices
- Test with keyboard navigation and screen readers
- Document new features in README and CHANGELOG

## License
This module is licensed under the GNU General Public License v3.0. See [LICENSE](LICENSE) for details.

## Support
- Issues: https://github.com/N6REJ/mod_bearsfaq/issues
- Community: Joomla forums

## Credits
- Author: N6REJ
- Email: troy@hallhome.us
- Website: https://hallhome.us
- Framework: Joomla 4.x/5.x
- UI Framework: Bootstrap 5
- Accessibility: WCAG 2.1 AA oriented

---

Bears FAQ Module — accessible, flexible FAQ tabs and accordions for Joomla 4/5 with robust ACL support.
