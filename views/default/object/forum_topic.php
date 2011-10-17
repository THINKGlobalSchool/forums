<?php
/**
 * Forums Forum Topic Object view
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$full = elgg_extract('full_view', $vars, FALSE);
$topic = elgg_extract('entity', $vars, FALSE);

if (!$topic) {
	return TRUE;
}

$owner = $topic->getOwnerEntity();
$container = $topic->getContainerEntity();

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'forums/forum_topic',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));


$date = elgg_view_friendly_time($topic->time_created);

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full) {
	$content = elgg_view('forums/replies', $vars);

	$content .= elgg_view('output/url', array(
		'text' => elgg_view_icon('speech-bubble') . elgg_echo("forums:label:replytotopic"), 
		'href' => '#forum-reply-edit-form-' . $topic->guid,
		'class' => 'forum-reply-button',
		'rel' => 'toggle',
	));
	
	// Reply form vars
	$form_vars = array(
		'id' => 'forum-reply-edit-form-' . $topic->guid,
		'class' => 'forum-reply-edit-form',
		'name' => 'forum-reply-edit-form',
	);
		
	$body_vars = forums_prepare_reply_form_vars(NULL, $topic->guid);

	$content .= elgg_view_form('forums/forum_reply/save', $form_vars, $body_vars);
	
	echo <<<HTML
	<div class='forum-topic'>
		$content
	</div>
HTML;

} else {
	// brief view
	$forum = $topic->getContainerEntity();

	// If anonymous, display as such
	if ($forum->anonymous) {
		// If the owner is an admin or a member of the moderator role, display mask
		if ($owner->isAdmin() || roles_is_member($forum->moderator_role, $owner->guid)) {
			$bymask = "<span class='moderator_mask'>" . elgg_echo('forums:label:bymask', array($forum->moderator_mask)) . "</span>";
			$owner_text = elgg_echo('forums:label:byline', array($bymask));
		} else {
			$owner_text = elgg_echo('forums:label:byanonymous');
		}

		$subtitle = "<p>$owner_text $date</p>";
	} else {
		$owner_link = elgg_view('output/url', array(
			'href' => "blog/owner/$owner->username",
			'text' => $owner->name,
		));

		$author_text = elgg_echo('byline', array($owner_link));
		$subtitle = "<p>$author_text $date</p>";

		$owner_icon = elgg_view_entity_icon($owner, 'tiny');
	}

	$params = array(
		'entity' => $topic,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
		'content' => '',
	);
	$params = $params + $vars;
	$body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $body);
}
