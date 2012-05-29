
<?php

require 'Write/ArrayWrite.php';
abstract class ConfigWrite {
    private $_skipOptions = array( 'options' );
    protected $_config    = null;

    /**
     * __construct 
     * 构造器，初始化
     * @access public
     * @return void
     */
	public function __construct(array $options = null) {
        if ( is_array( $options ) ){
            $this->setOptions( $options );
        }
	}

    /**
     * setConfig 
     * 设置配置控制类
     * @param Config $config 
     * @access public
     * @return void
     */
    public function setConfig( Config $config ){
        $this->_config = $config;
    }

    /**
     * setOptions 
     * 设置选项
     * @param array $options 
     * @access public
     * @return void
     */
    public function setOptions(array $options ){
        foreach ( $options as $key=>$value ){
            if( in_array( strtolower( $key ), $this->_skipOptions ) ){
                continue;
            }
            $method = 'set' . ucfirst( $key );
            if( method_exists( $this,$method ) ){
                $this->$method( $value );
            }
        }
        return $this;
    }


    /**
     * write 
     * 写文件操作
     * @abstract
     * @access public
     * @return void
     */
	abstract function write();
}
?>

