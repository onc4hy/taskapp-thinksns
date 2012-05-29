
<?php
    /**
     * AdminAction 
     * 心情管理
     * @uses Action
     * @package Admin
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    import('admin.Action.AdministratorAction');
	  class AdminAction extends AdministratorAction {
        /**
         * task 
         * TaskModel的实例化对象
         * @var mixed
         * @access private
         */
        private $task;

        /**
         * smile 
         * Smile的实例化对象
         * @var mixed
         * @access private
         */
        private $smile;
        /**
         * config 
         * TaskConfig的实例化对象
         * @var mixed
         * @access private
         */
        private $config;

        private $category;
        /**
         * _initialize 
         * 初始化
         * @access public
         * @return void
         */
        public function _initialize(){
        	parent::_initialize();
        	
        	$this->config = D( 'AppConfig' );
            $this->task  = D( 'Task' );
        }
        /**
         * basic 
         * 基础设置管理
         * @access public
         * @return void
         */
        public function index (){
            $config   = Common::changeType( $this->config->getConfigData(),"int");
            $this->assign( $config );
            $this->display();
        }

        /**
         * recycle 
         * 回收站
         * @access public
         * @return void
         */
        public function recycle(  ) {
        	//为使搜索条件在分页时也有效，将搜索条件记录到SESSION中
			if ( !empty($_POST) ) {
				$_SESSION['task_admin_search_recycle'] = serialize($_POST);
			}else if ( isset($_GET[C('VAR_PAGE')]) ) {
				$_POST = unserialize($_SESSION['task_admin_search_recycle']);
			}else {
				unset($_SESSION['task_admin_search_recycle']);
			}
			
        	$this->assign('isSearch', isset($_POST['isSearch'])?'1':'0');
        	
            //姓名，uid,任务内容
            //$_POST['name']     && $map['name']    = t( $_POST['name'] );
            $_POST['uid']      && $map['uid']     = intval( t( $_POST['uid'] ) );
            $_POST['content']  && $map['content'] = array( 'like',"%".t( $_POST['content'] )."%" );
            $_POST['title']    && $map['title']   = array( 'like',"%".t( $_POST['title'] )."%" );
            //isset($_POST['isHot']) && $_POST['isHot']!='' && $map['isHot'] = intval( $_POST['isHot'] );

            //处理时间
            //$_POST['stime'] && $_POST['etime'] && $map['cTime'] = $this->task->DateToTimeStemp(t( $_POST['stime'] ),t( $_POST['etime'] ) );

            //处理排序过程
            //$order = isset( $_POST['sorder'] )?t( $_POST['sorder'] )." ".t( $_POST['eorder'] ):"cTime DESC";
            $order = 'cTime DESC';

            $map['status'] = 2;

            $list = $this->task->where( $map )->order( $order )->findPage( 20 );
            $this->assign( $list );
            $this->display();
        }

        /**
         * recycleAction 
         * 回收站动作
         * @access public
         * @return void
         */
        public function recycleMan(  ){
            $act = $_REQUEST['type'];  //动作
            isset($_REQUEST['id']) && $map['id']  = array('in',$_REQUEST['id']);  //任务的id

            switch( $act ){
                case "resume":  //恢复
                    $result = $this->task->setField( 'status',1,$map );
                    break;
                case "delete"://彻底物理删除
                    if( empty( $map ) ){
                        echo -1;
                        exit();
                    }
                    $map['status'] = 2;
                    $result = $this->task->where( $map )->delete();
                    break;
                case "allresume":  //全部恢复
                    $result = $this->task->setField( 'status',1);
                    break;
                case "alldelete"://全部彻底物理删除
                    $map['status'] = 2;
                    $result = $this->task->where( $map )->delete();
                    if( $result ){
                        $this->success( "删除成功" );
                    }
                    break;
                default:
                    echo -1;
                    exit;
                    $this->error( "error_no_action" );
            }

            if( $result ){
                if ( !strpos($_REQUEST['id'],",") ){
                    echo 2;            //说明只是删除一个
                }else{
                    echo 1;            //删除多个
                }
            }else{
                echo -1;
            }

        }
        public function filterUser($var){
            if( 0 != intval($var['uid']) )
                return false;
            return true;
        }
        /**
         * ico 
         * 图像设置
         * @access public
         * @return void
         */
        public function ico (){
            //获取数据库的表情列表
            $smiletype     =  $this->smile->getSmileType() ;
            $this->assign( 'smiletype' , $smiletype );
            $this->display();
        }

        public function smile(){
            $type      = $_REQUEST['type'];
            $smilelist = $this->smile->getSmileFileList($type);
            $path      = __APP__."/Admin/doAddSmile/";

            $this->assign( 'smile_list',$smilelist );
            $this->assign( 'action_path',$path );
            $this->assign( 'smilepath',__PUBLIC__.'/images/biaoqing/'.$type.'/' );
            $this->assign( 'smiletype',$type );
            $this->display(  );
        }

        
        public function category() {
     	    $this->assign( 'category_list',$this->task->getCategory());
            $this->display();
        }
        
        public function addCategory() {
        	$this->display('editCategory');
        }
        
        public function doAddCategory(){
            $data['name'] = t($_POST['title']);
            $data['uid']  = 0;
            if (empty($data['name'])) {
            	echo 0;
            }else {
            	echo intval( M('task_category')->add($data) );
            }
        }
        
        public function editCategory() {
        	$category = M('task_category')->where('id='.intval($_GET['gid']))->find();
        	$this->assign('category', $category);
        	$this->display('editCategory');
        }

        public function doEditCategory() {
            $_POST['title'] = t($_POST['title']);
            if ( empty($_POST['title']) ) {
            	echo 0;
            }else {
            	echo M('task_category')->where('`id`='.intval($_GET['gid']))->setField('name', $_POST['title']) ? '1' : '0';
            }
        }
        
	  	public function doDeleteCategory(){
            echo M('task_category')->where('`id`='.intval($_POST['gid']))->delete() ? '1' : '0';
        }
        
        public function isCategoryExist() {
        	echo D('TaskCategory')->isCategoryExist( t($_POST['title']), 0, intval($_POST['gid']) ) ? '1' : '0';
        }
        
        public function isCategoryEmpty() {
        	echo D('TaskCategory')->isCategoryEmpty(intval($_POST['gid'])) ? '1' : '0';
        }
        
        
        /**
         * tasklist 
         * 获得所有人的tasklist
         * @access public
         * @return void
         */
        public function tasklist (){
	        //为使搜索条件在分页时也有效，将搜索条件记录到SESSION中
			if ( !empty($_POST) ) {
				$_SESSION['task_admin_search'] = serialize($_POST);
			}else if ( isset($_GET[C('VAR_PAGE')]) ) {
				$_POST = unserialize($_SESSION['task_admin_search']);
			}else {
				unset($_SESSION['task_admin_search']);
			}
			
        	$this->assign('isSearch', isset($_POST['isSearch'])?'1':'0');
        	
            //姓名，uid,任务内容
            //$_POST['name']		&& $this->task->name    = t( $_POST['name'] );
            $_POST['uid']		&& $this->task->uid     = intval( t( $_POST['uid'] ) );
            $_POST['content']	&& $this->task->content = array( 'like',"%".t( $_POST['content'] )."%" );
            $_POST['title']		&& $this->task->title   = array( 'like',"%".t( $_POST['title'] )."%" );
            isset($_POST['isHot']) && $_POST['isHot']!=''	&&	$this->task->isHot = intval( $_POST['isHot'] );

            //处理时间
            //$_POST['stime'] && $_POST['etime'] && $this->task->cTime = $this->task->DateToTimeStemp(t( $_POST['stime'] ),t( $_POST['etime'] ) );

            //处理排序过程
            //$order = isset( $_POST['sorder'] )?t( $_POST['sorder'] )." ".t( $_POST['eorder'] ):"cTime DESC";
            $order = 'cTime DESC';
            $list  = $this->task->getTaskList(null,null,$order,20);
            $this->assign( $_POST );
            $this->assign( $list );
            $this->display();
        }

        /**
         * doDeleteTask 
         * 删除mili
         * @access public
         * @return void
         */
        public function doDeleteTask(){
            $taskid['id'] = array( 'in',$_REQUEST['id']);//要删除的id.              
            $result       = $this->task->doDeleteTask($taskid);
            if( false !== $result){
                if ( !strpos($_REQUEST['id'],",") ){
                    echo 2;            //说明只是删除一个
                }else{
                    echo 1;            //删除多个
                }
            }else{
                echo -1;               //删除失败
            }
        }

        /**
         * doChangeBase 
         * 修改全局设置
         * @access public
         * @return void
         */
        public function doChangeBase (){
            $config = $_POST;
            if( $this->config->editConfig($config)){
            	$this->assign('jumpUrl', U('task/Admin/index'));
            	$this->success('保存成功');
            }else{
                $this->error( "保存失败" );
            }
        }

        public function doChangeIsHot(){
            
        	$task['id'] = array( 'in',$_REQUEST['id']);        //要推荐的id.
        	//$task['id'] = array( 'in',$_POST['id']);        //要推荐的id.
            $act  = $_REQUEST['type'];  //推荐动作
			
            $result  = $this->task->doIsHot($task,$act);

            if( false !== $result){
                    echo 1;            //推荐成功
            }else{
                echo -1;               //推荐失败
            }
        }
     
        /**
         * doChangeIco 
         * 删除表情
         * @access public
         * @return void
         */
        public function doChangeIco(){
            $id     = $_POST['id'];
            $idlist = explode( ',',$id );
            if( is_array( $idlist ) ){
                foreach ( $idlist as $value ){
                    $this->task->unsetConfig( $value,'ico' );
                }
                $this->task->getWrite()->write('smaile');
                echo 2;
            }else{
                $this->task->unsetConfig( $id,'ico' );
                $this->task->getWrite()->write('smaile');
                echo 1;
            }
        }

        public function doChangePath(){
            
        }

        /**
         * changeType 
         * 将数组中的数据转换成指定类型
         * @param mixed $data 
         * @param mixed $type 
         * @access private
         * @return void
         */
        private function changeType( $data , $type ){
            $result = $data;

            switch( $type ){
            case 'int':
                $method = "intval";
                break;
            case 'string':
                $method = "strtval";
                break;
            default:
                throw new ThinkException( '暂时只能转换数组和字符串类型' );
            }
            foreach ( $result as &$value ){
                $value = $method( $value );
            }
            return $result;
        }
    }
?>

