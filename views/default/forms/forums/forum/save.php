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

// Check if we've got an entity, if so, we're editing.
if ($guid) {
	$entity_hidden  = elgg_view('input/hidden', array(
		'name' => 'guid',
		'value' => $guid,
	));
	$entity = get_entity($guid);
} 

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
} else {
	$mask_display = 'none';
}

$moderator_mask_label = elgg_echo('forums:label:moderatormask');
$moderator_mask_input = elgg_view('input/text', array(
	'name' => 'moderator_mask',
	'value' => $moderator_mask,
));

$roles_label = elgg_echo('forums:label:moderator_role');
$roles_input = elgg_view('input/roledropdown', array(
	'name' => 'moderator_role',
	'id' => 'moderator-role',
	'value' => $moderator_role,
));

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
	<div id='moderator-mask-container' style='display: $mask_display;'>
		<label>$moderator_mask_label</label>
		$moderator_mask_input<br /><br />
	</div>
	<div>
		<label>$roles_label</label>
		$roles_input
	</div>
	<div class='elgg-foot'>
		$submit_input
		$entity_hidden
	</div>
</div>
HTML;

echo $form_body;