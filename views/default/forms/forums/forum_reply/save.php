<?php
/**
 * Forums Save Reply Form
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Map values
$guid = elgg_extract('guid', $vars);
$description = elgg_extract('description', $vars, '');
$topic_guid = elgg_extract('topic_guid', $vars);
$reply_guid = elgg_extract('reply_guid', $vars);

// Check if we've got an entity, if so, we're editing.
if ($guid) {
	$entity_hidden  = elgg_view('input/hidden', array(
		'name' => 'guid',
		'value' => $guid,
	));
	$entity = get_entity($guid);
}

$body_label = elgg_echo('forums:label:body');
$body_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'value' => $description
));

$topic_hidden = elgg_view('input/hidden', array(
	'name' => 'topic_guid',
	'value' => $topic_guid
));

$reply_hidden = elgg_view('input/hidden', array(
	'name' => 'reply_guid',
	'value' => $reply_guid
));

$submit_input = elgg_view('input/submit', array(
	'name' => 'submit',
	'value' => elgg_echo('save')
));

// Build Form Body
$form_body = <<<HTML

<div>
	<div>
        $body_input
	</div><br />
	<div class='elgg-foot'>
		$submit_input
		$entity_hidden
		$topic_hidden
		$reply_hidden
	</div>
</div>
HTML;

echo $form_body;