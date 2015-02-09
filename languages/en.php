<?php
/**
 * Forums English Translation
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.com/
 * 
 */

return array(
	// Generic
	'forum' => 'Forum',
	'forum_topic' => 'Topic',
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
	'forums:title:globalforums' => 'Global Forums',
	'forums:title:groupforums' => 'Group Forums',
	'forums:title:topics' => 'Discussion Topics',
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
	'forums:label:moderators' => 'Forum Moderators',
	'forums:label:noforums' => 'No forums have been created',
	'forums:label:notopics' => 'No topics have been created',
	'forums:label:noreplies' => 'No replies have been created',
	'forums:label:newtopic' => 'New Topic',
	'forums:label:body' => 'Body',
	'forums:label:replyby' => '%s',
	'forums:label:dateline' => 'posted %s', 
	'forums:label:topicstartedby' => ' Started by %s',
	'forums:label:replytothis' => 'Reply to this',
	'forums:label:replytotopic' => 'Post to topic',
	'forums:label:totopic' => '&nbsp;to topic: %s',
	'forums:label:moderatedby' => 'Moderated by: %s',
	'forums:label:anonymous' => 'Anonymous',
	'forums:label:byanonymous' => 'By anonymous',
	'forums:label:byline' => 'By %s',
	'forums:label:bymask' => '%s',
	'forums:label:moderatormask' => 'Moderator Mask',
	'forums:label:groupforums' => 'Group forums',
	'forums:label:replycount' => 'Replies: %s',
	'forums:label:topiccount' => 'Topics: %s',
	'forums:label:discussiondisabled' => 'Discussions Moved',
	'forums:label:redirectforums' => 'Discussions have been moved to %s',
	'forums:label:closethread' => 'Close Topic',
	'forums:label:closereplythread' => 'Close Thread',
	'forums:label:openthread' => 'Open Topic',
	'forums:label:openreplythread' => 'Open Thread',
	'forums:label:optout' => 'Stop Topic Notifications',
	'forums:label:optin' => 'Get Topic Notifications',
	'forums:label:closeconfirm' => 'Are you sure you want to close this topic/thread?',
	'forums:label:openconfirm' => 'Are you sure you want to open this topic/thread?',
	'forums:label:closed' => 'Topic closed',
	'forums:label:closeddesc' => 'This topic has been closed by a moderator',
	'forums:label:lastpost' => 'Last post: %s',
	'forums:label:never' => 'Never',
	'forums:label:inmask' => 'In %s',
	'forums:label:viewstats' => 'View Stats',
	'foruma:label:statsmask' => '%s Stats',
	'forums:label:close' => 'Close',
	'forums:label:closed' => '[CLOSED]',

	// Forum stats
	'forums:stats:totals' => '<strong>%s</strong> out of <strong>%s</strong> group members are participating in this %s.',
	'forums:stats:member' => 'Member',
	'forums:stats:numreplies' => '# of posts',
	'forums:stats:totalwordcount' => 'Total reply word count',
	'forums:stats:participatingmembers' => 'Participating Members',
	'forums:stats:notparticipatingmembers' => 'Not Participating Members',
	'forums:stats:repliestoreplies' => '# of replies',

	// River
	'river:create:object:forum_reply_river' => '%s posted a reply to the topic %s in the forum %s',
	'river:create:object:forum_topic_river' => '%s created a new topic titled %s in the forum %s',

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
	'forums:error:invalidstatus' => 'Invalid status',
	'forums:error:setstatus' => 'There was an error setting the topic/thread status',

	'forums:success:forum:save' => 'Successfully saved forum',
	'forums:success:forum:delete' => 'Successfully deleted the forum',
	'forums:success:forum_topic:save' => 'Successfully saved topic',
	'forums:success:forum_topic:delete' => 'Successfully deleted the topic',
	'forums:success:forum_reply:save' => 'Successfully saved reply',
	'forums:success:forum_reply:delete' => 'Successfully deleted the reply',
	'forums:success:forum_topic:open' => 'Topic opened',
	'forums:success:forum_topic:closed' => 'Topic closed',
	'forums:success:forum_reply:open' => 'Thread opened',
	'forums:success:forum_reply:closed' => 'Thread closed',
	'forums:success:forum_topic:notifications' => 'Successfuly set topic notifications settings',

	// New forum notification (for groups or subscribed)
	'forums:new_forum:subject' => 'New Forum',
	'forums:new_forum:summary' => '%s created a new forum titled: %s',
	'forums:new_forum:body' => "%s created a new forum titled: %s\n\n%s\n\nTo create a topic or view this forum, click here:\n%s
",
	
	// New Forum Topic Notification
	'forums:new_topic:subject' => 'New topic in Spot forum "%s"',
	'forums:new_topic:summary' => "%s posted a new topic on the forum \"%s\" titled \"%s\"",
	'forums:new_topic:body' => "%s has posted a new topic on the forum \"%s\" titled \"%s\"

To reply to or view the topic, click here:

%s
",
	// New Forum Reply to Topic Notification
	'forums:new_reply_topic:subject' => 'New reply in topic "%s" in Spot forum "%s"',
	'forums:new_reply_topic:summary' => "%s posted a reply in topic \"%s\" in the forum \"%s\"",
	'forums:new_reply_topic:body' => "%s has posted a reply in topic \"%s\" in the forum \"%s\"
	
It reads:

%s

To reply to this or view the topic, click here:

%s
",

	// Reply to reply object (to replies owner) notification
	'forums:new_reply_user:subject' => 'New reply to your post in Spot forum "%s"',
	'forums:new_reply_user:summary' => "%s has replied to your post in the topic \"%s\" in Spot forum \"%s\"",
	'forums:new_reply_user:body' => "%s has replied to your post in the topic \"%s\" in Spot forum \"%s\"
	
It reads:

%s

To reply to this or view the topic, click here:

%s
",

	// Other content
	'groups:enableforums' => 'Enable group forums',
	'groups:enableforum' => 'Enable group discussions',
);
