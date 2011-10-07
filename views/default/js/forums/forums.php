<?php
/**
 * Forums JS Library
 *
 * @package Forums
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>
//<script>
elgg.provide('elgg.forums');

// Init function
elgg.forums.init = function() {
	console.log('forums loaded');
}


elgg.register_hook_handler('init', 'system', elgg.forums.init);
//</script>