<?php
/**
 * Forums Reply Create River Entry
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 */
$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->description);
$excerpt = elgg_get_excerpt($excerpt);

// Custom summary
$owner = $object->getOwnerEntity();
$owner_link = "<a href='" . $owner->getURL . "'>" . $owner->name . "</a>";

$topic = get_entity($object->topic_guid);
$forum = $object->getContainerEntity();

$topic_link = elgg_view('output/url', array(
	'text' => $topic->title,
	'href' => $topic->getURL(),
));

$forum_link = elgg_view('output/url', array(
	'text' => $forum->title,
	'href' => $forum->getURL(),
));

$summary = elgg_echo('river:create:object:forum_reply', array($owner_link, $topic_link, $forum_link));

echo elgg_view('river/item', array(
	'item' => $vars['item'],
	'summary' => $summary,
	'message' => $excerpt,
));