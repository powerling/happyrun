<?php
namespace App\Api\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaveController extends BaseController
{

	public function modifyPic(Request $request){
        $phone = $request->get('phone');
        $disk = Storage::disk('qiniu');
        $path_pre = 'http://ovqzh14i2.bkt.clouddn.com/';
        $path = $disk->put('avatars', $request->file('pic'));
        $update = DB::table('act_saver')->where(['phone' => $phone])->update(['pic'=>$path_pre.$path]);
        if($update){
            $info = DB::table('act_saver')->where('phone',$phone)->first();
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
	
	
	//修改救援人用户名（完成）
	public function modifyName(Request $request)
	{
		$name = $request->input('name');
		$id = $request->input('id');
		
		$modifyName = DB::table('act_saver')->where('id',$id)->update(['name'=>$name]);
		
		if($modifyName){
			$data = [
		      'code'=>200,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_saver')->where('id',$id)->first()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>400,
		      'msg'=>'该id不存在，修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	//修改救援人员电话(完成)
	public function modifyPhone(Request $request){
        $phone = $request->get('phone');
        $id = $request->get('id');
        $select = DB::table('act_saver')->where('phone',$phone)->first();
        if(count($select)>0){
            return response()->json([
                'code'=> 400,
                'msg' =>  '该手机号已被使用！',
                'data' => null
            ]);
        }
        $modifyPhone = DB::table('act_saver')->where('id',$id)->update(['phone'=>$phone]);
        if($modifyPhone){
            $user = DB::table('act_saver')->where('id',$id)->first();
            DB::table('act_user')->where('pid',$user->id)->update(['account'=>$phone,'password'=> md5($phone)]);
            $data = [
                'code'=> 200,
                'msg'=>'修改成功',
                'data'=>DB::table('act_saver')->where('id',$id)->first()
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
	
	
	//修改救援人员密码（完成）
	public function modifyPassword(Request $request){
		$password = $request->input('newPassword');
		$id = $request->get('id');
		$type = $request->get('type');
		
		$modifyPassword = DB::table('act_user')->where(['pid'=>$id,'type'=>$type])->update(['password'=>md5($password)]);
		
		if($modifyPassword){
			$data = [
                'code'=> 200,
                'msg'=>'修改成功',
		        'data'=>null
		   ];
		   return response()->json($data);
		}
			$data = [
                'code'=> 400,
		        'msg'=>'修改失败',
		        'data'=> null
		   ];
		   return response()->json($data);

	}

	//救援人员忘记密码
    public function  resetPassword(Request $request){

    }
}