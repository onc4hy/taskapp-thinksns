<?php
class TaskStatisticsModel extends Model {
	
	public function statistics() {
		$app_alias		= getAppAlias('task');
    	$task_count		= M('task')->where('`status`=1')->count();
    	$recycle_count	= M('task')->where('`status`=2')->count();
    	return array(
    		"{$app_alias}数"	=> $task_count . '篇',
    		'回收站'			=> $recycle_count . '篇',
    	);
	}
}?>

