<?php
/**
 * Forums Forum Reply Set Status (open/closed)
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

$guid = get_input('guid');
$status = get_input('status', 'open');

if (!in_array($status, array('open', 'closed'))) {
	register_error(elgg_echo('forums:error:invalidstatus'));
	forward(REFERER);
}

$reply = get_entity($guid);

if (elgg_instanceof($reply, 'object', 'forum_reply')) {
	$reply->reply_status = $status;
	if (!$reply->save()) {
		register_error(elgg_echo('forums:error:setstatus'));
	} else {
		system_message(elgg_echo("forums:success:forum_reply:{$status}"));
	}
}

forward(REFERER);