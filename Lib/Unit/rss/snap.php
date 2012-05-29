
<?php
    require_once dirname(__FILE__) . '/rss_parser.php';

    /**
     * Snap{ 
     * 解析RSS/XML源。
     * @package 
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class Snap{

        private $username;
        private $service;
        public function __construct( $options = null ){
            if( is_array( $options ) ){
                $this->setOptions( $options );
            }
        }

        /**
         * setUsername 
         * 设置名字
         * @param string $name 
         * @access public
         * @return void
         */
        public function setUsername( $name ){
            if( !is_string( $name ) ){
                $this->error( '无效的URL格式' );
            }
            $this->username = $name;
        }

        /**
         * setService
         * 设置服务名
         * @param mixed $name 
         * @access public
         * @return void
         */
        public function setService( $name ){
            if( !is_string( $name ) ){
                throw new Exception( "用户名称必须为字符串" );
            }
            $this->service = $name;
        }
        /**
         * setOptions 
         * 设置私有属性
         * @param array $options 
         * @access public
         * @return void
         */
        public function setOptions(array $options ){
            $options = array_change_key_case( $options,CASE_LOWER );

            //循环设置属性
            foreach( $options as $key=>$value ){
                $action = ucfirst( $key );
                $method = "set".$action;
                $this->$method( $value );
            }

        }

        /**
         * parserContent 
         * 解析内容
         * @access private
         * @return void
         */
        private function parserContent(){
            switch( $this->service ){
                case "163" :
                    $url = $this->username.".blog.163.com";
                    $getstr = "/rss/";
                    $content = self::_readContent($url,$getstr);
                    if (strpos($content, '<item') === false) {
                        return false;
                    }
                    $content = iconv('GBK', 'UTF-8', $content);
                    $content = str_replace('<?xml version="1.0" encoding="GBK" ?>', '<?xml version="1.0" encoding="UTF-8" ?>', $content);
                    break;
                case "baidu":
                    $url     = "hi.baidu.com";
                    $getstr  = "/".$this->username."/rss/";
                    $content = self::_readContent($url,$getstr);

                    if (strpos($content, '<item') === false) {
                        return false;
                    }
                    $content = iconv('GBK', 'UTF-8', $content);
                    $content = str_replace('<?xml version="1.0" encoding="gb2312"?>', '<?xml version="1.0" encoding="UTF-8" ?>', $content);
                    break;
                case "msn":
                    $url     = $this->username.".spaces.live.com";
                    $getstr  = "/feed.rss";
                    $content = self::_readContent($url,$getstr);
                    if ($content === false) {
                        return false;
                    }
                    break;
                case "sohu":
                    $url = $this->username.".blog.sohu.com";
                    $getstr = "/rss";
                    $content = self::_readContent($url,$getstr);
                    if (strpos($content, '<html') !== false) {
                        return false;
                    }
                    break;
                case "sina":
                    $url     = "blog.sina.com.cn";
                    $getstr  = "/rss/".$this->username.".xml";
                    $content = self::_readContent($url,$getstr);
                    if ($content === false || $content === '') {
                        return false;
                    }
                    break;
                default:
                    return false;
            }
            return $content;
        }

        /**
         * update 
         * 解析RSS页面,输出是UTF-8格式
         * @access public
         * @return void
         */
        public function update(){
            $err = error_reporting();
            error_reporting(0);
            $content = $this->parserContent();
            $rss = false == $content ? false : new MagpieRSS($content, 'UTF-8', 'UTF-8', false);
            error_reporting($err);
            return $rss;
        }

        /**
         * _readContent 
         * 读取内容
         * @static
         * @access private
         * @return void
         */
        private static function _readContent( $url,$getstr ){
            $url2 = "";
            $fp = fsockopen( $url,80,$errno,$errstr,30 );
            if( !$fp ){
                echo "错误:".$errno."-".$errstr."<br />\n";
            }else{
                //发送http head请求rss页面
                $out = "GET ".$getstr." HTTP/1.0\r\n";
                $out .= "Host: ".$url."\r\n";
                $out .= "Content-Type: text/xml; charset=utf-8\r\n";
                $out .= "Connection: Close\r\n\r\n";

                fwrite($fp, $out);
                while (!feof($fp)){
                    $temp = fgets( $fp,1024 );
                    $data.= $temp;  //fgets为 逐行读取,后面的1024为一行最多返回多少字节的数据,可以不指定默认为1K,也就是1024字节.
                }
            }

            //去除http头信息
            preg_match("/\r\n\r\n(.+)/is", trim($data), $out);
            $str = trim($out[1]);
            //如果为空，就是重定向
            if( empty( $str) ){
                //分解出Location后面的真时地址
                $arr1 = explode("Location: ",trim( $data ));
                $arr2 = explode("\n",$arr1[1]); 
                $urlarr = explode( "/",str_replace( "http://","",$arr2[0] ) );
                $url = $urlarr[0];
                $getstr = trim(str_replace("http://","",str_replace( $urlarr[0],'',$arr2[0] )));
                //重新读取xml以提供解析
                $str = self::_readContent( $url,$getstr );
                return $str;
            }
            return $str;
        }
    }
?>

