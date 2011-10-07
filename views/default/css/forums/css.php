<?php
/**
 * Forums CSS
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>

.forum {
	
}

.forum .forum-description {
	font-size: 12px;
	color: #222;
	margin-top: 5px;
	margin-bottom: 5px;
}

.forum .forum-topics {
	
}

.forum .forum-topics-header {
	padding: 3px 0;
}

.forum .forum-topics-header h4 {
	float: left;
}

.forum .forum-topics-controls {
	float: right;
}

.forum .forum-topics-controls .elgg-button {
	font-size: 11px;
}

.forum .forum-topics-list {
	margin: 5px 0px;
}

.forum-topic {
}

.forum-reply {
}

.forum-topic .forum-reply-icon {
	margin-right: 5px;
	vertical-align: middle;
}

.forum-topic .forum-reply-subtext {
	text-transform: none;
	float: left;
}

.forum-topic .forum-reply-description {
	margin-bottom: 5px;
}

.forum-topic .forum-reply-description .elgg-output {
	margin-top: 5px;
}

.forum-topic .forum-reply-button {
	font-size: 11px;
}

.forum-topic .reply-to-reply {
	float: right;
}

.forum-topic .forum-reply-button .elgg-icon {
	vertical-align: middle;
}

.forum-topic .forum-reply-subtext a:hover, .forum-topic .forum-reply-button:hover {
	text-decoration: none;
}

.forum-reply-edit-form {
	margin: 8px 0;
	display: none;
}

/** Elgg overrides **/

.forum-topic .elgg-list, .forum-topic .elgg-list > li {
	border: none !important;
}

.forum-topic .elgg-menu {
	text-transform: none;
}

/** Forum reply indent **/
.forum-reply ul {
	margin-left: 20px;
}

/** Forum reply module **/

.forum-reply-module {
	margin-bottom: 5px;
	-webkit-border-radius: 0;
	-moz-border-radius: 0;
	border-radius: 0;
}

.forum-reply-module > .elgg-body {
	border-left: 6px solid #CCC;
}

.forum-reply-module > .elgg-head {
    background-color: #666;
    padding: 4px 1px 0px 4px;
}

.forum-reply-module > .elgg-head * {
	text-transform: none;
	font-size: 13px;
}
