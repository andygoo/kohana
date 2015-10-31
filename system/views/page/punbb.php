<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * PunBB pagination style
 * 
 * @preview  Pages: 1 … 4 5 6 7 8 … 15
 */
?>

<p class="pagination">

	<?php if ($current_page > 3): ?>
		<a href="<?php echo $page->url($first_page) ?>">1</a>
		<?php if ($current_page != 4) echo '&hellip;' ?>
	<?php endif ?>


	<?php for ($i = $current_page - 2, $stop = $current_page + 3; $i < $stop; ++$i): ?>

		<?php if ($i < 1 OR $i > $total_pages) continue ?>

		<?php if ($current_page == $i): ?>
			<strong><?php echo $i ?></strong>
		<?php else: ?>
			<a href="<?php echo $page->url($i) ?>"><?php echo $i ?></a>
		<?php endif ?>

	<?php endfor ?>


	<?php if ($current_page <= $total_pages - 3): ?>
		<?php if ($current_page != $total_pages - 3) echo '&hellip;' ?>
		<a href="<?php echo $page->url($total_pages) ?>"><?php echo $total_pages ?></a>
	<?php endif ?>

</p>