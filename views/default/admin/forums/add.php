<?php
/**
 * Forums Add Forum View
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
$form_vars = array(
	'id' => 'forum-edit-form',
);

$body_vars = forums_prepare_forum_form_vars();

echo elgg_view_form('forums/forum/save', $form_vars, $body_vars);