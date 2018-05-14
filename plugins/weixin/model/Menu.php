<?php

namespace plugins\weixin\model;
use think\Model;

//微信菜单
class Menu extends Model
{
    protected $table = '__WEIXINMENU__';
    
	public function getMenu(){
		
		$listdb1 = [];
		$query1 = getArray($this->where('fid',0)->order('list','asc')->select());
		foreach($query1 AS $rs){		
		    $query2 = getArray($this->where('fid',$rs['id'])->order('list','asc')->select());
			foreach($query2 AS $rs2){
				$rs['children'][]=$rs2;
			}
			$listdb1[] = $rs;
		}
		return $listdb1;
	}
	
	
	public function save_data($postdb=[]){
	    //$this->where('id','>',0)->delete();
	    
	    query('TRUNCATE TABLE `'.config('database.prefix').'weixinmenu` ');
	    
	    $detail=explode("#",$postdb['menulist']);
	    $fups=array();
	    $i=0;
	    foreach($detail AS $key=>$value){
	        $detail1=explode("@",$value);
	        $fup=intval($detail1[0]);
	        $name=$postdb[$fup]['name'];
	        $keyword=$postdb[$fup]['keyword'];
	        $linkurl=$postdb[$fup]['linkurl'];
	        $type=$postdb[$fup]['type'];
	        if($fup&&$name&&$i<3){
	            $i++;
	            $data = [
	                    'fid'=>0,
	                    'name'=>$name,
	                    'keyword'=>$keyword,
	                    'linkurl'=>$linkurl,
	                    'type'=>intval($type),
	            ];
	            $this->create($data);
	            //$db->query("INSERT INTO  `{$pre}weixinmenu`  (`uid`,`fid`,`name`,`keyword`,`linkurl`,`type`) VALUES ('$lfjuid',  '0',  '$name','$keyword','$linkurl','$type')");
	            $detai2=explode(",",$detail1[1]);
	            foreach($detai2 AS $keys=>$rss){
	                $fid=intval($rss);
	                if($fid){
	                    $fups[$i][]=$fup.','.$fid;
	                }
	            }
	        }
	    }
	    
	    foreach($fups AS $key=>$value){
	        $j=0;
	        foreach($value AS $keys=>$rss){
	            $arrays=explode(",",$rss);
	            $fup=$arrays[0];
	            $fid=$arrays[1];
	            
	            $name1=$postdb[$fup][$fid]['name'];
	            $keyword1=$postdb[$fup][$fid]['keyword'];
	            $linkurl1=$postdb[$fup][$fid]['linkurl'];
	            $type1=$postdb[$fup][$fid]['type'];
	            if($key&&$name1&&$j<5){
	                $j++;	                
	                $data = [
	                        'fid'=>$key,
	                        'name'=>$name1,
	                        'keyword'=>$keyword1,
	                        'linkurl'=>$linkurl1,
	                        'type'=>intval($type1),
	                ];
	                $this->create($data);
	                //$db->query("INSERT INTO  `{$pre}weixinmenu`  (`uid`,`fid`,`name`,`keyword`,`linkurl`,`type`) VALUES ('$lfjuid',  '$key',  '$name1','$keyword1','$linkurl1','$type1')");
	            }
	        }
	    }
	    return true;
	}
	
	public function build_menu_data($domain=''){
	    $Marray = $array = array();
	    $i=-1;	    
	    //$query =$db->query("SELECT * FROM `{$pre}weixinmenu` WHERE fid='0' AND hide=0 ORDER BY list DESC LIMIT 3");
	    //while($rs =$db->fetch_array($query)){
	    $query1 = getArray($this->where('fid',0)->order('list','asc')->select());
	    foreach($query1 AS $rs){
	        $i++;
	        $Marray[$i]['name'] = urlencode($rs['name']);
	        $j=-1;
	        //$query2 =$db->query("SELECT * FROM `{$pre}weixinmenu` WHERE fid='$rs[id]' AND hide=0 ORDER BY list DESC LIMIT 5");
	        //while($rs2 =$db->fetch_array($query2)){
	        $query2 = getArray($this->where('fid',$rs['id'])->order('list','asc')->select());
	        foreach($query2 AS $rs2){
	            $j++;
	            if($rs2['linkurl']){
	                if($rs2['linkurl']=='map'){
	                    $type = 'location_select';
	                    $Marray[$i]['sub_button'][$j]['key'] = urlencode($rs2['keyword']);
	                }else{
	                    preg_match('/^http/',$rs2['linkurl']) || $rs2['linkurl']=$domain.$rs2['linkurl'];
	                    $type = 'view';
	                    $Marray[$i]['sub_button'][$j]['url'] = urlencode($rs2['linkurl']);
	                }
	            }else{
	                $type = 'click';
	                $Marray[$i]['sub_button'][$j]['key'] = urlencode($rs2['keyword']);
	            }
	            $Marray[$i]['sub_button'][$j]['type'] = $type;
	            $Marray[$i]['sub_button'][$j]['name'] = urlencode($rs2['name']);
	        }
	        
	        if(!is_array($Marray[$i]['sub_button'])){
	            if($rs['linkurl']){
	                preg_match('/^http/',$rs['linkurl']) || $rs['linkurl']=$domain.$rs['linkurl'];
	                $type = 'view';
	                $Marray[$i]['url'] = urlencode($rs['linkurl']);
	            }else{
	                $type = 'click';
	                $Marray[$i]['key'] = urlencode($rs['keyword']);
	            }
	            $Marray[$i]['type'] = $type;
	        }
	    }
	    $array['button']=$Marray;
	    $data = json_encode($array);
	    $data = urldecode($data);
	    return $data;
	}
}