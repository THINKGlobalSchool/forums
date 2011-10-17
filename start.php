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
 * @TODO
 * 	- permissions
 *  - How to handle deleting a 'reply'
 *  - anonymous support
 *  - clean up listings?
 *  - Group support
 *  - Clean up display of threads
 */

elgg_register_event_handler('init', 'system', 'forums_init');

function forums_init() {
	// Relationship definitions
	define('FORUM_REPLY_RELATIONSHIP', 'forum_reply_to');

	// Register and load library
	elgg_register_library('elgg:forums', elgg_get_plugins_path() . 'forums/lib/forums.php');
	elgg_load_library('elgg:forums');

	// Register CSS
	$f_css = elgg_get_simplecache_url('css', 'forums/css');
	elgg_register_css('elgg.forums', $f_css);

	// Register JS libraries
	$f_js = elgg_get_simplecache_url('js', 'forums/forums');
	elgg_register_js('elgg.forums', $f_js);

	// Add main menu item
	$item = new ElggMenuItem('forums', elgg_echo('forums'), 'forums/all');
	elgg_register_menu_item('site', $item);

	// Register page handler
	elgg_register_page_handler('forums','forums_page_handler');

	// Add submenus
	elgg_register_event_handler('pagesetup', 'system', 'forums_submenus');
	
	// Register a handler for deleting topics
	elgg_register_event_handler('delete', 'object', 'forums_topic_delete_event_listener');

	// Item entity menu hook
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'forums_setup_entity_menu', 999);

	// Moderator permissions check
	elgg_register_plugin_hook_handler('permissions_check', 'object', 'forums_write_permission_check');

	// Forum permissions handler
	elgg_register_plugin_hook_handler('container_permissions_check', 'object', 'forums_container_write_permission_check');

	// Register URL handler
	elgg_register_entity_url_handler('object', 'forum', 'forum_url');
	elgg_register_entity_url_handler('object', 'forum_topic', 'forum_topic_url');
	elgg_register_entity_url_handler('object', 'forum_reply', 'forum_reply_url');

	// Register actions
	$action_base = elgg_get_plugins_path() . 'forums/actions/forums';
	elgg_register_action('forums/forum/save', "$action_base/forum/save.php", 'admin');
	elgg_register_action('forums/forum_topic/save', "$action_base/forum_topic/save.php");
	elgg_register_action('forums/forum_reply/save', "$action_base/forum_reply/save.php");
	elgg_register_action('forums/forum/delete', "$action_base/forum/delete.php", 'admin');
	elgg_register_action('forums/forum_topic/delete', "$action_base/forum_topic/delete.php");
	elgg_register_action('forums/forum_reply/delete', "$action_base/forum_reply/delete.php");

	return TRUE;
}

/**
 * Forum page handler
 */
function forums_page_handler($page) {
	gatekeeper(); // Logged in only
	elgg_load_css('elgg.forums');
	elgg_load_js('elgg.forums');
	
	elgg_push_breadcrumb(elgg_echo('forums'), elgg_get_site_url() . "forums/all");	
	
	switch($page[0]) {
		case 'view':
			$params = forums_get_page_content_view($page[1]);
			break;
		case 'all':
		default: 
			$params = forums_get_page_content_list();
			break;
		case 'forum':
			switch ($page[1]) {
				case 'edit':
					// @todo group stuff
					forward('admin/forums/edit?guid=' . $page[2]);
					break;
				default:
					forward('forums/all');
					break;
			}
			break;
		case 'forum_topic':
			// Handle topics
			switch ($page[1]) {
				case 'add':
					$params = forums_get_page_content_topic_edit($page[1], $page[2]);
					break;
				case 'edit':
					$params = forums_get_page_content_topic_edit($page[1], $page[2]);
					break;
				case 'view':
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
	
	// Custom sidebar (none at the moment)
	$params['sidebar'] .= elgg_view('todo/sidebar');

	$body = elgg_view_layout($params['layout'] ? $params['layout'] : 'content', $params);

	echo elgg_view_page($params['title'], $body);
	
	return TRUE;
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

	if ($entity->getSubtype() == 'forum'
		|| $entity->getSubtype() == 'forum_topic'
		|| $entity->getSubtype() == 'forum_reply')
	{
		foreach($return as $idx => $item) {
			if ($item->getName() == 'likes' || $item->getName() == 'access') {
				unset($return[$idx]);
			}
		}
	}

	// Add anonymous label
	if ($entity->getSubtype() == 'forum' && $entity->anonymous) {
		$options = array(
			'name' => "anonymous_forum",
			'text' =>  elgg_echo('forums:label:anonymous'),
			'href' => FALSE,
			'priority' => 1,
		);
		$return[] = ElggMenuItem::factory($options);
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
 * Extend permissions checking to extend can-edit for write users.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function forums_container_write_permission_check($hook, $entity_type, $returnvalue, $params) {
	if ($params['container']->getSubtype() == 'forum') {
		return TRUE; // Anyone can write to forum containers
	}
}

/**
 * Extend permissions checking to extend can-edit for write users.
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 */
function forums_write_permission_check($hook, $entity_type, $returnvalue, $params) {
	if ($params['entity']->getSubtype() == 'forum_topic'
		|| $params['entity']->getSubtype() == 'forum_reply') {

		$forum = get_entity($params['entity']->container_guid);

		// Check if member is part of the forum's moderator role
		if (roles_is_member($forum->moderator_role, $params['user']->guid)) {
			return TRUE;
		}
	}
}

