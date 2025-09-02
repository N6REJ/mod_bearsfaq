/**
 * Bears FAQ Module - Accessibility enhancements
 * Provides keyboard navigation and screen reader support
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all FAQ modules on the page
    const faqModules = document.querySelectorAll('.bearsfaq-tabs');
    
    faqModules.forEach(function(module) {
        initializeFAQAccessibility(module);
    });
});

function initializeFAQAccessibility(module) {
    const tabList = module.querySelector('[role="tablist"]');
    const tabs = module.querySelectorAll('[role="tab"]');
    const tabPanels = module.querySelectorAll('[role="tabpanel"]');
    
    if (!tabList || tabs.length === 0) return;
    
    // Add keyboard navigation for tabs
    tabList.addEventListener('keydown', function(e) {
        const currentTab = document.activeElement;
        const currentIndex = Array.from(tabs).indexOf(currentTab);
        let targetIndex = currentIndex;
        
        switch(e.key) {
            case 'ArrowRight':
            case 'ArrowDown':
                e.preventDefault();
                targetIndex = (currentIndex + 1) % tabs.length;
                break;
                
            case 'ArrowLeft':
            case 'ArrowUp':
                e.preventDefault();
                targetIndex = currentIndex === 0 ? tabs.length - 1 : currentIndex - 1;
                break;
                
            case 'Home':
                e.preventDefault();
                targetIndex = 0;
                break;
                
            case 'End':
                e.preventDefault();
                targetIndex = tabs.length - 1;
                break;
                
            default:
                return;
        }
        
        // Focus and activate the target tab
        const targetTab = tabs[targetIndex];
        targetTab.focus();
        targetTab.click();
    });
    
    // Update tabindex when tabs are activated
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            // Remove tabindex from all tabs
            tabs.forEach(function(t) {
                t.setAttribute('tabindex', '-1');
                t.setAttribute('aria-selected', 'false');
            });
            
            // Set tabindex and aria-selected for active tab
            tab.setAttribute('tabindex', '0');
            tab.setAttribute('aria-selected', 'true');
            
            // Handle text-primary class for list style
            const navList = tab.closest('.nav-list');
            if (navList) {
                // Remove text-primary from all list items
                navList.querySelectorAll('.nav-item').forEach(function(item) {
                    item.classList.remove('text-primary');
                });
                
                // Add text-primary to active tab's parent li
                const activeItem = tab.closest('.nav-item');
                if (activeItem) {
                    activeItem.classList.add('text-primary');
                }
            }
        });
    });
    
    // Enhance accordion accessibility
    const accordions = module.querySelectorAll('.accordion');
    accordions.forEach(function(accordion) {
        const accordionButtons = accordion.querySelectorAll('.accordion-button');
        
        accordionButtons.forEach(function(button) {
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    button.click();
                }
            });
            
            // Update aria-expanded when accordion state changes
            button.addEventListener('click', function() {
                setTimeout(function() {
                    const isExpanded = !button.classList.contains('collapsed');
                    button.setAttribute('aria-expanded', isExpanded.toString());
                    
                    // Announce state change to screen readers
                    const announcement = isExpanded ? 'Answer expanded' : 'Answer collapsed';
                    announceToScreenReader(announcement);
                }, 100);
            });
        });
    });
    
    // Make question text a link target but keep left-click for toggling
    const questionLinks = module.querySelectorAll('.accordion-question-link');
    questionLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const isLeftClick = e.button === 0;
            const hasModifier = e.metaKey || e.ctrlKey || e.shiftKey || e.altKey;
            if (isLeftClick && !hasModifier) {
                e.preventDefault(); // prevent navigation on simple left-click
                // let event bubble to button to toggle
            }
        });
        link.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const button = link.closest('.accordion-button');
                if (button) {
                    button.click();
                }
            }
        });
    });
    
    // Add live region for announcements
    if (!document.getElementById('bearsfaq-announcements')) {
        const liveRegion = document.createElement('div');
        liveRegion.id = 'bearsfaq-announcements';
        liveRegion.setAttribute('aria-live', 'polite');
        liveRegion.setAttribute('aria-atomic', 'true');
        liveRegion.className = 'sr-only';
        document.body.appendChild(liveRegion);
    }
}

function announceToScreenReader(message) {
    const liveRegion = document.getElementById('bearsfaq-announcements');
    if (liveRegion) {
        liveRegion.textContent = message;
        
        // Clear the message after a short delay
        setTimeout(function() {
            liveRegion.textContent = '';
        }, 1000);
    }
}

// Handle focus management for tab panels
document.addEventListener('shown.bs.tab', function(e) {
    const targetPanel = document.querySelector(e.target.getAttribute('data-bs-target'));
    if (targetPanel) {
        // Focus the tab panel for screen reader users
        targetPanel.focus();
        
        // Announce the tab change
        const tabName = e.target.textContent.trim();
        announceToScreenReader(`Switched to ${tabName} tab`);
    }
    
    // Handle text-primary class for list style tabs
    const navList = e.target.closest('.nav-list');
    if (navList) {
        // Remove text-primary from all list items
        navList.querySelectorAll('.nav-item').forEach(function(item) {
            item.classList.remove('text-primary');
        });
        
        // Add text-primary to active tab's parent li
        const activeItem = e.target.closest('.nav-item');
        if (activeItem) {
            activeItem.classList.add('text-primary');
        }
    }
});

// Handle accordion state announcements
document.addEventListener('shown.bs.collapse', function(e) {
    if (e.target.closest('.bearsfaq-tabs')) {
        const button = document.querySelector(`[data-bs-target="#${e.target.id}"]`);
        if (button) {
            const questionText = button.querySelector('.accordion-question');
            if (questionText) {
                announceToScreenReader(`Answer for "${questionText.textContent.trim()}" is now expanded`);
            }
        }
    }
});

document.addEventListener('hidden.bs.collapse', function(e) {
    if (e.target.closest('.bearsfaq-tabs')) {
        const button = document.querySelector(`[data-bs-target="#${e.target.id}"]`);
        if (button) {
            const questionText = button.querySelector('.accordion-question');
            if (questionText) {
                announceToScreenReader(`Answer for "${questionText.textContent.trim()}" is now collapsed`);
            }
        }
    }
});
