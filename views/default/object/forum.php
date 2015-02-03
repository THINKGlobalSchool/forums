<?php
/**
 * Forums 'Forum' Object view
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */
$forum = elgg_extract('entity', $vars, false);
$full_view = elgg_extract('full_view', $vars, false);

if (!$forum) {
	return '';
}

$linked_title = "<a href=\"{$forum->getURL()}\" title=\"" . htmlentities($forum->title) . "\">{$forum->title}</a>";

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'forums/forum',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

if ($full_view) {

	$params = array(
		'title' => FALSE,
		'entity' => $forum,
		'metadata' => $metadata,
		'content' => elgg_view('output/longtext', array('value' => $forum->description, 'class' => 'forum-description'))
	);
	
	$body = elgg_view('object/elements/summary', $params);
	$summary = elgg_view_image_block('', $body);
	
	$topics_title = elgg_echo('forums:title:topics');
	$topics_list = elgg_view('forums/topics', $vars);
	
	if (elgg_is_logged_in()) {
		$topic_new = elgg_view('output/url', array(
			'name' => 'forums-create-topic',
			'id' => 'forums-create-topic',
			'class' => 'elgg-button elgg-button-submit',
			'href' => elgg_get_site_url() . 'forums/forum_topic/add/' . $forum->guid,
			'text' => elgg_echo('forums:label:newtopic'),
		));
	}

	echo <<<___HTML
		<div class="forum">
			$summary
			<div class='forum-stats-container-$forum->guid'></div>
			<div class='forum-topics'>
				<div class='forum-topics-header'>
					<h3>$topics_title</h3>
					<div class='forum-topics-controls'>
						$topic_new
					</div>
					<div style='clear: both;'></div><br />
				</div>
				<div class='forum-topics-list'>
					$topics_list
				</div>
			</div>
		</div>
___HTML;

} else {
	// Grab container
	$container = $forum->getContainerEntity();

	// Check if we're viewing this forum outside of it's group container context
	if (elgg_instanceof($container, 'group') && elgg_get_page_owner_guid() != $container->guid) {
		// Add group info to listing
		$group_link = elgg_view('output/url', array(
			'value' => elgg_normalize_url("forums/group/{$container->guid}"),
			'text' => $container->name
		));
		$subtitle = "<div class='mbs mts'>" . elgg_echo('forums:label:inmask', array($group_link)) . "</div>";
	}

	// Display moderator role if in admin context
	if (elgg_get_context() == 'admin') {
		$role = get_entity($forum->moderator_role);
		$subtitle .= elgg_echo('forums:label:moderatedby', array($role->title)) . "&nbsp;";
	}

	// Get last post for this forum
	$last_post = elgg_get_entities(array(
		'type' => 'object',
		'subtypes' => array('forum_topic', 'forum_reply'),
		'container_guid' => $forum->guid,
		'limit' => 1
	));

	if (count($last_post)) {
		$last_post = $last_post[0];
		$owner = $last_post->getOwnerEntity();
		// If anonymous, display as such
		if ($forum->anonymous) {
			// If the owner is an admin or a member of the moderator role, display mask
			if (forums_is_moderator($owner, $forum)) {
				$owner_link = "<span class='moderator_mask'>" . elgg_echo('forums:label:bymask', array($forum->moderator_mask)) . "</span>";
			} else {
				$owner_link = elgg_echo('forums:label:anonymous');
			}
		} else {
			$owner_link = elgg_view('output/url', array(
				'href' => "profile/$owner->username",
				'text' => $owner->name,
			));
		}


		$last_post_text = elgg_view_friendly_time($last_post->time_created) . "&nbsp;" . lcfirst(elgg_echo('forums:label:byline', array($owner_link)));
	} else {
		$last_post_text = elgg_echo('forums:label:never');
	}

	$subtitle .= elgg_echo('forums:label:lastpost', array($last_post_text));

	$params = array(
		'entity' => $forum,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
	);
	
	$body = elgg_view('object/elements/summary', $params);
	echo "<div class='forum'>" . elgg_view_image_block('', $body) . "</div>";
	echo "<div class='forum-stats-container-{$forum->guid}'></div>";
}