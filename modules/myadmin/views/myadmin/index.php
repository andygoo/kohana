<h3 class="page-header">MySQL Info</h3>

<div class="table-responsive">
<table class="table table-hover table-bordered">
<?php foreach ($info as $key => $value):?>
<tr>
    <td><?= $key?></td>
    <td><?= $value?></td>
</tr>
<?php endforeach;?>
</table>
</div>