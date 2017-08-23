<?php
/**
 * 单条微博格式化
 * 
 */
$user = $arrWeibo['user'];
$text = $arrWeibo['text'];

//用户昵称
$nick = $user['nickname'];

//用户头像
$userAvatar = $user['photo'];

//用户地址
$userHome = 'http://t.cntv.cn/' . $user['userseqid'];

//微博链接
$link = 'http://t.cntv.cn/show' . $arrWeibo['id'];

// 上传了图片，图片地址
$thumbnail_pic = 'http://img.t.cntv.cn/thumbnail/9276d53ferv61wrljh12z97l';

// xxx前发表
// $format_time = F('format_time', $created_at);


$html = <<<EOF
  <div class="user-pic">
    <a target="_blank" href="${userHome}"><img width="50" height="50" src="${user['photo']}" alt="${user['nickname']}" title="${user['nickname']}"></a>
  </div>
  <div class="feed-content">
    <p class="feed-main">
       <a target="_blank" href="${userHome}" title="${user['nickname']}">${user['nickname']}</a>：${text}</p>
          <div class="preview-img">
            <div class="feed-img">
                <img class="zoom-move" src="${thumbnail_pic}" rel="e:zi,fw:0">
            </div>
        <div class="feed-img"><img width="120px" src="http://p5.img.cctvpic.com/fmspic/2012/05/31/1df2e2830dbd491abe1a6ca06873a714-180.jpg" alt=""><div class="video-view all-bg" rel="e:pv,i:zOgNqKo"></div></div></div>
            <div class="feed-info"><p>
    <a href="#" rel="e:dl">删除</a>|<a href="#" rel="e:fw" id="fw">转发</a>|<a href="#" rel="e:fr">收藏</a>|<a href="javascript:;" onclick="$('#inputor').focus();" rel="e:cm" id="cm">评论</a>
    </p><span><a href="http://t.cntv.cn/show/30105782">6月3日 16:15</a> 来自 <a href="http://t.cntv.cn" target="_blank" title="CNTV微博">CNTV微博</a></span>

    </div>
  </div>
EOF;

return array(
    'html'  => $html,
);
