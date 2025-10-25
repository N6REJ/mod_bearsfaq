<?php
/**
 * BearsFAQ Module - Displays FAQ articles grouped as tabs and accordions by tag
 * @package     Joomla.Site
 * @subpackage  mod_bearsfaq
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Tag\TagHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Content\Site\Model\ArticlesModel;
use Joomla\Component\Content\Site\Helper\RouteHelper as ContentHelperRoute;

// Ensure Bootstrap 5 assets are loaded
HTMLHelper::_('bootstrap.framework');

// Load module stylesheet from media/mod_bearsfaq
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$mediaCss  = 'media/mod_bearsfaq/css/mod_bearsfaq.css';
$moduleCss = 'modules/mod_bearsfaq/media/css/mod_bearsfaq.css';
$cssPath   = file_exists(JPATH_ROOT . '/' . $mediaCss)
    ? $mediaCss
    : (file_exists(JPATH_ROOT . '/' . $moduleCss) ? $moduleCss : $mediaCss);
$wa->registerAndUseStyle('mod_bearsfaq.styles', $cssPath);

// Load accessibility JavaScript
$mediaJs  = 'media/mod_bearsfaq/js/mod_bearsfaq.js';
$moduleJs = 'modules/mod_bearsfaq/media/js/mod_bearsfaq.js';
$jsPath   = file_exists(JPATH_ROOT . '/' . $mediaJs)
    ? $mediaJs
    : (file_exists(JPATH_ROOT . '/' . $moduleJs) ? $moduleJs : $mediaJs);
$wa->registerAndUseScript('mod_bearsfaq.accessibility', $jsPath, [], ['defer' => true]);

// Get database
$db    = Factory::getDbo();
$app   = Factory::getApplication();
$user  = Factory::getUser();

// Module params
$categoryId  = (int) $params->get('faq_category_id', 0);
$maxArticles = (int) $params->get('max_articles', 100);

// Get restricted tags
$restrictedTags = $params->get('restricted_tags', []);
if (!is_array($restrictedTags)) {
    $restrictedTags = $restrictedTags ? explode(',', $restrictedTags) : [];
}
// Convert to integers
$restrictedTags = array_map('intval', array_filter($restrictedTags));

// Check user group permissions
$restrictedGroups = (array) $params->get('restricted_user_groups', []);
$allowedGroups    = (array) $params->get('allowed_user_groups', []);

// Get user's groups
$userGroups = $user->getAuthorisedGroups();

// Check if user is in restricted groups (they get special access to restricted tags)
$isInRestrictedGroup = !empty($restrictedGroups) && count(array_intersect($userGroups, $restrictedGroups)) > 0;

// Check if user is in allowed groups (if specified)
// If no allowed groups are specified, everyone has access (including public/guest users)
if (!empty($allowedGroups)) {
    $hasAccess = false;
    foreach ($allowedGroups as $allowedGroup) {
        if (in_array((int)$allowedGroup, $userGroups)) {
            $hasAccess = true;
            break;
        }
    }
    // If user is not in allowed groups AND not in restricted groups, deny access
    if (!$hasAccess && !$isInRestrictedGroup) {
        // User is not in any allowed group or restricted group, don't show module
        return;
    }
}
// If no allowed groups specified, all users have access by default (including public)

// Validate/verify category
if (!$categoryId) {
    echo '<div class="alert alert-warning">' . Text::_('MOD_BEARSFAQ_NO_CATEGORY_SELECTED') . '</div>';
    return;
}

$query = $db->getQuery(true)
    ->select('COUNT(*)')
    ->from($db->qn('#__categories'))
    ->where('id = ' . (int) $categoryId)
    ->where($db->qn('extension') . ' = "com_content"');
$db->setQuery($query);
$categoryExists = (int) $db->loadResult();
if (!$categoryExists) {
    echo '<div class="alert alert-warning">' . Text::_('MOD_BEARSFAQ_NO_CATEGORY_FOUND') . '</div>';
    return;
}

// Get all published articles in FAQ category
$query = $db->getQuery(true)
    ->select('a.id, a.title, a.alias, a.introtext, a.fulltext, a.catid')
    ->from($db->qn('#__content', 'a'))
    ->where('a.catid = ' . (int)$categoryId)
    ->where('a.state = 1')
    ->order($db->qn('a.ordering') . ' ASC');
$db->setQuery($query, 0, $maxArticles);
$articles = $db->loadObjectList();

if (!$articles) {
    echo '<div class="alert alert-info">' . Text::_('MOD_BEARSFAQ_NO_ARTICLES_FOUND') . '</div>';
    return;
}

$faqTabs = [];

foreach ($articles as $article) {
    // Lookup tags for this article
    $query = $db->getQuery(true)
        ->select('t.title, t.alias, t.id')
        ->from('#__tags AS t')
        ->join('INNER', '#__contentitem_tag_map AS m ON m.tag_id = t.id')
        ->where('m.content_item_id = ' . (int)$article->id)
        ->order('t.title ASC');
    $db->setQuery($query);
    $tags = $db->loadObjectList();

    if (!$tags) {
        // Skip articles without tags: do not create a default tab
        continue;
    }
    foreach ($tags as $tag) {
        // Skip restricted tags only if user is NOT in a restricted group
        if (!$isInRestrictedGroup && !empty($restrictedTags) && in_array((int)$tag->id, $restrictedTags)) {
            continue;
        }
        
        if (!isset($faqTabs[$tag->alias])) {
            $faqTabs[$tag->alias] = [
                'title' => $tag->title,
                'tag_id' => (int) $tag->id,
                'articles' => []
            ];
        }
        $faqTabs[$tag->alias]['articles'][] = $article;
    }
}

if (empty($faqTabs)) {
    echo '<div class="alert alert-info">' . Text::_('MOD_BEARSFAQ_NO_TAGS_FOUND') . '</div>';
    return;
}

// Sort tabs alphabetically by title
uasort($faqTabs, function($a, $b) {
    return strcasecmp($a['title'], $b['title']);
});

$moduleId = 'bearsfaq_' . (isset($module->id) ? (int)$module->id : uniqid());

// Tabs style/orientation parameters
$tabStyle = $params->get('tab_style', 'tabs'); // 'tabs', 'pills', or 'list'
$tabOrientation = $params->get('tab_orientation', 'horizontal'); // 'horizontal' or 'vertical'

// Styling params as CSS variables
$activeTabColor      = trim((string) $params->get('active_tab_color', ''));
$inactiveTabColor    = trim((string) $params->get('inactive_tab_color', ''));
$tabFontColor        = trim((string) $params->get('tab_font_color', ''));
$questionColor       = trim((string) $params->get('question_color', ''));
$borderColor         = trim((string) $params->get('border_color', ''));
$activeUnderlineColor = trim((string) $params->get('active_underline_color', ''));
$borderRadius        = (int) $params->get('border_radius', 8);
$tabGap              = (int) $params->get('tab_gap', 2);
$tabAlignment        = trim((string) $params->get('tab_alignment', 'flex-start'));

$styleVars = [];
if ($activeTabColor   !== '') { $styleVars[] = '--bfq-active-tab-color:' . htmlspecialchars($activeTabColor, ENT_QUOTES, 'UTF-8'); }
if ($inactiveTabColor !== '') { $styleVars[] = '--bfq-inactive-tab-color:' . htmlspecialchars($inactiveTabColor, ENT_QUOTES, 'UTF-8'); }
if ($tabFontColor     !== '') { $styleVars[] = '--bfq-tab-font-color:' . htmlspecialchars($tabFontColor, ENT_QUOTES, 'UTF-8'); }
if ($questionColor    !== '') { $styleVars[] = '--bfq-question-color:' . htmlspecialchars($questionColor, ENT_QUOTES, 'UTF-8'); }
if ($borderColor      !== '') { $styleVars[] = '--bfq-border-color:' . htmlspecialchars($borderColor, ENT_QUOTES, 'UTF-8'); }
if ($activeUnderlineColor !== '') { $styleVars[] = '--bfq-active-underline-color:' . htmlspecialchars($activeUnderlineColor, ENT_QUOTES, 'UTF-8'); }
$styleVars[] = '--bfq-border-radius:' . $borderRadius . 'px';
$styleVars[] = '--bfq-tab-gap:' . $tabGap . 'px';
$styleVars[] = '--bfq-tab-alignment:' . htmlspecialchars($tabAlignment, ENT_QUOTES, 'UTF-8');

$styleAttr = $styleVars ? ' style="' . implode(';', $styleVars) . '"' : '';

// Add alignment class for special handling
$alignmentClass = '';
if ($tabAlignment === 'equal-width') {
    $alignmentClass = ' bearsfaq-equal-width';
} elseif ($tabAlignment === 'stretch') {
    $alignmentClass = ' bearsfaq-justified';
}

// Add orientation class for layout handling
$orientationClass = '';
if ($tabOrientation === 'vertical') {
    $orientationClass = ' bearsfaq-vertical';
}

echo '<div id="' . $moduleId . '" class="bearsfaq-tabs' . $alignmentClass . $orientationClass . '"' . $styleAttr . '>';

// Compose Bootstrap classes
if ($tabStyle === 'list') {
    $tabClass = 'nav-list'; // Custom class for list style
} elseif ($tabStyle === 'pills') {
    $tabClass = 'nav-pills';
} else {
    $tabClass = 'nav-tabs';
}

if ($tabOrientation === 'vertical') {
    // For vertical, use Bootstrap's flex-column
    $tabClass .= ' flex-column';
} else {
    $tabClass .= ' flex-row';
}

// Tabs header with enhanced accessibility
echo '<ul class="nav ' . $tabClass . ' mb-3" id="' . $moduleId . '-tab" role="tablist" aria-label="FAQ Categories">';
$i = 0;
foreach ($faqTabs as $tabId => $tabInfo) {
    $active = $i === 0 ? 'active' : '';
    $tabIndex = $active ? '0' : '-1'; // Only active tab is focusable initially
    // Add text-primary class to active list items when using list style
    $itemClass = ($tabStyle === 'list' && $active) ? ' text-primary' : '';
    echo '<li class="nav-item' . $itemClass . '" role="presentation">';
    echo '<a class="nav-link ' . $active . '" id="' . $moduleId . '-' . $tabId . '-tab" data-bs-toggle="tab" data-bs-target="#' . $moduleId . '-' . $tabId . '" href="' . (isset($tabInfo['tag_id']) ? Route::_('index.php?option=com_tags&view=tag&id=' . (int) $tabInfo['tag_id']) : '#') . '" role="tab" aria-controls="' . $moduleId . '-' . $tabId . '" aria-selected="' . ($active ? 'true' : 'false') . '" tabindex="' . $tabIndex . '" aria-describedby="' . $moduleId . '-' . $tabId . '-desc">' . htmlspecialchars($tabInfo['title']) . '</a>';

    echo '</li>';
    $i++;
}
echo '</ul>';
// Tabs content
echo '<div class="tab-content" id="' . $moduleId . '-tabContent">';
$i = 0;
foreach ($faqTabs as $tabId => $tabInfo) {
    $active = $i === 0 ? 'show active' : '';
    echo '<div class="tab-pane fade ' . $active . '" id="' . $moduleId . '-' . $tabId . '" role="tabpanel" aria-labelledby="' . $moduleId . '-' . $tabId . '-tab" tabindex="0">';
    // Hidden description for screen readers
    echo '<div id="' . $moduleId . '-' . $tabId . '-desc" class="sr-only">Frequently asked questions for ' . htmlspecialchars($tabInfo['title']) . ' category. Use arrow keys to navigate between questions and Enter or Space to expand answers.</div>';
    // Accordion for this tab
    $accordId = 'accordion-' . $moduleId . '-' . $tabId;
    echo '<div class="accordion" id="' . $accordId . '" role="region" aria-label="' . htmlspecialchars($tabInfo['title']) . ' FAQ Questions">';
    $q = 0;
    foreach ($tabInfo['articles'] as $faq) {
        $itemId = $accordId . '-item-' . $faq->id;
        $collapseId = $accordId . '-collapse-' . $faq->id;
        $headingId = $accordId . '-heading-' . $faq->id;
        $answerHTML = $faq->fulltext ? $faq->fulltext : $faq->introtext;
        echo '<div class="accordion-item">';
        echo '<h3 class="accordion-header" id="' . $headingId . '">';
        // Use data-bs-parent attribute to ensure only one is open per accordion/tab
        $link = Route::_(ContentHelperRoute::getArticleRoute($faq->id . ':' . $faq->alias, $faq->catid));
        echo '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#' . $collapseId . '" aria-expanded="false" aria-controls="' . $collapseId . '" aria-describedby="' . $headingId . '-hint">';
        echo '<a href="' . $link . '" class="accordion-question accordion-question-link">' . htmlspecialchars($faq->title) . '</a>';
        echo '<span id="' . $headingId . '-hint" class="sr-only">Press Enter or Space to expand answer</span>';
        echo '</button>';
        echo '</h3>';
        echo '<div id="' . $collapseId . '" class="accordion-collapse collapse" aria-labelledby="' . $headingId . '" data-bs-parent="#' . $accordId . '" role="region">';
        echo '<div class="accordion-body" role="article" aria-label="Answer to: ' . htmlspecialchars($faq->title) . '">' . $answerHTML . '</div>';
        echo '</div>';
        echo '</div>';
        $q++;
    }
    echo '</div>';
    echo '</div>';
    $i++;
}
echo '</div>';
echo '</div>';
