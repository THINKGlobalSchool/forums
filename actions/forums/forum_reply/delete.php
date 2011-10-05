<?php
/**
 * Forums Forum Reply Delete Action
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get inputs
$guid = get_input('guid');

$forum_reply = get_entity($guid);

$topic = get_entity($forum_reply->topic_guid);

if (elgg_instanceof($forum_reply, 'object', 'forum_reply')) {
	// Delete 
	if ($forum_reply->delete()) {
		// Success
		system_message(elgg_echo('forums:success:forum_reply:delete'));
		forward('forums/topic/view/' . $topic->guid);
	} else {
		// Error
		register_error(elgg_echo('forums:error:forum_reply:delete'));
		forward(REFERER);
	}
}
