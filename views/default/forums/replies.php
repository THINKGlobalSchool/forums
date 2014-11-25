<?php
/**
 * Forums Topic List
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['parent_open']
 */

$options = array(
	'type' => 'object',
	'subtype' => 'forum_reply',
	'limit' => get_input('limit', 15),
	'offset' => get_input('offset', 0),
	'full_view' => TRUE,
	'container_guid' => $vars['entity']->container_guid,
	'relationship' => FORUM_REPLY_RELATIONSHIP, 
	'relationship_guid' => $vars['entity']->guid, 
	'inverse_relationship' => TRUE,
	'order_by' => 'e.time_created asc',
	'parent_open' => $vars['parent_open']
);

$list = elgg_list_entities_from_relationship($options);

echo $list;
