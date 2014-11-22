<?php
/**
 * Forums Stats View
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['entity_guid'] - Forum/topic guid
 */

$guid = elgg_extract('entity_guid', $vars, FALSE);

$entity = get_entity($guid);

// Check for valid entity (forum/topic and not anonymous)
if (!(elgg_instanceof($entity, 'object', 'forum') || elgg_instanceof($entity, 'object', 'forum_topic'))
	|| $entity->anonymous 
	|| $entity->getContainerEntity()->anonymous)
{
	echo elgg_echo('forums:error:forum:invalid');
	return;
}

// Get container entity (should be a group)
if (elgg_instanceof($entity, 'object', 'forum')) {
	$container = $entity->getContainerEntity();
} else if (elgg_instanceof($entity, 'object', 'forum_topic')) {
	$container = $entity->getContainerEntity()->getContainerEntity();
}

// Validate group and permissions
if (!elgg_instanceof($container, 'group') || (elgg_instanceof($container, 'group') && !$container->canEdit())) {
	echo elgg_echo('forums:error:forum:invalid');
	return;
}

// Module title depends on subtype
$module_title = elgg_echo('foruma:label:statsmask', array(
	elgg_echo($entity->getSubtype())
));

// Make a link to close the module
$close_link = elgg_view('output/url', array(
	'value' => '#',
	'text' => elgg_echo('forums:label:close'),
	'class' => 'home-small right forum-stats-close'
));

// Build stats body
$member_stat_info = forums_get_group_members_stats($entity, $container);

$stat_string = elgg_echo('forums:stats:totals', array(
	$member_stat_info['total_participating_members'],
	$member_stat_info['total_group_members'],
	strtolower(elgg_echo($entity->getSubtype()))
));

$member_header = elgg_echo('forums:stats:member');
$replies_header = elgg_echo('forums:stats:numreplies');
$words_header = elgg_echo('forums:stats:totalwordcount');

$p_members_title = elgg_echo('forums:stats:participatingmembers');
$np_members_title = elgg_echo('forums:stats:notparticipatingmembers');

if (count($member_stat_info['participating_members_stats'])) {
	foreach ($member_stat_info['participating_members_stats'] as $member => $stats) {
		$member = get_entity($member);
		$member_link = elgg_view('output/url', array(
			'value' => $member->getURL(),
			'text' => $member->name
		));

		$reply_count = $stats['reply_count'];
		$word_count = $stats['word_count'];

		$p_content .= <<<HTML
			<tr>
				<td>$member_link</td>
				<td><label>$reply_count</label></td>
				<td><label>$word_count</label></td>
			</tr>
HTML;
	}

	$p_member_content = <<<HTML
		<h3 class='mbm mtl'>$p_members_title</h3>
		<table class='elgg-table'>
			<thead>
				<tr>
					<th><label>$member_header</label></th>
					<th><label>$replies_header</label></th>
					<th><label>$words_header</label></th>
				</tr>
			</thead>
			<tbody>
				$p_content
			</tbody>
		</table>
HTML;
}

if (count($member_stat_info['not_participating_members'])) {
	foreach ($member_stat_info['not_participating_members'] as $member) {
		$member = get_entity($member);
		$member_link = elgg_view('output/url', array(
			'value' => $member->getURL(),
			'text' => $member->name
		));

		$np_content .= <<<HTML
			<tr>
				<td>$member_link</td>
			</tr>
HTML;
	}

	$np_member_content = <<<HTML
		<h3 class='mbm mtl'>$np_members_title</h3>
		<table class='elgg-table'>
			<thead>
				<tr>
					<th><label>$member_header</label></th>
				</tr>
			</thead>
			<tbody>
				$np_content
			</tbody>
		</table>
HTML;
}


$module_body = <<<HTML
	<div class='forum-participation-count mbm'>$stat_string</div>
	$p_member_content
	$np_member_content 
HTML;

// Output
echo elgg_view_module('featured', $module_title . $close_link, $module_body, array('class' => 'forum-stats-module'));