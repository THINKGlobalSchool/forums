<?php
/**
 * Forums Forum Topic Delete Action
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

$forum_topic = get_entity($guid);

$forum = $forum_topic->getContainerEntity();

if (elgg_instanceof($forum_topic, 'object', 'forum_topic')) {
	// Delete 
	if ($forum_topic->delete()) {
		// Success
		system_message(elgg_echo('forums:success:forum_topic:delete'));
		forward('forums/view/' . $forum->guid);
	} else {
		// Error
		register_error(elgg_echo('forums:error:forum_topic:delete'));
		forward(REFERER);
	}
}
