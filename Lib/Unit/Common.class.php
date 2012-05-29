
<?php
    /**
     * Common 
     * 常用的公共方法
     * @package 
     * @version $id$
     * @copyright 2009-2011 SamPeng 
     * @author SamPeng <sampeng87@gmail.com> 
     * @license PHP Version 5.2 {@link www.sampeng.cn}
     */
    class Common {


        /**
         * array_my_diff 
         * 把$array中的$arr的key去掉
         * @param mixed $arr 
         * @param mixed $array 
         * @access public
         * @return void
         */
        public static function array_my_diff( $arr,$array ){
            $result = array(  );
            foreach ( $arr as $value ){
                if( is_array( $value ) ){
                    $array_my_diff( $arr,$value );
                }
                $temp_array = array_diff_key($value, array_flip($array));
                $result[]=array_diff_key($value,$temp_array);

            }
            return $result;
        }


        /**
         * mergeArray 
         * 迭代合并数组
         * <code>
         * $a = array( "a"=>"123","b"=>"321", "c"=>array( "ddd"=>"d","ccc"=>"d" ) );
         * $b = array( "c"=>array( "123"=>"123123123","333"=>"dsfsdfsdf" ),"d"=>"444" );
         * var_dump( mergeOptions( $a,$b ) );
         * </code>
         * @param array $array1 
         * @param mixed $array2 
         * @access public
         * @return void
         */
        public static function mergeArray(array $array1,$array2 = null  ){
            if ( is_array( $array1 ) ){
                foreach( $array2 as $key => $val ){
                    if (is_array( $val )) {
                        $array1[$key] = ( array_key_exists( $key,$array1 ) && is_array( $array1[$key] ) )? self::mergeArray( $array1[$key],$val ) : $array2[$key] ;
                    }else{
                        $array1[$key] = $val;
                    }
                }
            }
            return $array1;
        }


        /**
         * insertSort 
         * 插入排序。适合少量数据的排序。
         * @param mixed $array 
         * @access public
         * @return false  如果不是数组
         * @return array  返回数组
         */
        public static function insertSort( $array ){
            if ( !is_array( $array ) ){
                return false;
            }
            for ( $j = 1;$j < count( $array );$j++ ){
                $key = $array[$j];
                $i   = $j - 1;
                while ( $i >= 0 && $array[$i] > $key ){
                    $array[$i + 1] = $array[$i];
                    $i = $i -1;
                }
                $array[$i + 1] = $key;

            }

            return $array;

        }

        /**
         * OrderStatistics 
         * 分治法：求数组$array中,第K大的值问题
         * @param mixed $array 数组
         * @param mixed $p1 开始位置
         * @param mixed $p2 数组大小
         * @param mixed $k 第几小
         * @access public
         * @return void
         */
        public static function OrderStatistics( $array,$p1,$p2,$k ){
            $p = 0;
            $num = 0;
            if ( $k < 1 || $k > ($p2-$p1+1) ) return -1;//k超出范围时返回特殊标记
            if ( $p1 >= $p2  ) return $array[$p1];//若a[p1]--a[p2]只有一个元素。则返回该元素
            $p = self::Partition( $array, $p1, $p2 );  //求出分割点

            $num = $p - $p1;   //求划分点p前面到p1的元素个数

            if ( $k == ($num + 1) ) return $array[$p];  //如果k就在分割点，则直接返回分割点
            if ( $k <= $num ) return self::OrderStatistics( $array,$p1,$p-1,$k ) ;  //k在第一个分割部分

            return self::OrderStatistics( $array,$p + 1, $p2, $k - $num -1 );  //k在后面的分割部分
        }
        /**
         * Partition 
         * 求出分割点
         * @param mixed $array 
         * @param mixed $p1 
         * @param mixed $p2 
         * @access private
         * @return void
         */
        private static function Partition( &$array,$p1,$p2 ){
            $i = $p1;
            $j = $p2;
            $x = $array[ $i ];
            while ( $i < $j ){
                while ( $array[$j] >=$x &&  $i < $j ) $j --;

                if ( $i < $j ){
                    $array[$i] = $array[$j];
                    $i ++;
                }

                while ( $array[$i] <= $x && $i < $j  ) $i++;

                if( $i < $j ){
                    $array[$j] = $array[$i];
                    $j--;
                } 
            }
            $array[$i] = $x;
            return $i;
        }

        /**
         * changeType
         * 将数组中的数据转换成指定类型
         * @param mixed $data
         * @param mixed $type
         * @access private
         * @return void
         */
        public static function changeType( $data , $type ){
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
                is_numeric( $value ) && $value = $method( $value );
            }
            return $result;
        }

    }
?>

