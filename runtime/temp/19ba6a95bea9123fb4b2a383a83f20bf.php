<?php /*a:2:{s:94:"/Users/digitalsystem/SourceCode/collpay/paydemo-php/collpay-demo-php/view/index/payResult.html";i:1612404256;s:30:"../view/index/head_import.html";i:1612380975;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <!--告诉浏览器准备接受一个 HTML 文档-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--默认编码UTF-8-->
<meta charset="UTF-8">
<!--默认采用webkit模式-->
<meta name="renderer" content="webkit" />
<!--IE=edge告诉IE使用最新的引擎渲染网页，chrome=1则可以激活Chrome Frame-->
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
<!--(设置确保适当的绘制和触屏缩放)-->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--是否启用 WebApp 全屏模式，删除苹果默认的工具栏和菜单栏-->
<meta name="apple-mobile-web-app-capable" content="yes">
<!--控制状态栏显示样式-->
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<!--手机号码不被显示为拨号链接-->
<meta name="format-detection" content="telephone=yes" />
<!--Email不被显示为发送Email链接-->
<meta name="format-detection" content="email=no">
<!--关键字-->
<meta name="keywords" content="" />
<!--描述信息-->
<meta name="description" content="" />
<!--作者-->
<meta name="author" content="lgsp_Harold-Hua">
<title>Pay</title>
<!--重置部分默认样式-->
<link rel="stylesheet" type="text/css" href="/static/css/normalize.css" />
<!--bootstrap框架样式-->
<link rel="stylesheet" type="text/css" href="/static/css/bootstrap.min.css">
<!--公共样式-->
<link rel="stylesheet" type="text/css" href="/static/css/global.css" />
<!--私有样式-->
<!--公共js-->
<script src="/static/js/jquery-3.3.1.min.js"></script>
<script src="/static/js/bootstrap.min.js"></script>
<!--私有js-->
<style type="text/css">
    .bg-white {
        background: #FFF;
    }

    .bg-gray {
        background: #EFEFEF;
    }

    .interval {
        height: 4px;
    }
    /*.container-fluid {
        margin-top: 15px;
        padding-bottom: 15px;
    }*/

    .wrap_money {
        margin-top: 15px;
        margin-bottom: 15px;
        padding: 20px 15px 20px 10px;
        border-radius: 3px;
    }

    .wrap_money>span {
        float: left;
        line-height: 50px;
        font-size: 15px
    }

    .wrap_money input {
        padding-left: 20px;
        text-align: right;
        width: 90%;
        border: none;
        height: 50px;
        line-height: 50px;
        font-size: 28px;
        background-color: #EFEFEF;
    }

    .wrap_payment label {
        padding-top: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid #EFEFEF;
    }

    .wrap_payment label:last-child {
        border-bottom: none;
    }

    .choose {
        position: relative;
    }

    .choose .radio {
        position: relative;
        margin: 0;
        display: block;
        font-weight: 400;
        padding-right: 25px;
        cursor: pointer;
        font-size: 13px;
    }

    .choose .radio input {
        position: absolute;
        left: -9999px;
    }

    .choose .radio i {
        display: block;
        position: absolute;
        top: 10px;
        right: 0;
        width: 20px;
        height: 20px;
        outline: 0;
        border: 1px solid #e4e4e4;
        background: #ffffff;
        border-radius: 50%;
        transition: border-color .3s;
        -webkit-transition: border-color .3s;
    }

    .choose .radio input:checked+i {
        border: 1px solid #dd6572;
    }

    .choose .radio input+i:after {
        position: absolute;
        content: '';
        top: 4px;
        left: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #dd6572;
        opacity: 0;
        transition: opacity .1s;
        -webkit-transition: opacity .1s;
    }

    .choose .radio input:checked+i:after {
        opacity: 1;
    }

    .wrap_remarks {
        padding: 10px 0;
        position: relative;
    }

    .wrap_remarks span {
        position: absolute;
        height: 30px;
        line-height: 30px;
    }

    .wrap_remarks input {
        display: block;
        width: 100%;
        padding-left: 40px;
        height: 30px;
        line-height: 30px;
        font-size: 12px;
        border: none;
        text-align: right;
    }

    .wrap_button {
        margin-top: 120px;
    }

    .wrap_button input {
        width: 100%;
        padding: 9px 0;
        border: none;
        color: #FFF;
        text-align: center;
        background-color: #dd6572;
        border-radius: 3px;
    }
</style>
</head>


<body>
<header>

</header>

<section>
    <?php if($order): ?>
    <div class="container-fluid text-center">
        <div class="loading_wrap">
            <div ng-app="ionicApp" id="spinnerDiv">
                <p>
                    <!--居中显示-->
                    <ion-spinner icon="ios" style="stroke:#ea4747;fill:#de2e2ecc;"></ion-spinner>
                    <!--bubbles加载动画的样式-->
                </p>
                <marquee class="text center-block" behavior="alternate" direction="right" scrollamount="3">等待确认支付结果</marquee>
            </div>
        </div>

        <div class="success">
            <img class="img-responsive center-block" src="/static/img/icon/successful.png" />
            <h2>支付成功</h2>
            <p class="text">您已支付了&nbsp;<span >￥<?php echo htmlentities($order['price']/100); ?></span>&nbsp;
                <a  href="detail?orderNo=<?php echo htmlentities($order['orderNo']); ?>|">查看订单</a>
            </p>
        </div>

        <div class="fail">
            <img class="img-responsive center-block" src="/static/img/icon/fail.png" />
            <h2>支付失败</h2>
            <p class="text">您未能成功支付
                <a href="/">重新支付</a>
            </p>
        </div>
        <input type="hidden" id="status" value="<?php echo htmlentities($order['status']); ?>" />
        <input type="hidden" id="orderId" value="<?php echo htmlentities($order['id']); ?>" />
    </div>

    <?php else: ?>

    <?php endif; ?>
</section>

<footer>

</footer>

<script type="text/javascript">

    $(document).ready(function () {
        var status = $('#status').val();
        var orderId = $('#orderId').val();
        showStatus(status);
        setInterval(function(){
            if(status=='PENDING'){
                $.post({
                    type:'post',
                    data:{orderId:orderId},
                    url:'ajaxStatus',
                    success:function (val) {
                        status = val;
                        showStatus(status);
                    }
                })
            }
        }, 1000);
    });

    function showStatus(stausVal) {
        $('.loading_wrap').css('display', 'none');
        $('.success').css('display', 'none');
        $('.fail').css('display', 'none');
        if(stausVal=='COMPLETE'){
            $('.success').css('display', 'block');
        }else if(stausVal=='FAIL'){
            $('.fail').css('display', 'block');
        }else {
            $('.loading_wrap').css('display', 'block');
        }
    }

</script>
</body>

</html>
