
<?php
/**
 * array_my_diff 
 * 把$array中的$arr的key去掉
 * @param mixed $arr 
 * @param mixed $array 
 * @access public
 * @return void
 */
function array_my_diff( $arr,$array ){
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
function mergeArray(array $array1,$array2 = null  ){
	if ( is_array( $array1 ) ){
		foreach( $array2 as $key => $val ){
			if (is_array( $val )) {
				$array1[$key] = ( array_key_exists( $key,$array1 ) && is_array( $array1[$key] ) )? mergeArray( $array1[$key],$val ) : $array2[$key] ;
			}else{
				$array1[$key] = $val;
			}
		}
	}
	return $array1;
}

/**
 * getTaskShort 
 * 截取task的长度
 * @param mixed $content 
 * @param mixed $length 
 * @access public
 * @return void
 */
function getTaskShort($content,$length = 60) {
	$content	=	real_strip_tags($content);
	$content	=	getShort($content,$length);
	return $content;
}



//是否已设置头像
function isSetAvatar($uid){
    return is_file( SITE_PATH.'/data/uploads/avatar/'.$uid.'/small.jpg');
}




//获取微博条数
function getMiniNum($uid){
	return M('weibo')->where('uid='.$uid)->count();
}

//获取关注数
function getUserFollow($uid){
	$count['following'] = M('weibo_follow')->where("uid=$uid AND type=0")->count();
	$count['follower'] = M('weibo_follow')->where("fid=$uid AND type=0")->count();
	return $count;
}

/**
 * StrLenW
 * 计算长度
 * @param mixed $str
 * @access public
 * @return void
 */
function StrLenW($str) {
    $i = 0;
    $count = 0;
    $len = strlen ($str);
    while ($i < $len) {
        $chr = ord ($str[$i]);
        $count++;
        $i++;
        if($i >= $len) break;
        if($chr & 0x80) {
            $chr <<= 1;
            while ($chr & 0x80) {
                $i++;
                $chr <<= 1;
            }
        }
    }
    return $count;

}
function isAddApp() {
	return true;
}

function friend_areFriends() {
	return true;
}

/**
 * browseCount
 * 浏览计数
 * 返回ture为可以计数。返回false为不需要计数
 * @param mixed $type 应用名，请用APP_NAME做参数
 * @param mixed $id 应用ID
 * @param mixed $uid 当前访问者ID
 * @param mixed $lifttime 防刷新时间.单位秒
 * @access public
 * @return true|false
// */
//function browseCount( $type,$id,$uid,$lifttime = 30 ) {
//    $options = array( 'id'=>$id,'uid'=>$uid,'type'=>$type,'lefttime'=>$lifttime );
//    return B( 'Browse',$options );
//}

?>

