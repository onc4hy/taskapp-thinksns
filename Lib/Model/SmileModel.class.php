
<?php
    /**
     * SmileModel 
     * 表情数据库
     * @uses BaseModel
     * @package 
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class SmileModel extends BaseModel{

        /**
         * getSmileList 
         * 获取数据库中的表情列表
         * @param mixed $appname 
         * @access public
         * @return void
         */
        public function getSmileList( $type ){
            $this->type = $type;
            return $this->where()->findAll();
        }

        public function getSmileType(){
            $result = $this->field( 'distinct(type)' )->findAll();
            return $result;
        }

        public function addSmile($data){
            $count = count( $data['title'] );
            for ( $i=0;$i<$count;$i++ ){
                $this->title    = $data['title'][$i];
                $this->emotion  = $data['emotion'][$i];
                $this->filename = $data['filename'][$i];
                $this->type     = $data['type']?$data['type']:'mini';
                $result = $this->add();
            }
            unset( $data );
            //缓存起来
            if( $result ){
               $data = $this->where()->findAll();
               return $this->setCache( $data );
              }else{
                return false;
            }

        }


        /**
         * getSmile 
         * 获得smail数组
         * @param mixed $appname 
         * @access public
         * @return void
         */
        public function getSmile( $type ){
            $smile = ts_cache( "smile_".$type );
            if( $smile ){
                return $smile;
            }else{
               $data = $this->where()->findAll();
               if ($data) {
               	return $this->setCache($data);
               }else{
               	return addSmail(C($type.'ico'));
               }
            }
        }

        /**
         * getSmileFileList 
         * 获得表情文件列表
         * @param mixed $appname 
         * @access public
         * @return void
         */
        public function getSmileFileList($appname){
            $icopath =  __PUBLIC__."/images/biaoqing/".$appname;
            return $this->traversalDir( $icopath );
        }

        public function ChangeEm($map,$options){
            $map = $this->merge( $map );
            if( empty($map) ){
                throw new ThinkException( "不允许空条件修改该" );
            }
            return $this->where( $map )->field( $options )->save();
        }
        /**
         * traversalDir 
         * 遍历目录获得表情.能迭代目录
         * @param mixed $path 目录
         * @access private
         * @return void
         */
        private function traversalDir ( $path ){
            $result = array();
            $file   = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(($path)));
            $i = 0 ;
            foreach ( $file as $key=>$value ) {
                //排除.svn目录的文件
                if( !strpos( $value->getPathname(),".svn" ) && strpos( $value->getFilename(),".gif" ) ){
                    $result[$i]['title'] = '表情'.$i;
                    $result[$i]['filename'] = $value->getFilename();
                    $result[$i]['emotion'] = '';
                    $i++;
                }
            }
            return $result ;
        }

        private function setCache( $data ){
             $cache = array();
               foreach( $data as $value ){
                   $type[] = $value['type'];
                    $cache[$value['type']][$value['id']]['title'] = $value['title'];
                    $cache[$value['type']][$value['id']]['emotion'] = $value['emotion'];
                    $cache[$value['type']][$value['id']]['filename'] = $value['filename'];
               }
             foreach ( $type as $value ){
                $result = ts_cache( 'smile_'.$value,$cache[$value]);
             }
             if( $result  ){
                 return $cache[APP_NAME];
             }
               return false;
        }
    }

?>

