<?php
/**
 * Forums Forum Reply Object view
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$full = elgg_extract('full_view', $vars, FALSE);
$reply = elgg_extract('entity', $vars, FALSE);

if (!$reply) {
	return TRUE;
}

$owner = $reply->getOwnerEntity();
$container = $reply->getContainerEntity();

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'forums/forum_reply',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));


$date = elgg_view_friendly_time($reply->time_created);

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

$forum = $reply->getContainerEntity();
$topic = get_entity($reply->topic_guid);

// Safety precaution! In case someone tries to view this topic directly with http://site/view/{guid}
if (elgg_instanceof($forum->getContainerEntity(), 'group')) {
	elgg_set_page_owner_guid($forum->container_guid);
	group_gatekeeper();
}

if ($full) {
	$dateline = elgg_echo('forums:label:dateline', array($date));

	// If anonymous, display as such
	if ($forum->anonymous) {
		// If the owner is an admin or a member of the moderator role, display mask
		if (forums_is_moderator($owner, $forum)) {
			$owner_link = "<span class='moderator_mask'>" . elgg_echo('forums:label:bymask', array($forum->moderator_mask)) . "</span>";
		} else {
			$owner_link = elgg_echo('forums:label:anonymous');
		}
	} else {
		$icon_url = $owner->getIconURL('topbar');
		$owner_icon = "<img src=\"$icon_url\" alt=\"$owner->name\" title=\"$owner->name\" class='forum-reply-icon elgg-border-plain elgg-transition' />";
		$owner_link = elgg_view('output/url', array(
			'href' => "profile/$owner->username",
			'text' => $owner_icon . $owner->name,
		));
	}

	$title = "<div class='elgg-subtext forum-reply-subtext'>$owner_link $dateline</div> $metadata";

	$content = "<div class='forum-reply-description'>" . elgg_view('output/longtext', array(
		'value' => $reply->description
	)) . "</div>";

	// Logged in and open topics only
	if (elgg_is_logged_in() && $topic->topic_status != 'closed') {
		$content .= elgg_view('output/url', array(
			'text' => elgg_view_icon('speech-bubble') . elgg_echo("forums:label:replytothis"),
			'href' => '#forum-reply-edit-form-' . $reply->guid,
			'class' => 'forum-reply-button reply-to-reply',
			'rel' => 'toggle',
		)) . "<div style='clear: both;'></div>";

		// Reply form vars
		$form_vars = array(
			'id' => 'forum-reply-edit-form-' . $reply->guid,
			'class' => 'forum-reply-edit-form',
			'name' => 'forum-reply-edit-form',
		);

		$body_vars = forums_prepare_reply_form_vars(NULL, $reply->topic_guid, $reply->guid);

		$content .= elgg_view_form('forums/forum_reply/save', $form_vars, $body_vars);
	}

	$content .= elgg_view('forums/replies', $vars);

	$content = elgg_view_module('featured', $title, $content, array(
		'class' => 'forum-reply-module',
		'id' => 'forum-reply-' . $reply->guid,
	));

	echo <<<HTML
	<div class='forum-reply'>
		$content
	</div>
HTML;
} else {
	// brief view
	// If anonymous, display as such
	if ($forum->anonymous) {
		// If the owner is an admin or a member of the moderator role, display mask
		if (forums_is_moderator($owner, $forum)) {
			$bymask = "<span class='moderator_mask'>" . elgg_echo('forums:label:bymask', array($forum->moderator_mask)) . "</span>";
			$owner_text = elgg_echo('forums:label:byline', array($bymask));
		} else {
			$owner_text = elgg_echo('forums:label:byanonymous');
		}

		$subtitle = "<p>$owner_text $date</p>";
	} else {
		$owner_link = elgg_view('output/url', array(
			'href' => "profile/$owner->username",
			'text' => $owner->name,
		));

		$author_text = elgg_echo('byline', array($owner_link));
		$subtitle = "<p>$author_text $date</p>";
		$owner_icon = elgg_view_entity_icon($owner, 'tiny');
	}

	$params = array(
		'entity' => $reply,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
		'content' => '',
	);
	$params = $params + $vars;
	$body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $body);
}
