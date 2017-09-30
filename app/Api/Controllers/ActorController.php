<?php
namespace App\Api\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use JPush\Client as JPush;

class ActorController extends BaseController
{
	//修改组长用户名(完成)
	public function modifyName(Request $request)
	{
		$name = $request->input('name');
		$id = $request->input('id');
		
		$modifyName = DB::table('act_actor')->where('id',$id)->update(['name'=>$name]);
		
		if($modifyName){
			$data = [
		      'code'=>200,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_actor')->where('id',$id)->first()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>400,
		      'msg'=>'修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	//修改组长电话（完成）
	public function modifyPhone(Request $request){
		$phone = $request->get('phone');
		$id = $request->get('id');
        $select = DB::table('act_actor')->where('phone',$phone)->first();
        if(count($select)>0){
            return response()->json([
                'code'=> 400,
                'msg' =>  '该手机号已被使用！',
                'data' => null
            ]);
        }
		$modifyPhone = DB::table('act_actor')->where('id',$id)->update(['phone'=>$phone]);
		if($modifyPhone){
            $user = DB::table('act_actor')->where('id',$id)->first();
            DB::table('act_user')->where('pid',$user->id)->update(['account'=>$phone,'password'=> md5($phone)]);
			$data = [
		      'code'=>200,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_actor')->where('id',$id)->first()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>400,
		      'msg'=>'修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	//修改组长密码(完成)
	public function modifyPassword(Request $request){
		$password = $request->input('newPassword');
		$id = $request->get('id');
		$type = $request->get('type');
		
		$modifyPassword = DB::table('act_user')->where(['pid'=>$id,'type'=>$type])->update(['password'=>md5($password)]);
		
		if($modifyPassword){
			$data = [
                'code'=> 200,
		        'msg'=>'修改成功',
                'data' => null
		      //'data'=>
		   ];
		   return response()->json($data);
		}
			$data = [
                'code'=> 400,
		        'msg'=>'修改失败',
                'data' => null
		      //'data'=>
		   ];
		   return response()->json($data);

	}

	//修改组长头像（完成）
	public function modifyPic(Request $request){
        $phone = $request->get('phone');
        $disk = Storage::disk('qiniu');
        $path_pre = 'http://ovqzh14i2.bkt.clouddn.com/';
        $path = $disk->put('avatars', $request->file('pic'));
        $update = DB::table('act_actor')->where(['phone' => $phone])->update(['pic'=>$path_pre.$path]);
        if($update){
            $info = DB::table('act_actor')->where('phone',$phone)->first();
            return response()->json([
                'code'=> 200,
                'msg' => '修改成功！',
                'data' => $info
            ]);
        }
        return response()->json([
            'code'=> 400,
            'msg' => '操作失败！',
            'data' => null
        ]);
    }

    //开始活动
    public function startAction(Request $request){
	    $action_id = $request->get('id');
	    $result = DB::table('act_action')->where('id',$action_id)->update(['status'=>2]);
	    if($result){
	        $data = DB::table('act_action')->where('id',$action_id)->first();
	        return response()->json([
                'code'=> 200,
                'msg' => '活动进行中',
                'data' => $data
            ]);
        }
        return response()->json([
            'code'=> 400,
            'msg' => '活动开始失败',
            'data'=> null
        ]);
    }

    //结束活动
    public function endAction(Request $request){
        $action_id = $request->get('id');
        $result = DB::table('act_action')->where('id',$action_id)->update(['status'=>4]);
        if($result){
            $data = DB::table('act_action')->where('id',$action_id)->first();
            DB::table('act_user')->where([
                ['aid',$action_id],
                ['type','>',1],
            ])->delete();
            return response()->json([
                'code'=> 200,
                'msg' => '活动完美结束',
                'data' => $data
            ]);
        }
        return response()->json([
            'code'=> 400,
            'msg' => '活动结束失败',
            'data'=> null
        ]);
    }

    //组员信息
    public function group(Request $request){
        $group_id = $request->get('group_id');
        $group = DB::table('act_actor')->where('gid',$group_id)->get();
        if(count($group)>0){
            return response()->json([
                'code'=> 200,
                'msg' => '成功获取组员信息',
                'data' => $group
            ]);
        }
        return response()->json([
            'code'=> 400,
            'data' => '信息获取失败',
            'data' => null
        ]);
    }

    //修改活动名称(完成)
    public function modifyActionName(Request $request){
        $action_id = $request->get('action_id');
        $action_name = $request->get('action_name');
        $result = DB::table('act_action')->where(['id'=>$action_id])->update(['name'=>$action_name]);
        if($result){
            return response()->json([
                'code' => 200,
                'msg' => '活动名修改成功',
                'data' => DB::table('act_action')->where(['id'=>$action_id])->first()
            ]);
        }
        return response()->json([
            'code' => 400,
            'msg' => '活动名修改失败',
            'data' => null
        ]);
    }

    //上传路线信息
    public function uploadWay(Request $request){
        $action_info = $request->get('way_info');
        $action =  json_decode($action_info);
        $action_id = $action->action_id;
        $way_name = $action->name;
        $count = count($action->info);
//        return $action->info[1]->place_id;
//        return $action_id;
//        return $way_name;
//        return $count;
        if($count > 0){
            $way_id = DB::table('act_way')->insertGetId(['aid'=>$action_id,'name'=>$way_name,'places'=>$count,'startpid'=>$action->info[0]->place_id,'endedpid'=>$action->info[$count-1]->place_id]);
            foreach ($action->info as $value){
                DB::table('way_place_duty')->insert(['way_id'=>$way_id,'place_id'=>$value->place_id,'judger_id'=>$value->judger_id,'duty_id'=>$value->duty_id]);
            }
            $way_info = DB::table('act_way')->where('id',$way_id)->first();
            return response()->json([
                'code' => 200,
                'msg' => '线路设置成功!',
                'data' => $way_info
            ]);
        }else{
            $way_id = DB::table('act_way')->insertGetId(['aid'=>$action_id,'name'=>$way_name]);
            $way_info = DB::table('act_way')->where('id',$way_id)->first();
            return response()->json([
                'code' => 200,
                'msg' => '线路设置成功!',
                'data' => $way_info
            ]);
        }
        return response()->json([
            'code' => 400,
            'msg' => '线路设置失败!',
            'data' => null
        ]);
    }

    //某一条线路信息获取
    public function wayInformation(Request $request){
        $way_id = $request->get('way_id');
        $way_info =  DB::table('way_place_duty')->where('way_id',$way_id)->get();
        if(count($way_info)<=0){
            return response()->json([
                'code' => 400,
                'msg' => '该路线还未设置站点',
                'data' => null
            ]);
        }else{
            $data = array();
            foreach ($way_info as $info){
                $way = DB::table('act_way')->where('id',$info->way_id)->first();
                $place = DB::table('act_place')->where('id',$info->place_id)->first();
                $duty = DB::table('act_duty')->where('id',$info->duty_id)->first();
                $judger = DB::table('act_judger')->where('id',$info->judger_id)->first();
                $arr = array(
                    'id' => $info->id,
                    'way_id' => $info->way_id,
                    'way_name' => $way->name,
                    'place_id' => $info->place_id,
                    'place_name' => $place->name,
                    'duty_id' => $info->duty_id,
                    'duty_name' => $duty->title,
                    'judger_id' => $info->judger_id,
                    'judger_name' => $judger->name
                );
                array_push($data,$arr);
            }
            return response()->json([
                'code' =>200,
                'msg' => '路线信息获取成功!',
                'data' => $data
            ]);
        }
        return response()->json([
            'code' => 400,
            'msg' => '路线信息获取失败!',
            'data' => null
        ]);
    }

    //修改某一路线某一站点信息
    public function modifyPlaceInformation(Request $request){
        $id = $request->get('id');
        $place_id = $request->get('place_id');
        $judger_id = $request->get('judger_id');
        $duty_id = $request->get('duty_id');
        $result = DB::table('way_place_duty')->where('id',$id)->update(['place_id'=>$place_id,'judger_id'=>$judger_id,'duty_id'=>$duty_id]);
        if($result){
            return response()->json([
                'code' => 200,
                'msg' => '信息修改成功',
                'data' => DB::table('way_place_duty')->where('id',$id)->first()
            ]);
        }else{
            return response()->json([
                'code' => 400,
                'msg' => '信息修改失败',
                'data' => null

            ]);
        }
    }

    //修改队伍的组长
    public function modifyGrouper(Request $request){
        $group_info = $request->get('group');
        $info = json_decode($group_info);
        foreach ($info as $value){
            $actor_id = $value->actor_id;
            $group_id = $value->group_id;
            $actor = DB::table('act_actor')->where(['id'=>$actor_id])->first();
            DB::table('act_group')->where(['id'=>$group_id])->update(['lid'=>$actor_id,'name'=>$actor->name]);
        }
        return response()->json([
            'code' => 200,
            'msg' => '组长修改成功',
            'data' => null
        ]);
    }

    //修改参与者的分组
    public function modifyGroup(Request $request){
        $action_id = $request->get('action_id');
        $group = $request->get('group_info');
        $group_info = json_decode($group);
        DB::table('act_actor')->where('aid',$action_id)->update(['gid'=>null]);
        foreach ($group_info as $value){
            foreach ($value->actor as $array){
                DB::table('act_actor')->where(['id'=> $array])->update(['gid'=>$value->group_id]);
            }
        }
        return response()->json([
            'code' => 200,
            'msg' => '分组修改成功',
            'data' => null
        ]);
    }

    //分配路线
    public function matchWay(Request $request){
        $way = $request->get('group_way');
        $group_way = json_decode($way);
        foreach ($group_way as $value){
            DB::table('act_group')->where(['id'=>$value->group_id])->update(['wid'=>$value->way_id]);
        }
        return response()->json([
            'code'=>200,
            'msg'=> '路线匹配成功',
            'data' => null
        ]);
    }

    //删除路线
    public function deleteWay(Request $request){
        $way_id = $request->get('way_id');
        $result = DB::table('act_way')->where('id',$way_id)->delete();
        $result2 = DB::table('way_place_duty')->where('way_id',$way_id)->delete();
        if($result&&$result2){
            return response()->json([
                'code' => 200,
                'msg' => '该路线已被删除!',
                'data' => null
            ]);
        }else{
            return response()->json([
                'code' => 400,
                'msg' => '该路线删除失败!',
                'data' => null
            ]);
        }
    }

    //预约活动开始时间
    public function appointAction(Request $request){
        $action_id = $request->get('action_id');
        $startTime = $request->get('startTime');
        $result = DB::table('act_action')->where('id',$action_id)->update(['starttime'=>$startTime]);
        if ($result){
            return response()->json([
                'code' => 200,
                'msg' => '活动预约成功',
                'data' => DB::table('act_action')->where('id',$action_id)->first()
            ]);
        }else{
            return response()->json([
                'code' => 400,
                'msg' => '活动预约失败',
                'data' => null
            ]);
        }
    }

    //获取历史活动
    public function actionHistory(Request $request){
        $plotter_id =  $request->get('plotter_id');
        $data = DB::table('act_action')->where(['pid'=>$plotter_id])->get();
        if (count($data)>0){
            return response()->json([
                'code' => 200,
                'msg' => '历史活动获取成功',
                'data' => $data
            ]);
        }else{
            return response()->json([
                'code' => 400,
                'msg' => '没有历史活动',
                'data' => null
            ]);
        }
    }

    //查看任务
    public function checkDuty(Request $request){
        $way_id = $request->get('way_id');
        $code = $request->get('code');
        $place_id = $request->get('place_id');
        $result = DB::table('act_place')->where(['id'=>$place_id,'code'=>$code])->get();
        if(count($result)>0){
            $info = DB::table('way_place_duty')->where(['way_id'=>$way_id,'place_id'=>$place_id])->first();
            $data = DB::table('act_duty')->where(['id'=>$info->duty_id])->first();
            return response()->json([
                'code' => 200,
                'msg' => '任务信息获取成功！',
                'data' => $data
            ]);
        }else{
            return response()->json([
                'code' => 400,
                'msg' => '任务信息获取失败',
                'data' => null
            ]);
        }
    }

    //按照分组获取所有参与者信息和组长ID
    public function groupActor(Request $request){
        $action_id = $request->get('action_id');
        $data = DB::table('act_actor')->where('aid',$action_id)->get();
        $group = DB::table('act_group')->where('aid',$action_id)->get();
        $results = array();
        $group_id = 0;
        $grouper_id = 0;
        foreach ($group as $value){
                $group_infos = array();
                foreach ($data as $list){
                    if($value->lid == $list->id){
                        $grouper_id = $list->id;
                    }
                    if($value->id == $list->gid){
                        $group_id = $list->gid;
                        $group_info =  $list;
                        array_push($group_infos,$group_info);
                    }
                }
                $result = array(
                    'group_id' => $group_id,
                    'grouper_id' => $grouper_id,
                    'group_info' => $group_infos
                );
            array_push($results,$result);
        }
        return response()->json([
            'code' => 200,
            'msg' => '信息获取成功',
            'data' => $results
        ]);
    }

    //修改路线信息
    public function modifyWay(Request $request){
        $way_info = $request->get('way_info');
        $way_info = json_decode($way_info);
        DB::table('way_place_duty')->where('way_id',$way_info->way_id)->delete();
        foreach ($way_info->way_info as $value){
            DB::table('way_place_duty')->insert(['way_id'=>$way_info->way_id,'place_id'=>$value->place_id,'judger_id'=>$value->judger_id,'duty_id'=>$value->duty_id]);
        }
        return response()->json([
            'code' => 200,
            'msg' => '路线信息修改成功!',
            'data' => DB::table('way_place_duty')->where('way_id',$way_info->way_id)->get()
        ]);
    }

    //申请救援
    public function applySave(Request $request){
        $save_info = $request->get('save_info');
        $save_info = json_decode($save_info);
        $result_id = DB::table('act_save')->insertGetId([
            'starttime' => Carbon::now(),
            'gid'=>$save_info->group_id,
            'targetx'=>$save_info->save_x,
            'targety'=>$save_info->save_y,
            'targetloc'=>$save_info->save_place,
            'remark'=>$save_info->save_intro,
            'status'=>1
        ]);
        if($result_id){
            return response()->json([
                'code' => 200,
                'msg' => '救援申请成功',
                'data' => DB::table('act_save')->where(['id'=>$result_id])->first()
            ]);
        }
        return response()->json([
            'code' => 400,
            'msg' => '救援申请失败',
            'data' => null
        ]);
    }

    //修改暂存活动
    public function modifyAction(Request $request){
        $action_id = $request->get('action_id');
        $action_name = $request->get('action_name');
        $action_excel = $request->file('excel');
        if(isset($action_name)){
            DB::table('act_action')->where('id',$action_id)->update(['name' => $action_name]);
        }
        if(isset($action_excel)){

        }

    }

    //推送
    public function push(Request $request){
        $app_key  = env('PUSH_APP_KEY',null);
        $master_secret = env('PUSH_MASTER_SECRET',null);
        $client = new JPush($app_key, $master_secret,null);
//        $push = $client->push();
        $client->push()
            ->setPlatform('android')
            ->addAllAudience()
            ->setNotificationAlert('Hello, JPush')
            ->send();
    }
}