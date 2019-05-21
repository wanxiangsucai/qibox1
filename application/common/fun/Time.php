<?php
namespace app\common\fun;

/**
 * 取时间范围
 */
class Time{
    
    /**
     * 仅仅取某个时间段内的数据
     * @param string $type
     * @return string[]|number[]|string[][]|number[][]
     */
    public static function only($type='',$num=0){
        if ($type=='day') {
            if ($num>1) {
                $array = [
                    ['<',strtotime(date('Y-m-d 00:00:00'))-3600*24*($num-2)],
                    ['>',strtotime(date('Y-m-d 00:00:00'))-3600*24*($num-1)]
                ];
            }else{
                $array = ['>',strtotime(date('Y-m-d 00:00:00'))];
            }            
        }elseif ($type=='week') {
            if ($num>1){
                $a = strtotime(date('Y-m-d 00:00:00'))-((date('w')?:7)-1)*3600*24;
                $array = [
                    ['<',$a-3600*24*7*($num-2)],
                    ['>',$a-3600*24*7*($num-1)]
                ];
            }else{
                $a = strtotime(date('Y-m-d 00:00:00'))-((date('w')?:7)-1)*3600*24;
                $array = ['>',$a];
            }            
        }elseif($type=='month'||$type=='m'){
            if ($num>1){
                $next_year = date('Y');
                $next_m = date('m')-($num-1);
                if ($next_m<1) {
                    $next_m+=12;
                    $next_year--;
                }
                
                $end_year = date('Y');
                $end_m = date('m')-($num-2);
                if ($end_m<1) {
                    $end_m+=12;
                    $end_year--;
                }
                
                $array = [
                    ['<',strtotime(date($end_year.'-'.$end_m."-01 00:00:00"))],
                    ['>',strtotime(date($next_year.'-'.$next_m."-01 00:00:00"))]
                ];
            }else{
                $array = ['>',strtotime(date('Y-m-01 00:00:00'))];
            }            
        }
        return $array;
    }
    
}