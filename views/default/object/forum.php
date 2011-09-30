<?php
/**
 * Forums Forum Object view
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
$forum = elgg_extract('entity', $vars, false);
$full_view = elgg_extract('full_view', $vars, false);

if (!$forum) {
	return '';
}

$owner = get_entity($forum->owner_guid);
$owner_icon = elgg_view_entity_icon($owner, 'tiny');

$owner_link = "<a href=\"{$owner->getURL()}\">{$owner->name}</a>";
$author_text = elgg_echo('byline', array($owner_link));
$linked_title = "<a href=\"{$forum->getURL()}\" title=\"" . htmlentities($forum->title) . "\">{$forum->title}</a>";
$date = elgg_view_friendly_time($forum->time_updated);

$subtitle = "$author_text $date";

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'forums/forum',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

if ($full_view) {
	echo <<<___HTML
<div class="forum"></div>
___HTML;

} else {

	$params = array(
		'entity' => $forum,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => elgg_get_excerpt($forum->description),
	);
	
	$body = elgg_view('object/elements/summary', $params);
	echo elgg_view_image_block($owner_icon, $body);
}