

<?php
/**
 * ArrayWrite 
 * 数组形式配置
 * @uses ConfigWrite
 * @package 
 * @version $id$
 * @copyright 2009-2011 SamPeng 
 * @author SamPeng <sampeng87@gmail.com> 
 * @license PHP Version 5.2 {@link www.sampeng.cn}
 */
class ArrayWrite extends ConfigWrite {        
    /**
     * _filename 
     * 文件名
     * @var mixed
     * @access protected
     */
    protected $_filename = null;

    /**
     * _exclusiveLock 
     * 是否独占一个文件。默认为false
     * @var mixed
     * @access protected
     */
    protected $_exclusiveLock = false;

    /**
     * setFilename 
     * 设置文件名
     * @param string $filename 
     * @access public
     * @return void
     */
    public function setFilename ( $filename ){
        $this->_filename = $filename;

        return $this;
    }

    /**
     * setExclusiveLock 
     * 设置是否独占文件
     *
     * @param boolean $exclusiveLock 
     * @access public
     * @return void
     */
    public function setExclusiveLock( $exclusiveLock ){
        $this->_exclusiveLock = $exclusiveLock;

        return $this;
    }

    public function write ( $filename = null, Config $config = null, $exclusiveLock = null ){
        if( null !== $filename ){
            $this->setFilename( $filename );
        }

        if( null !== $config ){
            $this->setConfig( $config );
        }

        if( null !== $exclusiveLock ){
            $this->setExclusiveLock( $exclusiveLock );
        }

        if( null === $this->_filename ){
            require_once '/Exception/Exception.php';
            throw new MPFConfigException( "没有设置文件名" );
        }

        if( null === $this->_filename ){
            require_once 'MPF/Core/Config/Exception.php';
            throw new MPFConfigException( "没有设置配置" );
        }

        $data = $this->_config->toArray();
        $sectionName = $this->_config->getSectionName();

        if( is_string( $sectionName ) ){
            $data = array( $sectionName=>$data );
        }

        $arrayString = "<?php\n". "return " . var_export( $data,true ) . ";\n";
        $flags = 0;

        if( $this->_exclusiveLock ){
            $flags |= LOCK_EX; 
        }

        $file = new SplFileObject( $this->_filename,'w+');
        $file->setFlags( $flags );
        if( $file->isWritable() ) {
            $result = $file->fwrite( $arrayString );
        }else{
            require_once 'MPF/Core/Config/Exception.php';
            throw new MPFConfigException( "文件不可写" );
        }
        return $result;
        
    }
}
?>

