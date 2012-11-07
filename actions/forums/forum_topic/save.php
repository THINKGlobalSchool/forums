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
$tags = string_to_tag_array(get_input('tags'));
$guid = get_input('guid');

// Create Sticky form
elgg_make_sticky_form('forum-topic-edit-form');

// Check inputs
if (!$title || (!$guid && !$description)) {
	register_error(elgg_echo('forums:error:requiredfields'));
	forward(REFERER);
}

$forum = get_entity($container_guid);
if (!elgg_instanceof($forum, 'object', 'forum')) {
	register_error(elgg_echo('forums:error:forum:invalid'));
	forward(REFERER);
}

// New Topic
if (!$guid) {
	$topic = new ElggObject();
	$topic->subtype = 'forum_topic';
	$topic->access_id = $forum->access_id;
	$topic->container_guid = $container_guid;	
} else { // Editing
	$topic = get_entity($guid);
	if (!elgg_instanceof($topic, 'object', 'forum_topic')) {
		register_error(elgg_echo('forums:error:forum_topic:edit'));
		forward(REFERER);
	}
}

$topic->title = $title;

// Make sure not to set any tags on anonymous posts
if (!$topic->getContainerEntity()->anonymous) {
	$topic->tags = $tags;
}

// Try saving
if (!$topic->save()) {
	// Error.. say so and forward
	register_error(elgg_echo('forums:error:forum_topic:save'));
	forward(REFERER);
}

// If we're creating a new topic, create the initial reply from this topics description
if (!$guid) {
	// Notify users
	forums_notify_new_topic($topic);

	$reply = new ElggObject();
	$reply->subtype = 'forum_reply';
	$reply->access_id = $topic->access_id;
	$reply->description = $description;
	$reply->topic_guid = $topic->guid;

	// Set container guid to the topic's contaier guid (the forum)
	$reply->container_guid = $topic->container_guid;
	
	// Try saving
	if (!$reply->save()) {
		// Error.. say so and forward
		register_error(elgg_echo('forums:error:forum_reply:save'));
		forward(REFERER);
	}

	// Add river entries if we're posting in an anonymous forum
	if (!$topic->getContainerEntity()->anonymous) {
		add_to_river('river/object/forum_topic/create', 'create', elgg_get_logged_in_user_guid(), $topic->guid);
		add_to_river('river/object/forum_reply/create', 'create', elgg_get_logged_in_user_guid(), $reply->guid);
	}

	// Add reply to relationship
	add_entity_relationship($reply->guid, FORUM_REPLY_RELATIONSHIP, $topic->guid);
}

// Clear Sticky form
elgg_clear_sticky_form('forum-topic-edit-form');

system_message(elgg_echo('forums:success:forum_topic:save'));
forward(elgg_get_site_url() . 'forums/forum_topic/view/' . $topic->guid);