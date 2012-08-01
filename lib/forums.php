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
		//'layout' => 'one_column',
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
				$container = $entity->getContainerEntity();
			} else if ($entity->getSubtype() == 'forum_topic') {
				$forum = $entity->getContainerEntity();
				$container = $forum->getContainerEntity();
			}
			
			// We don't want to see the admin user's owner badge, unless the forum was created by a group
			if (elgg_instanceof($container, 'group')) {
				elgg_set_page_owner_guid($container->guid);
				elgg_push_breadcrumb($container->name, "forums/group/{$container->guid}/all");
			} else {
				elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
			}

			// If we're looking at a topic, show the forum breadcrumb
			if ($forum) {
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
function forums_get_page_content_list($container_guid = NULL) {
	$params = array(
		'filter' => '',
		//'header' => '',
	);

	$options = array(
		'type' => 'object',
		'subtype' => 'forum',
		'full_view' => FALSE,
		'container_guid' => $container_guid,
	);

	// We have a container_guid check for group
	if (elgg_instanceof($group = get_entity($container_guid), 'group')) {
		elgg_push_breadcrumb($group->name);

		// Only show add forum button for the group owner or admins
		if (elgg_is_admin_logged_in() || $group->canWriteToContainer()) {
			elgg_register_title_button();
		}
		$params['title'] = elgg_echo('forums:title:ownerforums', array($group->name));
	} else {
		$params['title'] = elgg_echo('forums:title:allforums');
		$options['metadata_name'] = 'site_forum';
		$options['metadata_value'] = TRUE;
	}

	$list = elgg_list_entities_from_metadata($options);
	if (!$list) {
		$params['content'] = elgg_echo('forums:label:none');
	} else {
		$params['content'] = $list;
	}

	return $params;
}

/**
 * Get page content to edit/create a group forum
 *
 * @param string $page_type Add/Edit
 * @param int    $guid      Forum/Container guid
 */
function forums_get_page_content_forum_edit($page_type, $guid = NULL) {

	$params = array(
		'filter' => '',
	);

	// Form vars
	$vars = array();
	$vars['id'] = 'forum-edit-form';
	$vars['name'] = 'forum-edit-form';

	if ($page_type == 'edit') {
		// Editing
		$title = elgg_echo('forums:title:editforum');
		if (elgg_entity_exists($guid)) {
			$forum = get_entity($guid);
			$group = $forum->getContainerEntity();

			$body_vars = forums_prepare_forum_form_vars($forum);

			$content = elgg_view_form('forums/forum/save', $vars, $body_vars);

			elgg_push_breadcrumb($group->name, "forums/group/{$group->guid}/all");
			elgg_push_breadcrumb($forum->title, $forum->getURL());
			elgg_push_breadcrumb(elgg_echo('edit'));
		} else {
			$content = elgg_echo('forums:error:forum:edit');
		}
	} else {
		// Adding
		$title = elgg_echo('forums:title:addforum');
		$group = get_entity($guid);

		elgg_push_breadcrumb($group->name, "forums/group/{$guid}/all");
		elgg_push_breadcrumb(elgg_echo('new'));

		$body_vars = forums_prepare_forum_form_vars();
		$content = elgg_view_form('forums/forum/save', $vars, $body_vars);
	}

	$params['content'] = $content;
	$params['title'] = $title;
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
			$forum = $topic->getContainerEntity();
			$body_vars = forums_prepare_topic_form_vars($topic, $forum->guid);
			$content = elgg_view_form('forums/forum_topic/save', $vars, $body_vars);
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

/**
 * Get page content to edit forum reply
 *
 * @param string $page_type Add/Edit
 * @param int    $guid      Forum/Topic guid
 */
function forums_get_page_content_reply_edit($guid = NULL) {
	$params = array(
		'filter' => '',
	);

	// Form vars
	$vars = array();
	$vars['id'] = 'forum-reply-edit-form';
	$vars['name'] = 'forum-reply-edit-form';

	$title = elgg_echo('forums:title:replyedit');
	if (elgg_entity_exists($guid) && elgg_instanceof($reply = get_entity($guid), 'object', 'forum_reply')) {
		$topic = get_entity($reply->topic_guid);
		$title .= elgg_echo('forums:label:totopic', array($topic->title));
		$body_vars = forums_prepare_reply_form_vars($reply, $topic->guid);
		$content = elgg_view_form('forums/forum_reply/save', $vars, $body_vars);
		$forum = $topic->getContainerEntity();
		elgg_push_breadcrumb($forum->title, $forum->getURL());
		elgg_push_breadcrumb($topic->title, $topic->getURL());
		elgg_push_breadcrumb(elgg_echo('forums:title:replyedit'));
	} else {
		$content = elgg_echo('forums:error:forum_reply:edit');
	}

	$params['content'] = $content;
	$params['title'] = $title;
	return $params;
}

/** Prepare forum form vars */
function forums_prepare_forum_form_vars($forum = NULL) {
	// Might be creating forums from an admin page, so make sure we set container
	if (!$container_guid = elgg_get_page_owner_guid()) {
		$container_guid = elgg_get_logged_in_user_guid();
	}

	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'anonymous' => '',
		'moderator_role' => '',
		'moderators' => '',
		'moderator_mask' => '',
		'guid' => '',
		'access_id' => ACCESS_LOGGED_IN,
		'container_guid' => $container_guid,
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
function forums_prepare_topic_form_vars($topic = NULL, $container_guid = '') {
	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'container_guid' => $container_guid,
		'guid' => '',
		'tags' => '',
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
function forums_prepare_reply_form_vars($reply = NULL, $topic_guid, $reply_guid = NULL) {
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
 * Get all replies for given topic
 *
 * @param ElggEntity $topic  the topic
 * @param array      $options param array
 *
 * 'limit'   =>  Limit to retrieve
 * 'offset'  =>  Offset to grab
 * 'count'   =>  How many to grab
 *
 * @return array
 */
function forums_get_topic_replies($topic, array $options = array()) {
	if (!elgg_instanceof($topic, 'object', 'forum_topic')) {
		return false;
	}

	$defaults = array(
		'limit' => 10,
		'offset' => 0,
		'count' => FALSE,
	);

	$options = array_merge($defaults, $options);

	// Perm options
	$options['type'] = 'object';
	$options['subtype'] = 'forum_reply';
	$options['container_guid'] = $topic->container_guid;
	$options['metadata_name'] = 'topic_guid';
	$options['metadata_value'] = $topic->guid;

	$replies = elgg_get_entities_from_metadata($options);

	return $replies;
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

/**
 * Convenience function to check if the given user counts as a moderator for
 * a given forum
 *
 * @param  ElggUser   $user
 * @param  ElggEntity $forum
 * @return bool
 */
function forums_is_moderator($user, $forum) {
	// Grab container to check for group
	$container = $forum->getContainerEntity();

	// Grab the optionally set moderators (for groups)
	$group_moderators = $forum->moderators;

	// Make sure we have an array
	if (!is_array($group_moderators)) {
		$group_moderators = array($group_moderators);
	}

	if ($user->isAdmin()
		|| (elgg_instanceof($container, 'group') && ($forum->canEdit($user->guid) ||  in_array($user->guid, $group_moderators)))
		|| roles_is_member($forum->moderator_role, $user->guid))
	{
		return TRUE;
	} else {
		return FALSE;
	}
}