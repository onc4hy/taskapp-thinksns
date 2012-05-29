
<?php
    /**
     * Factory 
     * 工厂类
     * @package 
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class ConfigFactory {
        /**
         * instance 
         * 
         * @var array
         * @access private
         */
        private $instance = array();

        /**
         * config 
         * 配置对象
         * @var array
         * @access private
         */
        private $config;


        public function __construct( array $options = null ){
            if( isset( $options ) ) {
                $this->setOptions( $options );
            }
        }

        /**
         * setInstance 
         * 设置实例化对象
         * @param Model $model 
         * @access public
         * @return void
         */
        public function setInstance( Model $model){
            $classname = strtolower( get_class( $model ) );
            $modelname = str_replace( "model",$classname );
            $this->instance[ $modelname ] = $model;
            return $this;
        }

        /**
         * setConfig 
         * 设置配置管理类
         * @param Config $config 
         * @access public
         * @return void
         */
        public function setConfig( Config $config ){
            $this->config = $config;
            return $this;
        }

        /**
         * setOptions 
         * 批量设置
         * @param array $options 
         * @access public
         * @return void
         */
        public function setOptions(array $options ){
            $option = array_change_key_case( $options,CASE_LOWER );

            //循环设置属性
            foreach ( $option as $key => $value ){
                $action = ucfirst( $key );
                $method = "set".$action;
                //对对象的设置特殊一点
                if( "Instance" == $action ){
                    foreach( $value as $intance ){
                        $this->$method( $intance );
                    }
                    continue;
                }
                $this->$method( $value );
            }
            return $this;
        }

        /**
         * write 
         * 写数据信息
         * @param mixed $instance 
         * @access public
         * @return void
         */
        public function write( $instance ){
            $instance = strtolower( $instance );
            $data     = array();

            switch ( $instance ){
                case "config":
                    $data['delete']   = $this->config->delete;
                    $data['all']      = $this->config->all;
                    $data['fileaway'] = $this->config->fileaway;
                    $data['type']     = $this->config->type->toArray();
                    break;
                case "smaile":
                    $data['ico'] = $this->config->ico;
                    break;
            }
            return $this->instance[$instance]->write( $data );
        }
    }
?>

