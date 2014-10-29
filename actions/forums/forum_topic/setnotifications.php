<?php
/**
 * Forums Forum Topic Notifications opt in/out
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get inputs
$topic_guid = get_input('topic');

$topic = get_entity($topic_guid);

$opt_action = get_input('opt_action', 0);

if (!elgg_instanceof($topic, 'object', 'forum_topic')) {
	register_error(elgg_echo('forums:error:forum_topic:invalid'));
   	forward(REFERER);
}

if ($opt_action) {
	add_entity_relationship(elgg_get_logged_in_user_guid(), FORUM_TOPIC_PARTICIPANT_RELATIONSHIP, $topic->guid);
	remove_entity_relationship(elgg_get_logged_in_user_guid(), FORUM_TOPIC_NO_NOTIFY_RELATIONSHIP, $topic->guid);
} else {
	remove_entity_relationship(elgg_get_logged_in_user_guid(), FORUM_TOPIC_PARTICIPANT_RELATIONSHIP, $topic->guid);
	add_entity_relationship(elgg_get_logged_in_user_guid(), FORUM_TOPIC_NO_NOTIFY_RELATIONSHIP, $topic->guid);
}

system_message(elgg_echo('forums:success:forum_topic:notifications'));
forward(REFERER);