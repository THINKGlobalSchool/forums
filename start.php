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
 */

elgg_register_event_handler('init', 'system', 'forums_init');

function forums_init() {

	// Register and load library
	elgg_register_library('elgg:forums', elgg_get_plugins_path() . 'forums/lib/forums.php');
	elgg_load_library('elgg:forums');

	// Register CSS
	$f_css = elgg_get_simplecache_url('css', 'forums/css');
	elgg_register_css('elgg.forums', $f_css);

	// Register JS libraries
	$f_js = elgg_get_simplecache_url('js', 'forums/forums');
	elgg_register_js('elgg.forums', $f_js);

	// Register page handler
	elgg_register_page_handler('forums','forums_page_handler');

	// Add submenus
	elgg_register_event_handler('pagesetup', 'system', 'forums_submenus');

	// Item entity menu hook
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'forums_setup_entity_menu', 999);

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
	elgg_register_action('forums/forum_topic/save', "$action_base/forum_topic/delete.php");
	elgg_register_action('forums/forum_reply/save', "$action_base/forum_reply/delete.php");

	return TRUE;
}

/**
 * Forum page handler
 */
function forums_page_handler($page) {
	gatekeeper(); // Logged in only
	
	elgg_push_breadcrumb(elgg_echo('forums'), elgg_get_site_url() . "forums/all");	
	
	switch($page[0]) {
		case 'view':
			$params = forums_get_page_content_view($page[1]);
			break;
		case 'all':
		default: 
			$params = forums_get_page_content_list();
			break;
	}
	
	// Custom sidebar (none at the moment)
	$params['sidebar'] .= elgg_view('todo/sidebar');

	$body = elgg_view_layout('content', $params);

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
	return elgg_get_site_url() . 'forums/viewtopic/' . $entity->guid;
}

/**
 * Populates the ->getUrl() method for forum reply entities
 *
 * @param ElggObject entity
 * @return string request url
 */
function forum_reply_url($entity) {
	return elgg_get_site_url() . 'forums/viewreply/' . $entity->guid;
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

	if (!elgg_instanceof($entity, 'object', 'forum')) {
		return $return;
	}

	$return = array();

	// Admin Only
	if (elgg_is_admin_logged_in()) {
		$options = array(
			'name' => 'edit',
			'text' => elgg_echo('edit'),
			'href' => elgg_get_site_url() . 'admin/forums/edit?guid=' . $entity->guid,
			'priority' => 2,
		);
		$return[] = ElggMenuItem::factory($options);

		$options = array(
			'name' => 'delete',
			'text' => elgg_view_icon('delete'),
			'title' => elgg_echo('delete:this'),
			'href' => "action/{$params['handler']}/delete?guid={$entity->getGUID()}",
			'confirm' => elgg_echo('deleteconfirm'),
			'priority' => 3,
		);

		$return[] = ElggMenuItem::factory($options);
	}

	return $return;
}


