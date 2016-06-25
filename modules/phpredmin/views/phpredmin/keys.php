
<h3 class="page-header">Keys</h3>

<form class="form-inline" method="get">
    <div class="form-group">
        <div class="input-group">
			<input type="text" name="key" class="form-control" value="<?= Arr::get($_GET, 'key')?>" placeholder="" required>
            <span class="input-group-btn">
                <button type="submit" class="btn btn-info">查找</button>
            </span>
        </div>
    </div>
</form>
<br>

<?php if (empty($keys)):?>
<div class="alert alert-success">
    <a class="close" data-dismiss="alert" href="#">×</a>
    Supported search patterns:
    <ul>
        <li>h?llo matches hello, hallo and hxllo</li>
        <li>h*llo matches hllo and heeeello</li>
        <li>h[ae]llo matches hello and hallo, but not hillo</li>
        <li>* matches the all keys</li>
    </ul>
    Use \ to escape special characters if you want to match them verbatim.
</div>
<?php else:?>
<div class="table-responsive">
<table class="table table-hover table-bordered">
<thead>
<tr>
    <th width="35%">Key</th>
    <th width="10%">Type</th>
    <th width="15%">TTL</th>
    <th width="15%">Encoding</th>
    <th width="10%">Size</th>
    <th width="15%">操作</th>
</tr>
</thead>
<tbody>
<?php foreach ($keys as $item):?>
<tr>
    <td style="max-width:350px; overflow: hidden;white-space: nowrap;text-overflow: ellipsis;"><?= $item['key']?></td>
    <td><?= $item['type']?></td>
    <td><?= $item['ttl']?></td>
    <td><?= $item['encode']?></td>
    <td><?= Text::bytes($item['size'])?></td>
    <td>
        <a href="<?= URL::site('phpredmin/view/'.$curr_db.'?key='.$item['key']);?>" class="btn btn-info btn-xs view-detail" data-key="<?= $item['key']?>">查看</a>&nbsp;&nbsp;&nbsp;
        <a href="<?= URL::site('phpredmin/del/'.$curr_db.'?key='.$item['key']);?>" class="btn btn-danger btn-xs" onclick="return confirm('确定删除吗？')">删除</a>
    </td>
</tr>
<?php endforeach;?>
</tbody>
</table>
</div>

<div id="pager"><?= $pager ?></div>

<?php endif;?>

<?php include Kohana::find_file('views/phpredmin', 'modals');?>

