<?php
/**
 * Forums CSS
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>
/** <style> /**/
.forum > .elgg-image-block > .elgg-body > .elgg-content {
	margin-left: 0;
	margin-top: 14px;
}

.forum .forum-description {
	font-size: 12px;
	color: #222;
	margin-top: 5px;
	margin-bottom: 5px;
}

.forum .forum-topics {
	border-top: 1px dotted #CCC;
	padding-top: 10px;
}

.forum .forum-topics-header {
	padding: 3px 0;
}

.forum .forum-topics-header h3 {
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
	box-shadow: -1px 1px 4px #bbb;
	margin-bottom: 4px;
}

.forum-reply-module.forum-reply-active {
	border: 2px solid #333 !important;
	box-shadow: 0px 0px 10px #85161d;
}

.forum-reply-module.forum-reply-active.forum-reply-module > .elgg-head {
	background: linear-gradient(-180deg, rgb(171, 51, 45) 0%, rgb(127, 19, 25) 100%);
	padding: 4px;
}

.forum-reply-module.forum-reply-active.forum-reply-module > .elgg-body {
	padding-left: 9px;
	padding-right: 9px;
	padding-bottom: 9px;
	padding-top: 11px;
}

.forum-topic .forum-reply-icon {
	margin-right: 5px;
	vertical-align: middle;
}

.forum-topic .forum-reply-subtext {
	text-transform: none;
	float: left;
	margin-top: 2px;
}

.forum-topic .forum-reply-description {
	margin-bottom: 5px;
	padding-right: 10px;
}

.forum-topic .forum-reply-description .elgg-output {
	margin-top: 5px;
}

.forum-topic .forum-reply-button {
	font-size: 11px;
}

.forum-topic .reply-to-reply {
	float: right;
	margin-right: 10px;
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

.moderator_mask {
	font-weight: bolder;
	color: #333;
}

.forum-entity-menu-item  {
	width:75px;	
	font-weight: bolder;
	color: #666;
}

.forum-list {
	border-top: 1px solid #ccc;
}

.forum-list > li {
	border-bottom: 1px solid #ccc;
	border-left: 1px solid #ccc;
	border-right: 1px solid #ccc;
}

.forum-list > li:nth-child(odd) {
	background-color: #fff;
}

.forum-list > li:nth-child(even) {
	background-color: #f0f0f0;
}

.forum-list > li .elgg-image-block {
	padding: 4px 0 6px;
	margin: 0;
	margin-right: 10px;
}

.forum-list > li .elgg-image-block .elgg-body:first-child {
	margin-left: 6px;
}

/** Forum stats **/
.forum-participation-count {
	color: #444;
}

.forum-stats-module {
	margin: 15px;
	box-shadow: 2px 2px 3px #666;
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
	border-radius: 10px 3px 3px 3px;
}

.forum-reply-module > .elgg-body {
	/*border-left: 6px solid #CCC;*/
	padding: 10px 1px 10px 10px;
}

.forum-reply-module > .elgg-head {
    background-color: #666;
    padding: 4px 1px 3px 4px;
}

.forum-reply-module > .elgg-head * {
	text-transform: none;
	font-size: 13px;
}

.forum-reply-module .elgg-menu-item-edit {
	margin-right: 15px;
}

.forum-reply-module .elgg-menu-item-delete {
	margin-left: 0;
}

.forum-reply-module .forum-reply-icon {
	margin-left: 3px;
}
