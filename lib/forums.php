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
		'layout' => 'one_column',
	);
	
	if (elgg_entity_exists($guid)) {
		$entity = get_entity($guid);
		if (elgg_instanceof($entity, 'object', 'forum')
			|| elgg_instanceof($entity, 'object', 'forum_topic')
			|| elgg_instanceof($entity, 'object', 'forum_reply')) 
		{
			$params['title'] = $entity->title;
			$params['content'] = elgg_view_entity($entity, array('full_view' => TRUE));
			
			if ($entity->getSubtype() == 'forum') {
				
			} else if ($entity->getSubtype() == 'forum_topic') {
				$forum = $entity->getContainerEntity();
				elgg_push_breadcrumb($forum->title, $forum->getURL());
			}
			
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

/**
 * Get page content to edit/create a forum topic
 * 
 * @param string $page_type Add/Edit
 * @param int    $guid      Forum/Topic guid
 */
function forums_get_page_content_topic_edit($page_type, $guid = NULL) {
	
	$params = array(
		'filter' => '',
	);
	
	// Form vars
	$vars = array();
	$vars['id'] = 'forum-topic-edit-form';
	$vars['name'] = 'forum-topic-edit-form';
	
	if ($page_type == 'edit') {
		// Editing
		$title = elgg_echo('forums:title:topicedit');
		if (elgg_entity_exists($guid) && elgg_instanceof($topic = get_entity($guid), 'object', 'forum_topic')) {
			$title .= ": \"$topic->title\"";
			$body_vars = forums_prepare_topic_form_vars($topic);
			$content = elgg_view_form('forums/forum_topic/save', $vars, $body_vars);
			$forum = $topic->getContainerEntity();
			elgg_push_breadcrumb($forum->title, $forum->getURL());
			elgg_push_breadcrumb($topic->title, $topic->getURL());
			elgg_push_breadcrumb(elgg_echo('edit'));
		} else {
			$content = elgg_echo('forums:error:forum_topic:edit');
		}
	} else {
		// Adding
		$title = elgg_echo('forums:label:newtopic');
		
		// Check for valid forum container
		if (elgg_entity_exists($guid) && elgg_instanceof($forum = get_entity($guid), 'object', 'forum')) {
			elgg_push_breadcrumb($forum->title, $forum->getURL());
			elgg_push_breadcrumb($title);
			$title = $forum->title . ": " . $title;
			$body_vars = forums_prepare_topic_form_vars(NULL, $forum->guid);
			$content = elgg_view_form('forums/forum_topic/save', $vars, $body_vars);
		} else {
			$content = elgg_echo('forums:error:forum:invalid');
		}		
	}
	
	$params['content'] = $content;
	$params['title'] = $title;
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

/** Prepare topic form vars */
function forums_prepare_topic_form_vars($topic, $container_guid = '') {
	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'container_guid' => $container_guid,
		'guid' => '',
	);

	if ($topic) {
		foreach (array_keys($values) as $field) {
			$values[$field] = $topic->$field;
		}
	}

	if (elgg_is_sticky_form('forum-topic-edit-form')) {
		foreach (array_keys($values) as $field) {
			$values[$field] = elgg_get_sticky_value('forum-topic-edit-form', $field);
		}
	}

	elgg_clear_sticky_form('forum-topic-edit-form');

	return $values;
}

/** Prepare reply form vars */
function forums_prepare_reply_form_vars($reply, $topic_guid, $reply_guid = NULL) {
	// input names => defaults
	$values = array(
		'description' => '',
		'topic_guid' => $topic_guid,
		'reply_guid' => $reply_guid ? $reply_guid : $topic_guid,
		'guid' => '',
	);

	if ($reply) {
		foreach (array_keys($values) as $field) {
			$values[$field] = $reply->$field;
		}
	}

	if (elgg_is_sticky_form('forum-topic-edit-form')) {
		foreach (array_keys($values) as $field) {
			$values[$field] = elgg_get_sticky_value('forum-topic-edit-form', $field);
		}
	}

	elgg_clear_sticky_form('forum-topic-edit-form');

	return $values;
}

/**
 * Get the parent reply that given reply is in response to
 * 
 * @param ElggEntity $reply the reply
 * @return ElggEntity the reply/topic
 */
function forums_get_reply_parent($reply) {
	$options = array(
		'type' => 'object',
		'limit' => 1,
		'container_guid' => $reply->container_guid,
		'relationship' => FORUM_REPLY_RELATIONSHIP, 
		'relationship_guid' => $reply->guid, 
		'inverse_relationship' => FALSE,
	);

	$entities = elgg_get_entities_from_relationship($options);
	return $entities[0];
}
