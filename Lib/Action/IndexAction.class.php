
<?php
/**
 * IndexAction
 * task的Action.接收和过滤网页传参
 * @uses Action
 * @package
 * @version $id$
 * @copyright houya
 * @author houya <onc4hy@gmail.com>
 * @license PHP Version 5.2
 */
class IndexAction extends Action {
        private $filter;
        private $task;
        private $taskNotice;
        private $taskNote;
        private $lasttask;
        private static $friends=array();
        /**
         * __initialize
         * 初始化
         * @access public
         * @return void
         */
        public function _initialize() {
        	//parent::_initialize();

			//设置任务Action的数据处理层
            $this->task = D( 'Task' );
            $this->taskNotice = D( 'TaskNotices' );
            $this->taskNote = D( 'TaskNotes' );
            $this->follow=D('Follow');
        }
        protected $app = null;
        /**
         * index
         * 好友的任务
         * @access public
         * @return void
         */
		 
        public function index() {

			//获得任务数据集,自动获得当前登录用户的好友任务
			$list = $this->__getTask(null,'*','cTime desc');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );
			
			//检查是否可以查看全部任务
			if( $this->__checkAllModel() ) {
					$list = $this->task->getAllData('popular');
					$relist= $this->task->getIsHot();
					$this->assign('relist',$relist);
					$this->assign( 'api',$this->api);
					$this->assign( 'uid',$this->mid );
					$this->assign( 'order',$_GET['order'] );
					$this->assign( $list );
					$this->assign( 'all','true' );
					global $ts;
					$this->setTitle("热门任务}");
					$this->display();

			}else {
					$this->error( L( 'error_all' ) );
			}
			//是否是好友,需要api辅助
        }
		
		// 极简列表
        public function minimallist() {

			//获得任务数据集,自动获得当前登录用户的好友任务
			$list = $this->__getTask(null,'*','cTime desc');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );
			
			//检查是否可以查看全部任务
			if( $this->__checkAllModel() ) {
					$list = $this->task->getAllData('popular');
					$relist= $this->task->getIsHot();
					$this->assign('relist',$relist);
					$this->assign( 'api',$this->api);
					$this->assign( 'uid',$this->mid );
					$this->assign( 'order',$_GET['order'] );
					$this->assign( $list );
					$this->assign( 'all','true' );
					global $ts;
					$this->setTitle("热门任务}");
					$this->display();

			}else {
					$this->error( L( 'error_all' ) );
			}
			//是否是好友,需要api辅助
        }

        /**
         * search
         * 搜索任务
         * @access public
         * @return void
         */
        public function search() {
			$keyword	=	h($_GET['key']);
			//获得任务数据集,自动获得当前登录用户的好友任务
			$map['title']  = array('like',"%{$keyword}%");
			if($keyword)
				$list = $this->task->getTaskList($map,'*','cTime desc',10);

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

			$relist= $this->task->getIsHot();
			$this->assign('relist',$relist);
			$this->assign( 'api',$this->api);
			$this->assign( 'uid',$this->mid );
			$this->assign( $list );
			$this->assign( 'all','true' );
			$this->setTitle("搜索文章: ".$keyword);
			$this->display();


			//是否是好友,需要api辅助
        }

        /**
         * my
         * 我的任务
         * @access public
         * @return void
         */
        public function my() {
        	//获得任务数据集
            $outline = D( 'TaskOutline' );
            $list    = isset( $_GET['outline'] )?
            	$outline->getList( $this->mid ): //草稿箱
            	$this->__getTask( $this->mid,'*','cTime desc' ); //我的任务

            foreach($list['data'] as $k => $v) {
            	if ( empty($v['category']['name']) && !empty($v['category']['id']) )
            		$list['data'][$k]['category']['name'] = M('task_category')->where('id='.$v['category']['id'])->getField('name');
            }

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

            //归档数据
            $url = isset( $_GET['cateId'] )? 'Index&act=my&cateId='.$_GET['cateId']:'Index&act=my';
            $file_away = $this->_getWiget( $url,$this->mid );

            //获得分类的计数
            $category = $this->__getTaskCategoryCount($this->mid);

            //草稿箱计数
            $outline = D( 'TaskOutline' )->where( 'uid ='.$this->mid )->count();

            //检查是否可以查看全部任务
            $this->__checkAllModel();

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );
			
			$relist= $this->task->getIsHot();
            $this->assign('relist',$relist);
            //获得归档传输数据
            $this->assign( 'oc',$outline );
            $this->assign( 'file_away',$file_away );
            $this->assign('category',$category);
            $this->assign( $list );
            global $ts;
            $this->setTitle("我的{$ts['app']['app_alias']}");
            $this->display('index');
        }

        public function news() {

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

	        //检查是否可以查看这个页面
			if( $this->__checkAllModel() ) {
    			$list = $this->task->getAllData('new');
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);
                $this->assign( 'api',$this->api);
                $this->assign( 'uid',$this->mid );
                $this->assign( 'order',$_GET['order'] );
                $this->assign( $list );
                $this->assign( 'all','true' );
                global $ts;
                $this->setTitle("最新{$ts['app']['app_alias']}");
                $this->display('index');
            }else {
            	$this->error( L( 'error_all' ) );
            }
        }
        public function followstask() {

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

        	//检查是否可以查看这个页面
        	$mid=$this->mid;
            if( $this->__checkAllModel() ) {
            	$list = $this->task->getFollowsTask($mid);
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);
                $this->assign( 'api',$this->api);
                $this->assign( 'uid',$this->mid );
                $this->assign( 'order',$_GET['order'] );
                $this->assign( $list );
                $this->assign( 'all','true' );
                global $ts;
                $this->setTitle("我的关注的人的{$ts['app']['app_alias']}");
                $this->display('index');
            }else {
            	$this->error( L( 'error_all' ) );
			}
        }
        private function __checkAllModel() {
        	return true;

        	//获取配置，是否可以查看全部的任务
            if( $this->task->getConfig( 'all' ) ) {
            	$this->assign( 'all','true' );
                return true;
            }
            return false;
        }


        /**
         * show
         * 任务显示页
         * @access public
         * @return void
         */
        public function show() {
				unset($_SESSION['task_use_widget_share']);
        //获得任务id
                $id      = $_GET['id'];
                $this->task->setUid( $this->mid );

                //全站任务
                if( $this->task->getConfig( 'all' ) ) {
                        $this->assign( 'all','true' );
                }


                //任务所有者
                $taskuid = $_GET['mid'];


                //获得任务的详细内容,第二参数通知是当前还是上一篇下一篇
                isset( $_GET['action'] ) && $how = $_GET['action'];
                $list     = $this->task->getTaskContent($id,$how,$taskuid);

                //检测是否有值。不允许非正常id访问
                if( false == $list ) {
                		$this->assign('jumpUrl',U('task/Index'));
                        $this->error( '任务不存在或者已删除！' );
                }
                 //Converts special HTML entities back to characters.
                $list['content'] = htmlspecialchars_decode($list['content']);
                
                //获得正确的当前任务ID
                $id = $list['id'];
                //是否是好友
                $this->assign( 'isFriend',friend_areFriends( $taskuid,$this->mid ) );

                //检测密码
                if (isset($_POST['password'])) {
                        if(md5(t($_POST['password'])) == $list['private_data']) {
                                Cookie::set($id.'password',md5(t($_POST['password'])));
                                $list['private'] = 0;
                        }

                } else {
                        if( 3 == $list['private'] && Cookie::get($id.'password') == $list['private_data']) {
                                $list['private'] = 0;
                        }
                }

                //不是任务所有人读任务才会刷新阅读数.只有非任务发表人才进行阅读数刷新
                if( !empty( $taskuid ) && $this->mid != $taskuid ) {
                        $options = array( 'id'=>$id,'uid'=>$this->mid,'type'=>APP_NAME,'lefttime'=>"30" );
                        //浏览计数，防刷新
                        //if(  browseCount( APP_NAME,$id,$this->mid,'30') ) {
                                $this->task->changeCount( $id );
                        //}
                }


                //获取发表人的id
                $name          = $this->task->getOneName( $taskuid );

                //他人任务渲染特殊的变量和数据
                if( $this->mid != $taskuid ) {
                //查看这篇任务，访问者是否推荐过
                        $recommend = D( 'TaskMention' )->checkRecommend( $this->mid,$list['id'] );

                        //如果是其它人的任务。需要获得最新的10条任务
                        $tasklist  = $this->task->getTaskTitle( $list['uid'] );
                        $this->assign( 'tasklist',$tasklist );
                        $this->assign( 'recommend',$recommend );
                }

                //渲染公共变量
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);
                $this->assign( $list );
                $this->assign( 'task', $list );
                $this->assign( 'guest',$this->mid );
                $this->assign( 'name',$name['name'] );
                $this->assign( 'uid',$taskuid );
                $this->assign('isOwner', $this->mid == $taskuid ? '1' : '0');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

				// 获取对象明细内容
				$detailList = $this->task->getDetailList($id);
				$this->assign( 'task_detail_list',$detailList );
				
				// 获取对象子内容
				$childList = $this->task->getChildList($id);
				$this->assign( 'task_child_list',$childList );
				
                global $ts;
                $this->setTitle(getUserName($list['uid']).'的文章: '.$list['title']);
                $this->display('taskContent');
        }

        /**
         * personal
         * 个人的任务列表
         * @access public
         * @return void
         */
        public function personal() {
        //获得任务数据集
                $uid   = intval($_GET['uid']);
                if($uid <= 0)
                	$this->error('参数错误');

                //获得task的列表
                $list             = $this->__getTask($uid,'*','cTime desc');

                //获得分类的计数
                $category = $this->__getTaskCategoryCount($uid);

                //归档数据
                $url       = isset( $GET['cateId'] )?
                    'Index/personal/uid/'.$uid.'/cateId/'.$_GET['cateId']:
                    'Index/personal/uid/'.$uid;
                $file_away = $this->_getWiget( $url,$uid);

                //组装数据
                $this->assign( 'file_away',$file_away );
                $this->assign('api',$this->api);

                $this->assign('category',$category);
                $name = getUserName($uid);
                $this->assign('name', $name);
                $this->assign( $list );

                global $ts;
                $this->setTitle($name . '的' . $ts['app']['app_alias']);
                $this->display('index');
        }

       /**
         * doDeletetask
         * 删除task
         * @access public
         * @return void
         */
        public function doDeletetask(  ) {

                $this->task->id = $_REQUEST['id']; //要删除的id;
                $result         = $this->task->doDeletetask(null,$this->mid);

                if( false != $result) {
					X('Credit')->setUserCredit($this->mid,'delete_task');
                    redirect( U('task/Index/my') );
                }else {
                    $this->error( "删除任务失败" );
                }
        }

        /**
         * deleteCategory
         * 删除分类
         * @access public
         * @return void
         */
        public function deleteCategory(  ) {
                $data['id'] = intval($_POST['id']);
                if( 0 === $data['id'] )
                        return false;

                //删除分类和将分类的任务转移到其它分类里
                isset( $_POST['toCate'] ) && !empty( $_POST['toCate'] ) && $toCate   = $_POST['toCate'];

                $category   = D( 'TaskCategory' );
                return $category->deleteCategory( $data,$toCate,$this->task );
        }

        /**
         * addTask
         * 添加task
         * @access public
         * @return void
         */
        public function addTask() {

                $category  = $this->task->getCategory($this->mid);
                $savetime  = $this->task->getConfig( 'savetime' );

                //表情控制
                $smile     = array();
                $smileType = $this->opts['ico_type'];
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

				//$smileList = $this->getSmile($smileType);
                //$smilePath = $this->getSmilePath($smileType);
                $this->assign( 'smileList',$smileList );
                $this->assign( 'smilePath',$smilePath );
                $this->assign( 'savetime',$savetime );
                $this->assign( 'task_category',$category );
                global $ts;
                $this->setTitle("发布任务");
                $this->display();
        }

        /**
         * addTask
         * 添加task
         * @access public
         * @return void
         */
        public function addAjaxTask() {
				$use = intval($_POST['used']);
                $category  = $this->task->getCategory($this->mid);
                $savetime  = $this->task->getConfig( 'savetime' );

                //表情控制
                $smile     = array();
                $smileType = $this->opts['ico_type'];
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );
			
				$this->assign( 'savetime',$savetime );
                $this->assign( 'category',$category );
                if($use){
                	$this->display('addAjaxTask_used');
                }else{
                	 $this->display();
                }

        } 

        public function edit() {
                $category_list = $this->task->getCategory($this->mid);
                $this->assign( 'task_category_list',$category_list );
				
			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

                $id = $_GET['id'];
                if( $_GET['edit'] ) {
                        $outline = D( 'TaskOutline' );
                        //检查是否存在这篇任务
                        if( false == $list = $outline->getTaskContent( $id,null,$_GET['mid']))
                                $this->error( L( 'error_no_task' ) );
                        //是否有权限修改本篇任务
                        //TODO 管理员
                        if( $list['uid'] != $this->mid ) {
                                $this->error( L( 'error_no_role' ) );
                        }

                        //处理提到的好友的格式数据
                        $mention = array_filter(unserialize( $list['friendId'] ));
                        if( !empty($mention) ) {
                                $friends = $this->api->user_getInfo( $mention,'id,name' );

                                foreach ( $friends as &$value ) {
                                        $value['uid'] = $value['id'];
                                        unset( $value['id'] );
                                }

                                $list['mention'] = $friends;
                        }else {
                                $list['mention'] = null;
                        }

                        $list['saveId'] = $list['id'];
                        unset( $list['id'] );

                        //定义连接
                        $link = __URL__."&act=doAddTask";
                        unset ( $list['friendId'] );
                //编辑新的任务
                }else {
                        $link = __URL__."&act=doUpdate";
                        $dao = $this->task;

                        if( false == $list = $this->task->getTaskContent( $id,null,$_GET['mid'] ))
                                $this->error( L( 'error_no_task' ) );

                        //是否有权限修改本篇任务
                        //TODO 管理员
                        if( $list['uid'] != $this->mid )
                                $this->error( L( 'error_no_role' ) );
                }

                foreach ( $list['mention'] as &$value ) {
                        $value['face']  = getUserFace( $value['uid'] );
                }

                $list['mention'] = json_encode( $list['mention'] );
				                         $relist= $this->task->getIsHot();
                        $this->assign('relist',$relist);
                 //表情控制
//                $smile     = array();
//                $smileType = $this->opts['ico_type'];
//
//
//                $smileList = $this->getSmile($smileType);
//                $smilePath = $this->getSmilePath($smileType);
//                $this->assign( 'smileList',$smileList );
//                $this->assign( 'smilePath',$smilePath );

				// 获取对象明细内容
				$detailList = $this->task->getDetailList($id);
				$this->assign( 'task_detail_list',$detailList );
				
				// 获取对象子内容
				$childList = $this->task->getChildList($id);
				$this->assign( 'task_child_list',$childList );

                $this->assign( 'link',$link );
                $this->assign( $list );
                $this->display();
        }

        /**
         * doAddtask
         * 添加task
         * @access public
         * @return void
         */
        public function doAddTask() {
            $title = text($_POST['title']);


        	if(empty($title)) {
            	$this->error( "请填写标题" );
            }
        		if( mb_strlen($title, 'UTF-8') > 25 ) {
					$this->error( "标题不得大于25个字符" );
                }

                //$content = text(html_entity_decode($_POST['content']));
                $content = $_POST['content'];

                //检查是否为空
                if( empty($_POST['content']) || empty( $content )  ) {
                        $this->error( "请填写内容" );
                }

                //得到发布任务人的名字
                $userName = $this->task->getOneName( $this->mid );

                //处理发布任务的数据
                $data = $this->__getPost();
                //添加任务
                $add = $this->task->doAddTask($data,true);

                //如果是有自动保存的数据。删除自动保存数据
                if( isset( $_POST['saveId'] ) && !empty( $_POST['saveId'] ) ) {
                        $mention = D( 'TaskOutline' );
                        $mention->where( 'id = '.$_POST['saveId'] )->delete();
                }

                if( $add ) {
					X('Credit')->setUserCredit($this->mid,'add_task');
					$this->assign('jumpUrl', U('task/Index/show',array('id'=>$add,'mid'=>$this->mid)));
					$html = '【'.text($data['title']).'】'.getShort($content,80).U('task/Index/show',array('id'=>$add,'mid'=>$this->mid));
					$images = matchImages($data['content']);
					$image  = $images[0]?$images[0]:false;

					$this->ajaxData = array('url'=>U('task/Index/show',array('id'=>$add,'mid'=>$this->mid)),
						'id' =>$add,
					    'html'=>$html,
					    'image'=>$image,
						'title'=>t($_POST['title']),
					);
					$this->success('发表成功');
                }else {
                    $this->error( "添加失败" );
                }
        }

        /**
         * doUpdate
         * 执行更新任务动作
         * @access public
         * @return void
         */
        public function doUpdate() {
        		if (empty($_POST['title'])) {
                    $this->error( "请填写标题" );
                }
        		if (mb_strlen($_POST['title'], 'UTF-8') > 25 ) {
                	$this->error( "标题不能大于25个字符" );
                }
                //$content = h($_POST['content']);
                $content = $_POST['content'];

                if( empty( $content ) ) {
                    $this->error( "请填写内容" );
                }

                $userName = $this->task->getOneName( $this->mid );

                $id       = intval($_POST['id']);
                //检查更新合法化
                if( $this->task->where( 'id = '.$id )->getField( 'uid' ) != $this->mid ) {
                        $this->error( L('error_no_role') );
                }
                $data = $this->__getPost();
                $save = $this->task->doSaveTask($data,$id);

                if ($save) {
                    redirect(U('task/Index/show', array('id'=>$id, 'mid'=>$this->mid)));
                } else {
                    $this->error( "修改失败" );
                }
        }

        public function doAddDetail() {
			$data = array();
			$data['task_id'] = $_POST['task_id'];
			$data['task_id'] = $_POST['task_id'];
			$data['behavior_type'] = $_POST['behavior_type'];
			$data['start_date'] = $_POST['start_date'];
			$data['start_time'] = $_POST['start_time'];
			$data['stop_date'] = $_POST['stop_date'];
			$data['stop_time'] = $_POST['stop_time'];
			$data['spent_time'] = $_POST['spent_time'];
			$data['done_ratio'] = $_POST['done_ratio'];
			$data['description'] = $_POST['description'];
			//添加任务Detail
			$add = $this->task->doAddDetail($data,true);
			header("Content-Type:text/html;charset=utf8");
			echo("{result:'success',message:''}");
			exit();
        }
		
        public function doRemoveDetail() {
			$data = array();
			$data['id'] = $_POST['id'];
			//添加任务Detail
			$effectRows = $this->task->doRemoveDetail($data,true);
			header("Content-Type:text/html;charset=utf8");
			echo("{result:'success',message:''}");
			exit();
        }
		
        public function doUpdateDetail() {
			$data = array();
			$data['task_id'] = $_POST['task_id'];
			$data['task_id'] = $_POST['task_id'];
			$data['behavior_type'] = $_POST['behavior_type'];
			$data['start_date'] = $_POST['start_date'];
			$data['start_time'] = $_POST['start_time'];
			$data['stop_date'] = $_POST['stop_date'];
			$data['stop_time'] = $_POST['stop_time'];
			$data['spent_time'] = $_POST['spent_time'];
			$data['done_ratio'] = $_POST['done_ratio'];
			$data['description'] = $_POST['description'];
			//添加任务Detail
			$add = $this->task->doUpdateDetail($data,true);
			header("Content-Type:text/html;charset=utf8");
			echo("{result:'success',message:''}");
			exit();
        }
		
		
        public function doRemoveChild() {
			$data = array();
			$data['id'] = $_REQUEST['id'];
			$data['parent_id'] = 0;
			$this->task->doRemoveChild($data);
			header("Content-Type:text/html;charset=utf8");
			echo("{result:'success',info:'',data:''}");
			exit();
        }
		
		
        private function __getPost() {
        		//得到发布任务人的名字
                $userName = $this->task->getOneName( $this->mid );
                $data['name']     = $userName['name'];
                //$data['content']  = safe($_POST['content']);
                $data['content']  = $_POST['content'];
                $data['uid']      = $this->mid;
                $data['category'] = intval($_POST['category']);
                $data['password'] = text($_POST['password']);
                $data['mention']  = $_POST['fri_ids'];
                $data['title']    = !empty($_POST['title']) ?text($_POST['title']):"无标题";
                $data['private']  = text( $_POST['private'] );
                $data['canableComment'] = intval(t($_POST['cc']));

                //处理attach数据
                $data['attach']         = serialize($this->__wipeVerticalArray($_POST['attach']));
                if(empty($_POST['attach']) || !isset($_POST['attach'])) {
                        $data['attach'] = null;
                }
				
				$data['share_type'] = $_POST['share_type'];
				$data['share_source'] = $_POST['share_source'];
				$data['share_target'] = $_POST['share_target'];
				$data['parent_id'] = $_POST['parent_id'];
				$data['level'] = $_POST['level'];
				$data['task_type'] = $_POST['task_type'];
				$data['task_status'] = $_POST['task_status'];
				$data['task_priority'] = $_POST['task_priority'];
				$data['start_date'] = $_POST['start_date'];
				$data['due_date'] = $_POST['due_date'];
				$data['estimated_time'] = $_POST['estimated_time'];
				$data['spent_time'] = $_POST['spent_time'];
				$data['done_ratio'] = $_POST['done_ratio'];
				$data['detail_summary'] = $_POST['detail_summary'];
				$data['rel_digest'] = $_POST['rel_digest'];
				$data['rel_tasks'] = $_POST['rel_tasks'];
					if(!empty($data['parent_id'])) {
						$data['level'] = 2;
					}else{
						$data['level'] = '';
					}
				
                return $data;
        }

        private function __getNotePost() {
        		//得到发布任务人的名字
                $userName = $this->task->getOneName( $this->mid );
                $data['uid']      = $this->mid;
                $data['name']     = $userName['name'];
                $data['content']  = safe($_POST['content']);
                $data['category'] = intval($_POST['category']);
                $data['password'] = text($_POST['password']);
                $data['mention']  = $_POST['fri_ids'];
                $data['title']    = !empty($_POST['title']) ?text($_POST['title']):"无标题";
                $data['private']  = text( $_POST['private'] );
                $data['canableComment'] = intval(t($_POST['cc']));

				$data['mTime'] = time();
				
                //处理attach数据
                $data['attach']         = serialize($this->__wipeVerticalArray($_POST['attach']));
                if(empty($_POST['attach']) || !isset($_POST['attach'])) {
                        $data['attach'] = null;
                }
				
                return $data;
        }

        private function __getNoticePost() {
        		//得到发布任务人的名字
                $userName = $this->task->getOneName( $this->mid );
                $data['uid']      = $this->mid;
                $data['name']     = $userName['name'];
                $data['content']  = safe($_POST['content']);
                $data['category'] = intval($_POST['category']);
                $data['password'] = text($_POST['password']);
                $data['mention']  = $_POST['fri_ids'];
                $data['title']    = !empty($_POST['title']) ?text($_POST['title']):"无标题";
                $data['private']  = text( $_POST['private'] );
                $data['canableComment'] = intval(t($_POST['cc']));

				$data['mTime'] = time();
				
                //处理attach数据
                $data['attach']         = serialize($this->__wipeVerticalArray($_POST['attach']));
                if(empty($_POST['attach']) || !isset($_POST['attach'])) {
                        $data['attach'] = null;
                }
				
                return $data;
        }

        private function __wipeVerticalArray($array) {
                $result = array();
                foreach($array as $key=>$value) {
                        $temp = explode('|', $value);
                        $result[$key]['id'] = $temp[0];
                        $result[$key]['name'] = $temp[1];
                }
                return $result;

        }

        /**
         * autoSave
         * 自动保存
         * @access public
         * @return void
         */
        public function autoSave(  ) {
                $content = trim(str_replace('&amp;nbsp;','',t($_POST['content'])));
                //检查是否为空
                if( empty($_POST['content']) || empty( $content )  ) {
                        $this->error( "请填写内容" );
                        exit();
                }

                $add="";
                $userName = $this->task->getOneName( $this->mid );

                //处理数据
                $data['name']     = $userName['name'];
                $data['content']  = $_POST['content'];
                $data['uid']      = $this->mid;
                $data['category'] = $_POST['category'];
                $data['password'] = $_POST['password'];
                $data['mention']  = $_POST['mention'];
                $data['title']    = !empty($_POST['title']) ?$_POST['title']:"无标题";
                $data['private']  = $_POST['privacy'];
                $data['canableComment'] = intval(t($_POST['cc']));
				
				$data['share_type'] = $_POST['share_type'];
				$data['share_source'] = $_POST['share_source'];
				$data['share_target'] = $_POST['share_target'];
				$data['parent_id'] = $_POST['parent_id'];
				$data['level'] = $_POST['level'];
				$data['task_type'] = $_POST['task_type'];
				$data['task_status'] = $_POST['task_status'];
				$data['task_priority'] = $_POST['task_priority'];
				$data['start_date'] = $_POST['start_date'];
				$data['due_date'] = $_POST['due_date'];
				$data['estimated_time'] = $_POST['estimated_time'];
				$data['spent_time'] = $_POST['spent_time'];
				$data['done_ratio'] = $_POST['done_ratio'];
				$data['detail_summary'] = $_POST['detail_summary'];
				$data['rel_digest'] = $_POST['rel_digest'];
				$data['rel_tasks'] = $_POST['rel_tasks'];
				
                if( isset( $_POST['updata'] ) ) {
                //更新数据，而不是添加新的草稿
                        $add = intval(trim($_POST['updata']));
                        $result = $this->task->updateAuto( $data,$add );
                }else {
                //自动保存
                        $add = $this->task->autoSave($data);
                }
                if( $add || $result) {
                        echo date('Y-m-d h:i:s',time()).",".$add;
                }else {
                        echo -1;
                }
        }

        /**
         * outline
         * 草稿箱
         * @access public
         * @return void
         */
        public function outline(  ) {
                $this->assign( $list );
                $this->display();
        }

        /**
         * deleteOutline
         * 删除
         * @access public
         * @return void
         */
        public function deleteOutline(  ) {
                if( empty($_POST['id']) ) {
                        echo -1;
                        return;
                }


                $map['id'] = array( "in",array_filter( explode( ',' , $_POST['id'] ) ));
                $outline = D( 'TaskOutline' );
                //检查合法性
                if( $outline->where( $map )->getField( 'uid' ) != $this->mid ) {
                        echo -1;
                }

                if( $result = $outline->where( $map )->delete() ) {
                        echo 1;
                }else {
                        echo -1;
                }
        }

        /**
         * taskImport
         * 外站任务导入
         * @access public
         * @return void
         */
        public function taskImport() {
        //检测和返回本登录用户已经订阅的源地址
                $subscribe = $this->task->checkGetSubscribe( $this->mid );

                $this->assign( "subscribe",$subscribe );
                $this->display();
        }

        /**
         * importList
         * 导入任务的列表
         * @access public
         * @return void
         */
        public function importList() {
                Import( '@.Unit.LeadIn' );
                $url = $_REQUEST['url'];
                //解析url。确定服务名和用户名
                $paramUrl = $this->_paramUrl($url);
                if ( false == $paramUrl ) $this->error( "URL解析失败" );
                $options = array(
                    "username" => $paramUrl['username'],
                    "service"  => $paramUrl['service'],
                );
                if(!is_string($paramUrl['username']) || !is_string($paramUrl['username'])){
                	    $this->error( "用户名必须为字符串" );
                        return false ;
                }
                $lead = new LeadIn( $options );
                //采集站点任务
                $result = $lead->get_source_data( $this->mid );
                if( false === $result ) {
                        $this->error( "此格式任务URL暂不支持，请检查链接" );
                        return false ;
                }

                //调用私有方法处理得到已经采集到但未导入的任务
                $importTask = $this->_getImportData( $result );
                $category   = $this->task->getCategory( $this->mid );



                //显示数据，供用户选择
                $this->assign( "importTask",$importTask );
                $this->assign( "category",$category );

                $this->display();

        }


        /**
         * doUpdateImport
         * 更新任务列表
         * @access public
         * @return void
         */
        public function doUpdateImport() {
                $sourceId = $_POST['id'];

                //根据源id获得服务名和用户名
                $map['id']                          = $sourceId[0];
                count( $sourceId ) >1 && $map['id'] = array('in',implode(",",$sourceId));
                $source_data                        = D( 'TaskSource' )->getSource( $map );
                //根据结果集进行更新采集
                Import( '@.Unit.LeadIn' );
                $leadIn = new LeadIn();

                $result = $leadIn->update_data( $source_data );

                //调用私有方法处理得到已经采集到但未导入的任务
                $importTask = array();
                if( count( $sourceId ) >1 ) {
                        foreach ( $result as $value ) {
                                $temp = $this->_getImportData( $value );
                                if( empty($temp) ) {
                                        continue;
                                }
                                $importTask = array_merge($importTask,$temp);
                        }
                }else {
                        $importTask = $this->_getImportData( $result );
                }
                $category   = $this->task->getCategory( $this->mid );


                //显示数据，供用户选择
                $this->assign( "importTask",$importTask );
                $this->assign( "category",$category );

                $this->display('importList');
        }

        /**
         * doDeleteSubscribe
         * 删除订阅源
         * @access public
         * @return void
         */
        public function doDeleteSubscribe() {
                Import( '@.Unit.LeadIn' );

                $sourceId = array_filter( explode( ',' , $_POST['sourceId'] ) );

                if( empty($sourceId) ) {
                        echo -1;
                        exit;
                }
                $leadIn = new LeadIn();
                if( $leadIn->deleSubscribe( $sourceId,$this->mid ) ) {
                        echo 1;
                        exit;
                }

        }
        /**
         * doImport
         * 执行导入任务到本地任务数据库
         * @access public
         * @return void
         */
        public function doImport() {
                $id        = $_POST['id'];
                //从item取出数据信息

                $map['id'] = array( 'in',$id );
                $task      = D( 'TaskItem' )->getItem( $map,'*' );
                unset( $map );
                foreach( $id as $key=>$value ) {
                        $map['title']    = $task[$key]['title'];
                        $map['cTime']    = $task[$key]['pubdate'];
                        $map['type']     = $task[$key]['sourceId'];
                        $map['uid']      = $this->mid;
                        $name            = $this->task->getOneName( $this->mid );
                        $map['content']  = $task[$key]['summary'];
                        $map['name']     = $name['name'];
                        $map['category'] = $_POST["class_".$value];
                        $map['private']  = $_POST["privacy_".$value];
                        $result[$value] = $this->task->doAddTask( $map,false );
                        $feedTitle[] = $result[$value]['title'];
                        $result[$value] = $result[$value]['appid'];
                }
                //发送动态
                $title['count'] = count($feedTitle);
                $feedTitle = array_slice($feedTitle,0,3);
                $body['title'] = implode('<br />', $feedTitle);
                $title['uid'] = $this->mid;

                $this->task->doFeed("task_import",$title,$body);
                if( !empty( $result ) ) {
                //删除已删除的
                        D( 'TaskItem' )->deleteImportTask( $result );
                        $this->redirect( 'my','Index' );
                }

        }

        /**
         * admin
         * 个人管理页面
         * @access public
         * @return void
         */
        public function admin() {
        	//获得分类名称
        	//获得分类下的任务数
            $category   = $this->__getTaskCategoryCount( $this->mid );
            $relist		= $this->task->getIsHot();
            $this->assign('relist',$relist);
            $this->assign( 'category',$category );
            global $ts;
            $this->setTitle("{$ts['app']['app_alias']}管理");
            $this->display();
        }


        /**
         * deleteCateFrame
         * 删除分类时，转移其中的任务
         * @access public
         * @return void
         */
        public function deleteCateFrame(  ) {
                $id       = $_GET['id'];
                $category = $this->task->getCategory( $this->mid );
                foreach( $category as $key=>$value ) {
                        if( $value['id'] == $id)
                                unset( $category[$key] );
                }
                $this->assign( 'category',$category );
                $this->display();

        }
        /**
         * addCategory
         * 添加分类
         * @access public
         * @return void
         */
        public function addCategory() {
                $data['name'] = h(t($_POST['name']));
                $data['uid']  = $this->mid;

                $category   = D( 'TaskCategory' );
                $result = $category->addCategory($data,$this->task);
        }

        public function addCategorys() {
                $this->display();
        }


        /**
         * editCategory
         * 修改分类
         * @access public
         * @return void
         */
        public function editCategory() {
        	foreach($_POST['name'] as $k => $v)
        		$_POST['name'][$k] = h(t($v));

        	if ( count($_POST['name']) != count(array_unique($_POST['name'])) )
        		$this->error('分类名不允许重复, 请重新输入');

			$category = D( 'TaskCategory' );
            $result   = $category->editCategory( $_POST['name'] );

            // 更新任务信息
            foreach ($_POST['name'] as $k => $v) {
            	M('task')->where("`category`='{$k}'")->setField('category_title', $v);
            }

            $this->assign('jumpUrl', U('task/Index/admin'));
            $this->success('保存成功');
        }

        /**
         * TODO 删除
         * recommend
         * 推荐操作
         * @access public
         * @return void
         */
        public function recommend(  ) {
                $name          = $this->task->getOneName($this->mid);
                $map['taskid'] = $_POST['id'];
                $map['uid']    = $this->mid;
                $map['name']   = $name['name'];
                $map['type']   = "recommend";
                $action        = $_POST['act'];

                //添加推荐和推荐人数据。并且更新任务的推荐数
                if( $result = D( 'TaskMention' )->addRecommendUser( $map,$action ) ) {
                        echo 1;
                }else {
                        echo -1;
                }
        }

        /**
         * TODO 删除
         */
        public function commentSuccess() {
        //$post = str_replace('\\', '', stripslashes($_POST['data']));
                $result = json_decode(stripslashes($_POST['data']));  //json被反解析成了stdClass类型
                $count = $this->__setTaskCount($result->appid);

                //发送两条消息
                $data = $this->__getNotifyData($result);
                $this->api->comment_notify('task',$data,$this->appId);
                echo $count;
        }

		// 便签列表
        public function notes() {

			//获得任务便签数据集
			//$list = $this->__getTaskNotes($this->mid,'*','cTime desc');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );
			
			//检查是否可以查看全部任务便签
			if( $this->__checkAllModel() ) {
					$list = $this->task->getAllNotesData();
					$relist= $this->task->getIsHot();
					$this->assign('relist',$relist);
					$this->assign( 'api',$this->api);
					$this->assign( 'uid',$this->mid );
					$this->assign( 'order',$_GET['order'] );
					$this->assign( $list );
					$this->assign( 'all','true' );
					global $ts;
					$this->setTitle("任务便签");
					$this->display();

			}else {
					$this->error( L( 'error_all' ) );
			}
        }
		
        public function myNotes() {

			//获得任务便签数据集
			$list = $this->__getNotes($this->mid,'*','cTime desc');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );
			
			//检查是否可以查看全部任务便签
			if( $this->__checkAllModel() ) {
					$list = $this->task->getAllNotesData();
					$relist= $this->task->getIsHot();
					$this->assign('relist',$relist);
					$this->assign( 'api',$this->api);
					$this->assign( 'uid',$this->mid );
					$this->assign( 'order',$_GET['order'] );
					$this->assign( $list );
					$this->assign( 'all','true' );
					global $ts;
					$this->setTitle("任务便签");
					$this->display("notes");

			}else {
					$this->error( L( 'error_all' ) );
			}
        }
		
        public function personalNotes() {
        //获得任务数据集
                $uid   = intval($_GET['uid']);
                if($uid <= 0)
                	$this->error('参数错误');

                //获得task的列表
                $list             = $this->__getNotes($uid,'*','cTime desc');

                //获得分类的计数
                $category = $this->__getTaskCategoryCount($uid);

                //归档数据
                $url       = isset( $GET['cateId'] )?
                    'Index/personal/uid/'.$uid.'/cateId/'.$_GET['cateId']:
                    'Index/personal/uid/'.$uid;
                $file_away = $this->_getWiget( $url,$uid);

                //组装数据
                $this->assign( 'file_away',$file_away );
                $this->assign('api',$this->api);

                $this->assign('category',$category);
                $name = getUserName($uid);
                $this->assign('name', $name);
                $this->assign( $list );

                global $ts;
                $this->setTitle($name . '的' . $ts['app']['app_alias']);
                $this->display('notes');
        }

		// 显示便签
		public function showNote() {
				unset($_SESSION['task_use_widget_share']);
        //获得任务id
                $id      = $_GET['id'];
                $this->task->setUid( $this->mid );

                //全站任务
                if( $this->task->getConfig( 'all' ) ) {
                        $this->assign( 'all','true' );
                }


                //便签所有者
                $noteUid = $_GET['mid'];


                //获得任务的详细内容,第二参数通知是当前还是上一篇下一篇
                isset( $_GET['action'] ) && $how = $_GET['action'];
				if(empty($how)) {
	                $list     = $this->taskNote->where("id=".$id)->find();
				}else{
					$map['id'] = array($how,$id);
	                $list     = $this->taskNote->where($map)->find();
				}
				$count     = $this->taskNote->where("uid=".$noteUid)->count();
				$list['count'] = $count;
				$num     = $this->taskNote->where("uid=".$noteUid." and id <=".$id)->count();
				$list['num'] = $num;

                //检测是否有值。不允许非正常id访问
                if( false == $list ) {
                		$this->assign('jumpUrl',U('task/Index'));
                        $this->error( '任务不存在或者已删除！' );
                }
                 //Converts special HTML entities back to characters.
                $list['content'] = htmlspecialchars_decode($list['content']);
                
                //获得正确的当前任务ID
                $id = $list['id'];
                //是否是好友
                $this->assign( 'isFriend',friend_areFriends( $noteUid,$this->mid ) );

                //检测密码
                if (isset($_POST['password'])) {
                        if(md5(t($_POST['password'])) == $list['private_data']) {
                                Cookie::set($id.'password',md5(t($_POST['password'])));
                                $list['private'] = 0;
                        }

                } else {
                        if( 3 == $list['private'] && Cookie::get($id.'password') == $list['private_data']) {
                                $list['private'] = 0;
                        }
                }

                //不是任务所有人读任务才会刷新阅读数.只有非任务发表人才进行阅读数刷新
                if( !empty( $noteUid ) && $this->mid != $noteUid ) {
                        $options = array( 'id'=>$id,'uid'=>$this->mid,'type'=>APP_NAME,'lefttime'=>"30" );
                        //浏览计数，防刷新
                        //if(  browseCount( APP_NAME,$id,$this->mid,'30') ) {
                                $this->taskNote->changeCount( $id );
                        //}
                }


                //获取发表人的id
                //$name          = $this->taskNote->getOneName( $noteUid );
				$uname = getUserName($noteUid);

                //他人任务渲染特殊的变量和数据
                if( $this->mid != $noteUid ) {
                //查看这篇任务，访问者是否推荐过
                        $recommend = D( 'TaskMention' )->checkRecommend( $this->mid,$list['id'] );

                        //如果是其它人的任务。需要获得最新的10条任务
                        $tasklist  = $this->taskNote->getNoteTitle( $list['uid'] );
                        $this->assign( 'tasklist',$tasklist );
                        $this->assign( 'recommend',$recommend );
                }

                //渲染公共变量
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);
                $this->assign( $list );
                $this->assign( 'task', $list );
                $this->assign( 'guest',$this->mid );
                $this->assign( 'name',$name['name'] );
                $this->assign( 'uname',$uname );
                $this->assign( 'uid',$this->mid );
                $this->assign('isOwner', $this->mid == $noteUid ? '1' : '0');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

                global $ts;
                $this->setTitle(getUserName($list['uid']).'的便签: '.$list['title']);
                $this->display('noteContent');
        }
		
 		// 添加便签
        public function addNote() {

                $category  = $this->task->getCategory($this->mid);
                $savetime  = $this->task->getConfig( 'savetime' );

                //表情控制
                $smile     = array();
                $smileType = $this->opts['ico_type'];
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

				//$smileList = $this->getSmile($smileType);
                //$smilePath = $this->getSmilePath($smileType);
                $this->assign( 'smileList',$smileList );
                $this->assign( 'smilePath',$smilePath );
                $this->assign( 'savetime',$savetime );
                $this->assign( 'task_category',$category );
                global $ts;
                $this->setTitle("发布便签");
                $this->display();
        }
		
		// 编辑便签
        public function editNote() {
                $category_list = $this->task->getCategory($this->mid);
                $this->assign( 'task_category_list',$category_list );
				
			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

                $id = $_GET['id'];
                if( $_GET['edit'] ) {
                        $outline = D( 'TaskOutline' );
                        //检查是否存在这篇任务
                        if( false == $list = $outline->getTaskContent( $id,null,$_GET['mid']))
                                $this->error( L( 'error_no_task' ) );
                        //是否有权限修改本篇任务
                        //TODO 管理员
                        if( $list['uid'] != $this->mid ) {
                                $this->error( L( 'error_no_role' ) );
                        }

                        //处理提到的好友的格式数据
                        $mention = array_filter(unserialize( $list['friendId'] ));
                        if( !empty($mention) ) {
                                $friends = $this->api->user_getInfo( $mention,'id,name' );

                                foreach ( $friends as &$value ) {
                                        $value['uid'] = $value['id'];
                                        unset( $value['id'] );
                                }

                                $list['mention'] = $friends;
                        }else {
                                $list['mention'] = null;
                        }

                        $list['saveId'] = $list['id'];
                        unset( $list['id'] );

                        //定义连接
                        $link = __URL__."&act=doAddNote";
                        unset ( $list['friendId'] );
                //编辑新的任务
                }else {
                        $link = __URL__."&act=doUpdateNote";
                        $dao = $this->taskNote;

                        if( false == $list = $dao->where("id=".$id)->find())
                                $this->error( L( 'error_no_task' ) );

                        //是否有权限修改本篇任务
                        //TODO 管理员
                        if( $list['uid'] != $this->mid )
                                $this->error( L( 'error_no_role' ) );
                }

                foreach ( $list['mention'] as &$value ) {
                        $value['face']  = getUserFace( $value['uid'] );
                }

                $list['mention'] = json_encode( $list['mention'] );
				                         $relist= $this->task->getIsHot();
                        $this->assign('relist',$relist);
                 //表情控制
//                $smile     = array();
//                $smileType = $this->opts['ico_type'];
//
//
//                $smileList = $this->getSmile($smileType);
//                $smilePath = $this->getSmilePath($smileType);
//                $this->assign( 'smileList',$smileList );
//                $this->assign( 'smilePath',$smilePath );

                $this->assign( 'link',$link );
                $this->assign( $list );
                $this->display();
        }

		// 执行添加便签
        public function doAddNote() {
            $title = text($_POST['title']);


        	if(empty($title)) {
            	$this->error( "请填写标题" );
            }
        		if( mb_strlen($title, 'UTF-8') > 25 ) {
					$this->error( "标题不得大于25个字符" );
                }

                $content = text(html_entity_decode($_POST['content']));

                //检查是否为空
                if( empty($_POST['content']) || empty( $content )  ) {
                        $this->error( "请填写内容" );
                }

                //得到发布任务人的名字
                $userName = $this->task->getOneName( $this->mid );

                //处理发布任务的数据
                $data = $this->__getNotePost();
				$data['cTime'] = time();
                //添加任务
                $add = $this->taskNote->add($data);

                //如果是有自动保存的数据。删除自动保存数据
                if( isset( $_POST['saveId'] ) && !empty( $_POST['saveId'] ) ) {
                        $mention = D( 'TaskOutline' );
                        $mention->where( 'id = '.$_POST['saveId'] )->delete();
                }

                if( $add ) {
					X('Credit')->setUserCredit($this->mid,'add_task');
					$this->assign('jumpUrl', U('task/Index/show',array('id'=>$add,'mid'=>$this->mid)));
					$html = '【'.text($data['title']).'】'.getShort($content,80).U('task/Index/showNote',array('id'=>$add,'mid'=>$this->mid));
					$images = matchImages($data['content']);
					$image  = $images[0]?$images[0]:false;

					$this->ajaxData = array('url'=>U('task/Index/showNote',array('id'=>$add,'mid'=>$this->mid)),
						'id' =>$add,
					    'html'=>$html,
					    'image'=>$image,
						'title'=>t($_POST['title']),
					);
               		$this->assign('jumpUrl',U('task/Index/notes'));
					$this->success('发表成功');
                }else {
                    $this->error( "添加失败" );
                }
        }

		// 执行修改便签
        public function doUpdateNote() {
        		if (empty($_POST['title'])) {
                    $this->error( "请填写标题" );
                }
        		if (mb_strlen($_POST['title'], 'UTF-8') > 25 ) {
                	$this->error( "标题不能大于25个字符" );
                }
                $content = h($_POST['content']);

                if( empty( $content ) ) {
                    $this->error( "请填写内容" );
                }

                $userName = $this->task->getOneName( $this->mid );

                $id       = intval($_POST['id']);
                //检查更新合法化
                if( $this->taskNote->where( 'id = '.$id )->getField( 'uid' ) != $this->mid ) {
                        $this->error( L('error_no_role') );
                }
                $data = $this->__getNotePost();
				$data['id'] = $id;
                $save = $this->taskNote->save($data);

                if ($save) {
                    redirect(U('task/Index/showNote', array('id'=>$id, 'mid'=>$this->mid)));
                } else {
                    $this->error( "修改失败" );
                }
        }

		// 执行删除便签
		public function doDeleteNote() {

                $id = $_REQUEST['id']; //要删除的id;
                $result         = $this->taskNote->where("id=".$id)->delete();

                if( false != $result) {
					X('Credit')->setUserCredit($this->mid,'delete_note');
                    redirect( U('task/Index/notes') );
                }else {
                    $this->error( "删除任务便签失败" );
                }
        }
		
		// 通告
        public function notices() {

			//获得任务通告数据集
			//$list = $this->__getTaskNotices($this->mid,'*','cTime desc');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );
			
			//检查是否可以查看全部任务通告
			if( $this->__checkAllModel() ) {
					$list = $this->task->getAllNoticesData();
					$relist= $this->task->getIsHot();
					$this->assign('relist',$relist);
					$this->assign( 'api',$this->api);
					$this->assign( 'uid',$this->mid );
					$this->assign( 'order',$_GET['order'] );
					$this->assign( $list );
					$this->assign( 'all','true' );
					global $ts;
					$this->setTitle("任务通告");
					$this->display();

			}else {
					$this->error( L( 'error_all' ) );
			}
        }
		
        public function myNotices() {

			//获得任务便签数据集
			$list = $this->__getNotices($this->mid,'*','cTime desc');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );
			
			//检查是否可以查看全部任务便签
			if( $this->__checkAllModel() ) {
					$list = $this->task->getAllNoticesData();
					$relist= $this->task->getIsHot();
					$this->assign('relist',$relist);
					$this->assign( 'api',$this->api);
					$this->assign( 'uid',$this->mid );
					$this->assign( 'order',$_GET['order'] );
					$this->assign( $list );
					$this->assign( 'all','true' );
					global $ts;
					$this->setTitle("任务便签");
					$this->display("notes");

			}else {
					$this->error( L( 'error_all' ) );
			}
        }
		
        public function personalNotices() {
        //获得任务数据集
                $uid   = intval($_GET['uid']);
                if($uid <= 0)
                	$this->error('参数错误');

                //获得task的列表
                $list             = $this->__getNotices($uid,'*','cTime desc');

                //获得分类的计数
                $category = $this->__getTaskCategoryCount($uid);

                //归档数据
                $url       = isset( $GET['cateId'] )?
                    'Index/personal/uid/'.$uid.'/cateId/'.$_GET['cateId']:
                    'Index/personal/uid/'.$uid;
                $file_away = $this->_getWiget( $url,$uid);

                //组装数据
                $this->assign( 'file_away',$file_away );
                $this->assign('api',$this->api);

                $this->assign('category',$category);
                $name = getUserName($uid);
                $this->assign('name', $name);
                $this->assign( $list );

                global $ts;
                $this->setTitle($name . '的' . $ts['app']['app_alias']);
                $this->display('notices');
        }

		// 显示通告
		public function showNotice() {
				unset($_SESSION['task_use_widget_share']);
        //获得任务id
                $id      = $_GET['id'];
                $this->task->setUid( $this->mid );

                //全站任务
                if( $this->task->getConfig( 'all' ) ) {
                        $this->assign( 'all','true' );
                }


                //通告所有者
                $noticeUid = $_GET['mid'];


                //获得任务的详细内容,第二参数通知是当前还是上一篇下一篇
                isset( $_GET['action'] ) && $how = $_GET['action'];
				if(empty($how)) {
	                $list     = $this->taskNotice->where("id=".$id)->find();
				}else{
					$map['id'] = array($how,$id);
	                $list     = $this->taskNotice	->where($map)->find();
				}
				$count     = $this->taskNotice->where("uid=".$noticeUid)->count();
				$list['count'] = $count;
				$num     = $this->taskNotice->where("uid=".$noticeUid." and id <=".$id)->count();
				$list['num'] = $num;

                //检测是否有值。不允许非正常id访问
                if( false == $list ) {
                		$this->assign('jumpUrl',U('task/Index'));
                        $this->error( '任务不存在或者已删除！' );
                }
                 //Converts special HTML entities back to characters.
                $list['content'] = htmlspecialchars_decode($list['content']);
                
                //获得正确的当前任务ID
                $id = $list['id'];
                //是否是好友
                $this->assign( 'isFriend',friend_areFriends( $noticeUid,$this->mid ) );

                //检测密码
                if (isset($_POST['password'])) {
                        if(md5(t($_POST['password'])) == $list['private_data']) {
                                Cookie::set($id.'password',md5(t($_POST['password'])));
                                $list['private'] = 0;
                        }

                } else {
                        if( 3 == $list['private'] && Cookie::get($id.'password') == $list['private_data']) {
                                $list['private'] = 0;
                        }
                }

                //不是任务所有人读任务才会刷新阅读数.只有非任务发表人才进行阅读数刷新
                if( !empty( $noticeUid ) && $this->mid != $noticeUid ) {
                        $options = array( 'id'=>$id,'uid'=>$this->mid,'type'=>APP_NAME,'lefttime'=>"30" );
                        //浏览计数，防刷新
                        //if(  browseCount( APP_NAME,$id,$this->mid,'30') ) {
                                $this->taskNotice->changeCount( $id );
                        //}
                }


                //获取发表人的id
                //$name          = $this->taskNotice->getOneName( $noticeUid );
				$uname = getUserName($noticeUid);

                //他人任务渲染特殊的变量和数据
                if( $this->mid != $noticeUid ) {
                //查看这篇任务，访问者是否推荐过
                        $recommend = D( 'TaskMention' )->checkRecommend( $this->mid,$list['id'] );

                        //如果是其它人的任务。需要获得最新的10条任务
                        $tasklist  = $this->taskNotice->getNoticeTitle( $list['uid'] );
                        $this->assign( 'tasklist',$tasklist );
                        $this->assign( 'recommend',$recommend );
                }

                //渲染公共变量
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);
                $this->assign( $list );
                $this->assign( 'task', $list );
                $this->assign( 'guest',$this->mid );
                $this->assign( 'name',$name['name'] );
                $this->assign( 'uname',$uname );
                $this->assign( 'uid',$this->mid );
                $this->assign('isOwner', $this->mid == $noticeUid ? '1' : '0');

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

                global $ts;
                $this->setTitle(getUserName($list['uid']).'的通告: '.$list['title']);
                $this->display('noticeContent');
        }
		
		// 添加通告
        public function addNotice() {

                $category  = $this->task->getCategory($this->mid);
                $savetime  = $this->task->getConfig( 'savetime' );

                //表情控制
                $smile     = array();
                $smileType = $this->opts['ico_type'];
                $relist= $this->task->getIsHot();
                $this->assign('relist',$relist);

			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );

			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

				//$smileList = $this->getSmile($smileType);
                //$smilePath = $this->getSmilePath($smileType);
                $this->assign( 'smileList',$smileList );
                $this->assign( 'smilePath',$smilePath );
                $this->assign( 'savetime',$savetime );
                $this->assign( 'task_category',$category );
                global $ts;
                $this->setTitle("发布通告");
                $this->display();
        }
		
		// 编辑通告
        public function editNotice() {
                $category_list = $this->task->getCategory($this->mid);
                $this->assign( 'task_category_list',$category_list );
				
			$parentList = $this->task->getParentList($this->mid);
			$this->assign( 'task_parentTask_list',$parentList );
			
			$typeList = $this->task->getTypeList();
			$this->assign( 'task_type_list',$typeList );
			$privateTypeList = $this->task->getPrivateTypeList();
			$this->assign( 'task_privateType_list',$privateTypeList );
			$statusTypeList = $this->task->getStatusTypeList();
			$this->assign( 'task_statusType_list',$statusTypeList );
			$priorityTypeList = $this->task->getPriorityTypeList();
			$this->assign( 'task_priorityType_list',$priorityTypeList );
			$behaviorTypeList = $this->task->getBehaviorTypeList();
			$this->assign( 'task_behaviorType_list',$behaviorTypeList );
			$noticesList = $this->task->getNoticesList();
			$this->assign( 'task_notices_list',$noticesList );
			$notesList = $this->task->getNotesList();
			$this->assign( 'task_notes_list',$notesList );

                $id = $_GET['id'];
                if( $_GET['edit'] ) {
                        $outline = D( 'TaskOutline' );
                        //检查是否存在这篇任务
                        if( false == $list = $outline->getTaskContent( $id,null,$_GET['mid']))
                                $this->error( L( 'error_no_task' ) );
                        //是否有权限修改本篇任务
                        //TODO 管理员
                        if( $list['uid'] != $this->mid ) {
                                $this->error( L( 'error_no_role' ) );
                        }

                        //处理提到的好友的格式数据
                        $mention = array_filter(unserialize( $list['friendId'] ));
                        if( !empty($mention) ) {
                                $friends = $this->api->user_getInfo( $mention,'id,name' );

                                foreach ( $friends as &$value ) {
                                        $value['uid'] = $value['id'];
                                        unset( $value['id'] );
                                }

                                $list['mention'] = $friends;
                        }else {
                                $list['mention'] = null;
                        }

                        $list['saveId'] = $list['id'];
                        unset( $list['id'] );

                        //定义连接
                        $link = __URL__."&act=doAddNotice";
                        unset ( $list['friendId'] );
                //编辑新的任务
                }else {
                        $link = __URL__."&act=doUpdateNotice";
                        $dao = $this->taskNotice;

                        if( false == $list = $dao->where("id=".$id)->find())
                                $this->error( L( 'error_no_task' ) );

                        //是否有权限修改本篇任务
                        //TODO 管理员
                        if( $list['uid'] != $this->mid )
                                $this->error( L( 'error_no_role' ) );
                }

                foreach ( $list['mention'] as &$value ) {
                        $value['face']  = getUserFace( $value['uid'] );
                }

                $list['mention'] = json_encode( $list['mention'] );
				                         $relist= $this->task->getIsHot();
                        $this->assign('relist',$relist);
                 //表情控制
//                $smile     = array();
//                $smileType = $this->opts['ico_type'];
//
//
//                $smileList = $this->getSmile($smileType);
//                $smilePath = $this->getSmilePath($smileType);
//                $this->assign( 'smileList',$smileList );
//                $this->assign( 'smilePath',$smilePath );

                $this->assign( 'link',$link );
                $this->assign( $list );
                $this->display();
        }

		// 执行添加通告
        public function doAddNotice() {
            $title = text($_POST['title']);


        	if(empty($title)) {
            	$this->error( "请填写标题" );
            }
        		if( mb_strlen($title, 'UTF-8') > 25 ) {
					$this->error( "标题不得大于25个字符" );
                }

                $content = text(html_entity_decode($_POST['content']));

                //检查是否为空
                if( empty($_POST['content']) || empty( $content )  ) {
                        $this->error( "请填写内容" );
                }

                //得到发布任务人的名字
                $userName = $this->task->getOneName( $this->mid );

                //处理发布任务的数据
                $data = $this->__getNoticePost();
				$data['cTime'] = time();
                //添加任务
                $add = $this->taskNotice->add($data);

                //如果是有自动保存的数据。删除自动保存数据
                if( isset( $_POST['saveId'] ) && !empty( $_POST['saveId'] ) ) {
                        $mention = D( 'TaskOutline' );
                        $mention->where( 'id = '.$_POST['saveId'] )->delete();
                }

                if( $add ) {
					X('Credit')->setUserCredit($this->mid,'add_notice');
					$this->assign('jumpUrl', U('task/Index/showNotice',array('id'=>$add,'mid'=>$this->mid)));
					$html = '【'.text($data['title']).'】'.getShort($content,80).U('task/Index/showNotice',array('id'=>$add,'mid'=>$this->mid));
					$images = matchImages($data['content']);
					$image  = $images[0]?$images[0]:false;

					$this->ajaxData = array('url'=>U('task/Index/showNotice',array('id'=>$add,'mid'=>$this->mid)),
						'id' =>$add,
					    'html'=>$html,
					    'image'=>$image,
						'title'=>t($_POST['title']),
					);
               		$this->assign('jumpUrl',U('task/Index/notices'));
					$this->success('发表成功');
                }else {
                    $this->error( "添加失败" );
                }
        }

		// 执行修改通告
        public function doUpdateNotice() {
        		if (empty($_POST['title'])) {
                    $this->error( "请填写标题" );
                }
        		if (mb_strlen($_POST['title'], 'UTF-8') > 25 ) {
                	$this->error( "标题不能大于25个字符" );
                }
                $content = h($_POST['content']);

                if( empty( $content ) ) {
                    $this->error( "请填写内容" );
                }

                $userName = $this->task->getOneName( $this->mid );

                $id       = intval($_POST['id']);
                //检查更新合法化
                if( $this->taskNotice->where( 'id = '.$id )->getField( 'uid' ) != $this->mid ) {
                        $this->error( L('error_no_role') );
                }
                $data = $this->__getNoticePost();
				$data['id'] = $id;
                $save = $this->taskNotice->save($data);

                if ($save) {
                    redirect(U('task/Index/showNotice', array('id'=>$id, 'mid'=>$this->mid)));
                } else {
                    $this->error( "修改失败" );
                }
        }

		// 执行删除便签
		public function doDeleteNotice() {

                $id = $_REQUEST['id']; //要删除的id;
                $result         = $this->taskNotice->where("id=".$id)->delete();

                if( false != $result) {
					X('Credit')->setUserCredit($this->mid,'delete_notice');
                    redirect( U('task/Index/notices') );
                }else {
                    $this->error( "删除任务通告失败" );
                }
        }
		
		// 搜索工具方法
        public function topTask() {
			$map = array('parent_id'=>'0');
				
			$list = $this->task->getTaskList($map,'*','cTime desc',10);
			$relist= $this->task->getIsHot();
			$this->assign('relist',$relist);
			$this->assign( 'api',$this->api);
			$this->assign( 'uid',$this->mid );
			$this->assign( $list );
			$this->assign( 'all','true' );
			$this->assign( 'searchTool','topTask' );
			$this->setTitle("顶层任务");
			
			$this->display("search");
        }
		
        public function myTopTask() {
			$map = array('parent_id'=>'0','uid'=>$this->mid);
				
			$list = $this->task->getTaskList($map,'*','cTime desc',10);
			$relist= $this->task->getIsHot();
			$this->assign('relist',$relist);
			$this->assign( 'api',$this->api);
			$this->assign( 'uid',$this->mid );
			$this->assign( $list );
			$this->assign( 'all','true' );
			$this->assign( 'searchTool','myTopTask' );
			$this->setTitle("我的顶层任务");
			
			$this->display("search");
        }
		
		
        private function __getTaskCategoryCount($uid) {
                $cateId = null;
                if(isset($_GET['cateId'])) {
                        $cateId = intval($_GET['cateId']);
                }
                $category = $this->task->getTaskCategory($uid,$cateId);
                if(!$category) {
                        $this->error(L('参数错误'));
                        exit;
                }
                return $category;
        }

        /**
         * TODO 删除
         */
        private function __getNotifyData($data) {
        //发送两条消息
                $result['toUid'] = $data->toUid;
                $need  = $this->task->where('id='.$data->appid)->field('uid,title')->find();


                $result['uids'] =$need['uid'];
                $result['url'] = sprintf('%s/Index/show/id/%s/mid/%s','{'.$this->appId.'}',$data->appid,$result['uids']);
                $result['title_body']['comment'] = $data->comment;
                $result['title_data']['title'] = sprintf("<a href='%s'>%s</a>",$result['url'],$need['title']);
                $result['title_data']['type']  = "任务";
                if(empty($data->toUid) && $this->mid != $need['uid'] && $data->quietly == 0){
                    $title['title'] = $result['title_data']['title'];
                    $uid = $result["uids"];
                    $title['user'] = '<a href="__TS__/space/'.$uid.'">'.getUserName($uid)."</a>";
                    $body['comment'] = $data->comment;
                    $this->task->doFeed('task_comment',$title,$body);
                }
                return $result;
        }
        /**
         * TODO 删除
         */
        public function deleteSuccess() {
                $id = $_POST['id'];
                echo $this->__setTaskCount($id);;
        }

        /**
         * TODO 删除
         */
        private function __setTaskCount($id) {
                $count = $this->api->comment_getCount('task',$id);
                $result = $this->task->setCount($id,$count);
                return $count;
        }

        /**
         * fileAway
         * 获取归档查询的数据
         * @param mixed $uid
         * @access private
         * @return void
         */
        private function fileAway($uid,$cateId = null) {
                $findTime           = $_GET['date']; //获得传入的参数
                $this->task->status = 1;
                $this->task->uid    = $uid;
                isset( $cateId ) && $this->task->category = $cateId;

                return $this->task->fileAway( $findTime ) ;
        }

        /**
         * __gettask
         * 获得task列表
         * @param int|array|string $uid uid
         * @access private
         * @return void
         */
        private function __getTask ($uid=null,$field=null,$order=null,$limit=null) {
        	//将数字或者数字型字符串转换成整型
                is_numeric( $uid ) && $uid = intval( $uid );

                //获取被提到的好友数据列表
                if( isset( $_GET['mention'] ) ) {
                        $this->assign( "mention",1 );
                        return $this->task->getMentionTask($uid);
                }

                //归档
                if( isset( $_GET['date'] ) ) {
                        return $this->fileAway( $uid,$_GET['cateId'] );
                }

                //分类
                if( isset( $_GET['cateId'] ) ) {
                        $this->task->category = $_GET['cateId'];
                        $this->assign( 'cateId',$_GET['cateId'] );
                }

                //给task对象的uid属性赋值
                if( isset( $uid ) ) {
                        $map['uid']   = $uid;
                }else {
                        $gid     = $_GET['gid'];
                       // $friends = $this->api->friend_getGroupUids($gid);
                        if(empty($friends)) return false;
                        $map['uid']  = array( "in",$friends);
                        $this->task->private = array('neq',2);
                }
                return $this->task->getTaskList ($map, $field, $order);
        }

        /**
         * __getNotices
         * 获得task通告列表
         * @param int|array|string $uid uid
         * @access private
         * @return void
         */
        private function __getNotices ($uid=null,$field=null,$order=null,$limit=null) {
        	//将数字或者数字型字符串转换成整型
                is_numeric( $uid ) && $uid = intval( $uid );

                //给task对象的uid属性赋值
                if( isset( $uid ) ) {
                        $map['uid']   = $uid;
                }else {
                        $gid     = $_GET['gid'];
                       // $friends = $this->api->friend_getGroupUids($gid);
                        if(empty($friends)) return false;
                        $map['uid']  = array( "in",$friends);
                        $this->task->private = array('neq',2);
                }
                return $this->task->getNoticesList ($map, $field, $order);
        }

        /**
         * __getNotes
         * 获得task便签列表
         * @param int|array|string $uid uid
         * @access private
         * @return void
         */
        private function __getNotes ($uid=null,$field=null,$order=null,$limit=null) {
        	//将数字或者数字型字符串转换成整型
                is_numeric( $uid ) && $uid = intval( $uid );

                //给task对象的uid属性赋值
                if( isset( $uid ) ) {
                        $map['uid']   = $uid;
                }else {
                        $gid     = $_GET['gid'];
                       // $friends = $this->api->friend_getGroupUids($gid);
                        if(empty($friends)) return false;
                        $map['uid']  = array( "in",$friends);
                        $this->task->private = array('neq',2);
                }
                return $this->task->getNotesList ($map, $field, $order);
        }

        /**
         * _getWiget
         * 获得需要传递给widget的数据
         * @param mixed $link
         * @param mixed $uid
         * @access private
         * @return void
         */
        private function _getWiget($link,$uid) {
                $condition['uid'] = $uid;
                if( empty( $uid) )
                        unset( $condition);
                $map['fileaway']  = L( 'fileaway' );
                $map['link']      = $link;
                $map['condition'] = $condition ;
                $map['limit']     = $this->task->getConfig( 'fileawaypage' );
                $map['tableName'] = C('DB_PREFIX').'_task';
                $map['APP']       = __APP__;
                return $map;
        }

        /**
         * _paramUrl
         * 解析导入的路径
         * @param mixed $url
         * @access private
         * @return void
         */
        private function _paramUrl( $url ) {
        //判断合法性
                if ( false == preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/", $url)) {
                        $this->error( "不是合法的URL格式" );
                        return false;
                }
                $result = array( 'service'=>'','username'=>'' );
                $url      = str_replace( 'http://','',$url );
                $urlarray = explode( '/',$url );
                $target   = array( '163','baidu','spaces','sohu','sina','msn' );
                foreach( $target as $value ) {
                        if( strpos( $urlarray[0],$value ) ) {
                                switch( $value ) {
                                        case '163':
                                                $result['service'] = '163';
                                                $temp = explode( '.',$urlarray[0] );
                                                $result['username'] = $temp[0];
                                                break;
                                        case 'baidu':
                                                $result['service'] = 'baidu';
                                                $result['username'] = $urlarray[1];
                                                break;
                                        case 'sohu':
                                                $result['service'] = 'sohu';
                                                $temp = explode( '.',$urlarray[0] );
                                                $result['username'] = $temp[0];
                                                break;
                                        case 'sina':
                                                $result['service'] = 'sina';
                                                $result['username'] = isset( $urlarray[2] )?$urlarray[2]:$urlarray[1];
                                                break;
                                        case 'msn':
                                                $result['service'] = 'msn';
                                                $result['username'] = $urlarray[1];
                                                break;
                                        case 'spaces':
                                                $result['service'] = 'msn';
                                                $temp = explode( '.',$urlarray[0] );
                                                $result['username'] = $temp[0];
                                                break;
                                        default:
                                                $this->assign( '错误的URL' );
                                                return false;
                                //throw new ThinkException( "错误的url" );
                                }
                        }
                }
                //检测格式。过滤掉特殊格式。防止解析出错
                if( strpos( '.', $result['username'] ) ) {
                        return false;
                }elseif( strpos( '/',$result['username'] ) ) {
                        return false;
                }
                return $result;
        }

        /**
         * _getImportData
         * 获得引入任务的数据
         * @param mixed $result
         * @access private
         * @return void
         */
        private function _getImportData( $result ) {
                if( !empty( $result['change_ids'] ) ) {
                        $map = "`id` IN ( ".implode( ",",$result['change_ids'] )." )";
                }

                if( !empty( $result['change_ids'] ) && !empty( $result['source_id'] ) ) {
                        $map .= " OR ";
                }

                if( !empty( $result['source_id'] ) ) {
                        $map .= '(`sourceId` = '.$result['source_id']." AND `boot` = 0)";
                }

                $item = D( 'TaskItem' );
                $importTask = $item->getItem( $map,'id,link,title' );
                return $importTask;
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
        private static function _checkCategory( $cateId,$category ) {
                $temp = array();
                foreach( $category as $value ) {
                        $temp[] = $value['id'];
                }
                return in_array($cateId,$temp);
        }
        private function _checkUser( $uid ) {
                $result = $this->api->user_getInfo($uid,'id');
                return $result;
        }
}

?>

