<?php
/**
 * Forums Start.php
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 * THIS PLUGIN NEEDS TO BE FIRST IN THE LIST
 */

elgg_register_event_handler('init', 'system', 'forums_init');
elgg_register_event_handler('ready', 'system', 'forums_ready');

function forums_init() {
	// Relationship definitions
	define('FORUM_REPLY_RELATIONSHIP', 'forum_reply_to');
	define('FORUM_TOPIC_PARTICIPANT_RELATIONSHIP', 'participating_in_topic');
	define('FORUM_TOPIC_NO_NOTIFY_RELATIONSHIP', 'dont_notify_topic');

	// Anonymous access definition
	define('ACCESS_ANONYMOUS', -9887);

	// Register and load library
	elgg_register_library('elgg:forums', elgg_get_plugins_path() . 'forums/lib/forums.php');
	elgg_load_library('elgg:forums');

	// Register CSS
	$f_css = elgg_get_simplecache_url('css', 'forums/css');
	elgg_register_simplecache_view('css/forums/css');
	elgg_register_css('elgg.forums', $f_css);
	elgg_load_css('elgg.forums');

	// Register JS libraries
	$f_js = elgg_get_simplecache_url('js', 'forums/forums');
	elgg_register_simplecache_view('js/forums/forums');
	elgg_register_js('elgg.forums', $f_js);

	// Add main menu item for logged in users
	if (elgg_is_logged_in()) {
		$item = new ElggMenuItem('forums', elgg_echo('forums'), 'forums/all');
		elgg_register_menu_item('site', $item);
	}

	// Register page handler
	elgg_register_page_handler('forums','forums_page_handler');
	
	// Unregister discussion page handler
	elgg_unregister_page_handler('discussion');
	
	// Register new discussion handler
	elgg_register_page_handler('discussion','discussion_redirect_page_handler');

	// Add submenus
	elgg_register_event_handler('pagesetup', 'system', 'forums_submenus');

	// add the group forums tool option
	add_group_tool_option('forums', elgg_echo('groups:enableforums'), TRUE);

	// Profile block hook
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'forums_owner_block_menu', 999);

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

	// Change river subtype options for forums
	elgg_register_plugin_hook_handler('get_options', 'river', 'forums_river_options_setup');

	// Remove public from forum access array
	elgg_register_plugin_hook_handler('access:collections:write', 'user', 'forums_access_id_handler');

	elgg_register_plugin_hook_handler('access:collections:read', 'user', 'forums_read_access_handler');

	// Register forum topics as a type to check for the parent (forum) container's container in modules
	elgg_register_plugin_hook_handler('check_parent_container', 'modules', 'forums_container_check_handler');

	// Include forum topics when grabbing tagdashboard subtypes from metadata
	elgg_register_plugin_hook_handler('tagdashboards:metadata:subtypes', 'container_check', 'forums_tagdashboard_metadata_subtype_handler');

	// notifications
	register_notification_object('object', 'forum', elgg_echo('forums:new_forum:subject'));
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'new_forum_notify_message');

	// Unregister 'discussions'
	unregister_entity_type('object', 'groupforumtopic');

	// Register forums and forum topics
	elgg_register_entity_type('object', 'forum');
	// elgg_register_entity_type('object', 'forum_topic');
	// elgg_register_entity_type('object', 'forum_reply');

	// Unregister discussions
	unregister_entity_type('object', 'groupforumtopic');

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
	elgg_register_action('forums/forum_topic/setnotifications', "$action_base/forum_topic/setnotifications.php");

	// Register ajax views
	elgg_register_ajax_view('forums/modules/global_forums');
	elgg_register_ajax_view('forums/modules/group_forums');

	return TRUE;
}

// Forums ready handler
function forums_ready() {
	remove_group_tool_option('forum');
}

/**
 * Forum page handler
 */
function forums_page_handler($page) {
	elgg_load_js('elgg.forums');
	
	elgg_push_breadcrumb(elgg_echo('forums:label:siteforums'), elgg_get_site_url() . "forums/all");
	
	switch($page[0]) {
		case 'add':
			gatekeeper();
			group_gatekeeper();
			$guid = elgg_get_page_owner_guid();
			if (elgg_instanceof($group = get_entity($guid), 'group')
			&& (elgg_is_admin_logged_in() || $group->canWriteToContainer())) {
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
					gatekeeper();
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
				register_error(elgg_echo('noaccess'));
				$_SESSION['last_forward_from'] = current_page_url();
				forward('');
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
					gatekeeper();
					group_gatekeeper();
					$params = forums_get_page_content_topic_edit($page[1], $page[2]);
					break;
				case 'edit':
					gatekeeper();
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
			// Handle replies
			switch ($page[1]) {
				case 'edit':
					gatekeeper();
					$params = forums_get_page_content_reply_edit($page[2]);
					break;
				case 'view':
					$reply = get_entity($page[2]);
					$topic = get_entity($reply->topic_guid);
					if (elgg_instanceof($reply, 'object', 'forum_reply') && elgg_instanceof($topic, 'object', 'forum_topic')) {
						forward($topic->getURL() . '#forum-reply-' . $reply->guid);
					}
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
			'name' => "access",
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
			'priority' => 1100,
			'item_class' => 'forum-entity-menu-item',
			'href' => FALSE,
			'section' => 'info',
		);
		$return[] = ElggMenuItem::factory($options);

		$options = array(
			'name' => 'reply_count',
			'text' => elgg_echo('forums:label:replycount', array($reply_count)),
			'priority' => 1000,
			'item_class' => 'forum-entity-menu-item',
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
			'priority' => 1000,
			'item_class' => 'forum-entity-menu-item',
			'href' => FALSE,
			'section' => 'info',
		);
		$return[] = ElggMenuItem::factory($options);

		// Opt-in/out of notifications
		if (check_entity_relationship(elgg_get_logged_in_user_guid(), FORUM_TOPIC_PARTICIPANT_RELATIONSHIP, $entity->guid)) {
			$opt_text = elgg_echo('forums:label:optout');
			$opt_action = 0;
		} else {
			$opt_text = elgg_echo('forums:label:optin');
			$opt_action = 1;
		}

		$options = array(
			'name' => 'topic_opt',
			'text' => $opt_text,
			'priority' => 2,
			'href' => elgg_add_action_tokens_to_url("action/forums/forum_topic/setnotifications?opt_action={$opt_action}&topic={$entity->guid}"),
			'section' => 'actions',
			'action' => true
		);

		$return[] = ElggMenuItem::factory($options);

		if ($entity->canEdit()) {

			if ($entity->topic_status == 'closed') {
				$options = array(
					'name' => 'open_topic',
					'text' => elgg_echo('forums:label:openthread'),
					'priority' => 1,
					'href' => "action/forums/forum_topic/status?guid={$entity->getGUID()}&status=open",
					'confirm' => elgg_echo('forums:label:openconfirm'),
					'section' => 'actions',
				);
			} else {
				$options = array(
					'name' => 'close_topic',
					'text' => elgg_echo('forums:label:closethread'),
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
	if ($params['container'] && $params['container']->getSubtype() == 'forum') {
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
 * Customize river options
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function forums_river_options_setup($hook, $type, $value, $params) {
	if ($value['subtype'] == 'forum' || $value['subtypes'] == 'forum') {
		unset($value['subtype']);
		$value['subtypes'] = array('forum', 'forum_topic', 'forum_reply');
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
		
		foreach ($value as $idx => $item) {
			if ($item->getName() == 'discussion') {
				unset($value[$idx]);
			}
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
	if (!elgg_is_logged_in()) {
		return $value;
	}
	$user = elgg_get_logged_in_user_entity();

	if (@!$user->is_parent) {
		$value[] = ACCESS_ANONYMOUS;
	}

	return $value;
}

/**
 * If a forum subtype is being included in a tag module, we
 * we want to check for forum topics as well
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function forums_container_check_handler($hook, $type, $value, $params) {
	if (in_array('forum', $params)) {
		return TRUE;
	}
	return $value;
}

/**
 * Include forum topics when grabbing tagdashboard subtypes from metadata
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function forums_tagdashboard_metadata_subtype_handler($hook, $type, $value, $params) {
	if (in_array('forum', $value)) {
		$value[] = 'forum_topic';
	}
	return $value;
}


/**
 * Set the notification message for forums
 * 
 * @param string $hook    Hook name
 * @param string $type    Hook type
 * @param string $message The current message body
 * @param array  $params  Parameters about the blog posted
 * @return string
 */
function new_forum_notify_message($hook, $type, $message, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	if (elgg_instanceof($entity, 'object', 'forum')) {
		$descr = $entity->description;
		$title = $entity->title;
		$owner = $entity->getOwnerEntity();
		return elgg_echo('forums:new_forum:body', array(
			$owner->name,
			$title,
			$descr,
			$entity->getURL()
		));
	}
	return null;
}