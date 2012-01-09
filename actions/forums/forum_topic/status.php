<?php
/**
 * Forums Forum Topic Set Status (open/closed)
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$guid = get_input('guid');
$status = get_input('status', 'open');

if (!in_array($status, array('open', 'closed'))) {
	register_error(elgg_echo('forums:error:invalidstatus'));
	forward(REFERER);
}

$topic = get_entity($guid);

if (elgg_instanceof($topic, 'object', 'forum_topic')) {
	$topic->topic_status = $status;
	if (!$topic->save()) {
		register_error(elgg_echo('forums:error:setstatus'));
	} else {
		system_message(elgg_echo("forums:success:forum_topic:{$status}"));
	}
}

forward(REFERER);