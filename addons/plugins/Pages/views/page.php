<?php
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * Displays a single page.
 *
 * @package esoTalk
 */

$page = $data["page"];
?>
<div class='postContent thing'>
<div class='postHeader'>
	<div class='info'>
		<h3><?php echo $page["title"]; ?></h3>
	</div>
	<div class='controls'>
	</div>
</div>

<?php if (!empty($page["content"])): ?>
<div class='postBody'>
<?php echo $page["content"]; ?>
</div>
<?php endif; ?>

</div>

</div>