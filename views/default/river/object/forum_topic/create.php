<?php
/**
 * Forums Topic Create River Entry
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 *
 */
$object = $vars['item']->getObjectEntity();

// Custom summary
$owner = $object->getOwnerEntity();
$owner_link = "<a href='" . $owner->getURL() . "'>" . $owner->name . "</a>";

$forum = $object->getContainerEntity();

$topic_link = elgg_view('output/url', array(
	'text' => $object->title,
	'href' => $object->getURL(),
));

$forum_link = elgg_view('output/url', array(
	'text' => $forum->title,
	'href' => $forum->getURL(),
));

$summary = elgg_echo('river:create:object:forum_topic_river', array($owner_link, $topic_link, $forum_link));

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'summary' => $summary,
));