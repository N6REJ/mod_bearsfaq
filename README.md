# Bears FAQ Module

A modern, accessible Joomla module that displays FAQ articles grouped as tabs and accordions based on article tags. Built with Bootstrap 5 and comprehensive accessibility features.

## Features

### 🎯 Core Functionality
- Tag-based Organization: Automatically groups FAQ articles by their assigned tags
- Multiple Navigation Styles: Tabs, Pills, or List
- Flexible Layouts: Horizontal or vertical tab orientations
- Accordion Content: Each tab contains an accordion of FAQ questions and answers
- Smart Fallback: Untagged articles are grouped under a "General" category

### 🎨 Customizable Styling
- Tab Styles: Choose between traditional Tabs, modern Pills, or List
- Color Customization: Configure colors for active/inactive tabs, borders, text, and the active tab underline
- Active Underline Control: Adjustable height and color (supports RGBA)
- Layout Options: Multiple tab alignment options (left, center, right, justified, equal width, etc.)
- Responsive Design: Works seamlessly across all device sizes
- Professional Appearance: Enhanced styling with shadows, borders, and smooth transitions

### ♿ Accessibility Features
- WCAG 2.1 AA Compliant: Full accessibility support for screen readers
- Keyboard Navigation: Complete keyboard support with arrow keys, Home/End
- Screen Reader Optimized: Proper ARIA labels, live regions, and semantic HTML
- Focus Management: Clear focus indicators and logical tab order
- High Contrast Support: Enhanced visibility for users with visual impairments
- Reduced Motion Support: Respects user preferences for reduced animations

## Installation

1. Download the module package
2. In Joomla Administrator, go to Extensions > Manage > Install
3. Upload the module package file
4. Go to Extensions > Modules to configure and assign the module

## Configuration

### Basic Settings

#### FAQ Category
- Field: FAQ Category ID
- Description: Select the Joomla content category containing your FAQ articles
- Required: Yes

#### Maximum Articles
- Field: Max Articles
- Description: Maximum number of FAQ articles to display
- Default: 100
- Range: 1-500

#### Tab Style
- Options:
  - Tabs: Traditional tab interface
  - Pills: Modern pill-style navigation
  - List: Simple list navigation optimized for vertical layouts
- Default: Tabs

Behavior details for List:
- Vertical orientation: Shows standard bullets for inactive items; active item gets Bootstrap text-primary coloring and a Unicode arrow (→) indicator after the item
- Horizontal orientation: Uses a simple Bootstrap navbar presentation without the arrow indicator or hover translate effect

#### Tab Orientation
- Options:
  - Horizontal: Tabs displayed horizontally above content
  - Vertical: Tabs displayed vertically beside content
- Default: Horizontal

### Styling Options

#### Colors
- Active Tab Background: Background color for the currently selected tab
- Inactive Tab Background: Background color for non-selected tabs
- Tab Font Color: Text color for all tabs (only applied when explicitly set)
- Question Color: Color for question links in accordions
- Border Color: Color for tab borders
- Active Tab Underline Color: Color of the underline bar for active tabs (supports RGBA)

#### Layout
- Border Radius: Rounded corner radius for tabs (0-50px)
- Tab Gap: Space between tabs (0-20px)
- Tab Alignment: How tabs are positioned within their container
  - Left, Right, Centered, Space Between, Space Around, Justified, Equal Width

#### Underline Bar (Tabs and Pills)
- Active Underline Height: Height of the active underline bar (in px)
- Active Underline Color: RGBA or HEX; applied to the active tab underline bar

## Usage

### Setting Up FAQ Content

1. Create FAQ Category:
   - Go to Content > Categories
   - Create a new category (e.g., "FAQ")
   - Note the category alias for reference

2. Create FAQ Articles:
   - Go to Content > Articles
   - Create articles in your FAQ category
   - Add relevant tags to group related questions
   - Use clear, descriptive titles for questions

3. Organize with Tags:
   - Create tags for different FAQ topics (e.g., "General", "Technical", "Billing")
   - Assign appropriate tags to each FAQ article
   - Articles can have multiple tags and will appear in multiple groups

### Module Configuration

1. Assign Module:
   - Go to Extensions > Modules
   - Find "Bears FAQ" module
   - Configure position and menu assignment

2. Configure Settings:
   - Select your FAQ category
   - Choose navigation style (Tabs, Pills, or List) and orientation
   - Customize colors (including RGBA underline color) and layout as needed

3. Test Accessibility:
   - Test keyboard navigation (Tab, Arrow keys, Enter/Space)
   - Verify screen reader compatibility
   - Check focus indicators are visible

### List Style Behavior
- Vertical orientation:
  - Inactive items show standard bullets
  - Active item is highlighted using Bootstrap's text-primary class
  - An arrow indicator (Unicode →) appears after the active item
  - A subtle translateX hover effect is applied to links
- Horizontal orientation:
  - Renders as a simple Bootstrap navbar without the arrow indicator or hover translate effect

## Technical Details

### Requirements
- Joomla: 4.0+ or 5.0+
- PHP: 7.4+
- Bootstrap: 5.x (automatically loaded)

### File Structure
```
mod_bearsfaq/
├── mod_bearsfaq.php          # Main module file
├── mod_bearsfaq.xml          # Module manifest
├── README.md                 # Documentation
├── LICENSE                   # GPL v3 license
├── language/                 # Language files
│   └── en-GB/
│       ├── en-GB.mod_bearsfaq.ini
│       └── en-GB.mod_bearsfaq.sys.ini
└── media/                    # Assets
    ├── css/
    │   └── mod_bearsfaq.css  # Module styles
    └── js/
        └── mod_bearsfaq.js   # Accessibility enhancements
```

### CSS Variables
The module uses CSS custom properties for easy customization:

```css
.bearsfaq-tabs {
  --bfq-accent: var(--bs-primary);
  --bfq-active-tab-color: inherit;
  --bfq-inactive-tab-color: transparent;
  --bfq-tab-font-color: inherit;
  --bfq-question-color: inherit;
  --bfq-border-color: rgba(0,0,0,0.275);
  --bfq-active-underline-height: 2px;
  --bfq-active-underline-color: rgba(13,110,253,1); /* example; configurable via params */
  --bfq-border-radius: 8px;
  --bfq-tab-gap: 2px;
  --bfq-tab-alignment: flex-start;
}
```

### JavaScript Features
- Keyboard Navigation: Arrow keys, Home/End support
- Screen Reader Announcements: Live region updates
- Focus Management: Proper tabindex handling
- Bootstrap Integration: Event handling for tabs and accordions
- Active List Item Sync: When using List style, the active item's parent <li> is toggled with text-primary on tab switches, ensuring the active state color updates dynamically

## Accessibility Features

### Keyboard Navigation
- Tab: Navigate between interactive elements
- Arrow Keys: Move between tabs
- Home/End: Jump to first/last tab
- Enter/Space: Activate tabs and expand accordions

### Screen Reader Support
- Semantic HTML: Proper heading hierarchy (H1 > H3)
- ARIA Labels: Descriptive labels for all interactive elements
- Live Regions: Announcements for state changes
- Skip Links: Quick navigation for keyboard users

### Visual Accessibility
- Focus Indicators: Clear outlines for keyboard focus
- High Contrast: Enhanced borders in high contrast mode
- Reduced Motion: Respects user motion preferences
- Color Independence: Information not conveyed by color alone

## Customization

### CSS Overrides
Add custom styles to your template's CSS:

```css
/* Example: Custom tab styling */
.bearsfaq-tabs .nav-link {
  border-radius: 15px !important;
  margin-right: 10px !important;
}

/* Example: Custom accordion styling */
.bearsfaq-tabs .accordion-button {
  font-size: 1.1rem !important;
  padding: 1rem 1.5rem !important;
}
```

### Template Integration
The module inherits your template's styling by default. Colors and fonts will match your site's design unless explicitly overridden.

## Troubleshooting

### Common Issues

No FAQs Displayed
- Verify FAQ category exists and contains published articles
- Check that articles are assigned to the correct category
- Ensure module is published and assigned to correct menu items

Tags Not Grouping Correctly
- Verify articles have tags assigned
- Check tag names for typos or extra spaces
- Ensure tags are published

Styling Issues
- Clear Joomla cache after making changes
- Check for CSS conflicts with template
- Verify Bootstrap 5 is loaded

Accessibility Problems
- Test with keyboard navigation
- Use screen reader testing tools
- Check browser console for JavaScript errors

### Debug Mode
Enable Joomla debug mode to see detailed error messages if the module fails to load.

## Browser Support

- Modern Browsers: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- Mobile: iOS Safari 14+, Chrome Mobile 90+
- Accessibility Tools: NVDA, JAWS, VoiceOver, Dragon NaturallySpeaking

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test accessibility compliance
5. Submit a pull request

### Development Guidelines
- Follow Joomla coding standards
- Maintain WCAG 2.1 AA compliance
- Test with keyboard navigation
- Verify screen reader compatibility
- Document any new features

## License

This module is licensed under the GNU General Public License v3.0. See [LICENSE](LICENSE) for details.

## Support

- Documentation: This README file
- Issues: https://github.com/N6REJ/mod_bearsfaq/issues
- Community: Joomla community forums

## Changelog

### Version 1.1.0
- Added "List" navigation style
  - Vertical: bullets for inactive items, text-primary for active, Unicode arrow (→) indicator
  - Horizontal: simplified Bootstrap navbar presentation (no arrow/hover transform)
- Active underline bar for tabs/pills now configurable:
  - Height via --bfq-active-underline-height
  - Color via --bfq-active-underline-color (supports RGBA)
- JavaScript enhancement: dynamically toggles text-primary on active list item when switching tabs (click and shown.bs.tab)
- Scoped CSS selectors so arrow/spacing apply only in vertical list mode; horizontal list remains clean
- Removed unused j51icons font-face and related arrow fallback

### Version 1.0.0
- Initial release
- Tag-based FAQ organization
- Bootstrap 5 integration
- Full accessibility support
- Customizable styling options
- Horizontal and vertical layouts
- Tab and pill navigation styles

## Credits

- Author: N6REJ
- Email: troy@hallhome.us
- Website: https://hallhome.us
- Framework: Joomla 4.x/5.x
- UI Framework: Bootstrap 5
- Accessibility: WCAG 2.1 AA compliant

---

Bears FAQ Module - Making FAQ management accessible and beautiful for Joomla websites.
