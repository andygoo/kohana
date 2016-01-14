<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>好车无忧管理后台</title>
<?= HTML::style('media/bootstrap/css/bootstrap.min.css')?>
<?= HTML::style('media/css/dashboard.css')?>
<style>
.sidebar{padding-top:0px}
.navbar-inverse .navbar-brand{color:#eee}
.navbar-inverse .navbar-nav>li>a{color:#eee}
.collapsed>.glyphicon-menu-down:before{content: "\e257";}

.list-group-item {border-color:#efefef}
.list-group-item>a {display: block; margin-left: 18px;}
.list-group-item>a:hover {text-decoration: none;}
.list-group-item:hover {background: #FCFCFC;}
.list-group-item.active>a, .list-group-item>a:focus {color:#f37800;text-decoration: none;}
.list-group-item.active, .list-group-item.active:focus, .list-group-item.active:hover {
    background-color: #FCFCFC;
    border-color: #efefef;
    background-image: none;
    text-shadow: none;
}

#accordion .panel {
    margin-bottom: 0px;
    background-color: #f5f5f5;
    border: 0px;
    border-radius: 0px;
    -webkit-box-shadow: 0px;
    box-shadow: 0px;
    border-top: 1px solid #fff;
    border-bottom: 1px solid #dbdbdb;	
}
#accordion .panel>a{color:#555}
#accordion .panel>a>i{color:#999;font-size:12px;margin-top:2px;}
.navbar-inverse .navbar-nav>.open>a, .navbar-inverse .navbar-nav>.open>a:focus, .navbar-inverse .navbar-nav>.open>a:hover {
	background: #367fa9;color: #f9f9f9;
}
.page-header a:hover {text-decoration: none}
</style>
<?= HTML::script('media/js/jquery.min.js')?>
<?= HTML::script('media/bootstrap/js/bootstrap.min.js')?>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top" style="background: #3c8dbc">
	<div class="container-fluid">
		<div class="navbar-header">
			<a class="navbar-brand ajax-click" href="<?= URL::site()?>"><i class="glyphicon glyphicon-home"></i>&nbsp;好车无忧管理后台</a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
			    <li class="dropdown">
			        <a href="#" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i>&nbsp;<?= $user['username']?><span class="caret"></span></a>
                    <ul class="dropdown-menu">
				        <li><a href="<?= URL::site('admin/password')?>" class="ajax-modal-sm">修改密码</a></li>
                    </ul>
                </li>
				<li><a href="<?= URL::site('admin/logout')?>"><i class="glyphicon glyphicon-off"></i>&nbsp;退出</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3 col-md-2 sidebar">
			<ul class="nav nav-sidebar" id="accordion">
			    <?php $id = 0;?>
			    <?php foreach ($menu as $name=>$items):?>
			    <?php $id++;?>
				<li class="panel">
				    <a href="#menu<?= $id?>" class="collapsed" data-toggle="collapse" data-parent="#accordion"><?= $name?><i class="glyphicon glyphicon-menu-down pull-right"></i></a>
    			    <ul id="menu<?= $id?>" class="list-group collapse">
        			    <?php foreach ($items as $sub_name=>$url):?>
        				<li class="list-group-item<?php if($uri==$url):?> __active curr<?php endif;?>">
        				    <a class="ajax-click" href="<?= URL::site($url)?>"><?= $sub_name?></a>
        				</li>
        			    <?php endforeach;?>
    			    </ul>
				</li>
			    <?php endforeach;?>
			</ul>
		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="content">
		    <?= $content?>
	    </div>
	</div>
</div>

<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<script>
$(function(){
	$('#accordion').find('.curr').parent('ul').prev().click();
	var currentState = {
        url: document.location.href,
        title: document.title,
        html: $("#content").html()
    };
	$(document).on('click', '.ajax-click, .pagination>li>a', function(){
		var t = $(this);
		var url = t.attr('href');
		if (url.split('#')[0].length) {
    		$.get(url, function(res) {
    			$('#content').html(res);
    			var state = {
                    url: url,
                    title: document.title,
                    html: res
                };
                history.pushState(state,null,url);
    		});
		}
		return false;
	});
    window.addEventListener("popstate",function(event) {
        if(event && event.state) {
            console.log('111111');
            document.title = event.state.title;
            $("#content").html(event.state.html);
        } else{
            console.log('222222');
            document.title = currentState.title;
            $("#content").html(currentState.html);
        }
    });
	$(document).on('click', '.ajax-modal, .ajax-modal-sm, .ajax-modal-lg', function(){
		var t = $(this);
	    var m = $('#myModal');
		if (t.hasClass('ajax-modal-sm')) {
			m.find('.modal-dialog').attr('class', 'modal-dialog modal-sm');
		} else if (t.hasClass('ajax-modal-lg')) {
			m.find('.modal-dialog').attr('class', 'modal-dialog modal-lg');
		} else {
			m.find('.modal-dialog').attr('class', 'modal-dialog');
		}
		var url = t.attr('href');
		if (url.split('#')[0].length) {
    		$.get(url, function(res) {
    			m.find('.modal-body').html(res);
    			m.modal('show');
    			var state = {
                    url: url,
                    title: document.title,
                    html: res
                };
                history.pushState(state,null,url);
    		});
		}
		return false;
	});
	$('#myModal').on('show.bs.modal', function (e) {
		var modal = $(this);
		var page_title = modal.find('.page-header');
        modal.find('.modal-title').html(page_title.text());
        page_title.hide();
        modal.find('form').attr('class', '');
	});
	$('#myModal').on('hidden.bs.modal', function (e) {
	    history.back();
	});
});
</script>

</body>
</html>
