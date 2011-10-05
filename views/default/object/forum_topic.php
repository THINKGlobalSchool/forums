<?php
/**
 * Forums Forum Topic Object view
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$full = elgg_extract('full_view', $vars, FALSE);
$topic = elgg_extract('entity', $vars, FALSE);

if (!$topic) {
	return TRUE;
}

$owner = $topic->getOwnerEntity();
$container = $topic->getContainerEntity();

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'forums/forum_topic',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));


$date = elgg_view_friendly_time($topic->time_created);

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full) {
	$icon_url = $owner->getIconURL('topbar');
	$owner_icon = "<img src=\"$icon_url\" alt=\"$owner->name\" title=\"$owner->name\" class='forum-reply-icon elgg-border-plain elgg-transition' />";
	$owner_link = elgg_view('output/url', array(
		'href' => "blog/owner/$owner->username",
		'text' => $owner_icon . $owner->name,
	));
	
	$dateline = elgg_echo('forums:label:dateline', array($date));

	$title = "<div class='elgg-subtext forum-topic-subtext'>$owner_link $dateline</div> $metadata";
	
	$description = "<div class='forum-topic-description'>" . $topic->description . "</div>";
	
	$content = elgg_view_module('inline', $title, $description);
	
	echo <<<HTML
	<div class='forum-topic'>
		$content
	</div>
HTML;

} else {
	// brief view
	$owner_link = elgg_view('output/url', array(
		'href' => "blog/owner/$owner->username",
		'text' => $owner->name,
	));
	
	$author_text = elgg_echo('byline', array($owner_link));
	$subtitle = "<p>$author_text $date</p>";
	
	$params = array(
		'entity' => $topic,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
		'content' => '',
	);
	$params = $params + $vars;
	$body = elgg_view('object/elements/summary', $params);

	$owner_icon = elgg_view_entity_icon($owner, 'tiny');

	echo elgg_view_image_block($owner_icon, $body);
}
