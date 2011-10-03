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

/**
 * Get forum view content
 * @param int $guid		Object guid
 */
function forums_get_page_content_view($guid) {
	
	$params = array(
		'filter' => '',
		'header' => '',
	);

	if (elgg_entity_exists($guid)) {
		$entity = get_entity($guid);
		if (elgg_instanceof($entity, 'object', 'forum')) {
			$owner = $entity->getOwnerEntity();
			$params['title'] = $entity->title;
			$params['content'] = elgg_view_entity($entity, array('full_view' => TRUE));
			elgg_push_breadcrumb($entity->title);
			return $params;
		} else {
			// Most likely a permission issue here
			register_error(elgg_echo('forums:error:permissiondenied'));
			forward();
		}
	}
	
	$params['content'] = elgg_echo('forums:error:notfound');
	return $params;
}

/**
 * Get page components to list site forums
 *
 * @return array
 */
function forums_get_page_content_list() {
	
	$params = array(
		'filter' => '',
		'header' => '',
	);

	$options = array(
		'type' => 'object',
		'subtype' => 'forum',
		'full_view' => FALSE,
	);

	$params['title'] = elgg_echo('forums:title:allforums');

	$list = elgg_list_entities($options);
	if (!$list) {
		$params['content'] = elgg_echo('forums:label:none');
	} else {
		$params['content'] = $list;
	}

	return $params;
}

/** Prepare forum form vars */
function forums_prepare_forum_form_vars($forum) {
	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'anonymous' => '',
		'moderator_role' => '',
		'guid' => '',
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
