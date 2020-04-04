<?php
// +----------------------------------------------------------------------
// | 应用公共文件
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

use app\common\model\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use think\addons\Service;
use think\facade\App;
use think\facade\Cache;
use \think\facade\Db;

error_reporting(0);


/**
 * @return mixed
 * 获取用户更信息
 */
if (!function_exists('getUserById')) {

    function getUserById($id)
    {
        return Db::name('user')->find($id);
    }
}

if (!function_exists('getRegionById')) {

    function getRegionById($id)
    {
        return Db::name('region')->find($id);
    }
}
/**
 * @return mixed
 * 获取站点信息
 */
if (!function_exists('site_name')) {

    function site_name()
    {
        return Db::name('config')->where('code', 'site_name')
            ->value('value');
    }
}

if (!function_exists('site_logo')) {

    function site_logo()
    {
        return Db::name('config')->where('code', 'site_logo')
            ->value('value');
    }
}
//获取配置信息
if (!function_exists('getConfigByCode')) {

    function getConfigByCode($code)
    {
        return Db::name('config')->where('code', $code)
            ->value('value');

    }
}
/*
 * 百度编辑器内容
 */
if (!function_exists('build_ueditor')) {
    function build_ueditor($params = array())
    {
        $name = isset($params['name']) ? $params['name'] : null;
        $theme = isset($params['theme']) ? $params['theme'] : 'normal';
        $content = isset($params['content']) ? $params['content'] : null;
        //http://fex.baidu.com/ueditor/#start-toolbar
        /* 指定使用哪种主题 */
        $themes = array(
            'normal' => "[   
           'fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'gmap', 'insertframe', 'insertcode', 'webapp', 'pagebreak', 'template', 'background', '|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'print', 'preview', 'searchreplace', 'drafts', 'help'
       ]", 'simple' => " ['fullscreen', 'source', 'undo', 'redo', 'bold']",
        );
        switch ($theme) {
            case 'simple':
                $theme_config = $themes['simple'];
                break;
            case 'normal':
                $theme_config = $themes['normal'];
                break;
            default:
                $theme_config = $themes['normal'];
                break;
        }
        /* 配置界面语言 */
        switch (config('default_lang')) {
            case 'zh-cn':
                $lang = '/static/plugins/ueditor/lang/zh-cn/zh-cn.js';
                break;
            case 'en-us':
                $lang = '/static/plugins/ueditor/lang/en/en.js';
                break;
            default:
                $lang = '/static/plugins/ueditor/lang/zh-cn/zh-cn.js';
                break;
        }
        $include_js = '<script type="text/javascript" charset="utf-8" src="/static/plugins/ueditor/ueditor.config.js"></script> <script type="text/javascript" charset="utf-8" src="/static/plugins/ueditor/ueditor.all.min.js""> </script><script type="text/javascript" charset="utf-8" src="' . $lang . '"></script>';
        $content = json_encode($content);
        $str = <<<EOT
$include_js
<script type="text/javascript">
var ue = UE.getEditor('{$name}',{
    toolbars:[{$theme_config}],
        });
    if($content){
ue.ready(function() {
       this.setContent($content);	
})
   }
      
</script>
EOT;
        return $str;
    }
}

if (!function_exists('p')) {
    function p($var, $die = 0)
    {
        print_r($var);
        $die && die();
    }
}
if (!function_exists('isMobile')) {
    function isMobile()
    {
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        if (isset ($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}

//是否https;

if (!function_exists('is_https')) {
    function is_https()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        }
        return false;
    }


}

/**
 * 获取http类型
 */
if (!function_exists('get_http_type')) {
    function get_http_type()
    {
        return $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

    }
}
/**
 * 从前日期
 */

if (!function_exists('timeAgo')) {

    function timeAgo($posttime)
    {
        //当前时间的时间戳
        $nowtimes = strtotime(date('Y-m-d H:i:s'), time());
        //之前时间参数的时间戳
        $posttimes = strtotime($posttime);
        //相差时间戳
        $counttime = $nowtimes - $posttimes;

        //进行时间转换
        if ($counttime <= 10) {

            return '刚刚';

        } else if ($counttime > 10 && $counttime <= 30) {

            return '刚才';

        } else if ($counttime > 30 && $counttime <= 60) {

            return '刚一会';

        } else if ($counttime > 60 && $counttime <= 120) {

            return '1分钟前';

        } else if ($counttime > 120 && $counttime <= 180) {

            return '2分钟前';

        } else if ($counttime > 180 && $counttime < 3600) {

            return intval(($counttime / 60)) . '分钟前';

        } else if ($counttime >= 3600 && $counttime < 3600 * 24) {

            return intval(($counttime / 3600)) . '小时前';

        } else if ($counttime >= 3600 * 24 && $counttime < 3600 * 24 * 2) {

            return '昨天';

        } else if ($counttime >= 3600 * 24 * 2 && $counttime < 3600 * 24 * 3) {

            return '前天';

        } else if ($counttime >= 3600 * 24 * 3 && $counttime <= 3600 * 24 * 20) {

            return intval(($counttime / (3600 * 24))) . '天前';

        } else {

            return $posttime;

        }
    }


    /***
     *
     * 通过子类型和商户id取得需要的账号
     * 根据账号的主类型获取对应的支付通道
     * 获取支付通道的费率 api
     *
     * 返回支付需要信息 通过api反射获取通道对应文件 进行支付数据处理
     *
     *
     *
     *
     *
     */
    if (!function_exists('getPayAccount')) {
        function getPayAccount($order)
        {
            Db::startTrans();
            try {
                //  $ip = new \Ip2Region();
                //$result = $ip->binarySearch($order['ip']);

                //$arr = explode('|', isset($result['region']) ? $result['region'] : '');
//                $province_id = 0;
//                if ($arr != null && count($arr) > 2) {
//                    $province = db('area')->whereLike('name', "%{$arr[2]}%")->find();
//                    if ($province != null && isset($province['id'])) {
//                        $province_id = $province['id'];
//                    }
//                }

                //    $amount= Db::name('PayAmount')->where(['amount'=>$order['tradeMoney']])->find();


                //  $subType = $order['subtype'];
                $map = array();
                //   $map['area'] = array(['like', "%{$province_id},%"], ['like', "%,{$province_id}%"], ['=', $province_id], ['=', '0'], 'or');
                //$map['amount'] = array(['like', "%{$amount['id']},%"], ['like', "%,{$amount['id']}%"], ['=', $amount['id']], ['=', '0'], 'or');
                //      $map['a.max'] = array(['>=', $order['tradeMoney']], ['=', '0'], 'or');
                //   $map['user.gr'] = array(['like', "%38%"], ['=', '0'], 'or');
                //   $map['a.subtype'] = array(['like', '%' . $subType . '%'], ['=', '0'], 'or');

//                $user = db('user')
//                    ->where(array('id' => $order['uId'], 'status' => 'normal', 'is_deleted' => 0))
//                    ->find();

                //   var_dump($user);




                $skuId = $order['skuId'];
                $sku = \app\common\model\GoodsSku::getOne($skuId);//规格

                $tradeMoney = $order['orderMoney'];

//            var_dump($sku['price']);
                $adminId = $sku['adminId'];

                $autoacc = Db::name('PayAccount')->alias('a')
                    ->join('order_pay queue',
                        'queue.aid = a.id and  queue.mTime>UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE))',
                        'left')
                    ->join('admin admin2', 'a.adminId=admin2.id', 'left')
                    ->join('admin admin1', 'admin1.id=admin2.pid', 'left')
                    ->join('admin merchant', 'merchant.pid=admin1.pid', 'left')
                    ->join('bank bank', 'a.bankId=bank.id', 'left')
                    ->join('pay_channel channel',
                        'channel.id=a.channelId or channel.pid=a.channelId',
                        'inner')
                    ->join('pay_channel supChannel',
                        'channel.pid=supChannel.id ',
                        'left')
                    ->where(array(
                            'channel.status' => 1,
                            'a.status' => 1,
                            'a.is_deleted' => 0,

                        )
                    )
                    ->where('a.userId', 'not null')
                    ->where('channel.id|channel.pid|supChannel.id|supChannel.pid', '=', $order['type'])
                    ->where('a.preMoney', '>=', $tradeMoney)
                    ->where('merchant.id', '=', $sku['adminId'])
                    ->where(['a.status' => 1])
                    ->where("(`channel`.`isSocket` = 1 AND `a`.`socket_id` is not null) or `channel`.`isSocket` = 0")
                    ->where($map)
                    ->where('a.min', '<=', $tradeMoney)
                    ->orderRaw('a.mTime asc,a.useTime/a.weight asc')->
                    field(array(
                        'channel.strategy' => 'strategy',
                        'channel.param' => 'param',
                        'a.id',
                        'a.adminId',

                        'a.extra',
                        'a.token',
                        'a.preMoney',
                        'bank.name' => 'bankName',
                        'bank.code' => 'bankCode',

                        'channel.api',
                        'channel.api2',
                        'channel.id' => 'channelId',
                        'a.dayMax',
                        'a.bankNum',
                        'a.userName',
                        'a.realNamed',
                        'a.useTime',
                        'a.userId',
                        'a.userTime',
                        'a.idCard',
                        'channel.canGo',
                        "sum(if(queue.isPay='1'  and queue.cTime>UNIX_TIMESTAMP(CAST(SYSDATE()AS DATE)) ,queue.tradeMoney,0))" => 'dayMoney',
                        "sum(if(queue.mTime>unix_timestamp()-unit*60 ,1,0))" => 'noOrder',
                        //    "sum(if(queue.payip='" . $order['ip'] . "'  and queue.cTime>unix_timestamp()-unit*60 ,1,0))" => 'person',

                    ))
                    ->group('a.id,a.dayMax,a.useTime,a.adminId,a.username,a.unitTime,strategy,channel.api,channel.param,channelId,bankName,bankCode')
                    ->having('noOrder <  a.unitTime and (dayMoney<=a.dayMax or a.dayMax=0)');
                $autoacc->find();
//echo ($autoacc->getLastSql());
                $autoacc = $autoacc->find();

                if ($autoacc == null) {
                    return null;
                }
                \app\common\model\PayAccount::update(['mTime' => time(),
                    'preMoney' => $autoacc['preMoney'] - $tradeMoney,
                    'useTime' => $autoacc['useTime'] + 1],
                    array('id' => $autoacc['id']));


                //MoneyLog::create(['user_id' => $autoacc['uid'], 'uid' => $autoacc['uid'], 'linkId' => $order['id'], 'money' => $user['preMoney'], 'before' => $user['preMoney'], 'after' => 0, 'memo' => $order['id'] . '使用']);


                // $queue['payip'] = $order['ip'];
                $queue['tradeMoney'] = $tradeMoney;
                $queue['adminId'] = $autoacc['adminId'];
                $queue['channelId'] = $autoacc['channelId'];
                $queue['aid'] = $autoacc['id'];
                $queue['userId'] = $autoacc['userId'];
                $queue['toUserId'] = $autoacc['userId'];
                $queue['status'] = 1;
                $queue['cTime'] = time();
                $queue['strategy']=$autoacc['strategy'];
                $queue['orderId'] = $order['id'];
                $queue['mark'] = 'L' . date("YmdHis", time()) . $order['id'];
                $queue['mTime'] = time();
                $queue['show'] = $sku['title'];
                $queue['canGo'] = $autoacc['canGo'];
                //   $queue['subtype'] = $order['subtype'];
                $ret = Db::name('order_pay')->insertGetId($queue);

                Db::name('order_pay')->where(['id'=>$ret])->update(['mark'=>'L' . date("YmdHis", time()) . $ret]);


                Db::name('order')->where(['id' => $queue['orderId']])
                    ->update(['channelId' => $autoacc['channelId'],
                        'tradeMoney' => $tradeMoney]);
                Db::name('order_pay')->where(['id' => $ret])
                    ->update(['strategy' => $autoacc['strategy'],
                        'tradeMoney' => $tradeMoney]);
                Db::commit();
                $queue['id'] = $ret;

                $queue['mark'] = number2chinese($ret);


                $domain = request()->domain();
                $queue['idCard'] = $autoacc['idCard'];
                $queue['domain'] = $domain;
                $queue['info'] = $autoacc['param'];
                $queue['extra'] = $autoacc['extra'];
                $queue['token'] = $autoacc['token'];
                $queue['realNamed'] = $autoacc['realNamed'];
                $queue['api'] = $autoacc['api'];

                $queue['bankNum'] = $autoacc['bankNum'];
                $queue['bankCode'] = $autoacc['bankCode'];
                $queue['bankName'] = $autoacc['bankName'];
                return $queue;
            } catch (\Exception $e) {

                Db::rollback();
                return null;
            }


        }
    }

}

function send_post($url, $param)
{
    $postdata = http_build_query($param);
    //  var_dump($postdata);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                "version:v4.0\r\n",
            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;

}


function send_get($url)
{
//    $postdata = http_build_query($param);
    //  var_dump($postdata);
    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                "version:v4.0\r\n",
//            'content' => $postdata,
            'timeout' => 15 * 60 // 超时时间（单位:s）
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;

}


/**
 * 获得插件列表
 * @return array
 */
if (!function_exists('get_service_list')) {

    function get_service_list()
    {


        $addons_path = app()->getRootPath() . 'app' . DIRECTORY_SEPARATOR . 'pay' .
            DIRECTORY_SEPARATOR . 'service';
        // 如果插件目录不存在则创建
        if (!is_dir($addons_path)) {
            return null;
        }


        $results = scandir($addons_path);

        $list = [];
        $arr = array();
        foreach ($results as $name) {

            if ($name === '.' or $name === '..')
                continue;
            if (is_file($addons_path . $name))
                continue;
            $addon = $addons_path . DIRECTORY_SEPARATOR . $name;

            if (is_dir($addon))
                continue;

            if (!is_file($addon))
                continue;


            $ret = new \ReflectionClass(str_replace(".php", '', "app\pay\service\\" . $name));
            $doc = $ret->getDocComment();


            $item['id'] = str_replace(".php", '', "app\pay\service\\" . $name);

            $item['value'] = replaceother($doc);
            array_push($arr, $item);
            //   var_dump($arr);

            Cache::set('addonslist', $list);
        }
        // }else{
        //    $list = Cache::get('addonslist')  ;
        //}

        return $arr;
    }
}
if (!function_exists('replaceother')) {
    function replaceother($doc)
    {
        $arr = explode("\n", $doc);
        if (sizeof($arr) > 1) {
            return str_replace('*', '', $arr[1]);
        }
        return $doc;
    }
}

function get_java_service_list()
{
    return json_decode(send_get('http://127.0.0.1:521/api'), true);
}

function number2chinese($p)
{
    return date('yyyymi') . $p;
}


function getRand(){
    return md5(uniqid(microtime(true),true));
}

function getConfig(){
    $configs = Config::getList([], 0, []);

    $domain=str_replace('.','_',$_SERVER['HTTP_HOST']);

    $config['domain']=$domain;
    foreach ($configs as $key => $value) {
        if(!empty($config[$domain.$value['code']])&&$config[$domain.$value['code']]){
            $config[$value['code']] =	 $config[$domain.$value['code']] ;
        }else{
            $config[str_replace($domain.'_','',$value['code'])] = $value['value'];
        }



    }
    $config['title']=$config['site_seo_desc'];



    // $config['title']=$config['site_seo_desc'];
    return $config;
}