<?php
/**
 * PunBB pagination style
 * 
 * @preview  Pages: 1 … 4 5 6 7 8 … 15
 */
?>
<nav>
<ul class="pagination">
<?php if ($previous_page): ?>
	<li><a href="<?= $page->url($previous_page) ?>"><?= '上一页' ?></a></li>
<?php else: ?>
	<li class="disabled"><a href="#"><?= '上一页' ?></a></li>
<?php endif ?>

<?php if ($current_page > 3): ?>
	<li><a href="<?= $page->url($first_page) ?>">1</a></li>
	<?php if ($current_page != 4): ?>
	<li class="disabled"><a href="#">&hellip;</a></li>
	<?php endif;?>
<?php endif ?>

<?php for ($i = $current_page - 2, $stop = $current_page + 3; $i < $stop; ++$i): ?>
	<?php if ($i < 1 OR $i > $total_pages) continue ?>
	<?php if ($current_page == $i): ?>
		<li class="active"><a href="#"><?= $i ?></a></li>
	<?php else: ?>
		<li><a href="<?= $page->url($i) ?>"><?= $i ?></a></li>
	<?php endif ?>
<?php endfor ?>

<?php if ($current_page <= $total_pages - 3): ?>
	<?php if ($current_page != $total_pages - 3): ?>
	<li class="disabled"><a href="#">&hellip;</a></li>
	<?php endif;?>
	<li><a href="<?= $page->url($total_pages) ?>"><?= $total_pages ?></a></li>
<?php endif ?>

<?php if ($next_page): ?>
	<li><a href="<?= $page->url($next_page) ?>"><?= '下一页' ?></a></li>
<?php else: ?>
	<li class="disabled"><a href="#"><?= '下一页' ?></a></li>
<?php endif ?>
</ul>
</nav>