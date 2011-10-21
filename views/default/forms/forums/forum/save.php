<?php
/**
 * Forums Save Forum Form
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Map values
$title = elgg_extract('title', $vars, '');
$guid = elgg_extract('guid', $vars);
$description = elgg_extract('description', $vars, '');
$moderator_role = elgg_extract('moderator_role', $vars, '');
$anonymous = elgg_extract('anonymous', $vars, FALSE);
$moderator_mask = elgg_extract('moderator_mask', $vars, FALSE);
$access_id = elgg_extract('access_id', $vars);
$container_guid = elgg_extract('container_guid', $vars, elgg_get_page_owner_guid());

// Check if we've got an entity, if so, we're editing.
if ($guid) {
	$entity_hidden  = elgg_view('input/hidden', array(
		'name' => 'guid',
		'value' => $guid,
	));
	$entity = get_entity($guid);
} 

$container_hidden = elgg_view('input/hidden', array(
	'name' => 'container_guid',
	'value' => $container_guid
));

// Labels/Input
$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array(
	'name' => 'title',
	'value' => $title
));

$description_label = elgg_echo('description');
$description_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'value' => $description
));

$anonymous_label = elgg_echo('forums:label:anonymous');
$anonymous_input = elgg_view('input/dropdown', array(
	'name' => 'anonymous',
	'id' => 'anonymous',
	'value' => $anonymous,
	'options_values' => array(
		FALSE => elgg_echo('forums:label:no'),
		TRUE => elgg_echo('forums:label:yes')
	)
));

if ($anonymous) {
	$mask_display = 'visible';
	$access_display = 'none';
} else {
	$mask_display = 'none';
	$access_display = 'visible';
}

$moderator_mask_label = elgg_echo('forums:label:moderatormask');
$moderator_mask_input = elgg_view('input/text', array(
	'name' => 'moderator_mask',
	'value' => $moderator_mask,
));

// If the container is a group, don't display the moderator role
if (!elgg_instanceof($entity = get_entity($container_guid), 'group')) {
	$roles_label = elgg_echo('forums:label:moderator_role');
	$roles_input = elgg_view('input/roledropdown', array(
		'name' => 'moderator_role',
		'id' => 'moderator-role',
		'value' => $moderator_role,
		'show_hidden' => TRUE,
	));
} else {
	$c = elgg_get_context();
	elgg_set_context('group_forum_access');
	$access_label = elgg_echo('access');
	$access_input = elgg_view('input/access' , array(
		'name' => 'access_id',
		'value' => $access_id,
	));
	elgg_set_context($c);

	$access = <<<HTML
		<div>
			<label>$access_label</label><br />
			$access_input
		</div><br />
HTML;
}

$submit_input = elgg_view('input/submit', array(
	'name' => 'submit',
	'value' => elgg_echo('save')
));

// Build Form Body
$form_body = <<<HTML

<div>
	<div>
		<label>$title_label</label><br />
        $title_input
	</div><br />
	<div>
		<label>$description_label</label><br />
        $description_input
	</div><br />
	<div>
		<label>$anonymous_label</label>
        $anonymous_input
	</div><br />
	<div id='anonymous-container' style='display: $mask_display;'>
		<div>
			<label>$moderator_mask_label</label>
			$moderator_mask_input<br /><br />
		</div>
	</div>
	<div id='access-container' style='display: $access_display;'>
		$access
	</div>
	<div>
		<label>$roles_label</label>
		$roles_input
	</div><br />
	<div class='elgg-foot'>
		$submit_input
		$entity_hidden
		$container_hidden
	</div>
</div>
HTML;

echo $form_body;