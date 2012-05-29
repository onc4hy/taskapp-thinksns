
<?php
/**
 * TaskPriorityModel
 * 任务优先级model
 * @uses Model
 * @package
 * @version $id$
 */
class TaskPriorityModel extends BaseModel {

/**
 * getPriority
 * 获取所有访问权限选项
 * @access public
 * @return void
 */
    public function getPriority($uid = 0) {
    	//先从缓存里面获取
        $result = $this->where("`uid`=$uid")->field( 'name,uid,id' )->findAll();

        //重组数据集结构
        $newresult = array();
        foreach ( $result as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }

        return $newresult;
    }

    public function isPriorityExist($name, $uid = 0, $gid = 0) {
    	$map['name'] = $name;
    	$map['uid']	 = array('in', "$uid,0");
    	$map['id']   = array('neq', $gid);
    	return $this->where($map)->find() ? true : false;
    }

    public function isPriorityEmpty($gid) {
    	return M('task')->where('`category`='.intval($gid))->find() ? false : true;
    }

    /**
     * addPriority
     * 增加访问权限选项
     * @param mixed $map
     * @access public
     * @return void
     */
    public function addPriority( $map,$dao = null ) {
        //检查是否为空
        $this->__equalTrueEchoMsg(empty($map['name']), -3);
        //检测是否有重复的访问权限选项
        $this->__checkPriority($map,$dao);
        $map = $this->merge( $map );
        $result = $this->add($map);
        $this->__issetQueryToMsg($result, -1);
    }

   private function __checkPriority($data,$dao) {
   		global $ts;
        $category = $this->getPriority( $ts['user']['uid'] );
        foreach( $category as $value ) {
            $this->__equalTrueEchoMsg($value['name'] == $data['name'], -2);
        }
    }
    private function __equalTrueEchoMsg($query,$msg) {
        if(true === $query) {
            echo $msg;
            exit();
        }

    }

    private function  __issetQueryToMsg($query,$msg) {
        if(isset($query)) {
            echo $query;
        }else {
            echo $msg;
        }
        exit();
    }

    /**
     * deletePriority
     * 删除访问权限选项
     * @param mixed $map
     * @access public
     * @return void
     */
    public function deletePriority( $map,$formCate = null,$obj = null ) {
		//先判断合法性
        if( empty( $map ) )
            throw new ThinkException( "不能是空条件删除" );
        //转移被删访问权限选项下的任务到默认访问权限选项
        if( isset( $formCate ) ) {
            $result = $obj->setField( 'private',$formCate,'private = '.$map['id'] );

            $private_data = M('task_private_option')->where("`id`={$formCate}")->getField('name');
            $obj->setField('private_data', $private_data, "`private`={$formCate}");
        }else {
        //删除访问权限选项下的所有任务
            $obj->where( 'private = '.$map['id'] )->delete();
        }

        //删除访问权限选项
        return $this->where( $map )->delete();
    }
    public function deletePriorityForId( $map) {
    //先判断合法性
        if( empty( $map ) )
            throw new ThinkException( "不能是空条件删除" );
        //转移被删访问权限选项下的任务到默认访问权限选项
//        $obj=D("Task");
//        if( isset( $formCate ) ) {
//
//            $result = $obj->setField( 'category',$formCate,'category = '.$map['id'] );
//        }else {
//        //删除访问权限选项下的所有任务
//            $obj->where( 'category = '.$map['id'] )->delete();
//        }

        //删除访问权限选项
        return $this->where($map)->delete();
    }
    /**
     * editPriority
     * 编辑访问权限选项
     * @param mixed $map
     * @access public
     * @return void
     */
    public function editPriority( $data ) {
        foreach( $data as $key=>$value ) {
            $map1[] = "`id` = $key";
            $map2[] = "WHEN `id` = $key THEN '$value'";
        }
        $case = implode( ' ', $map2 );
        $where = implode( ' or ',$map1 );


        $sql = "UPDATE `".C('DB_PREFIX')."task_private_option`
                    SET `name` = (case {$case} end)
                    WHERE {$where} ";
        $query = $this->execute( $sql );
        return $query;

    }

    /**
     * getPriorityName
     * 通过id获得名字
     * @param mixed $id
     * @access public
     * @return void
     */
    public function getPriorityName( $id ) {
        $map['id'] = $id;
        $result = $this->where( $map )->field('name')->find();
        return $result['name'];
    }


    /** 
     * getUserPriority
     * 获得用户的访问权限选项
     * @param mixed $uid
     * @access public
     * @return void
     */
    public function getUserPriority( $uid ) {
        $map['uid'] = array( 'in',"$uid,0" );
        $result     = $this->where( $map )->field( 'name,id,uid' )->order('`uid` ASC, id ASC')->findAll();
        return $result;
    }
}
?>

