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

// Ensure Bootstrap 5 assets are loaded
HTMLHelper::_('bootstrap.framework');
HTMLHelper::_('bootstrap.tabs');

// Get database
$db    = Factory::getDbo();
$app   = Factory::getApplication();

// Module params (could be extended)
$faqCategoryAlias = 'faq';
$maxArticles      = 100;

// Find category ID for 'faq'
$query = $db->getQuery(true)
    ->select($db->qn('id'))
    ->from($db->qn('#__categories'))
    ->where($db->qn('alias') . ' = ' . $db->q($faqCategoryAlias))
    ->where($db->qn('extension') . ' = "com_content"');
$db->setQuery($query);
$categoryId = $db->loadResult();

if (!$categoryId) {
    echo '<div class="alert alert-warning">FAQ category not found (alias: ' . htmlspecialchars($faqCategoryAlias) . ')</div>';
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
    echo '<div class="alert alert-info">No FAQ articles found in category.</div>';
    return;
}

$faqTabs = [];
$untaggedKey = '__untagged__';

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
        $faqTabs[$untaggedKey]['title'] = Text::_('MOD_BEARSFAQ_TAB_GENERAL');
        $faqTabs[$untaggedKey]['articles'][] = $article;
        continue;
    }
    foreach ($tags as $tag) {
        if (!isset($faqTabs[$tag->alias])) {
            $faqTabs[$tag->alias] = [
                'title' => $tag->title,
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
echo '<div id="' . $moduleId . '" class="bearsfaq-tabs">';
// Tabs header
echo '<ul class="nav nav-tabs mb-3" id="' . $moduleId . '-tab" role="tablist">';
$i = 0;
foreach ($faqTabs as $tabId => $tabInfo) {
    $active = $i === 0 ? 'active' : '';
    echo '<li class="nav-item" role="presentation">';
    echo '<button class="nav-link ' . $active . '" id="' . $moduleId . '-' . $tabId . '-tab" data-bs-toggle="tab" data-bs-target="#' . $moduleId . '-' . $tabId . '" type="button" role="tab" aria-controls="' . $moduleId . '-' . $tabId . '" aria-selected="' . ($active ? 'true' : 'false') . '">' . htmlspecialchars($tabInfo['title']) . '</button>';
    echo '</li>';
    $i++;
}
echo '</ul>';
// Tabs content
echo '<div class="tab-content" id="' . $moduleId . '-tabContent">';
$i = 0;
foreach ($faqTabs as $tabId => $tabInfo) {
    $active = $i === 0 ? 'show active' : '';
    echo '<div class="tab-pane fade ' . $active . '" id="' . $moduleId . '-' . $tabId . '" role="tabpanel" aria-labelledby="' . $moduleId . '-' . $tabId . '-tab">';
    // Accordion for this tab
    $accordId = 'accordion-' . $moduleId . '-' . $tabId;
    echo '<div class="accordion" id="' . $accordId . '">';
    $q = 0;
    foreach ($tabInfo['articles'] as $faq) {
        $itemId = $accordId . '-item-' . $faq->id;
        $collapseId = $accordId . '-collapse-' . $faq->id;
        $headingId = $accordId . '-heading-' . $faq->id;
        $isFirst = $q == 0 ? 'show' : '';
        $answerHTML = $faq->fulltext ? $faq->fulltext : $faq->introtext;
        echo '<div class="accordion-item">';
        echo '<h2 class="accordion-header" id="' . $headingId . '">';
        // Use data-bs-parent attribute to ensure only one is open per accordion/tab
        echo '<button class="accordion-button ' . ($isFirst ? '' : 'collapsed') . '" type="button" data-bs-toggle="collapse" data-bs-target="#' . $collapseId . '" aria-expanded="' . ($isFirst ? 'true' : 'false') . '" aria-controls="' . $collapseId . '">';
        echo htmlspecialchars($faq->title);
        echo '</button>';
        echo '</h2>';
        echo '<div id="' . $collapseId . '" class="accordion-collapse collapse ' . $isFirst . '" aria-labelledby="' . $headingId . '" data-bs-parent="#' . $accordId . '">';
        echo '<div class="accordion-body">' . $answerHTML . '</div>';
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
