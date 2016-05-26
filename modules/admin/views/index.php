
<?= HTML::script('media/js/Sortable.min.js')?>

<h3 class="page-header">首页</h3>

<div class="row" id="pannel">
  <div class="col-xs-12 col-sm-6">
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">Panel title1</h3>
        </div>
        <div class="panel-body">
            Panel content
        </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Panel title2</h3>
        </div>
        <div class="panel-body">
            Panel content
        </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6">
    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title">Panel title3</h3>
        </div>
        <div class="panel-body">
            Panel content
        </div>
    </div>
  </div>
  <div class="col-xs-12 col-sm-6">
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">Panel title4</h3>
        </div>
        <div class="panel-body">
            Panel content
        </div>
    </div>
  </div>
</div>

<script>
var pannel = document.getElementById("pannel");
Sortable.create(pannel, {
	handle: ".panel-title",
	animation: 150,
	store: {
    	get: function (sortable) {
    		var order = localStorage.getItem(sortable.options.group);
    		return order ? order.split('|') : [];
    	},
    	set: function (sortable) {
    		var order = sortable.toArray();
    		localStorage.setItem(sortable.options.group, order.join('|'));
    	}
    }
});
</script>

