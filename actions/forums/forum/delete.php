<?php
/**
 * Forums Forum Delete Action
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

$forum = get_entity($guid);

// Check to make sure we can write to the container (for group owners)
$container = $forum->getContainerEntity();
if (!$container->canWriteToContainer(elgg_get_logged_in_user_guid())) {
	register_error(elgg_echo('actionunauthorized'));
	forward(REFERER);
}

if (elgg_instanceof($forum, 'object', 'forum')) {
	// Delete 
	if ($forum->delete()) {
		// Success
		system_message(elgg_echo('forums:success:forum:delete'));
	} else {
		// Error
		register_error(elgg_echo('forums:error:forum:delete'));
	}

	// Forward to group list, or admin page
	if (elgg_instanceof($container, 'group')) {
		forward("forums/group/{$container->guid}/all");
	} else {
		forward('admin/forums/manage');
	}
}
