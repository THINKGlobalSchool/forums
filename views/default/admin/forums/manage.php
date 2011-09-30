<?php
/**
 * Forums Admin Management 
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
	'subtype' => 'forum',
	'limit' => 10,
	'pagination' => TRUE,
	'full_view' => FALSE,
);

$forums = elgg_list_entities($options);

if (!$forums) {
	$forums = elgg_echo('forums:label:none');
}

echo "<a href='". elgg_get_site_url() . "admin/forums/add' class='elgg-button elgg-button-action'>" . elgg_echo('forums:label:new') . "</a><div style='clear: both;'></div>";

echo elgg_view_module('inline', elgg_echo('forums:label:current'), $forums);