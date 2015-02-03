<?php
/**
 * Forums JS Library
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>
//<script>
elgg.provide('elgg.forums');

// Init function
elgg.forums.init = function() {
	// Delegate view stats click
	$(document).delegate('li.elgg-menu-item-view-stats a', 'click', elgg.forums.viewStatsClick);

	// Delegate stats close link
	$(document).delegate('.forum-stats-close', 'click', elgg.forums.statsCloseClick);

	// Delegate reply mouseover/mouseout
	$(document).delegate('.reply-to-reply', 'mouseover mouseout', elgg.forums.replyHover);

	// Delegate reply click
	$(document).delegate('.reply-to-reply', 'click', elgg.forums.replyClick);

	// Show the moderator mask input
	$('select#anonymous').change(function() {
		if($(this).val() == '1') {
			$('#anonymous-container').show();
			$('#access-container').hide();
			$('#forum-tags-container').hide();
		} else {
			$('#anonymous-container').hide();
			$('#access-container').show();
			$('#forum-tags-container').show();
		}
	});
}

// View stats click
elgg.forums.viewStatsClick = function(event) {
	event.preventDefault();
	var guid = $(this).data('guid');
	var $module_container = $('.forum-stats-container-' + guid);
	$module_container.html('<div class="elgg-ajax-loader"></div>');

	elgg.get(elgg.get_site_url() + "ajax/view/forums/stats", {
		data: {
			entity_guid: guid
		},
		success: function(data) {
			$module_container.hide().html(data).fadeIn('fast');
		},
		error: function() {
			//
		}
	});
}

// Close stats click
elgg.forums.statsCloseClick = function(event) {
	event.preventDefault();
	$(this).closest('.forum-stats-module').fadeOut('fast');
}

// Forum reply hover
elgg.forums.replyHover = function(event) {
	var $reply_container = $('#forum-reply-' + $(this).data('reply_guid'));
	if (!$(this).hasClass('elgg-state-active')) {
		if (event.type == 'mouseover') {
			$reply_container.addClass('forum-reply-active');
		} else if (event.type == 'mouseout') {
			$reply_container.removeClass('forum-reply-active');
		}
	}
}

// Reply click handler
elgg.forums.replyClick = function(event) {
	var $reply_container = $('#forum-reply-' + $(this).data('reply_guid'));
	if ($(this).hasClass('elgg-state-active')) {
		$reply_container.addClass('forum-reply-active');
	} else {
		$reply_container.removeClass('forum-reply-active');
	}
}

elgg.register_hook_handler('init', 'system', elgg.forums.init);