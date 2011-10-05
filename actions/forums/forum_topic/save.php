<?php
/**
 * Forums Forum Topic Save Action
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get inputs
$title = get_input('title');
$description = get_input ('description');
$container_guid = get_input('container_guid');
$guid = get_input('guid');

// Create Sticky form
elgg_make_sticky_form('forum-topic-edit-form');

// Check inputs
if (!$title || !$description) {
	register_error(elgg_echo('forums:error:requiredfields'));
	forward(REFERER);
}

// New Forum
if (!$guid) {
	$topic = new ElggObject();
	$topic->subtype = 'forum_topic';
	$topic->access_id = ACCESS_LOGGED_IN;
	$topic->container_guid = $container_guid;
} else { // Editing
	$topic = get_entity($guid);
	if (!elgg_instanceof($topic, 'object', 'forum_topic')) {
		register_error(elgg_echo('forums:error:forum_topic:edit'));
		forward(REFERER);
	}
}

$topic->title = $title;
$topic->description = $description;

// Try saving
if (!$topic->save()) {
	// Error.. say so and forward
	register_error(elgg_echo('forums:error:forum_topic:save'));
	forward(REFERER);
}

// Clear Sticky form
elgg_clear_sticky_form('forum-topic-edit-form');

system_message(elgg_echo('forums:success:forum_topic:save'));
forward(elgg_get_site_url() . 'forums/view/' . $container_guid);