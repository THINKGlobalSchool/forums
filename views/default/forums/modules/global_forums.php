<?php
/**
 * Forums global list module
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Forums options
$options = array(
	'type' => 'object',
	'subtype' => 'forum',
	'full_view' => FALSE,
	'metadata_name' => 'site_forum',
	'metadata_value' => TRUE,
	'list_class' => 'forum-list'
);

echo elgg_list_entities_from_metadata($options);