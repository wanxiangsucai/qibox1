<?php
namespace app\admin\controller;

use app\common\controller\AdminBase; 

use app\common\util\Database;
use think\Db;

class Mysql extends AdminBase
{
	protected $db;
	
    protected function _initialize()
    {
        parent::_initialize();
		
        $this->db = new Database();

    }
	
	public function index()
    {
		list($totalsize,$listdb2) = $this->db->list_table();
		
		$totalsize=number_format($totalsize/(1024*1024),3);
/*
	@include("tablename.php");$array='';
	foreach($tableName AS $key=>$value){
		$listdb2[$key] && $array[$key]=$listdb2[$key];
	}
	$listdb=$array?$array+$listdb2:$listdb2;

	*/
		if(file_exists(RUNTIME_PATH."bak_mysql.txt"))
	   {
	      $breakbak=read_file(RUNTIME_PATH."bak_mysql.txt");
		  $breakbak=mymd5($breakbak,'DE');
	   }
		
		
		
		return $this->fetch('index',[
		        'listdb'=>$listdb2,
		        'totalsize'=>$totalsize,
		        'breakbak'=>$breakbak
		       
		]);
	}
	
	public function backup()
	{
	    extract(get_post());
        
        if(!$tabledb&&!$tabledbreto){
            showmsg('请选择一个数据表');
        }
        if(!$tabledb&&$tabledbreto){
            $detail=explode("|",$tabledbreto);
            $num=count($detail);
            for($i=0;$i<$num-1;$i++){
                $tabledb[]=$detail[$i];
            }
        }
        empty($tableid) && $tableid=0;
        empty($page) && $page=0;
        empty($step) && $step=0;
        empty($rand_dir) && $rand_dir='';
        
        $rsdb = $this->db->bak_out($tabledb,$rowsnum,$tableid,$page,$step,$rand_dir,$baksize);
        
        $bakdir = RUNTIME_PATH.'mysql_bak/'.$rand_dir;
        
        return $this->fetch('backup',['rsdb'=>$rsdb, 'bakdir'=>$bakdir] );
        
	}
	
	public function into($goto='')
    {
        if(!empty($goto)){
            set_cookie('mysql_into', mymd5( "1\t".(time()+60) ),60);
            $array = get_post();
            $baktime = $array['baktime'];
            $step = $array['step'];
            empty($step) && $step=0;
            $this->db->bak_into($baktime,$step);
            exit;
	    }
		
        $selectname = $this->db->bak_time();
        if(file_exists(RUNTIME_PATH.'mysql_bak/mysql_insert.txt')){
            echo "<CENTER><table><tr bgcolor=#FF0000><td colspan=5 height=30><div align=center><A HREF=".read_file(ROOT_PATH."cache{$webdb[web_dir]}/mysql_insert.txt")."><b><font color=ffffff>上次还原数据被中断是否继续,点击继续</font></b></A></div></td></tr></table></CENTER>";
            exit;
        }       
		return $this->fetch('into',['selectname'=>$selectname]);
	}
	
	
	/**
	 * 查看某个表里边的数据与结构
	 * @param string $table
	 * @param string $keyword
	 * @param string $types
	 * @param string $field
	 * @param string $ordertype
	 * @param string $orderby
	 * @return mixed|string
	 */
	public function showtable($table='',$keyword='',$types='',$field='',$ordertype='',$orderby=''){
	    if($keyword && $field){
	        if($types){
	            $map = [$field=>$keyword];
	        }else{
	            $map = [$field=>['like',"%{$keyword}%"]];
	            $SQL=" WHERE binary `$field` LIKE '%$keyword%'";
	        }
	    }
	    
	    if($ordertype && $orderby){
	        $order = "$ordertype $orderby";
	    }
	    
	    $titledb = table_field($table,'',false);	    
	    
	    $data_list = Db::table($table)->where($map)->order($order)->paginate(30);
	    $pages = $data_list->render();
	    
	    $listdb = getArray($data_list)['data'];
	    
	    foreach($listdb AS $key=>$rs){
	        foreach($titledb AS $_field){
	            $value = $rs[$_field];
	            if(strlen($value)>32){
	                $value = str_replace(array('<','>','&nbsp;'),array('&lt;','&gt;','&amp;nbsp;'),$value);
	                $value = "<textarea name='textfield' style='width:300px;height:50px'>{$value}</textarea>";
	            }elseif( is_null($value) ){
	                $value= 'NULL';
	            }elseif($value == ''){
	                $value = '&nbsp;';
	            }
	            $rs[$_field] = $value;
	        }
	        $listdb[$key] = $rs;
	    }
	    
	    $this->assign('listdb',$listdb);
	    $this->assign('titledb',$titledb);
	    $this->assign('showpage',$pages);
	    
	    $this->assign('table',$table);
	    $this->assign('orderby',$orderby);
	    $this->assign('ordertype',$ordertype);
	    $this->assign('field',$field);
	    $this->assign('types',$types);
	    $this->assign('keyword',$keyword);
	    
	    return $this->fetch();
	}
	
	public function tool($sql='')
    {
        if(IS_POST){
            if ($sql && into_sql($sql)) {
            //if ($sql && Db::execute($sql) ) {
                $this->success('数据库执行成功');
            } else {
                $this->error('执行失败');
            }
		}
		return $this->fetch();		
	}

}
?>