
<?php
    /**
     * NewArray
     *
     * @package Config
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class Config extends ArrayObject{

        /**
         * allowModifications 
         * 是否允许修改
         * @var mixed
         * @access private
         */
        private $allowModifications;

        private $count;
        public function __construct( array $array, $allowModifications = false ){

            $this->allowModifications = $allowModifications;

            foreach( $array as &$value ){
                is_array( $value ) && $value = new self( $value,$allowModifications );
            }

            parent::__construct( $array );
        }

        /**
         * __unset 
         * 消费数组元素
         * @param mixed $key 
         * @access public
         * @return void
         */
        public function __unset( $key ){
            $this->offsetUnset( $key );
        }

        /**
         * __get 
         * 获取内容
         * @param mixed $key 
         * @access public
         * @return void
         */
        public function __get( $key ){
            return $this->offsetGet( $key );
        }

        /**
         * __set 
         * 设置数组
         * @param mixed $index 
         * @param mixed $value 
         * @access public
         * @return void
         */
        public function __set( $data,$value ){
                $this->offsetSet( $data,$value );
        }

        /**
         * toSerialize 
         * 序列化输出
         * @access public
         * @return void
         */
        public function toSerialize() {
            return serialize( $this->getArrayCopy() );
        }

        /**
         * toArray 
         * 以数组方式输出
         * @access public
         * @return void
         */
        public function toArray(){
            $array = $this->getArrayCopy();
            foreach( $array as &$value ){
                ( $value instanceof self ) && $value = $value->toArray();
            }
            return $array;
        }

        /**
         * toString 
         * 以字符串方式输出
         * @access public
         * @return void
         */
        public function toString(){
            return var_export( $this->toArray(), true );
        }

        public function append( $value ){
            if( is_array( $value ) ){
                parent::append( new self( $value ) );
            }else{
                parent::append( $value );
            }
            return $this;
        }

        /**
         * setReadOnly 
         * 设置只读
         * @access public
         * @return void
         */
        public function setReadOnly(){
            $this->allowModifications = false;
            foreach( $this as $value ){
                if( $value instanceof Config ){
                    $value->setReadOnly();
                }
            }
        }

        /**
         * readOnly 
         * 是否只读
         * @access public
         * @return void
         */
        public function readOnly(){
            return !$this->allowModifications;
        }

        /**
         * mergeArray 
         * 迭代合并数组
         * <code>
         * $a = array( "data"=>"321" );
         * var_dump( mergeOptions( $a,$b ) );
         * </code>
         * @param array $array1 
         * @access public
         * @return void
         */
        public function mergeArray(Config $merge){
            foreach ( $merge as $key =>$item ){
                if( array_key_exists( $key,$this->toArray() ) ){
                    if( $item instanceof Config && $this->$key instanceof Config ){
                        $this->$key = $this->$key->merge( new self( $item->toArray(), !$this->readOnly() ) );
                    } else {
                        $this->$key = $item;
                    }
                }else{
                    if( $item instanceof Config ){
                        $this->$key = new self( $item->toArray(), $this->readOnly() );
                    }else{
                        $this->$key = $item;
                    }
                }
            }
            return $this;
        }
    }

?>

