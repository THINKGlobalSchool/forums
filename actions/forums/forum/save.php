<?php
/**
 * Forums Forum Save Action
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
$anonymous = get_input('anonymous');
$moderator_role = get_input('moderator_role');
$moderators = get_input('members');	// Supplied if editing a group forum
$moderator_mask = get_input('moderator_mask', FALSE);
$container_guid = get_input('container_guid');
$access_id = get_input('access_id', ACCESS_LOGGED_IN);
$guid = get_input('guid');

// Check to make sure we can write to the container (for group owners)
$container = get_entity($container_guid);
if (!$container->canWriteToContainer(elgg_get_logged_in_user_guid())) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

// Create Sticky form
elgg_make_sticky_form('forum-edit-form');

// Check inputs
if (!$title || !$description) {
	register_error(elgg_echo('forums:error:requiredfields'));
	forward(REFERER);
}

// If forum is anonymous, use a different access id
$access_id = $anonymous ? ACCESS_ANONYMOUS : $access_id;

// New Forum
if (!$guid) {
	$forum = new ElggObject();
	$forum->subtype = 'forum';
	$forum->container_guid = $container_guid;

	// Save some metadata to easily identify this as a site forum
	if (!elgg_instanceof($container, 'group')) {
		$forum->site_forum = TRUE;
	} else {
		$forum->site_forum = FALSE;
	}
} else { // Editing
	$forum = get_entity($guid);
	if (!elgg_instanceof($forum, 'object', 'forum')) {
		register_error(elgg_echo('forums:error:forum:edit'));
		forward(REFERER);
	}

	// Get this forums topics and replies to update their access_id
	$options = array(
		'type' => 'object',
		'subtypes' => array('forum_topic', 'forum_reply'),
		'limit' => 0,
		'container_guid' => $forum->guid,
	);

	$contents = new ElggBatch('elgg_get_entities', $options);

	foreach ($contents as $content) {
		$content->access_id = $access_id;
		$content->save();
	}
}

$forum->title = $title;
$forum->description = $description;
$forum->anonymous = $anonymous;
$forum->moderator_role = $moderator_role;
$forum->moderators = $moderators;
$forum->moderator_mask = $moderator_mask;

// If access ID has changed, we need to update topics/replies as well
if ($access_id != $forum->access_id) {
	$update_children = TRUE;
}

$forum->access_id = $access_id;

// Try saving
if (!$forum->save()) {
	// Error.. say so and forward
	register_error(elgg_echo('forums:error:forum:save'));
	forward(REFERER);
}


// Update child entities access
if ($update_children) {
	$options = array(
		'type' => 'object',
		'subtypes' => array('forum_topic', 'forum_reply'),
		'container_guid' => $forum->guid,
	);

	$children = new ElggBatch('elgg_get_entities', $options);
	
	foreach ($children as $child) {
		$child->access_id = $access_id;
	}
}


// Clear Sticky form
elgg_clear_sticky_form('forum-edit-form');

system_message(elgg_echo('forums:success:forum:save'));

if (elgg_instanceof($container, 'group')) {
	forward(elgg_get_site_url() . "forums/group/{$container->guid}/all");
} else {
	forward(elgg_get_site_url() . 'admin/forums/manage');
}

