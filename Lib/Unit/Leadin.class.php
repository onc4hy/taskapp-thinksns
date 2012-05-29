
<?php
    /**
     * TaskLeadIn{ 
     * 其它站任务导入
     * @package 
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class LeadIn{
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
         * _addSubscribe 
         * 添加订阅
         * @param mixed $uid 
         * @access protected
         * @return void
         */
        public function addSubscribe( $uid ){
            $source    = $this->_factory( 'source' );
            $subscribe = $this->_factory( 'subscribe' );
            //订阅源项处理
            //获取此条订阅源，并进行判断
            $source_result = $this->_selectSource($source);

            if( $source_result ){
                //已经有这个源了
                $source_id = $source_result;
            }else{
                $map['service']  = $this->service;
                $map['username'] = $this->username;

                //还没有人订阅，添加这一条源的订阅到数据库
                $source_id       = $source->addSource( $map );
            }

            //检查是否已经订阅过该源
            unset( $map );
            $map['uid']      = $uid;
            $map['sourceId'] = $source_id;
            $subscribe_id    = $subscribe->getSubscribe( $map );

            if( false === $subscribe_id ){
                $subscribe->addSubscribe( $map );
            }
        
            return $source_id;
        }

        /**
         * deleSubscribe 
         * 删除订阅
         * @access public
         * @return void
         */
        public function deleSubscribe(array $source_id ,$uid){
            if( $source_id === array() ){
                return false;
            }

            //产生订阅的对象
            $subscribe = $this->_factory( 'Subscribe' );
            $source    = $this->_factory( 'source' );
            $item      = $this->_factory( 'item' );


            //删除订阅内容
            $map['uid']  = $uid;
            $map['sourceId'] = array( 'in',$source_id );
            //删除该用户的订阅数据
            $result = $subscribe->deleSubscribe( $map );
            unset( $map['uid'] );

            //取得该订阅的订阅数
            $liveNum = $subscribe->getCount( $map );
            
            //如果没有订阅了,删除整个订阅源
            if( 0 == $liveNum){
                //删除组件
                $delete = D( "TaskItem" )->deleteItem( $map );
                unset( $map );

                //删除这条订阅源
                $map['id'] = array( 'in',$source_id );
                $delete = $source->deleteSource( $map );
                unset( $map );
                return true;
            }
            return false;
        }

        /**
         * snap 
         * 更新订阅源
         * @access public
         * @return void
         */
        public function get_source_data($uid){
            require_once dirname( __FILE__ ) . '/rss/rss_parser.php';
            require_once dirname( __FILE__ ) . '/rss/snap.php';
                
            //获得订阅源id
            $source_result = $this->addSubscribe($uid);

            $options = array( 
                        "service"  => $this->service,
                        "username" => $this->username,
                );
            //snap对象解析相对应的url和数据集合，以数组形式返回
            $snap = new Snap($options);
            $result = $this->_update( $snap->update(),$source_result );
            false == $result && $this->deleSubscribe( $source_result,$uid );
            return $result;
        }

        /**
         * update_data 
         * 更新数据
         * @param mixed $data 
         * @access public
         * @return void
         */
        public function update_data( $data ){
            require_once dirname( __FILE__ ) . '/rss/rss_parser.php';
            require_once dirname( __FILE__ ) . '/rss/snap.php';
            if( 1 < count( $data ) ){
                //如果大于1，则是更新多个源的数据
                foreach ( $data as $value ){
                    $options = array( 
                            "service" => $value['service'],
                            'username' => $value['username'],
                        ) ;
                    $snap = new Snap( $options );
                    $result[] = $this->_update( $snap->update(),$value['id'] );
                }
            }else{
                //如果小于1，则是更新单个的源数据
                $options = array( 
                             "service" => $data[0]['service'],
                            'username' => $data[0]['username'],
                                ) ;
                $snap = new Snap( $options ) ;
                $result = $this->_update( $snap->update(),$data[0]['id'] );
            }
            return $result;
        }

        /**
         * update 
         * 更新源和订阅信息。返回干净的任务信息数组
         * @access public
         * @return void
         */
        private function _update( $data,$source_id ){
            if( false == $data ) return false;
            //创建需要用到的数据库层
            $source    = $this->_factory( 'Source' );
            //$item      = $this->_factory( 'item' );
            $subscribe = $this->_factory( 'subscribe' );

            //获取源i
            $new_links = array();
            $old_links = array();

            //取得最新的链接
            foreach( $data->items as $item ){
                $new_links[] = $item['link'];
            }

            //取得数据库中已有的链接
            $map['sourceId'] = $source_id;
            $item_links      = D( 'TaskItem' )->getItem( $map,'link' );
            foreach( $item_links as $value ){
                $old_links[] = $value['link'];
            }

            //消除已经添加过的link
            $links = array_diff( $new_links,$old_links );

            //将新数据添加到到订阅数据库中,获取添加的item的id
            $change_ids = D( 'TaskItem' )->addItem( $data,$links,$source_id );

            //如果被添加的id不为空
            if( $change_ids !== array() ){
                $new_num = count( $change_ids );
                //修改源数据信息
                $source->editNew( $source_id,$change_ids );
                //修改订阅信息
                $subscribe->editNew( $source_id,$new_num );
            }

            $result['change_ids'] = $change_ids;
            $result['source_id'] = $source_id;
            //将任务数据信息回传,用以保存或者显示
            return $result;
        }
        
        /**
         * _factory 
         * 私有工厂方法
         * @param Model $Classname 
         * @access private
         * @return void
         */
        private function _factory( $classname ){
           return D( "Task".ucfirst( $classname ) );
        }

        /**
         * selectSource 
         * 检查源是否存在

         * @param object $source 
         * @access private
         * @return void
         */
        private function _selectSource($source){
            //检查是否有该订阅源
            $map['service']  = $this->service;
            $map['username'] = $this->username;
            $source_result   = $source->getSourceId( $map );
            return $source_result;
        }
    }
?>

