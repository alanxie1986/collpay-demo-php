<?php
namespace app\controller;

use app\BaseController;
use app\model\Order;
use think\Db;
use think\Exception;
use think\facade\View;

//二维码
use Endroid\QrCode\QrCode as EndroidQrCode;


class Index extends BaseController
{
    //newpay24平台分配给商家的用户号
    private  const code = 'fed7f0a391794dc5ab451342e75d532a';

    //newpay24平台分配给商家的秘钥
    private const key = '08cb49637c46458d9a3e538b81fea8fa';

    //网页方式支付成功后回跳地址
    private const returnUrl = 'https://demojava.newpay24.com/paySuccess';
  //  private const returnUrl = 'http://192.168.100.156:9880/paySuccess';

    //异步通知支付结果地址(支付成功后newpay24平台服务器会通知这个地址)
    private const backendUrl = 'https://demojava.newpay24.com/asynCallback';
//    private const backendUrl = 'http://192.168.100.156:9880/asynCallback';

    //newpay24平台 代收下单接口接口地址
    private const apiUrl = 'https://api.newpay24.com/collpay';
//    private const apiUrl = 'http://192.168.100.156:8080/backend/api/collpay';

    /**
     * 打开首页
     */
    public function index()
    {
        return  View::fetch();
    }

    /**
     * 提交支付
     */
    public function payment()
    {
        $price = $this->request->param('price');
        $payType = $this->request->param('payType');
        $price = intval($price * 100);

        $order = $this->createOrder($price, $payType);
        $apiResult = self::requstApi($order);
        View::assign('order',$order);
        View::assign('resultCode',$apiResult['server_code']);
        View::assign('resultMsg',$apiResult['message']);
        if ($apiResult['server_code']=='SUCCESS') {
            $order->save();
            View::assign('payData',$apiResult['pay_data']);

        }

        switch ($payType) {
            case '1002': //微信h5
            case '1004': //支付宝h5
                return View::fetch('jumpToPay');
            case '1001': //微信二维码
            case '1003': //支付宝二维码
            case '1007': //微信个人码
            case '1008': //支付宝个人码
                return View::fetch('jumpToQrCode');
        }
    }

    /**
     * 生成二维码
     * @param $qrCode
     */
    public function qrCode($qrCode){
        $QrModel = new EndroidQrCode();
        $QrModel->setText($qrCode); //设置二维码上的内容
        header('Content-Type: '.$QrModel->getContentType());
        echo $QrModel->writeString();
    }


    /**
     * newpay24平台支付结果异步通知(支付结果以该接口为准)
     */
    public function asynCallback()
    {
        $callbackParam = $this->request->getContent();
        if ($callbackParam) {
            $callbackData = json_decode($callbackParam, true);
            //验签
            $sign = $callbackData['sign'];
            unset($callbackData['sign']);
            $signOfValidate = self::makeSign($callbackData);
            if ($sign != $signOfValidate) {
                return 'FAIL';
            }

            $orderNo = $callbackData['merchant_no'];
            //用订单找出order
            $order = Order::where('order_no', $orderNo)->find();
            $status = $callbackData['trade_status'];
            if ($order) {
                //更新支付状态
                $order['status'] = $status;
                $order['end_time'] = date('Y-m-d H:i:s');
                $order->save();
            }
            return 'SUCCESS';
        }
        return 'FAIL';
    }


    /**
     * 支付完成后跳回这个支付支付结果显示页
     */
    public function paySuccess(){
        $orderNo =  $this->request->param('merchant_no');
        $order = null;
        if($orderNo){
            $order =  Order::where('order_no', $orderNo)->find();
        }
        View::assign('order',$order);
        return View::fetch('payResult');
    }

    /**
     * 如果用户停留在支付结果页刷新结果
     */
    public function ajaxStatus(){
        $orderId =  $this->request->param('orderId');
        $order = Order::find($orderId);
        return $order['status'];
    }

    //===============================================构造订单代码段=====================================================================

    //构造订单实体记录
    private function createOrder($price,$payType){
        $order = new Order();
        $order['commodity_name'] = '游戏点卡';
        $order['pay_type'] = $payType;
        $randomStr = self::randomkeys(8);
        $milliSecond = self::getMillisecond();
        $order['order_no'] = 'DEMO'.$randomStr.$milliSecond;
        $order['newpay_no'] = '';
        $order['status'] = 'PENDING';
        $order['create_time'] = date('Y-m-d H:i:s');
        $order['price'] = $price;
        return $order;
    }

    //随机数
    private static function randomkeys($length)
    {
        $pattern = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $pattern{mt_rand(0, 35)};    //生成php随机数
        }
        return $key;
    }

    //获取毫秒级别的时间戳
    private static function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }

    //=======================================调用接口代码段=============================================================================
    /**
     * 请求NEWPAY24平台接口
     * @param $order
     */
    private static function requstApi($order)
    {
        $apiParam = self::constructApiParam($order);
        $jsonPost = json_encode($apiParam);
        return self::postJsonCurl($jsonPost,self::apiUrl, 20 );
    }

    /**
     * @param $json 请求报文
     * @param $url  请求地址
     * @param int $second  超时秒数
     * @return bool|mixed  如果成功返回一个数组 失败返回一个false
     */
    private static function postJsonCurl($json, $url, $second=5 )
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curl, CURLOPT_TIMEOUT, $second);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 200 ) {
            return ['server_code'=>'FAIL','message'=>'请求失败'];
        }
        curl_close($curl);
        return json_decode($json_response, true);
    }


    //构造接口请求参数
    private static function constructApiParam($order){
        $apiParam['code'] = self::code;
        $apiParam['amount'] = $order['price'];
        $apiParam['commodity_name'] = $order['commodity_name'];
        $apiParam['merchant_no'] = $order['order_no'];
        $apiParam['pay_type'] = $order['pay_type'];
        $apiParam['return_url'] = self::returnUrl;
        $apiParam['back_end_url'] = self::backendUrl;
        $apiParam['nonce_str'] = self::randomkeys(20);
        $sign = self::makeSign($apiParam);
        $apiParam['sign'] = $sign;
        return $apiParam;
    }

    //****生成签名****
    private static function makeSign($values)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = self::toUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".self::key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    private static function toUrlParams($values)
    {
        $buff = "";
        foreach ($values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    //========================================================================================================================

}
