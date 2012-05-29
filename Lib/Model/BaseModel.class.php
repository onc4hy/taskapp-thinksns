
<?php
    Import( '@.Unit.Common' );
    /**
     * BaseModel 
     * 任务的base类
     *
     * @uses Model
     * @package Model::Mini
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class BaseModel extends Model{
        /**
         * API 
         * API名,可以为common里面的扩展API类
         * @var string
         * @access protected
         */
        protected $api;

        /**
         * config 
         * mini的配置
         * @var mixed
         * @access protected
         */
        protected $config;

        /**
         * write 
         * 写入配置文件的处理类
         * @var mixed
         * @access protected
         */
        protected $write;

        /**
         * _initialize 
         * 进行mini博客的时候进行初始化
         *
         * 获取uid,mid,或者friendsId.
         * @access protected
         * @return void
         */
        protected function _initialize(){
            //$this->api = new TS_API();
        }
        
        /**
         * fileAwayCount 
         * 归档计数，和fileAway获得归档具体内容一样处理。只是是获得记录数
         * @param mixed $findTime 
         * @param mixed $condition 
         * @access public
         * @return void
         */
        public function fileAwayCount( $findTime,$condition ){
                if( is_array( $findTime) ){
                    $start_temp   = $this->paramData( strval($findTime[0] ));
                    $end_temp     = $this->paramData( strval($findTime[1] ));
                                                      
                    $start        = $start_temp[0];
                    $end          = $end_temp[1];
                }else{
                    $findTime  = strval( $findTime );
                    $paramTime = self::paramData( $findTime );
                    $start     = $paramTime[0];
                    $end       = $paramTime[1];
                }

                $this->cTime = array( 'between', array( $start,$end ) );
                //如果查询时没有设置其它查询条件，就只是按时间来进行归档查询
                $map = $this->merge( $condition );
                $result = $this->where( $map )->field( "count(*)" )->findAll();
                return $result;

        }
        /**
         * feed_publish 
         * 发送动态
         * @param mixed $type 
         * @param mixed $title 
         * @param mixed $body 
         * @static
         * @access protected
         * @return void
         */
        public function doFeed($type,$title,$body = null){
            //$appid = A('Index')->getAppId();
            //return api("Feed_put", $appid . "_" . $type, $title,$body);
            //return $this->api->feed_publish( $type,$title,$body,$appid);
        }

        protected  function doNotify($uid,$type,$title,$body,$url){
        	//return api("Notify_put", $appid . "_" . $type, $uid, $title,$body);
            //$this->api->notify_setAppId(A('Index')->getAppId());
            //return $this->api->notify_send( $uid,$type,$title,$body,$url );
        }

        public function getFriends(){
            //return $this->api->friend_get();
        }

        /**
         * checkNull 
         * 检查变量是否为空,暂时只能检查一维数组
         * @param mixed $value 
         * @access public
         * @return void
         */
        protected static function checkNull( $value ){
            if( !isset( $value ) || empty( $value ) ){
                return true;            
            }else{
                return false;
            }
        }
        
        /**
         * paramData 
         * 处理归档查询的时间格式
         * @param string $findTime 200903这样格式的参数
         * @static
         * @access protected
         * @return void
         */
        protected function paramData( $findTime ){
            //处理年份
            $year = $findTime[0].$findTime[1].$findTime[2].$findTime[3];
            //处理月份
            $month_temp = explode( $year,$findTime);
            $month = $month_temp[1];
            //归档查询
            if ( !empty( $month ) ){

                //判断时间.处理结束日期
                switch (true) {
                    case ( in_array( $month,array( 1,3,5,7,8,10,12 ) ) ):
                        $day = 31;
                        break;
                    case ( 2 == $month ):
                        if( 0 != $year % 4 ){
                            $day = 28;
                        }else{
                            $day = 29;
                        }
                        break;
                    default:
                        $day = 30;
                        break;
                }
                //被查询区段开始时期的时间戳
                $start = mktime( 0, 0, 0 ,$month,1,$year  );

                //被查询区段的结束时期时间戳
                $end   = mktime( 24, 0, 0 ,$month,$day,$year  );

                //反之,某一年的归档
            }elseif( isset( $year[4] ) ){
                $start = mktime( 0, 0, 0, 1, 1, $year );
                $end = mktime( 24, 0, 0, 12,31, $year  );
            }else{
                //其它操作
            }

            //fd( array( friendlyDate($start),friendlyDate($end) ) );
            return array( $start,$end );

        }


        /**
         * getOneName 
         * 获得某一个人的姓名
         * @param mixed $uid 
         * @access protected
         * @return void
         */
        public function getOneName( $uid ){
            //return $this->api->user_getInfo($uid,'name');
        }

        /**
         * setConfig 
         * 设置配置控制器
         * @param mixed $model 
         * @access protected
         * @return void
         */
        protected function setConfig( $data ){
            //引入配置管理类
            Import( '@.Unit.Config' );
            //引入配置信息
            //配置管理对象,把配置数组交给配置管理对象处理
            $config = new Config( $data  );
                
            $this->config = $config;
        }

        /**
         * setWrite 
         * 设置配置写入类
         * @param ArrayWrite $write 
         * @access protected
         * @return void
         */
        protected function setWrite( ArrayWrite $write ){
            $this->write = $write;
        }

        /**
         * merge 
         * 合并条件
         * @param mixed $map 
         * @access private
         * @return void
         */
        protected function merge ( $map = null ){
            if( isset( $map ) ){
                $map = array_merge( $this->data,$map );
            }else{
                $map = $this->data;
            }

            return $map;
        }

        public function getApi(  ){
            return $this->api;
        }

        /**
         * replace 
         * 在数据集中替换
         * @param mixed $data 
         * @access private
         * @return void
         */
        protected function replace( $data ){
            $result = $data;

            //修改content
            foreach( $result as &$value ){
                $value['content'] = str_replace('{PUBLIC_URL}',__PUBLIC__,$this->replaceContent( $value['content'] ));
            
            }
            return $result;
        }

        /**
         * replaceContent 
         * 替换内容
         * @param mixed $content 
         * @access private
         * @return void
         */
        protected function replaceContent( $content ){
            $path = '{PUBLIC_URL}/images/biaoqing/mini/';//路径
            

            //循环替换掉文本中所有ubb表情
            foreach( $this->config->ico as $value ){

                $img = sprintf("<img title='%s' src='%s%s'>",$value['title'],$path,$value['filename']);
                $content = str_replace( $value['emotion'],$img,$content );

            }
            return $content;
        }

        protected function replayPath( ){
            $config = $this->config->replay; //回复的配置
        }

        /**
         * getTaskContent 
         * 获得某一条任务的详细页面
         * @param mixed $id 
         * @access public
         * @return void
         */
        public function getTaskContent( $id,$how =null,$uid = null  ){
            $mention  = self::factoryModel( 'mention' );
            $category = self::factoryModel( 'category' );//获取分类的实例对象
            

            isset( $uid ) && $map['uid'] = $uid;

                switch( $how ){
                    case "gt":
                        $map['id']  = array( $how,$id );
                        $order = "ID ASC";
                        break;
                    case "lt":
                        $map['id']  = array( $how,$id );
                        $order = "ID DESC";
                        break;
                    case "first":
                        $order = "ID ASC";
                        break;
                    case "last":
                        $order = "ID DESC";
                        break;
                    default:
                        $map['id']  = $id;
                        break;
                }
           
                $map['uid'] = $uid;
            //组装查询条件
            $map    = $this->merge( $map );
         
            $result = $this->where( $map )->order( $order )->find();
            if( false == $result ){
                if( "gt" == $how ){
                    return $this->getTaskContent( $id,'first',$uid );
                }

                if( "lt" == $how ){
                    return $this->getTaskContent( $id,'last',$uid );
                }
                return false;
            }
            //清除data。防止污染
            $this->data   = null;
            $this->status = 1;
            

            //关联查询分类
            $result['category'] = array( 
                                    "name" => $category->getCategoryName( $result['category'] ),  //获取所有的分类
                                    "id"   => $result['category']
                                        );
            //追加任务中提到的内容
            $mention            = $mention->getTaskMention( $result['id'] );
            $result['mention']  = $mention[$result['id']];
            $result['count']    = $this->where( "uid = '".$result['uid']."' AND status = 1 " )->count();
            $result['num']      = $this->where( 'id <'.$result['id']." AND status = 1 AND uid =".$uid )->count()+1;
            $result['content']  = stripslashes( $result['content'] );
			
			
            return $result;
        }

        /**
         * factoryModel 
         * 工厂方法
         * @param mixed $name 
         * @static
         * @access private
         * @return void
         */
        public static function factoryModel( $name ){
            return D("Task".ucfirst( $name ));
        }
}
?>

