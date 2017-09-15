<?php
namespace App\Api\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $group = DB::table('act_actor')->where('gid',$group_id)->first();
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