<?php
/**
 * Forums Helper Library
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

/** Prepare forum form vars */
function forums_prepare_forum_form_vars($forum) {
	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'anonymous' => '',
		'moderator_role' => '',
	);

	if ($forum) {
		foreach (array_keys($values) as $field) {
			$values[$field] = $forum->$field;
		}
	}

	if (elgg_is_sticky_form('forum-edit-form')) {
		foreach (array_keys($values) as $field) {
			$values[$field] = elgg_get_sticky_value('forum-edit-form', $field);
		}
	}

	elgg_clear_sticky_form('forum-edit-form');

	return $values;
}