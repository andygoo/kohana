<!DOCTYPE html>
<html lang="zh-cn" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>微信公众平台</title>
</head>
<body>

<button id="tt">tttttt</button>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
var jsapi_config = <?php echo $jsapi_config;?>;
jsapi_config.jsApiList.push('onMenuShareTimeline','onMenuShareAppMessage','chooseImage');
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

    document.querySelector('#tt').onclick = function () {
    	wx.chooseImage({
    	    count: 2, // 默认9
    	    sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
    	    sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
    	    success: function (res) {
    	        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
    	    }
    	});
    }
});
</script>
</body>
</html>
