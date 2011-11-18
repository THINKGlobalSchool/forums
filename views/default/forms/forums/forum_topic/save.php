<?php
/**
 * Forums Save Topic Form
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
$container_guid = elgg_extract('container_guid', $vars);
$tags = elgg_extract('tags', $vars, '');

$forum = get_entity($container_guid);

// Check if we've got an entity, if so, we're editing.
if ($guid) {
	$entity_hidden  = elgg_view('input/hidden', array(
		'name' => 'guid',
		'value' => $guid,
	));
	$entity = get_entity($guid);
} else {
	$body_label = elgg_echo('forums:label:body');
	$body_input = elgg_view('input/longtext', array(
		'name' => 'description',
		'value' => $description
	));

	$body = <<<HTML
		<div>
			<label>$body_label</label><br />
        	$body_input
		</div><br />
HTML;
}

// Labels/Input
$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array(
	'name' => 'title',
	'value' => $title
));

// Don't show tags input on anonymous forums
if (!$forum->anonymous) {
	$tags_label = elgg_echo('tags');
	$tags_input = elgg_view('input/tags', array(
		'name' => 'tags',
		'value' => $tags
	));
}

$container_hidden = elgg_view('input/hidden', array(
	'name' => 'container_guid',
	'value' => $container_guid
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
	$body
	<div id='tags-input'>
		<label>$tags_label</label><br />
		$tags_input
	</div><br />
	<div class='elgg-foot'>
		$submit_input
		$entity_hidden
		$container_hidden
	</div>
</div>
HTML;

echo $form_body;