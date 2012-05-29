
<?php
    /**
     * TaskSubscribeModel 
     * 订阅信息
     * @uses BaseModel
     * @package 
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class TaskSubscribeModel extends BaseModel{
        /**
         * editNew 
         * 编辑订阅项
         * @param mixed $source_id 
         * @param mixed $new_num 
         * @access public
         * @return void
         */
        public function editNew( $source_id,$new_num ){
            if( is_null($source_id) ){
                throw new ThinkException( "修改数据库必须有条件" );
            }
            $tablePrefix = $this->tablePrefix."blog_";

            $sql = "UPDATE {$tablePrefix}subscribe SET newNum = newNum + {$new_num} WHERE sourceId = '{$source_id}'";
            $result = $this->query( $sql )  ;
            return $result;
        }

        /**
         * deleSubscribe 
         * 删除资源项
         * @param mixed $map 
         * @access public
         * @return void
         */
        public function deleSubscribe( $map ){
            if( is_null( $map ) || empty( $map ) ){
                throw new ThinkException( "删除数据必须有值" );
            }
            if( $delete = $this->where($map)->delete() ){
                return true;
            }else{
                return false;
            }
        }

        public function getCount( $map ){
            return $this->where( $map )->count();
        }

        /**
         * addSubscribe 
         * 添加新的订阅
         * @param mixed $map 
         * @access public
         * @return void
         */
        public function addSubscribe( $map ){
            $map['newNum'] = 0;
            return $this->add( $map );
        }

        public function getSubscribes( $map,$fields = null ){
            $map    = $this->merge( $map );
            $result = $this->where( $map )->field( $fields )->findAll();
            return $result;
            
        }

        public function getSourceId( $map ){
            $map = $this->merge( $map );
            $result = $this->getFields( 'sourceId',$map );
            return $result;
        }

        public function getSubscribe( $map ){
            $map    = $this->merge( $map );
            $result = $this->where( $map )->find();
            return $result;
        }
    }
?>

