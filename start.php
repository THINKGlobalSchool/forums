<?php
/**
 * Forums Start.php
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * THIS PLUGIN NEEDS TO BE FIRST IN THE LIST
 */

elgg_register_event_handler('init', 'system', 'forums_init');

function forums_init() {
	// Relationship definitions
	define('FORUM_REPLY_RELATIONSHIP', 'forum_reply_to');

	// Anonymous access definition
	define('ACCESS_ANONYMOUS', -9887);

	// Register and load library
	elgg_register_library('elgg:forums', elgg_get_plugins_path() . 'forums/lib/forums.php');
	elgg_load_library('elgg:forums');

	// Register CSS
	$f_css = elgg_get_simplecache_url('css', 'forums/css');
	elgg_register_simplecache_view('css/forums/css');
	elgg_register_css('elgg.forums', $f_css);

	// Register JS libraries
	$f_js = elgg_get_simplecache_url('js', 'forums/forums');
	elgg_register_simplecache_view('js/forums/forums');
	elgg_register_js('elgg.forums', $f_js);

	// Add main menu item
	$item = new ElggMenuItem('forums', elgg_echo('forums'), 'forums/all');
	elgg_register_menu_item('site', $item);

	// Register page handler
	elgg_register_page_handler('forums','forums_page_handler');
	
	// Unregister discussion page handler
	elgg_unregister_page_handler('discussion');
	
	// Register new discussion handler
	elgg_register_page_handler('discussion','discussion_redirect_page_handler');

	// Add submenus
	elgg_register_event_handler('pagesetup', 'system', 'forums_submenus');

	// add the group foruns tool option
	add_group_tool_option('forums', elgg_echo('groups:enableforums'), TRUE);

	// Profile block hook
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'forums_owner_block_menu');

	// Register a handler for deleting topics
	elgg_register_event_handler('delete', 'object', 'forums_topic_delete_event_listener');

	// Register a handler for deleting replies
	elgg_register_event_handler('delete', 'object', 'forums_reply_delete_event_listener');

	// Item entity menu hook
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'forums_setup_entity_menu', 999);

	// Moderator permissions check
	elgg_register_plugin_hook_handler('permissions_check', 'object', 'forums_write_permission_check');

	// Forum permissions handler
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'forums_container_write_permission_check');

	// Remove comments from forum related river entries
	elgg_register_plugin_hook_handler('register', 'menu:river', 'forums_river_menu_setup');

	// Remove public from forum access array
	elgg_register_plugin_hook_handler('access:collections:write', 'user', 'forums_access_id_handler');

	elgg_register_plugin_hook_handler('access:collections:read', 'user', 'forums_read_access_handler');

	// Register URL handler
	elgg_register_entity_url_handler('object', 'forum', 'forum_url');
	elgg_register_entity_url_handler('object', 'forum_topic', 'forum_topic_url');
	elgg_register_entity_url_handler('object', 'forum_reply', 'forum_reply_url');

	// Register actions
	$action_base = elgg_get_plugins_path() . 'forums/actions/forums';
	elgg_register_action('forums/forum/save', "$action_base/forum/save.php");
	elgg_register_action('forums/forum_topic/save', "$action_base/forum_topic/save.php");
	elgg_register_action('forums/forum_reply/save', "$action_base/forum_reply/save.php");
	elgg_register_action('forums/forum/delete', "$action_base/forum/delete.php");
	elgg_register_action('forums/forum_topic/delete', "$action_base/forum_topic/delete.php");
	elgg_register_action('forums/forum_reply/delete', "$action_base/forum_reply/delete.php");
	elgg_register_action('forums/forum_topic/status', "$action_base/forum_topic/status.php");

	return TRUE;
}

/**
 * Forum page handler
 */
function forums_page_handler($page) {
	gatekeeper(); // Logged in only
	elgg_load_css('elgg.forums');
	elgg_load_js('elgg.forums');
	
	elgg_push_breadcrumb(elgg_echo('forums:label:siteforums'), elgg_get_site_url() . "forums/all");
	
	switch($page[0]) {
		case 'add':
			group_gatekeeper();
			$guid = elgg_get_page_owner_guid();
			if (elgg_instanceof($group = get_entity($guid), 'group')
			&& (elgg_is_admin_logged_in() || $group->getOwnerEntity() == elgg_get_logged_in_user_entity())) {
				$params = forums_get_page_content_forum_edit($page[0], $guid);
			} else {
				// Not a group, or no permission
				forward('forums/all');
			}
			break;
		case 'view':
			group_gatekeeper();
			$params = forums_get_page_content_view($page[1]);
			break;
		case 'all':
		default: 
			$params = forums_get_page_content_list();
			break;
		case 'group':
			group_gatekeeper();
			$params = forums_get_page_content_list($page[1]);
			break;
		case 'forum':
			switch ($page[1]) {
				case 'edit':
					$forum = get_entity($page[2]);
					if ($forum && elgg_instanceof($forum->getContainerEntity(), 'group')) {
						elgg_set_page_owner_guid($forum->getContainerGUID());
						$params = forums_get_page_content_forum_edit($page[1], $page[2]);
					} else {
						forward('admin/forums/edit?guid=' . $page[2]);
					}
					break;
				default:
					forward('forums/all');
					break;
			}
			break;
		case 'forum_topic':
			// Grab entity
			$entity = get_entity($page[2]);

			if (!elgg_instanceof($entity, 'object', 'forum') && !elgg_instanceof($entity, 'object', 'forum_topic')) {
				forward('forums');
			}

			// Get the container of the forum
			if ($entity->getSubtype() == 'forum_topic') {
				// Forum topic
				$forum = get_entity($entity->container_guid);
				if (!elgg_instanceof($forum, 'object', 'forum')) {
					forward('forums');
				}
				$container = get_entity($forum->container_guid);
			} else {
				// Forum
				$container = get_entity($entity->container_guid);
			}

			// Set page owner to group if the forum's container is one
			if (elgg_instanceof($container, 'group')) {
				elgg_set_page_owner_guid($container->guid);
			}

			// Handle topics
			switch ($page[1]) {
				case 'add':
					group_gatekeeper();
					$params = forums_get_page_content_topic_edit($page[1], $page[2]);
					break;
				case 'edit':
					group_gatekeeper();
					$params = forums_get_page_content_topic_edit($page[1], $page[2]);
					break;
				case 'view':
					group_gatekeeper();
					$params = forums_get_page_content_view($page[2]);
					break;
				default:
					forward('forums/all');
					break;
			}
			break;
		case 'forum_reply':
			// Handle topics
			switch ($page[1]) {
				case 'edit':
					$params = forums_get_page_content_reply_edit($page[2]);
					break;
				default:
					forward('forums/all');
					break;
			}
			break;
	}

	$body = elgg_view_layout($params['layout'] ? $params['layout'] : 'content', $params);

	echo elgg_view_page($params['title'], $body);
	
	return TRUE;
}

/**
 * Redirect Discussion page handler
 */
function discussion_redirect_page_handler($page) {
	$forum_link = elgg_view('output/url', array(
		'value' => elgg_get_site_url() . "forums",
		'text' => elgg_echo('forums'),
	));

	if (elgg_instanceof(get_entity($page[1]), 'group')) {
		elgg_set_page_owner_guid($page[1]);
		$forum_link = elgg_view('output/url', array(
			'value' => elgg_get_site_url() . "forums/group/{$page[1]}/all",
			'text' => elgg_echo('forums'),
		));
	}

	$content = elgg_echo('forums:label:redirectforums', array($forum_link));
	
	$params = array(
		'content' => $content,
		'title' => elgg_echo('forums:label:discussiondisabled'),
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);
	
	echo elgg_view_page($title, $body);
}


/**
 * Populates the ->getUrl() method for forum entities
 *
 * @param ElggObject entity
 * @return string request url
 */
function forum_url($entity) {
	return elgg_get_site_url() . 'forums/view/' . $entity->guid;
}

/**
 * Populates the ->getUrl() method for forum topic entities
 *
 * @param ElggObject entity
 * @return string request url
 */
function forum_topic_url($entity) {
	return elgg_get_site_url() . 'forums/forum_topic/view/' . $entity->guid;
}

/**
 * Populates the ->getUrl() method for forum reply entities
 *
 * @param ElggObject entity
 * @return string request url
 */
function forum_reply_url($entity) {
	return elgg_get_site_url() . 'forums/forum_reply/view/' . $entity->guid;
}

/**
 * Setup Forums Submenus
 */
function forums_submenus() {
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('administer', 'manage', 'forums');
	}
}

/**
 * Item entity plugin hook
 */
function forums_setup_entity_menu($hook, $type, $return, $params) {
	$entity = $params['entity'];
	$subtype = $entity->getSubtype();

	if ($subtype == 'forum' || $subtype == 'forum_topic' || $subtype == 'forum_reply') {
		foreach($return as $idx => $item) {
			// Remove access for forum topics and forum replies
			if (($subtype == 'forum_topic' || $subtype =='forum_reply') && $item->getName() == 'access') {
				unset($return[$idx]);
			}

			// Remove access for anonymous groups
			if ($subtype == 'forum' && $item->getName() == 'access' && $entity->anonymous) {
				unset($return[$idx]);
			}

			// Remove delete button unless we're a group owner, moderator or admin
			if ($subtype != 'forum' && $item->getName() == 'delete') {
				$forum = $entity->getContainerEntity();
				$container = $forum->getContainerEntity();

				if (!forums_is_moderator(elgg_get_logged_in_user_entity(), $forum)) {
					// Remove delete
					unset($return[$idx]);
				}
			}
		}
	}

	// Add anonymous label
	if ($subtype == 'forum' && $entity->anonymous) {
		$options = array(
			'name' => "anonymous_forum",
			'text' =>  elgg_echo('forums:label:anonymous'),
			'href' => FALSE,
			'priority' => 100,
			'section' => 'info',
		);
		$return[] = ElggMenuItem::factory($options);
	}

	// Count topics/replies for forum listing
	if ($subtype == 'forum') {
		foreach($return as $idx => $item) {
			// Remove likes
			if ($item->getName() == 'likes') {
				unset($return[$idx]);
			}
		}
		
		$topic_count = elgg_get_entities(array(
			'type' => 'object',
			'subtype' => 'forum_topic',
			'container_guid' => $entity->guid,
			'limit' => 0,
			'count' => TRUE,
		));
		
		$topic_count = $topic_count ? $topic_count : 0;

		$reply_count = elgg_get_entities(array(
			'type' => 'object',
			'subtype' => 'forum_reply',
			'container_guid' => $entity->guid,
			'limit' => 0,
			'count' => TRUE,
		));
		
		$reply_count = $reply_count ? $reply_count : 0;

		$options = array(
			'name' => 'topic_count',
			'text' => elgg_echo('forums:label:topiccount', array($topic_count)),
			'priority' => 2,
			'href' => FALSE,
			'section' => 'info',
		);
		$return[] = ElggMenuItem::factory($options);

		$options = array(
			'name' => 'reply_count',
			'text' => elgg_echo('forums:label:replycount', array($reply_count)),
			'priority' => 3,
			'href' => FALSE,
			'section' => 'info',
		);
		$return[] = ElggMenuItem::factory($options);
	}

	// Count replies, close/open thread command
	if ($subtype == 'forum_topic') {
		// Count replies
		$count = elgg_get_entities_from_metadata(array(
			'type' => 'object',
			'subtype' => 'forum_reply',
			'metadata_name' => 'topic_guid',
			'metadata_value' => $entity->guid,
			'limit' => 0,
			'count' => TRUE,
		));
		
		$count = $count ? $count : 0;

		$options = array(
			'name' => 'reply_count',
			'text' => elgg_echo('forums:label:replycount', array($count)),
			'priority' => 3,
			'href' => FALSE,
			'section' => 'info',
		);
		$return[] = ElggMenuItem::factory($options);

		if ($entity->canEdit()) {

			if ($entity->topic_status == 'closed') {
				$options = array(
					'name' => 'open_topic',
					'text' => elgg_echo('forums:label:openthread', array($count)),
					'priority' => 1,
					'href' => "action/forums/forum_topic/status?guid={$entity->getGUID()}&status=open",
					'confirm' => elgg_echo('forums:label:openconfirm'),
					'section' => 'actions',
				);
			} else {
				$options = array(
					'name' => 'close_topic',
					'text' => elgg_echo('forums:label:closethread', array($count)),
					'priority' => 1,
					'href' => "action/forums/forum_topic/status?guid={$entity->getGUID()}&status=closed",
					'confirm' => elgg_echo('forums:label:closeconfirm'),
					'section' => 'actions',
				);
			}
			$return[] = ElggMenuItem::factory($options);
		}

		// Display topic closed for all users
		if ($entity->topic_status == 'closed') {
			$options = array(
				'name' => 'topic_closed',
				'text' => elgg_echo('forums:label:closed'),
				'priority' => 2,
				'href' => FALSE,
				'section' => 'info',
			);

			$return[] = ElggMenuItem::factory($options);
		}
	}
	return $return;
}

/**
 * Topic deleted, so remove all replies
 */
function forums_topic_delete_event_listener($event, $object_type, $object) {
	if (elgg_instanceof($object, 'object', 'forum_topic')) {
		// Grab topic replies
		$replies = forums_get_topic_replies($object, array(
			'limit' => 0,
		));

		// Delete replies
		foreach($replies as $reply) {
			$reply->delete();
		}
	}
	return TRUE;
}

/**
 * Reply deleted, so remove all replies
 */
function forums_reply_delete_event_listener($event, $object_type, $object) {
	if (elgg_instanceof($object, 'object', 'forum_reply')) {
		// Grab all replies to this reply, and delete
		$options = array(
			'type' => 'object',
			'subtype' => 'forum_reply',
			'limit' => 0,
			'container_guid' => $object->container_guid,
			'relationship' => FORUM_REPLY_RELATIONSHIP,
			'relationship_guid' => $object->guid,
			'inverse_relationship' => TRUE,
		);

		$replies = elgg_get_entities_from_relationship($options);

		// Delete replies
		foreach($replies as $reply) {
			$reply->delete();
		}
	}
	return TRUE;
}

/**
 * Extend permissions checking to extend can-edit for write users.
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 */
function forums_container_write_permission_check($hook, $type, $value, $params) {
	if ($params['container']->getSubtype() == 'forum') {
		return TRUE; // Anyone can write to forum containers
	}
}

/**
 * Extend permissions checking to extend can-edit for write users.
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 */
function forums_write_permission_check($hook, $type, $value, $params) {
	if ($params['entity']->getSubtype() == 'forum_topic'
		|| $params['entity']->getSubtype() == 'forum_reply') {

		$forum = get_entity($params['entity']->container_guid);

		// Check if member is part of the forum's moderator role
		if (forums_is_moderator($params['user'], $forum)) {
			return TRUE;
		}
	}
}

/**
 * Remove comment button from forum objects
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function forums_river_menu_setup($hook, $type, $value, $params) {
	if (elgg_is_logged_in()) {
		$item = $params['item'];
		$object = $item->getObjectEntity();
		if (elgg_instanceof($object, 'object', 'forum_reply') || elgg_instanceof($object, 'object', 'forum_topic')) {
			return array();
		}
	}

	return $value;
}

/**
 * Plugin hook to add forums to the group profile block
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function forums_owner_block_menu($hook, $type, $value, $params) {
	if (elgg_instanceof($params['entity'], 'group')) {
		if ($params['entity']->forums_enable == 'yes') {
			$url = "forums/group/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('forums', elgg_echo('forums:label:groupforums'), $url);
			$value[] = $item;
		}
	}
	return $value;
}

/**
 * Plugin hook to remove unwanted access levels from forums
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function forums_access_id_handler($hook, $type, $value, $params) {
	if (elgg_in_context('group_forum_access')) {
		unset($value[ACCESS_PUBLIC]); // Remove public
		unset($value[ACCESS_PRIVATE]); // Remove private
	}
	return $value;
}

/**
 * Insert the anonymous access level for qualified users
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function forums_read_access_handler($hook, $type, $value, $params) {
	$user = elgg_get_logged_in_user_entity();

	if (!$user->is_parent) {
		$value[] = ACCESS_ANONYMOUS;
	}
	return $value;
}