
<?php
    /**
     * TaskSourceModel 
     * 任务订阅源
     * @uses Model
     * @package 
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class TaskSourceModel extends BaseModel{
        /**
         * addSource 
         * 添加源
         * @param mixed $map 
         * @access public
         * @return void
         */
        public function addSource( $map ) {
            $map['cTime']    = time();
            $map['lastSnap'] = time();
            $map['changes']  = 0;
            $map['isNew']    = 0;
            $map = $this->merge( $map );
            $result = $this->add($map);
            return $result;
        }

        /**
         * getSource 
         * 查找源和返回源信息
         * @param mixed $map 
         * @access public
         * @return void
         */
        public function getSource( $map ){
            return $this->where( $map )->findAll();
        }

        public function getSourceId( $map ){
            $result = $this->where( $map )->field( 'id' )->find();
            return $result['id'];
        }

        public function editNew( $source_id,$change_ids ){
            $where['id']     = $source_id;
            $map['changes']  = serialize( $change_ids );
            $map['isNew']    = 0;
            $map['lastSnap'] = time();
            $result = $this->where( $where )->save($map);
            return $result;
        }

        public function deleteSource( $map ){
            if( empty( $map ) || array( 'in','' ) == $map){
                throw new ThinkException( "不允许空条件删除数据" );
            }

            return $this->where( $map )->delete();
        }
    }
?>

