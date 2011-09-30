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

if (elgg_instanceof($forum, 'object', 'forum')) {
	// Delete 
	if ($forum->delete()) {
		// Success
		system_message(elgg_echo('forums:success:forum:delete'));
		forward('admin/forums/manage');
	} else {
		// Error
		register_error(elgg_echo('forums:error:forum:delete'));
		forward('admin/forums/manage');
	}
}
