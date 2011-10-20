<?php
/**
 * Forums Forum Object view
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
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
		'content' => "<div class='forum-description'>" . $forum->description  . "</div>",
	);
	
	$body = elgg_view('object/elements/summary', $params);
	$summary = elgg_view_image_block('', $body);
	
	$topics_title = elgg_echo('forums:title:topics');
	$topics_list = elgg_view('forums/topics', $vars);
	
	$topic_new = elgg_view('output/url', array(
		'name' => 'forums-create-topic',
		'id' => 'forums-create-topic',
		'class' => 'elgg-button elgg-button-submit',
		'href' => elgg_get_site_url() . 'forums/forum_topic/add/' . $forum->guid,
		'text' => elgg_echo('forums:label:newtopic'),
	));
	
	echo <<<___HTML
		<div class="forum">
			$summary
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
	// Display moderator role if in admin context
	if (elgg_get_context() == 'admin') {
		$role = get_entity($forum->moderator_role);
		$subtitle = elgg_echo('forums:label:moderatedby', array($role->title));
	}

	$params = array(
		'entity' => $forum,
		'metadata' => $metadata,
		'content' => elgg_get_excerpt($forum->description),
		'subtitle' => $subtitle,
	);
	

	
	$body = elgg_view('object/elements/summary', $params);
	echo elgg_view_image_block('', $body);
}