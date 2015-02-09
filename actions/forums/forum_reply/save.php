<?php
/**
 * Forums Forum Reply Save Action
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get inputs
$description = get_input ('description');
$topic_guid = get_input('topic_guid');
$reply_guid = get_input('reply_guid');
$guid = get_input('guid');

// Create Sticky form
elgg_make_sticky_form('forum-reply-edit-form');

// Check inputs
$topic = get_entity($topic_guid);
if (!elgg_instanceof($topic, 'object', 'forum_topic')) {
	register_error(elgg_echo('forums:error:forum_topic:invalid'));
	forward(REFERER);
}

// Double check to make sure we're not replying to a closed topic
if ($topic->topic_status == 'closed') {
	register_error(elgg_echo('forums:label:closeddesc'));
	forward(REFERER);
}

if (!$description) {
	register_error(elgg_echo('forums:error:requiredfields'));
	forward(REFERER);
}

// New Forum
if (!$guid) {
	$reply = new ElggObject();
	$reply->subtype = 'forum_reply';
	$reply->access_id = $topic->access_id;
	$reply->topic_guid = $topic->guid;

	// Set container guid to the topic's contaier guid (the forum)
	$reply->container_guid = $topic->container_guid;
} else { // Editing
	$reply = get_entity($guid);
	if (!elgg_instanceof($reply, 'object', 'forum_reply')) {
		register_error(elgg_echo('forums:error:forum_reply:edit'));
		forward(REFERER);
	}
}

$reply->description = $description;

// Try saving
if (!$reply->save()) {
	// Error.. say so and forward
	register_error(elgg_echo('forums:error:forum_reply:save'));
	forward(REFERER);
}

// Add relationship and river entry(on create only)
if (!$guid) {
	// Get the entity that this object is in reply too
	$reply_to = get_entity($reply_guid);
	if (!elgg_instanceof($reply_to, 'object', 'forum_topic') && !elgg_instanceof($reply_to, 'object', 'forum_reply')) {
		register_error(elgg_echo('forums:error:forum_topic:invalid'));
		forward(REFERER);
	}
	
	// This states that: 'reply' is a forum_reply_to 'reply/topic' 
	add_entity_relationship($reply->guid, FORUM_REPLY_RELATIONSHIP, $reply_to->guid);

	// Add a relationship for the user to signify that they are now participating in the topic
	add_entity_relationship(elgg_get_logged_in_user_guid(), FORUM_TOPIC_PARTICIPANT_RELATIONSHIP, $topic->guid);

	// Notify users of new reply
	forums_notify_new_reply($reply);

	// Add river entry if we're not posting in an anonymous forum
	if (!$reply->getContainerEntity()->anonymous) {
		elgg_create_river_item(array(
			'view' => 'river/object/forum_reply/create',
			'action_type' => 'create',
			'subject_guid' => elgg_get_logged_in_user_guid(),
			'object_guid' => $reply->guid
		));
	}
}

// Clear Sticky form
elgg_clear_sticky_form('forum-reply-edit-form');

system_message(elgg_echo('forums:success:forum_reply:save'));
forward(elgg_get_site_url() . 'forums/forum_topic/view/' . $topic->guid . '/#forum-reply-' . $reply->guid);