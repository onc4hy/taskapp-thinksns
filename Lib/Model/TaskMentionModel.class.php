
<?php
    /**
     * TaskMentionModel 
     * task涉及到的人或者其它事务
     * @uses BaseModel
     * @package 
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class TaskMentionModel extends BaseModel{

        private $_type = "friends";

        protected function _initialize(){
            $this->type = $this->_type;
            parent::_initialize();
        }
        /**
         * addMentionModel 
         * 增加任务提及到的人
         * @param mixed $data 
         * @access public
         * @return void
         */
        public function addMention ( $taskid,$data ){
            foreach( $data as $value ){
                $user = $this->getOneName( $value );
                if( !$user['name'] ){
                    $user['name'] = "没这个人";
                }
                $select[] = "SELECT '{$taskid}','{$value}','{$user['name']}','{$this->_type}'";
                setScore($value, 'mentioned');
            }
            $select = implode( ' UNION ALL ',$select );

            $sql = "INSERT INTO `{$this->tablePrefix}task_mention` ( `taskid` , `uid` , `name`,`type` )
                    {$select}";
            if( $result = $this->execute( $sql ) ){

                return true;
            }else{
                return false;
            }
        }

        /**
         * getMention 
         * 通过uid获取到相对应的taskId
         * @param mixed $uid 
         * @access public
         * @return void
         */
        public function getUserMention($uid = null){
            
            //根据参数做不同的判断和处理
            if( is_null( $uid ) ){
                $map = $this->data;
            }elseif( is_array( $uid ) ){
                $map['uid'] = array( 'in',$uid  );
            }else{
                $map['uid'] = intval($uid);
            }

            $map = $this->merge( $map );
            //查询数据集
            $result = $this->where( $map )->findAll();

            //重组数据集
            return $this->recombine( $result );

        }

        public function checkRecommend( $uid,$taskid ){
            $map['uid']    = $uid;
            $map['type']   = 'recommend';
            $map['taskid'] = $taskid;
            return $this->where( $map )->find();
        }
        /**
         * getTaskMention 
         * 通过taskid获得被提到的人
         * @param mixed $uid 
         * @access public
         * @return void
         */
        public function getTaskMention( $id ){
            $map['taskid'] = $id;
            $map = $this->merge( $map );
            $result = $this->where( $map )->findAll();

            //重组数据集
            return $this->recombine( $result );
        }

        /**
         * updateMention 
         * 更新相关人员
         * @param mixed $friendIds 
         * @access public
         * @return void
         */
        public function updateMention( $taskid,$friendIds ){
            if( empty( $taskid ) || false == $taskid ){
                throw new ThinkException( "删除数据库必须有条件" );
            }

            //删除旧的
            $this->where( 'taskid='.$taskid." AND type= '"+$this->_type+"'" )->delete();
            if( array_filter( $friendIds ) )
                return $this->addMention( $taskid,$friendIds );//添加新的
            return true;
        }

        /**
         * addRecommendUser 
         * 添加博客推荐
         * @param mixed $map 
         * @param mixed $action 
         * @param mixed $obj 
         * @access public
         * @return void
         */
        public function addRecommendUser( $map,$action ){
            //推荐
            if( 'recommend' == $action  ){
                $this->add( $map );
                $sql = "UPDATE {$this->tablePrefix}task
                        SET recommendCount = recommendCount + 1
                        WHERE `id` = {$map['taskid']}
                    ";
            //取消推荐
            }else{
                $this->where($map)->delete( );
                $sql = "UPDATE {$this->tablePrefix}task
                        SET recommendCount = recommendCount - 1
                        WHERE `id` = {$map['taskid']}
                    ";
            }
            $result = $this->execute( $sql ) ;
            return $result;

        }

        private function recombine( $data ){
            if( $data ){
                foreach( $data as $value ){
                    $newresult[$value['taskid']][] = array( "uid" => $value['uid'],
                                                          "name"=> $value['name']);
                }
                return $newresult;
            }else{
                return false;
            }
        }

    }
?>

