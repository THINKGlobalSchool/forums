<?php
/**
 * Forums Helper Library
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
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
	);

	$entity = get_entity($guid);
	if (elgg_instanceof($entity, 'object', 'forum')
		|| elgg_instanceof($entity, 'object', 'forum_topic')
		|| elgg_instanceof($entity, 'object', 'forum_reply')) 
	{
		$params['title'] = $entity->title;
		$params['content'] = elgg_view_entity($entity, array('full_view' => TRUE));
		
		if ($entity->getSubtype() == 'forum') {
			$container = $entity->getContainerEntity();

			// Set layout to one_column if forum is anonymous
			$params['layout'] = $entity->site_forum ? 'one_column' : 'one_sidebar';
		} else if ($entity->getSubtype() == 'forum_topic') {
			$forum = $entity->getContainerEntity();
			$container = $forum->getContainerEntity();
			$params['layout'] = $forum->site_forum ? 'one_column' : 'one_sidebar';
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
		register_error(elgg_echo('noaccess'));
		$_SESSION['last_forward_from'] = current_page_url();
		forward('');
	}
}

/**
 * Get page components to list site forums
 *
 * @return array
 */
function forums_get_page_content_list($container_guid = NULL) {
	$params = array(
		'filter' => '',
	);

	// If we have a container_guid check for group
	if (elgg_instanceof($group = get_entity($container_guid), 'group')) {
		elgg_push_breadcrumb($group->name);

		$options = array(
			'type' => 'object',
			'subtype' => 'forum',
			'full_view' => FALSE,
			'container_guid' => $container_guid,
			'list_class' => 'forum-list'
		);

		// Only show add forum button for the group owner or admins
		if (elgg_is_admin_logged_in() || $group->canWriteToContainer()) {
			elgg_register_title_button();
		}
		$params['title'] = elgg_echo('forums:title:ownerforums', array($group->name));

		$list = elgg_list_entities_from_metadata($options);
		if (!$list) {
			$params['content'] = elgg_echo('forums:label:none');
		} else {
			$params['content'] = $list;
		}

	} else {
		$params['title'] = elgg_echo('forums:title:allforums');

		// Create a module for global forums
		$global_module = elgg_view('modules/genericmodule', array(
			'view' => 'forums/modules/global_forums',
			'module_id' => 'forums-global-module'
		));

		$params['content'] .= elgg_view_module('featured', elgg_echo('forums:title:globalforums'), $global_module);

		$group_module = elgg_view('modules/genericmodule', array(
			'view' => 'forums/modules/group_forums',
			'module_id' => 'forums-global-module'
		));

		$params['content'] .= elgg_view_module('featured', elgg_echo('forums:title:groupforums'), $group_module);
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
		'tags' => '',
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
function forums_get_reply_parent($reply) {;
	$options = array(
		'type' => 'object',
		'subtypes' => ELGG_ENTITIES_ANY_VALUE,
		'limit' => 1,
		'relationship' => FORUM_REPLY_RELATIONSHIP, 
		'relationship_guid' => $reply->guid, 
		'inverse_relationship' => FALSE,
	);

	$entities = elgg_get_entities_from_relationship($options);
	return $entities[0];
}

/**
 * Notify necessary users that a new topic has been posted to a forum
 *
 * Group Forums:
 * - Notifies forum owner
 * - Notifies designated moderators
 * - Group owner
 * 
 * Site Forums:
 * - Notifies users who are members of the moderator role
 * 
 * @param ElggEntity $topic The forum topic
 * @return bool
 */
function forums_notify_new_topic($topic) {
	// Sanity check
	if (!elgg_instanceof($topic, 'object', 'forum_topic')) {
		return FALSE;
	}

	// Grab the forum
	$forum = $topic->getContainerEntity();
	
	// User who made the post
	$poster = elgg_get_logged_in_user_entity();

	// Get forum owners/moderators
	$notify_users = forums_get_owner_moderator_notify_list($forum);

	// Notify Users
	foreach ($notify_users as $n) {
		// Don't send a notification to the poster regardless of moderator/owner
		if ($n != $poster->guid) {
			
			// Determine sender from info
			$sender_info = forums_get_notification_sender_info($forum, $poster);

			$sender_guid = $sender_info['guid'];
			$sender_name = $sender_info['name'];
			
			notify_user(
				$n,
				$sender_guid,
				elgg_echo('forums:new_topic:subject', array(
					$forum->title,
				)),
				elgg_echo('forums:new_topic:body', array(
					$sender_name,
					$forum->title,
					$topic->title,
					$topic->getURL()
				))
			);
		}
	}
	return TRUE;
}

/**
 * Notify necessary users that there is a new forum reply
 *
 * Notifies: 
 * - Topic owner (when replying directly to a topic, or a reply) 
 * - Reply owner 
 * - Forum moderators (designated by group, or moderator role)
 * - Forum owner
 * - Group owner
 * - Users who are participating in the topic
 * 
 * @param ElggEntity $reply The forum reply
 * @return bool
 */
function forums_notify_new_reply($reply) {
	// Sanity check
	if (!elgg_instanceof($reply, 'object', 'forum_reply')) {
		return FALSE;
	}
	
	// Get the reply or topic this was in response to
	$parent = forums_get_reply_parent($reply);
	
	// Get topic this was posted in
	$topic = get_entity($reply->topic_guid);

	// Grab the forum
	$forum = $reply->getContainerEntity();
	
	// User who made the post
	$poster = elgg_get_logged_in_user_entity();

	// Get forum owners/moderators
	$notify_users = forums_get_owner_moderator_notify_list($forum);

	// Notify the topic ownner
	$notify_users[] = $topic->owner_guid;

	// If the parent object isn't the topic, it's a reply so notify both
	if ($parent->owner_guid != $topic->owner_guid) {
		$notify_users[] = $parent->owner_guid;
	}

	// Get participating users
	$participating_users = elgg_get_entities_from_relationship(array(
		'type' => 'user',
		'relationship' => FORUM_TOPIC_PARTICIPANT_RELATIONSHIP, 
		'relationship_guid' => $topic->guid, 
		'inverse_relationship' => TRUE,
	));

	foreach ($participating_users as $user) {
		$notify_users[] = $user->guid;
	}

	// Flush out dupes
	$notify_users = array_unique($notify_users);

	// Notify Users
	foreach ($notify_users as $n) {
		// Check if user has opted out of notifications for this topic
		if (check_entity_relationship($n, FORUM_TOPIC_NO_NOTIFY_RELATIONSHIP, $topic->guid)) {
			continue;
		}

		// Don't send a notification to the poster
		if ($n != $poster->guid) {
			// Determine sender from info
			$sender_info = forums_get_notification_sender_info($forum, $poster);

			$sender_guid = $sender_info['guid'];
			$sender_name = $sender_info['name'];

			// If replying to a topic, or sending notification to an owner/moderator
			if (elgg_instanceof($parent, 'object', 'forum_topic') || $n != $parent->owner_guid) {
			
				notify_user(
					$n,
					$sender_guid,
					elgg_echo('forums:new_reply_topic:subject', array(
						$topic->title,
						$forum->title,
					)),
					elgg_echo('forums:new_reply_topic:body', array(
						$sender_name,
						$topic->title,
						$forum->title,
						$reply->description,
						$topic->getURL() . "#forum-reply-{$reply->guid}",
					))
				);
			} else {
				// Send a more personal message to the owner of the reply
				notify_user(
					$n,
					$sender_guid,
					elgg_echo('forums:new_reply_user:subject', array(
						$forum->title,
					)),
					elgg_echo('forums:new_reply_user:body', array(
						$sender_name,
						$topic->title,
						$forum->title,
						$reply->description,
						$topic->getURL() . "#forum-reply-{$reply->guid}",
					))
				);
			}
		}
	}
	return TRUE;
}

/**
 * Helper function to generate a list of users to provide 
 * notifications to when posting content to a forum
 * 
 * @param ElggEntity $forum
 * @return mixed
 */
function forums_get_owner_moderator_notify_list($forum) {
	// Make sure we have a valid forum
	if (!elgg_instanceof($forum, 'object', 'forum')) {
		return FALSE;
	}

	// Notification users depends on wether or not this is a site forum
	if ($forum->site_forum) {	/** This is a 'site' forum (Created by an admin)**/
		// Get moderator role
		$moderator_role = get_entity($forum->moderator_role);
		
		// Get moderator role members
		$members = $moderator_role->getMembers(0);

		// Notify list
		$notify_users = array();

		// Add each member to the notify list
		foreach ($members as $m) {
			$notify_users[] = $m->guid;
		}
	} else {	/** This is a group forum, created by a member/owner of a group **/
		// Grab the group
		$group = $forum->getContainerEntity();
		
		// Set up initial notify list
		$notify_users = array(
			$forum->owner_guid,	// Forum owner
			$group->owner_guid,	// Group owner
		);

		// Get moderators if any
		if ($moderators = $forum->moderators) {
			// Make sure we have an array
			if (!is_array($moderators)) {
				$moderators = array($moderators);
			} 

			foreach ($moderators as $mod) {
				if (get_user($mod)) {
					// Add user to notify list
					$notify_users[] = $mod;
				}
			}
		}
	}
	// Make sure we don't have dupes (forum owner could also be a moderator, or group owner)
	$notify_users = array_unique($notify_users);
	return $notify_users;
}

/**
 * Get sender info for forum notifications. 
 * 
 * - If forum is anonymous, this will determine if the user is a moderator
 * and return either anonymous or the forum's moderator mask
 *
 * @param  ElggEntity $forum  The forum we're sending a notification for
 * @param  ElggUser   $poster The user we're sending a notification from
 * @return array
 */
function forums_get_notification_sender_info($forum, $poster) {
	// If the forum is anonymous
	if ($forum->anonymous) {
		// We need to send from the site to hide the owner
		$sender_guid = elgg_get_site_entity()->guid;
		
		// Check if the sender is a moderator for this forum
		if (forums_is_moderator($poster, $forum)) {
			// Sender name is the defined moderator mask
			$sender_name = $forum->moderator_mask;
		} else {
			// Sender name is plain old anonymous
			$sender_name = elgg_echo('forums:label:anonymous');
		}
	} else {
		//  Not anonymous, use real info
		$sender_guid = $poster->guid;
		$sender_name = $poster->name;
	}
	
	return array(
		'guid' => $sender_guid,
		'name' => $sender_name,
	);
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
	if (!elgg_is_logged_in()) {
		return FALSE;
	}
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

/**
 * Merge tags from forum into topic
 * 
 * @param ElggEntity $forum
 * @param ElggEntity $topic
 * @return bool
 */
function merge_forum_and_topic_tags($forum, $topic) {
	// Check for anonymous forum
	if ($forum->anonymous) {
		// Nuke topic tags, that's it.
		$topic->tags = NULL;
		return TRUE;
	}


	// Make sure the forum entity has tags
	if ($forum_tags = $forum->tags) {

		// Make sure forum tags is an array
		if (!is_array($forum->tags)) {
			$forum_tags = array($forum_tags);
		}

		$topic_tags = $topic->tags;

		// Make sure topic tags is an array
		if (!is_array($topic->tags)) {
			if (!$topic_tags) {
				$topic_tags = array();
			} else {
				$topic_tags = array($topic_tags);
			}
		}

		// Merge forum and topic tags
		$new_tags = array_merge($forum_tags, $topic_tags);

		// Remove dupes
		$new_tags = array_unique($new_tags);

		// Update topic tags
		$topic->tags = $new_tags;

		return TRUE;
	}

	return FALSE;
}

/**
 * Get forum members status
 * 
 * @param  ElggObject $forum_entity Forum/Topic entity
 * @param  ElggGroup  $group        Group to glean this information from         
 * 
 * @return array() member stats info, as below:
 *
 * array(
 *     'total_group_members'         => (int) Total Group Members
 *     'total_participating_members' => (int) Total Members participating in this forum/topic
 *     'participating_members_stats' => array(
 *         'member_guid' => array(
 *             'reply_count'          => (int) # of replies in this forum/topic
 *             'reply_to_reply_count' => (int) # of replies to other user's posts
 *             'word_count'           => (int) Word count of combined replies in forum/topic
 *         )
 *     )
 *     'not_participating_members'    => array(member_guid, ...) Members not participating
 * )
 */
function forums_get_group_members_stats($forum_entity, $group) {
	$dbprefix = elgg_get_config('dbprefix');

	// Get group members info
	$group_members = $group->getMembers(0);
	$group_member_guids = array();

	foreach ($group_members as $member) {
		$group_member_guids[] = $member->guid;
	}

	// Common participation options
	$pm_options = array(
		'type' => 'object',
		'subtype' => 'forum_reply',
		'owner_guids' => $group_member_guids, // Limit to group member guids
		'limit' => 0,
		'joins' => "JOIN {$dbprefix}users_entity ue on ue.guid = e.owner_guid",
		'group_by' => 'ue.guid'
	);

	// Handle different subtypes
	if (elgg_instanceof($forum_entity, 'object', 'forum')) {
		// Forum entity options
		$pm_options['container_guid'] = $forum_entity->guid;
	} else if (elgg_instanceof($forum_entity, 'object', 'forum_topic')) {
		// Forum topic entity options
		$pm_options['container_guid'] = $forum_entity->container_guid;
		$pm_options['metadata_name'] = 'topic_guid';
		$pm_options['metadata_value'] = $forum_entity->guid;
	} else {
		// Not a forum/topic.. bounce
		return FALSE;
	}
	// Going to ignore access here to save on expensive queries
	$ia = elgg_get_ignore_access();
	elgg_set_ignore_access(TRUE);

	// Get replies for this entity, grouped by user to sort out participation
	$pm = elgg_get_entities_from_metadata($pm_options);

	// Build an array of participating members
	$p_member_guids = array();

	// Copy group members to non-participating array, we'll remove below
	$np_member_guids = $group_member_guids;

	// Add participating members and remove from non-participating
	foreach ($pm as $e) {
		// Add to participating member guids
		$p_member_guids[] = $e->owner_guid;

		// Remove user from non-participating array
		if(($k = array_search($e->owner_guid, $np_member_guids)) !== FALSE) {
			unset($np_member_guids[$k]);
		}
	}

	// Start building participating member stats
	$pm_stats = array();

	// Fix pm options and re-use conditional subtype options
	unset($pm_options['joins']);
	unset($pm_options['owner_guids']);
	unset($pm_options['group_by']);

	// Loop over participating members to build individual stats
	foreach ($p_member_guids as $m) {
		$pm_options['owner_guid'] = $m;

		// Get members's replies
		$mr = elgg_get_entities_from_metadata($pm_options);

		// Count 'em
		$reply_count = count($mr);

		// Grab reply guids to start building 'reply to reply' count
		$reply_guids = array();
		foreach ($mr as $r) {
			$reply_guids[] = $r->guid;
		}

		// Reply to reply options
		$dbprefix = elgg_get_config('dbprefix');
		$rtr_options = array(
			'guids' => $reply_guids,
			'limit' => 0,
			'joins' => array(
				"JOIN {$dbprefix}entity_relationships r on r.guid_one = e.guid",
				"JOIN {$dbprefix}entities er on er.guid = r.guid_two"
			),
			'wheres' => array(
				"e.owner_guid != er.owner_guid"
			)
		);

		$rtr = elgg_get_entities($rtr_options);

		$reply_to_reply_count = count($rtr);

		// Build word count
		$words = '';
		foreach ($mr as $r) {
			// Use the longtext view to grab description content, then strip tags
			$words .= ' ' . strip_tags(elgg_view('output/longtext', array('value' => $r->description)));
		}

		// Count words
		$word_count = str_word_count($words);

		$pm_stats[$m] = array(
			'reply_count' => $reply_count,
			'replies_to_replies' => $reply_to_reply_count,
			'word_count' => $word_count
		);
	}

	// Count participating members
	$tpm = count($pm);

	// Count group members
	$tm = count($group_members);

	// Reset access
	elgg_set_ignore_access($ia);

	// Return
	return array(
		'total_group_members' => $tm,
		'total_participating_members' => $tpm,
		'participating_members_stats' => $pm_stats,
		'not_participating_members' => $np_member_guids
	);
}