<?php
/**
 * Forums group list module
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Start building grroup forums options
$options = array(
	'type' => 'object',
	'subtype' => 'forum',
	'full_view' => FALSE,
	'metadata_name' => 'site_forum',
	'metadata_value' => FALSE, // DON'T include site forums, easy peasy
	'group_by' => 'fi.container_guid',
	'order_by' => 'fi.time_created DESC',
	'list_class' => 'forum-list'
);

$dbprefix = elgg_get_config('dbprefix');

// Need to throw in a new select 
$options['selects'][] = 'fi.time_created';

// Magical sql (grab forums posts and order the forums by last activity/create_date)
$options['joins'][] = "JOIN (
	SELECT DISTINCT xyz.container_guid, xyz.time_created
	FROM {$dbprefix}entities xyz
	ORDER BY xyz.time_created DESC
) fi on fi.container_guid = e.guid";

echo elgg_list_entities_from_metadata($options);