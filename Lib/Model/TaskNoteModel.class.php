	
<?php
    /**
     * TaskNoteModel 
     * @uses BaseModel
     * @package 
     * @version $id$
     * @copyright 2012 Houya
     * @author Houya <onc4hy@gmail.com> 
     * @license PHP Version 5.2 {@link www.houya.info}
     */
class TaskNoteModel extends BaseModel{
	
	protected $tableName = "taskNotes";
	 
    public $_type = 0;
    public $cuid = 0;

    /**
     * limit
     * 每页显示多少条
     * @var float
     * @access public
     */

    public function _initialize() {
    //初始化只搜索status为0的。status字段代表没被删除的
        $this->status = 1;
        //$emotion_obj  = D( 'Smile' );
        $config_obj   = D( 'AppConfig','task' );
        ////获取配置
        $config = $config_obj->getConfig( APP_NAME );
        $config = self::changeType( $config,'int' ); //将数组各元素转换成int类型
        $this->setConfig( $config );
        parent::_initialize();
    }

    public function setCount($appid,$count) {
        $map['id'] = $appid;
        $map2['commentCount'] = $count;
        return $this->where($map)->save($map2);
    }
    /**
     * getTaskList
     * 通过userId获取到用户列表
     * 通过用户Id获取用户心情
     * @param array|string|int $userId
     * @param array|object $options 查询参数
     * @access public
     * @return object|array
     */
    public function getNoteList($map = null,$field=null,$order = null,$limit = null) {
    //处理where条件
	    $map = $this->merge( $map );
        //连贯查询.获得数据集
        $limit = isset( $limit )?$limit:$this->config->limitpage;
        $result         = $this->where( $map )->field( $field )->order( $order )->findPage( $limit) ;
        
        //对数据集进行处理
        $data           = $result['data'];
        $data           = $this->replace( $data ); //本类重写父类的replace方法。替换任务的分类和追加任务的提及到的人
        //$data           = intval( $this->config->replay ) ? $this->appendReplay($data):$data;//追加回复
		//dump ($data);
		
			foreach($data as $key=>$value) {
				$id = $value['id'];
				$detailList = $this->getDetailList($id);
				$data[$key]['detail_list'] = $detailList;
				$childList = $this->getChildList($id);
				$data[$key]['child_list'] = $childList;
				
				$id = $value['parent_id'];
				$name = $this->where("id=".$id)->getField("title");
				$data[$key]['parent_id'] = array(
					"id"=>$id,
					"name"=>$name,
				);
				$id = $value['share_source'];
				$name = M("user")->where("uid=".$id)->getField("uname");
				$data[$key]['share_source'] = array(
					"id"=>$id,
					"name"=>$name,
				);
			}

        $result['data'] = $data;
        
     	return $result;
    }

	// 通告
    public function getAllNoticesData($where,$order){
            //TODO 根据条件决定排序方式,尚有优化空间
            //$temp_order_map = $this->getOrderMap($order);
            //根据以上处理条件获取数据集
            $result             = $this->getTaskNoticeList($where,null,$order);
            $result['category'] = $this->getCategory();
            return $result;
    }
	
    public function getTaskNoticeList($where = null,$field=null,$order = null,$limit = null) {
		$dao = D('taskNotices');
		//处理where条件
	    //$map = $this->merge( $map );
        //连贯查询.获得数据集
        $limit = isset( $limit )?$limit:$this->config->limitpage;
        $result         = $dao->where( $where )->field( $field )->order( $order )->findPage( $limit) ;
        
        //对数据集进行处理
        $data           = $result['data'];
        $data           = $this->replace( $data ); //本类重写父类的replace方法。替换任务的分类和追加任务的提及到的人
        //$data           = intval( $this->config->replay ) ? $this->appendReplay($data):$data;//追加回复
		//dump ($data);
		
        $result['data'] = $data;
        
     	return $result;
    }

	// 便签
    public function getAllNotesData($where,$order){
            //TODO 根据条件决定排序方式,尚有优化空间
            //$temp_order_map = $this->getOrderMap($order);
            //根据以上处理条件获取数据集
            $result             = $this->getTaskNoteList($where,null,$order);
            $result['category'] = $this->getCategory();
			
            return $result;
    }
	
    public function getTaskNoteList($where = null,$field=null,$order = null,$limit = null) {
		$dao = D('taskNotes');
		//处理where条件 
	    //$map = $this->merge( $where );
        //连贯查询.获得数据集
        $limit = isset( $limit )?$limit:$this->config->limitpage;
        $result         = $dao->where( $where )->field( $field )->order( $order )->findPage( $limit) ;
        
        //对数据集进行处理
        $data           = $result['data'];
        $data           = $this->replace( $data );  //本类重写父类的replace方法。替换任务的分类和追加任务的提及到的人
        //$data           = intval( $this->config->replay ) ? $this->appendReplay($data):$data;//追加回复
		//dump ($data);
		
        $result['data'] = $data;
        
     	return $result;
    }

    /**
     * getTaskContent
     * 重写父类的getTaskContent
     * @param mixed $id
     * @param mixed $how
     * @param mixed $uid
     * @access public
     * @return void
     */
    public function getTaskContent( $id,$how =null,$uid = null  ) {
        $result         = parent::getTaskContent( $id,$how,$uid );
        if(false == $result) return false;
        $result['role']  = $this->checkCommentRole( $result['canableComment'],$uid,$this->cuid );
        $result['title'] = stripslashes( $result['title'] );
        $result['attach'] = unserialize($result['attach']);
        return $result;
    }

    public function setUid($value) {
        $this->cuid = $value;
    }
    /**
     * getMentionTask
     * 获取提到我的好友的帖子数据
     * @param mixed $uid
     * @access public
     * @return void
     */
    public function getMentionNote( $uid = null ) {
        $mention   = self::factoryModel( 'mention' );


        if( isset( $uid ) ) {
            $userId = $uid;

        }else {
        //获得当前登录者的好友
            $userId   = $this->getFriends();
        }

        //通过用户id获得相应的taskid列表
        $tasklist   = $mention->getUserMention( $userId );

        //获得taskId列表和组装查询条件
        $taskidlist = array_keys( $tasklist );
        if( empty( $tasklist ) )
            return false;
        $map['id']  = array( 'in',$taskidlist );

        //组合查询条件，如，status=1;
        $map        = $this->merge( $map );

        //返回查询结果
        if( $result = $this->where( $map )->findPage( $this->config->limitpage) ) {
            $data           = $this->replace( $result['data'],$tasklist );
            $result['data'] = $data;
            return $result;
        }
        return false;

    }

    public function getCategory( $uid ) {
        $category        = self::factoryModel( 'Category' );
        if( isset( $uid ) ) {
            $categorycontent = $category->getUserCategory($uid);
        }else {
            $categorycontent = $category->getCategory();
        }
        return $categorycontent;
    }

    public function getTypeList( $uid ) {
        $type        = self::factoryModel( 'Type' );
        if( isset( $uid ) ) {
            $typeList = $type->where("uid=".$uid)->select();
        }else { 
            $typeList = $type->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $typeList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
    public function getPrivateTypeList( $uid ) {
        $privateType        = self::factoryModel( 'PrivateType' );
        if( isset( $uid ) ) {
            $privateTypeList = $privateType->where("uid=".$uid)->select();
        }else { 
            $privateTypeList = $privateType->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $privateTypeList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
    public function getStatusTypeList( $uid ) {
        $statusType        = self::factoryModel( 'StatusType' );
        if( isset( $uid ) ) {
            $statusTypeList = $statusType->where("uid=".$uid)->select();
        }else { 
            $statusTypeList = $statusType->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $statusTypeList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
    public function getPriorityTypeList( $uid ) {
        $priorityType        = self::factoryModel( 'PriorityType' );
        if( isset( $uid ) ) {
            $priorityTypeList = $priorityType->where("uid=".$uid)->select();
        }else { 
            $priorityTypeList = $priorityType->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $priorityTypeList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
    public function getShareTypeList( $uid ) {
        $shareType        = self::factoryModel( 'ShareType' );
        if( isset( $uid ) ) {
            $shareTypeList = $shareType->where("uid=".$uid)->select();
        }else { 
            $shareTypeList = $shareType->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $shareTypeList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
    public function getBehaviorTypeList( $uid ) {
        $behaviorType        = self::factoryModel( 'BehaviorType' );
        if( isset( $uid ) ) {
            $behaviorTypeList = $behaviorType->where("uid=".$uid)->select();
        }else { 
            $behaviorTypeList = $behaviorType->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $behaviorTypeList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
    public function getRelTasksList( $uid ) {
        $relTasks        = self::factoryModel( 'RelTasks' );
        if( isset( $uid ) ) {
            $relTasksList = $relTasks->where("uid=".$uid)->select();
        }else { 
            $relTasksList = $relTasks->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $relTasksList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
    public function getNoticesList( $uid ) {
        $notices        = self::factoryModel( 'Notices' );
        if( isset( $uid ) ) {
            $noticesList = $notices->where("uid=".$uid)->select();
        }else { 
            $noticesList = $notices->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $noticesList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
    public function getNotesList( $uid ) {
        $notes        = self::factoryModel( 'Notes' );
        if( isset( $uid ) ) {
            $notesList = $notes->where("uid=".$uid)->select();
        }else { 
            $notesList = $notes->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $notesList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['content'] = $value['content'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }

    public function getParentList( $uid ) {
        $dao        = D( 'tasks' );
        if( isset( $uid ) ) {
            $parentList = $dao->where("uid=".$uid)->select();
        }else { 
            $parentList = $dao->select();
        }
        //重组数据集结构
        $newresult = array();
        foreach ( $parentList as $value ) {
            $newresult[$value['id']]['id']   = $value['id'];
            $newresult[$value['id']]['name'] = $value['name'];
            $newresult[$value['id']]['title'] = $value['title'];
            $newresult[$value['id']]['uid']  = $value['uid'];
        }
        return $newresult;
    }
	
    public function getRelatedList( $id ) {
        $relDao        = D("task");
        if( isset( $id ) ) {
            $relIds = $relDao->where(array("id"=>$id))->getField("rel_ids");
        }else { 
            $relIds = $relDao->getField("rel_ids");
        }
		if (!empty($relIds)) {
			$relList = $relDao->where(array("id"=>array("in",$relIds)))->select();
		}
        return $relList;
    }
	
    public function getDetailList( $id ) {
        $detailDao        = D("taskDetail");
        if( isset( $id ) ) {
            $detailList = $detailDao->where(array("task_id"=>$id))->select();
        }else { 
			$detailList = null;	
        }
        return $detailList;
    }
	
    public function getChildList( $id ) {
        $childDao        = D("task");
        if( isset( $id ) ) {
            $childList = $childDao->where(array("parent_id"=>$id))->select();
        }else { 
            $childList = null;
        }
        return $childList;
    }
	
    /**
     * checkCommentRole
     * 检查是否可以评论
     * @param mixed $role 评论权限
     * @param mixed $userId 任务所有者
     * @access protected
     * @return void
     */
    private function checkCommentRole( $role,$userId,$mid ) {
        if( $userId == $mid ) {
            return 1;
        }
        switch ( $role ) {
            case 1:  //全站可评论
                return 1;
            case 2:  //好友可评论
                if( $this->api->friend_areFriends($mid,$userId) ) {
                    return 1;
                }else {
                    return 2;
                }
            case 3:  //关闭评论
                return 3;
        }
    }
  public function getIsHot() {  //获取推荐博客...重复//TS_2.0
    //处理where条件
	    $map['isHot'] = 1;
	    $map['status']= 1;
	    $order        = 'rTime DESC';
		
	        //连贯查询.获得数据集
	   
	    $hotlist = $this->where( $map )->order( $order )->findAll();
        //对数据集进行处理
        //$data           = $result['data'];
        //$data           = $this->replace( $data ); //本类重写父类的replace方法。替换任务的分类和追加任务的提及到的人
        //$data           = intval( $this->config->replay ) ? $this->appendReplay($data):$data;//追加回复
		//dump ($data);
        return $hotlist;
    }
	
    public function getAllData($order){
                //TODO 根据条件决定排序方式,尚有优化空间
            $temp_order_map = $this->getOrderMap($order);
            //根据以上处理条件获取数据集
            $result             = $this->getTaskList($temp_order_map['map'],null,$temp_order_map['order']);
            $result['category'] = $this->getCategory();
            return $result;
    }
	
    public function getFollowsNote($mid){
    	$fl=D("Follow");
        $followlist=$fl->getfollowList($mid);
                		//dump ($followlist);
                		foreach($followlist as $key=>$value)
						{
 							 $folist[$key]=$value['fid'];
						} 
                		$map['uid']  = array('in',$folist);
                		$order = 'cTime DESC';
            $result             = $this->getTaskList($map,null,$order);
            $result['category'] = $this->getCategory();
            return $result;	
    }
    private function getOrderMap($order){
           switch( $order ) {
                case 'hot':    //推荐阅读
                    $map['isHot'] = 1;
                    $order        = 'rTime DESC';
                    break;
                case 'new':    //最新排行
                    $order = 'cTime DESC';
                    break;
                case 'popular':    //人气排行
                    $order        = 'hot DESC';
                    $map['cTime'] = self::_orderDate( $this->config->allorder );//取得时间
                    break;
                case 'read':   //阅读排行
                    $order = 'readCount DESC';
                    $map['cTime'] = self::_orderDate( $this->config->allorder );//取得时间
                    break;
                case 'comment':   //评论排行
                    $order = 'commentCount DESC';
                    $map['cTime'] = self::_orderDate( $this->config->allorder );//取得时间
                    break;

                default:      //默认时间排行
                    $order = 'cTime DESC';
            }
            $map['private'] = array('neq',2);
            $result['map'] = $map;
            $result['order'] = $order;
            return $result;
    }
    /**
     * getTaskCategoryCount
     * 根据uid获得任务分类的任务数
     * @param mixed $uid
     * @access public
     * @return void
     */
    public function getTaskCategoryCount( $uid ) {
        $sql = "SELECT count( 1 ) as count, category
                    FROM `{$this->tablePrefix}tasks`
                    WHERE `category` IN (
                                          SELECT `id`
                                          FROM {$this->tablePrefix}task_category
                                          WHERE `uid` = 0 OR `uid` = {$uid} 
                                      ) AND `uid` = {$uid} AND `status` = 1
                                          GROUP BY category
            ";
        $result = $this->query( $sql );
        return $result;
    }
    public function getTaskCategory($uid,$cateId) {
        $list = $this->getCategory($uid);
        $result = $this->getTaskCount($uid,$list);
        if(isset( $cateId ) && !self::_checkCategory( $cateId,$list )) return false;
        return $result;
    }

    public function getTaskCount($uid,$list) {
        $result = $list;
        $count = $this->getTaskCategoryCount( $uid );
        //重组数据
        $count_arr = array();
        foreach ( $count as $value ) {
            $count_arr[$value['category']] = $value['count'];
        }
        foreach ($result as &$value) {
            $value['count'] = $count_arr[$value['id']] ? $count_arr[$value['id']] : 0;
        }
        return $result;
    }
    /**
     * _checkCategory
     * 检查分类是否合法
     * @param mixed $cateId
     * @param mixed $category
     * @static
     * @access private
     * @return void
     */
    private static function _checkCategory($cateId,$category ) {
        $temp = array();
        foreach( $category as $value ) {
            $temp[] = $value['id'];
        }
        return in_array($cateId,$temp);
    }

    /**
     * doDeleteTask
     * 删除Mili博客，检查配置DELETE=true,则真实删除。如果DELETE=false，则是状态为1;
     * @param array|string $map 删除条件
     * @access public
     * @return void
     */
    public function doDeleteNote( $map = null,$uid=null ) {
    //获得配置信息
        $config    = $this->config->delete;

        //获得删除条件
        $condition = $this->merge( $map );

        //检测uid是否合法
        $mid = $this->where( $condition )->getField( 'uid' );
        if( isset($uid) && $uid != $mid ) {
            return false;
        }
        if(!isset($uid)){
        	$uid = $mid;
        }
        //判断是否合法。不允许删除整个表
        if( !isset( $condition ) && empty( $condition ) )
            throw new ThinkException( "不允许删除整个表" );
        //如果配置文件中delete的值为true则真是删除，如果delete=false,则设置status＝2;

        if( false == $config ) {
            $result = $this->where( $condition )->setField( 'status',2 );
            $count = $this->where( 'uid ='.$uid.' AND status <> 2' )->count();
        }else {
            $result = $this->where( $condition )->delete();
            $count = $this->where( 'uid ='.$uid)->count();
        }
//        setScore($uid,'delete_task');
//      //  修改空间中的计数
//      $this->api	=	new TS_API();
//     $this->opts = $this->api->option_get();
//      $result = $this->api->space_changeCount( 'task',$count );
        
        return $result;
    }

    /**
     * changeCount
     * 修改任务的浏览数
     * @param mixed $taskid
     * @access public
     * @return void
     */
    public function changeCount( $taskid ) {
        $sql = "UPDATE {$this->tablePrefix}tasks
                    SET readCount=readCount+1,hot = commentCount*readCount+round(readCount/(commentCount+1),0)
                    WHERE id='$taskid' LIMIT 1 ";
        $result = $this->execute($sql);
        if ( $result ) {
            return true;

        }
        return false;
    }

    /**
     * fileAway
     * 归档查询
     *
     * @param string|array $findTime 查询时间。
     * @param mixed $condition 查询条件
     * @param Model $object 查询对象
     * @param mixed $limit 查询limit
     * @access public
     * @return void
     */
    public function fileAway($findTime ,$condition = null) {
    //如果是数组。进行的解析不同
        if( is_array( $findTime) ) {
            $start_temp   = $this->paramData( strval($findTime[0] ));
            $end_temp     = $this->paramData( strval($findTime[1] ));

            $start        = $start_temp[0];
            $end          = $end_temp[1];
        }else {
            $findTime  = strval( $findTime );
            $paramTime = self::paramData( $findTime );
            $start     = $paramTime[0];
            $end       = $paramTime[1];
        }
        $this->cTime = array( 'between', array( $start,$end ) );
        //如果查询时没有设置其它查询条件，就只是按时间来进行归档查询
        $map = $this->merge( $condition );
        //查询条件赋值
        $result = $this->where( $map )->field( '*' )->order( 'cTime DESC' )->findPage( $this->config->limitpage);
        $result['data'] = $this->replace( $result['data'] );//追加内容

        //追加用户名
        return $result;
    }
    /**
     * doAddTask
     * 添加任务
     * @param mixed $map 任务内容
     * @param mixed $feed 是否发送动态
     * @access public
     * @return void
     */
    public function doAddNote ($map,$import) {
        $map['cTime']        = isset( $map['cTime'] )?$map['cTime']:time();
        $map['mTime']        =$map['cTime'];
        $map['type']  		 = isset( $map['type'])?$map['type']:$this->_type;
    	$map['tags']		= $map['tags'];
    	$map['private']		= $map['private'];
        $map['private_data'] = md5($map['password']);
        $map['category_title'] = M('task_category')->where("`id`={$map['category']}")->getField('name');
        $content 			 = $map['content'];// 用于发通知截取
        $map['content'] 	 = t(h($map['content']));

        unset( $map['password'] );
        $friendsId = isset( $map['mention'] )?explode(',',$map['mention']):null;//解析提到的好友
        unset( $map['mention'] );

        $map['share_type']  		 = isset( $map['share_type'])?$map['share_type']:$this->_share_type;
        $map['share_source']  		 = isset( $map['share_source'])?$map['share_source']:$this->_share_source;
        $map['share_target']  		 = isset( $map['share_target'])?$map['share_target']:$this->_share_target;
        $map['parent_id']  		 = isset( $map['parent_id'])?$map['parent_id']:$this->_parent_id;
        $map['level']  		 = isset( $map['level'])?$map['level']:$this->_level;
        $map['task_type']  		 = isset( $map['task_type'])?$map['task_type']:$this->_task_type;
        $map['task_status']  		 = isset( $map['task_status'])?$map['task_status']:$this->_task_status;
        $map['task_priority']  		 = isset( $map['task_priority'])?$map['task_priority']:$this->_task_priority;
        $map['start_date']  		 = isset( $map['start_date'])?$map['start_date']:$this->_start_date;
        $map['due_date']  		 = isset( $map['due_date'])?$map['due_date']:$this->_due_date;
        $map['estimated_time']  		 = isset( $map['estimated_time'])?$map['estimated_time']:$this->_estimated_time;
        $map['spent_time']  		 = isset( $map['spent_time'])?$map['spent_time']:$this->_spent_time;
        $map['done_ratio']  		 = isset( $map['done_ratio'])?$map['done_ratio']:$this->_done_ratio;
        $map['detail_summary']  		 = isset( $map['detail_summary'])?$map['detail_summary']:$this->_detail_summary;
        $map['rel_digest']  		 = isset( $map['rel_digest'])?$map['rel_digest']:$this->_rel_digest;
        $map['rel_tasks']  		 = isset( $map['rel_tasks'])?$map['rel_tasks']:$this->_rel_tasks;
		
        $map    = $this->merge( $map );
        $addId  = $this->add( $map );
        
        $temp = array_filter( $friendsId );
        //$appid = A('Index')->getAppId();
        //添加任务提到的好友
        if( !empty( $friendsId ) && !empty($temp) ) {
            $mention = self::factoryModel( 'mention' );
            $result  = $mention->addMention( $addId,$temp );
            for($i =0 ;$i<count($temp);$i++){
                  setScore($map['uid'], 'mention');
            }

            //发送通知给提到的好友

            $body['content']     = getTaskShort(t($content),40);
            $url                 = sprintf( "%s/Index/show/id/%s/mid/%s",'{'.$appid.'}',$addId,$map['uid'] );
            $title_data['title']   = sprintf("<a href='%s'>%s</a>",$url,$map['title']);
            $this->doNotify( $temp,"task_mention",$title_data,$body,$url );
        }
        if( !$addId ) {
            return false;
        }
        //获得配置信息
        $config    = $this->config->delete;
        if( $config ) {
        //修改空间中的计数
            $count = $this->where( 'uid ='.$map['uid'] )->count();
        }else {
        //修改空间中的计数
            $count = $this->where( 'uid ='.$map['uid'].' AND status <> 2' )->count();
        }
        //$this->api->space_changeCount( 'task',$count );

        //发送动态
        if( $import ) {
        //$title['title']   = sprintf("<a href=\"%s/Index/show/id/%s/mid/%s\">%s</a>",__APP__,$addId,$map['uid'],$map['title']);
            $title['title']   = sprintf("<a href=\"%s/Index/show/id/%s/mid/%s\">%s</a>",'{SITE_URL}',$addId,$map['uid'],$map['title']);
            $title['title'] = stripslashes($title['title']);
            //setScore($map['uid'],'add_task');
            $body['content'] = getTaskShort($this->replaceSpecialChar(t($map['content'])),80);
            $body['title'] = stripslashes($body['title']);
            $this->doFeed("task",$title,$body);
        }else {
            //setScore($map['uid'],'add_task');
            $result['appid'] = $addId;
            $result['title'] = sprintf("<a href=\"%s/Index/show/id/%s/mid/%s\">%s</a>",'{SITE_URL}',$addId,$map['uid'],$map['title']);
            return $result;
        }


        return $addId;
    }

    public function doSaveNote( $map,$taskid ) {
        $map['mTime'] = isset( $map['cTime'] )?$map['cTime']:time();
        $map['type']  = isset( $map['type'])?$map['type']:$this->_type;
        $map['category_title'] = M('task_category')->where("`id`={$map['category']}")->getField('name');
    	$map['tags']		= $map['tags'];
    	$map['private']		= $map['private'];
        $map['private_data'] = md5($map['password']);
        $map['content'] 	 = t(h($map['content']));

        unset( $map['password'] );
        //添加task相关好友
        $friendsId = isset( $map['mention'] )?explode(',',$map['mention']):null;
        unset( $map['mention'] );
        $map    = $this->merge( $map );

        if( !empty( $friendsId ) ) {
            $mention = self::factoryModel( 'mention' );
            $result  = $mention->updateMention( $taskid,$friendsId );
        }
		
        $map['share_type']  		 = isset( $map['share_type'])?$map['share_type']:$this->_share_type;
        $map['share_source']  		 = isset( $map['share_source'])?$map['share_source']:$this->_share_source;
        $map['share_target']  		 = isset( $map['share_target'])?$map['share_target']:$this->_share_target;
        $map['parent_id']  		 = isset( $map['parent_id'])?$map['parent_id']:$this->_parent_id;
        $map['level']  		 = isset( $map['level'])?$map['level']:$this->_level;
        $map['task_type']  		 = isset( $map['task_type'])?$map['task_type']:$this->_task_type;
        $map['task_status']  		 = isset( $map['task_status'])?$map['task_status']:$this->_task_status;
        $map['task_priority']  		 = isset( $map['task_priority'])?$map['task_priority']:$this->_task_priority;
        $map['start_date']  		 = isset( $map['start_date'])?$map['start_date']:$this->_start_date;
        $map['due_date']  		 = isset( $map['due_date'])?$map['due_date']:$this->_due_date;
        $map['estimated_time']  		 = isset( $map['estimated_time'])?$map['estimated_time']:$this->_estimated_time;
        $map['spent_time']  		 = isset( $map['spent_time'])?$map['spent_time']:$this->_spent_time;
        $map['done_ratio']  		 = isset( $map['done_ratio'])?$map['done_ratio']:$this->_done_ratio;
        $map['detail_summary']  		 = isset( $map['detail_summary'])?$map['detail_summary']:$this->_detail_summary;
        $map['rel_digest']  		 = isset( $map['rel_digest'])?$map['rel_digest']:$this->_rel_digest;
        $map['rel_tasks']  		 = isset( $map['rel_tasks'])?$map['rel_tasks']:$this->_rel_tasks;
		
        $addId  = $this->where( 'id = '.$taskid )->save( $map );


        if( !$result && !empty( $friendsId ) ) {
            return false;
        }

        return $addId;

    }

    /**
     * updateAuto
     * 更新任务的列表
     * @param mixed $map
     * @param mixed $id
     * @access public
     * @return void
     */
    public function updateAuto( $map,$id ) {
        $outline = self::factoryModel( 'outline' );
        return $outline->doUpdateOutline( $map,$id );

    }
    /**
     * autosave
     * 自动保存
     * @param mixed $map
     * @access public
     * @return void
     */
    public function autosave( $map ) {
        $outline = self::factoryModel( 'outline' );
        return $outline->doAddOutline( $map );
    }
    /**
     * getConfig
     * 获取配置
     * @param mixed $index
     * @access public
     * @return void
     */
    public function getConfig( $index ) {
        $config = $this->config->$index;
        return $config;
    }


    /**
     * unsetConfig
     * 删除配置
     * @param mixed $index
     * @param mixed $group
     * @access public
     * @return void
     */
    public function unsetConfig( $index , $group = null ) {
        if( isset( $group ) ) {
            unset( $this->config->$group->$index );
        }else {
            unset( $this->config->$index );
        }
        return $this;
    }

    /**
     * DateToTimeStemp
     * 时间换算成时间戳返回
     * @param mixed $stime
     * @param mixed $etime
     * @access public
     * @return void
     */
    public function DateToTimeStemp( $stime,$etime ) {
        $stime = strval( $stime );
        $etime = strval( $etime );

        //如果输入时间是YYMMDD格式。直接换算成时间戳
        if( isset( $stime[7] ) && isset( $etime[7] ) ) {
        //开始时间
            $syear  = substr( $stime,0,4 );
            $smonth = substr( $stime,4,2 );
            $sday   = substr( $stime,6,2 );
            $stime  = mktime( 0, 0, 0, $smonth,$sday,$syear );

            //结束时间
            $eyear  = substr( $etime,0,4 );
            $emonth = substr( $etime,4,2 );
            $eday   = substr( $etime,6,2 );
            $etime  = mktime( 0, 0, 0, $emonth,$eday,$eyear );

            return array( 'between',array( $stime,$etime ) );
        }

        //如果输入时间是YYYYMM格式
        $start_temp   = $this->paramData( $stime );
        $end_temp     = $this->paramData( $etime );
        $start        = $start_temp[0];
        $end          = $end_temp[1];

        return array( 'between',array( $start,$end ) );
    }

    public function getTaskTitle( $uid ) {
        $map['uid'] = $uid;
        $map = $this->merge( $map );
        return $this->where( $map )->field( 'title,id' )->order( 'cTime desc' )->limit( "0,10" )->findAll();
    }

    /**
     * checkGetSubscribe
     * 检查和返回以注册过的订阅源
     * @param mixed $uid
     * @access public
     * @return void
     */
    public function checkGetSubscribe( $uid ) {
        $subscribe  = $this->factoryModel( 'subscribe' );
        $map['uid'] = $uid;
        $source_id  = $subscribe->getSourceId( $map );

        unset( $map );

        $source    = $this->factoryModel( 'source' );
        if( empty($source_id))
            return false;
        $map['id'] = array( 'in',$source_id );
        $result    = $source->getSource( $map );

        //重组数据,根据服务名和用户名重组链接
        foreach ( $result as &$value ) {
            switch( $value['service'] ) {
                case "163":
                    $link = "http://%s.task.163.com/rss/";
                    break;
                case "sohu":
                    $link = "http://%s.task.sohu.com/rss";
                    break;
                case "baidu":
                    $link = "http://hi.baidu.com/%s/rss/";
                    break;
                case "sina":
                    $link = "http://task.sina.com.cn/rss/%s.xml";
                    break;
                case "msn":
                    $link = "http://%s.spaces.live.com/feed.rss";
                    break;
                default:
                    $link = $value['service'];
            //throw new ThinkException( "系统异常" );
            }
            $value['link'] = sprintf( $link,$value['username'] );
        //unset ( $value['service'] );
        //unset( $value['username'] );
        }
        return $result;
    }

    /**
     * doIsHot
     * 设置推荐
     * @param mixed $map
     * @param mixed $act
     * @access public
     * @return void
     */
    public function doIsHot( $map,$act ) {
        if( empty($map) ) {
            throw new ThinkException( "不允许空条件操作数据库" );
        }
        switch( $act ) {
            case "recommend":   //推荐
                $field = array( 'isHot','rTime' );
                $val = array( 1,time() );
                $result = $this->setField( $field,$val,$map );
                break;
            case "cancel":   //取消推荐
                $field = array( 'isHot','rTime' );
                $val = array( 0,0 );
                $result = $this->setField( $field,$val,$map );
                break;

        }
        return $result;
    }

    /**
     * replace
     * 对数据集进行追加处理
     * @param array $data 数据集
     * @param array $mention 需要被追加的值
     * @access protected
     * @return void
     */
    protected function replace( $data,$mentiondata = null ) {
        $result         = $data;
        $categoryname   = $this->getCategory(null);  //获取所有的分类

        //如果$mention为空就需要从数据库中取出数据
        if ( empty( $mentiondata ) ) {
            $mention        = self::factoryModel( 'mention' );
            $mentioncontent = $mention->getUserMention();

        }
        //TODO 配置信息,截取字数控制

        foreach ( $result as &$value ) {
            if(3 == $value['private']) {
               // if(Cookie::get($value['id'].'password') == $value['private_data']) {
               //     $value['private'] = 0;
               // }   Change
            }
            $value['content']  = str_replace( "&amp;nbsp;","",h($value['content']));
//            $value['category'] = array(
//                "name" => $categoryname[$value['category']]['name'],
//                "id"   => $value['category']); //替换任务类型

            //追加任务中提到的内容
            $value['mention'] = !isset( $mentiondata )?
                $mentioncontent[$value['id']]:
                $mentiondata[$value['id']];
            //任务截断
            $short = $this->config->titleshort == 0 ? 4000: $this->config->titleshort;
            
            $suffix = (StrLenW( $value['content'] ) > $short) ? $this->config->suffix : '';
            $value['content'] = getTaskShort( $value['content'], $short ) . $suffix;

            //任务标题
            $value['title'] = stripslashes( $value['title'] );
        }
        return $result;
    }


    /**
     * changeType
     * 将数组中的数据转换成指定类型
     * @param mixed $data
     * @param mixed $type
     * @access private
     * @return void
     */
    private static function changeType( $data , $type ) {
        $result = $data;

        switch( $type ) {
            case 'int':
                $method = "intval";
                break;
            case 'string':
                $method = "strtval";
                break;
            default:
                throw new ThinkException( '暂时只能转换数组和字符串类型' );
        }
        foreach ( $result as &$value ) {
            is_numeric( $value ) && $value = $method( $value );
        }
        return $result;
    }


    private function replaceSpecialChar($code) {
        $code = str_replace("&amp;nbsp;", "", $code);

        $code = str_replace("<br>", "", $code);

        $code = str_replace("<br />", "", $code);

        $code = str_replace("<P>",  "", $code);

        $code = str_replace("</P>","",$code);

        return trim($code);
    }
    /**
     * _orderDate
     * 解析任务排序时间区段
     * @param mixed $options
     * @access private
     * @return void
     */
    private function _orderDate( $options ) {
        if('all' == $options) return array('lt',time());
        $now_year  = intval( date( 'Y',time() ) );
        $now_month = intval( date( 'n',time() ) );
        $now_day   = intval( date( 'j',time() ) );

        //定义偏移量
        $month = self::_getExcursion($options, 'month');
        $year = self::_getExcursion($options, 'year');
        $day = self::_getExcursion($options, 'day');

        //换算时间戳
        $toDate = mktime( 0,0,0,$now_month-$month,$now_day-$day,$now_year-$year );
        //返回数组型数据集
        return array( "between",array( $toDate,time() ) );
    }
    private static function _getExcursion($options,$field){
        $excursion = array(
                            'one'   => array('month'=>1),
                           'three' => array('month'=>3),
                           'half'  => array('month'=>6),
                           'year'  => array('year'=>1),
                           'oneDay'=> array('day'=>1),
                           'threeDay'=>array('day'=>3),
                           'oneWeek'=>array('day'=>7),
                           );
        return isset($excursion[$options][$field])?$excursion[$options][$field]:0;
    }
}
?>

