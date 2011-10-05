<?php
/**
 * Forums Topic List
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$options = array(
	'type' => 'object',
	'subtype' => 'forum_topic',
	'limit' => get_input('limit', 15),
	'offset' => get_input('offset', 0),
	'full_view' => FALSE,
	'container_guid' => $vars['entity']->guid,
);

$list = elgg_list_entities($options);

if (!$list) {
	$list = elgg_echo('forums:label:notopics');
}

echo $list;