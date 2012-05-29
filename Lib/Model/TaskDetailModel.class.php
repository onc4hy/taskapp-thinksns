	
<?php
    /**
     * TaskDetailModel 
     * 明细的具体数据
     * @uses BaseModel
     * @package 
     * @version $id$
     * @copyright 2012 Houya
     * @author Houya <onc4hy@gmail.com> 
     * @license PHP Version 5.2 {@link www.houya.info}
     */
    class TaskDetailModel extends BaseModel{
	
		protected $tableName = "task_detail";
		
        public function getDetail( $map,$field ){
            return $this->where( $map )->field( $field )->findAll();
        }

        public function addDetail( $data,$links,$source_id ){
            $time = time();
            foreach( $data->items as $item){
                if( in_array( $item['link'] ,$links) ){
                    $map['title'] = t( $item['title'] );
                    $map['summary'] = h( $item['description'] );
                    $map['pubdate'] = $item['date_timestamp'];
                    $map['sourceId'] = $source_id;
                    $map['snapday'] = $time;
                    $map['link'] = $item['link'];
                    $map['boot'] = 0;
                    $change_ids[] = $this->add( $map );
                }
            }
            return $change_ids;
        }

        public function deleteDetail( $map ){
            //if( empty( $map ) || array( 'in','' ) == $map){
                //throw new ThinkException( "不允许空条件删除数据" );
            //}
            return $this->where( $map )->delete();
        }
        /**
         * deleteImportTask 
         * 删除已经导入的
         * @param mixed $result 
         * @access public
         * @return void
         */
        public function deleteImportTask( $result ){
            $id = array_keys( array_filter( $result) ) ;
            $map['id'] = array( "in",$id);
            if( empty($id) || array() == $id ){
                throw new ThinkException( "删除数据不允许空条件" );
            }
            $map2['boot'] = 1;
            $result = $this->where( $map )->save($map2);
            return $result;
        }
    }
?>

