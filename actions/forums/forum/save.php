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
$moderator_mask = get_input('moderator_mask', FALSE);
$guid = get_input('guid');

// Create Sticky form
elgg_make_sticky_form('forum-edit-form');

// Check inputs
if (!$title || !$description) {
	register_error(elgg_echo('forums:error:requiredfields'));
	forward(REFERER);
}

// New Forum
if (!$guid) {
	$forum = new ElggObject();
	$forum->subtype = 'forum';
	$forum->access_id = ACCESS_LOGGED_IN;
} else { // Editing
	$forum = get_entity($guid);
	if (!elgg_instanceof($forum, 'object', 'forum')) {
		register_error(elgg_echo('forums:error:forum:edit'));
		forward(REFERER);
	}
}

$forum->title = $title;
$forum->description = $description;
$forum->anonymous = $anonymous;
$forum->moderator_role = $moderator_role;
$forum->moderator_mask = $moderator_mask;

// Try saving
if (!$forum->save()) {
	// Error.. say so and forward
	register_error(elgg_echo('forums:error:forum:save'));
	forward(REFERER);
}

// Clear Sticky form
elgg_clear_sticky_form('forum-edit-form');

system_message(elgg_echo('forums:success:forum:save'));
forward(elgg_get_site_url() . 'admin/forums/manage');