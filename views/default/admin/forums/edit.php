<?php
/**
 * Forums Edit Forum View
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
elgg_load_js('elgg.forums');

$entity = get_entity(get_input('guid'));

if (elgg_instanceof($entity, 'object', 'forum')) {
	$form_vars = array(
		'id' => 'forum-edit-form',
	);

	$body_vars = forums_prepare_forum_form_vars($entity);

	echo elgg_view_form('forums/forum/save', $form_vars, $body_vars);
} else {
	echo elgg_echo('forums:error:notfound');
}

