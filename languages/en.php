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
	'forums:add' => 'New Group Forum',

	// Page titles 
	'forums:title:allforums' => 'All Site Forums',
	'forums:title:topics' => 'Topics in forum: %s',
	'forums:title:topicedit' => 'Edit Topic',
	'forums:title:replyedit' => 'Edit reply',
	'forums:title:ownerforums' => '%s\'s Forums',
	'forums:title:addforum' => 'New Forum',
	'forums:title:editforum' => 'Edit Forum',

	// Labels
	'forums:label:siteforums' => 'Site Forums',
	'forums:label:none' => 'No Forums',
	'forums:label:current' => 'Current Forums',
	'forums:label:new' => 'New Forum',
	'forums:label:anonymous' => 'Anonymous Forum',
	'forums:label:yes' => 'Yes',
	'forums:label:no' => 'No',
	'forums:label:moderator_role' => 'Moderator Role',
	'forums:label:notopics' => 'No topics have been created',
	'forums:label:noreplies' => 'No replies have been created',
	'forums:label:newtopic' => 'New Topic',
	'forums:label:body' => 'Body',
	'forums:label:replyby' => '%s',
	'forums:label:dateline' => 'posted %s', 
	'forums:label:replytothis' => 'Reply to this',
	'forums:label:replytotopic' => 'Reply to topic',
	'forums:label:totopic' => '&nbsp;to topic: %s',
	'forums:label:moderatedby' => 'Moderated by: %s',
	'forums:label:anonymous' => 'Anonymous',
	'forums:label:byanonymous' => 'By anonymous',
	'forums:label:byline' => 'By %s',
	'forums:label:bymask' => '%s',
	'forums:label:moderatormask' => 'Moderator Mask',
	'forums:label:groupforums' => 'Group forums',

	// River
	'river:create:object:forum_reply' => '%s posted a reply to the topic %s in the forum %s',
	'river:create:object:forum_topic' => '%s create a new topic titled %s in the forum %s',

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
	'forums:error:forum_topic:invalid' => 'There was an error editing the topic',
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
	'groups:enableforum' => 'Enable group forums',
);

add_translation('en',$english);
