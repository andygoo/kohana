
<h3 class="page-header">Redis Info</h3>

<ul class="nav nav-tabs" id="redisTab">
    <li class="active"><a data-toggle="tab" href="#info">Info</a></li>
    <li><a data-toggle="tab" href="#config">Config</a></li>
</ul>
<br>

<div class="tab-content">
    <div class="tab-pane active in" id="info">
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
    </div>
    <div class="tab-pane" id="config">
        <div class="table-responsive">
        <table class="table table-hover table-bordered">
        <?php foreach ($config as $key => $value):?>
        <tr>
            <td><?= $key?></td>
            <td><?= $value?></td>
        </tr>
        <?php endforeach;?>
        </table>
        </div>
    </div>
</div>
