<?php
/** 
 * Set topic participation for all forums
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

$options = array(
	'type' => 'object',
	'subtype' => 'forum_topic',
	'limit' => 0
);

$topics = elgg_get_entities($options);

echo "<h1>Set topic participation relationships</h1>";

foreach ($topics as $topic) {
	echo "<br />Topic: {$topic->guid}<br /><br />";

	$replies = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'forum_reply',
		'metadata_name' => 'topic_guid',
		'metadata_value' => $topic->guid,
		'group_by' => 'e.owner_guid',
		'limit' => 0,
	));

	foreach ($replies as $reply) {
		echo "Unique Reply Owner: {$reply->owner_guid}<br />";
		add_entity_relationship($reply->owner_guid, 'participating_in_topic', $topic->guid);
	}
}
