<?php
namespace App\Api\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActorController extends BaseController
{
	//修改组长用户名
	public function modifyName(Request $request)
	{
		$name = $request->input('name');
		$id = $request->input('id');
		
		$modifyName = DB::table('act_actor')->where('id',$id)->update(['name'=>$name]);
		
		if($modifyName){
			$data = [
		      'code'=>1,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_actor')->where('id',$id)->get()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>0,
		      'msg'=>'修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	//修改组长电话
	public function modifyPhone(Request $request){
		$phone = $request->input('phone');
		$id = $request->get('id');
		
		$modifyPhone = DB::table('act_actor')->where('id',$id)->update(['phone'=>$phone]);
		
		if($modifyPhone){
			$data = [
		      'code'=>1,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_actor')->where('id',$id)->get()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>0,
		      'msg'=>'修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	//修改组长密码
	public function modifyPassword(Request $request){
		$password = $request->input('password');
		$id = $request->get('id');
		
		$modifyPassword = DB::table('act_user')->where('id',$id)->update(['password'=>md5($password)]);
		
		if($modifyPassword){
			$data = [
		      'code'=>1,
		      'msg'=>'修改成功',
		      //'data'=>
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>0,
		      'msg'=>'修改失败',
		      //'data'=>
		   ];
		   return response()->json($data);
		}
	}
	
}