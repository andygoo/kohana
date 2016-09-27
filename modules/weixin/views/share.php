<!DOCTYPE html>
<html lang="zh-cn" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>微信公众平台</title>
<?php echo HTML::script('media/js/jquery.min.js')?>
</head>
<body>

<button id="chooseImageBtn">选择图片</button>
<button id="uploadImageBtn">上传</button>
<div id="pics"></div>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
var jsapi_config = <?php echo $jsapi_config;?>;
jsapi_config.jsApiList.push('onMenuShareTimeline','onMenuShareAppMessage','chooseImage','previewImage','uploadImage','downloadImage');
wx.config(jsapi_config);

var share_appmsg_config = {
    title: '拒绝炸成灰糖果礼物一大堆！', // 分享标题
    desc: '一起来抢糖果啦！我在炸弹堆里抢到了糖果，获得了丰厚的奖品~你也一起来玩儿吧!', // 分享描述
    link: '/activity/halloween/index', // 分享链接
    imgUrl: '/activity/halloween/img/share.jpg', // 分享图标
    type: 'link', // 分享类型,music、video或link，不填默认为link
    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
    success: function () {
        alert('success');
    },
    cancel: function () {
        alert('cancel');
    }
}
var share_timeline_config = {
    title: '一起来抢糖果啦！我在好车无忧炸弹堆里抢到了糖果，获得了丰厚的奖品~你也一起来玩儿吧!', // 分享标题
    link: 'activity/halloween/index', // 分享链接
    imgUrl: 'activity/halloween/img/share.jpg', // 分享图标
    success: function () {
        alert('success');
    },
    cancel: function () {
        alert('cancel');
    }
}
wx.ready(function () {
    wx.onMenuShareAppMessage(share_appmsg_config);
    wx.onMenuShareTimeline(share_timeline_config);

    $('#chooseImageBtn').click(function () {
    	wx.chooseImage({
    	    count: 6, // 默认9
    	    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
    	    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
    	    success: function (res) {
    	        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
    	        for (var i in localIds) {
    	        	$('#pics').append('<img width="100" src="'+localIds[i]+'">');
    	        }
    	    }
    	});
    });
    $(document).on('click', '#pics img', function() {
        var inx = $(this).index();
        var localIds = [];
        $('#pics img').each(function(i, o) {
        	localIds.push($(o).attr('src'));
        });
        wx.previewImage({
            current: localIds[inx], // 当前显示图片的http链接
            urls: localIds // 需要预览的图片http链接列表
        });
    });
    $('#uploadImageBtn').click(function () {
        var localIds = [];// 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
        $('#pics img').each(function(i, o) {
        	localIds.push($(o).attr('src'));
        });
        for (var i in localIds) {
	        wx.uploadImage({
	            localId: localIds[i], // 需要上传的图片的本地ID，由chooseImage接口获得
	            isShowProgressTips: 1, // 默认为1，显示进度提示
	            success: function (res) {
	                var serverId = res.serverId; // 返回图片的服务器端ID
	                //$('#pics').append(serverId+'<br>');
	                $.get('<?php echo URL::site('weixin/down_image?media_id=')?>'+serverId, function(res) {
	    	        	$('#pics').append(res);
		            });
	            }
	        });
        }
    });
    /*
    $('#downloadImageBtn').click(function () {
        wx.downloadImage({
            serverId: '', // 需要下载的图片的服务器端ID，由uploadImage接口获得
            isShowProgressTips: 1, // 默认为1，显示进度提示
            success: function (res) {
                var localId = res.localId; // 返回图片下载后的本地ID
            }
        });
    });*/
});
</script>
</body>
</html>
