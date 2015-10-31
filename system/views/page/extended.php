<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Extended pagination style
 * 
 * @preview  « Previous | Page 2 of 11 | Showing items 6-10 of 52 | Next »
 */
?>

<p class="pagination">

	<?php if ($previous_page): ?>
		<a href="<?php echo $page->url($previous_page) ?>">&laquo;&nbsp;<?php echo '上一页' ?></a>
	<?php else: ?>
		&laquo;&nbsp;<?php echo '上一页' ?>
	<?php endif ?>

	| <?php echo 'Page' ?> <?php echo $current_page ?> <?php echo 'of' ?> <?php echo $total_pages ?>

	| <?php echo 'Showing items' ?> <?php echo $current_first_item ?>&ndash;<?php echo $current_last_item ?> <?php echo 'of' ?> <?php echo $total_items ?>

	| <?php if ($next_page): ?>
		<a href="<?php echo $page->url($next_page) ?>"><?php echo '下一页' ?>&nbsp;&raquo;</a>
	<?php else: ?>
		<?php echo '下一页' ?>&nbsp;&raquo;
	<?php endif ?>

</p>