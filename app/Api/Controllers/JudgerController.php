<?php
namespace App\Api\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JudgerController extends BaseController
{
    //裁判修改用户名（完成）
	public function modifyName(Request $request)
	{
		$name = $request->input('name');
		$id = $request->input('id');
		
		$modifyName = DB::table('act_judger')->where('id',$id)->update(['name'=>$name]);
		
		
		
		if($modifyName){
			$data = [
		      'code'=>200,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_judger')->where('id',$id)->first()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>400,
		      'msg'=>'查无此人，修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}

	//
	public function modifyPhone(Request $request){
        $phone = $request->get('phone');
        $id = $request->get('id');
        $select = DB::table('act_judger')->where('phone',$phone)->first();
        if(count($select)>0){
            return response()->json([
                'code'=> 400,
                'result' => false,
                'msg' =>  '该手机号已被使用！'
            ]);
        }
        $modifyPhone = DB::table('act_judger')->where('id',$id)->update(['phone'=>$phone]);
        if($modifyPhone){
            $user = DB::table('act_judger')->where('id',$id)->first();
            DB::table('act_user')->where('pid',$user->id)->update(['account'=>$phone,'password'=> md5($phone)]);
            $data = [
                'code'=> 200,
                'msg'=>'修改成功',
                'data'=>DB::table('act_judger')->where('id',$id)->first()
            ];
            return response()->json($data);
        }else{
            $data = [
                'code'=> 400,
                'msg'=>'修改失败',
                'data'=>null
            ];
            return response()->json($data);
        }
	}
	
    //修改裁判密码（完成）
	public function modifyPassword(Request $request){
		$password = $request->input('password');
		$type = $request->get('type');
		$id = $request->get('id');
		
		$modifyPassword = DB::table('act_user')->where(['pid'=>$id,'type'=>$type])->update(['password'=>md5($password)]);
		
		if($modifyPassword){
			$data = [
		      'code'=>200,
		      'msg'=>'修改成功',
		      //'data'=>
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>400,
		      'msg'=>'该id不存在',
		      //'data'=>
		   ];
		   return response()->json($data);
		}
	}


	public function modifyPic(Request $request){
        $phone = $request->get('phone');
        $disk = Storage::disk('qiniu');
        $path_pre = 'http://ovqzh14i2.bkt.clouddn.com/';
        $path = $disk->put('avatars', $request->file('pic'));
        $update = DB::table('act_judger')->where(['phone' => $phone])->update(['pic'=>$path_pre.$path]);
        if($update){
            $info = DB::table('act_judger')->where('phone',$phone)->first();
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
	
}
