<?php
/**
 * Forums English Translation
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$english = array(
	// Generic
	'forum' => 'Forum',
	'forums' => 'Forums',
	'item:object:forum' => 'Forums',
	'item:object:forum_topic' => 'Forum Topics',
	'item:object:forum_reply' => 'Forum Replies',
	'admin:forums' => 'Forums',
	'admin:forums:manage' => 'Manage',
	'admin:forums:add' => 'New Forum',
	'admin:forums:edit' => 'Edit Forum',

	// Page titles 
	'forums:title:allforums' => 'All Site Forums',
	'forums:title:topics' => 'Topics in forum: %s',
	'forums:title:topicedit' => 'Edit Topic',

	// Labels
	'forums:label:none' => 'No Forums',
	'forums:label:current' => 'Current Forums',
	'forums:label:new' => 'New Forum',
	'forums:label:anonymous' => 'Anonymous Forum',
	'forums:label:yes' => 'Yes',
	'forums:label:no' => 'No',
	'forums:label:moderator_role' => 'Moderator Role',
	'forums:label:notopics' => 'No topics have been created',
	'forums:label:newtopic' => 'New Topic',
	'forums:label:body' => 'Body',
	'forums:label:replyby' => '%s',
	'forums:label:dateline' => 'posted %s', 

	// River

	// Messages
	'forums:error:requiredfields' => 'One or more required fields are missing',
	'forums:error:notfound' => 'Item not found',
	'forums:error:permissiondenied' => 'You do not have permission to view this item',
	'forums:error:forum:delete' => 'There was an error deleting the forum',
	'forums:error:forum:save' => 'There was an error saving the forum',
	'forums:error:forum:edit' => 'There was an error editing the forum',
	'forums:error:forum:invalid' => 'Invalid Forum',
	'forums:error:forum_topic:delete' => 'There was an error deleting the topic',
	'forums:error:forum_topic:save' => 'There was an error saving the topic',
	'forums:error:forum_topic:edit' => 'There was an error editing the topic',
	'forums:error:forum_reply:delete' => 'There was an error deleting the reply',
	'forums:error:forum_reply:save' => 'There was an error saving the reply',
	'forums:error:forum_reply:edit' => 'There was an error editing the reply', 

	'forums:success:forum:save' => 'Successfully saved forum',
	'forums:success:forum:delete' => 'Successfully deleted the forum',
	'forums:success:forum_topic:save' => 'Successfully saved topic',
	'forums:success:forum_topic:delete' => 'Successfully deleted the topic',
	'forums:success:forum_reply:save' => 'Successfully saved reply',
	'forums:success:forum_reply:delete' => 'Successfully deleted the reply',

	// Other content
);

add_translation('en',$english);
