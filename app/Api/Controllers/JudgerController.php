<?php
namespace App\Api\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JudgerController extends BaseController
{
	public function modifyName(Request $request)
	{
		$name = $request->input('name');
		$id = $request->input('id');
		
		$modifyName = DB::table('act_judger')->where('id',$id)->update(['name'=>$name]);
		
		
		
		if($modifyName){
			$data = [
		      'code'=>1,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_judger')->where('id',$id)->get()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>0,
		      'msg'=>'查无此人，修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	
	public function modifyPhone(Request $request){
		$phone = $request->input('phone');
		$id = $request->get('id');
		
		$modifyPhone = DB::table('act_judger')->where('id',$id)->update(['phone'=>$phone]);
		
		if($modifyPhone){
			$data = [
		      'code'=>1,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_judger')->where('id',$id)->get()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>0,
		      'msg'=>'该id不存在',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
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
		      'msg'=>'该id不存在',
		      //'data'=>
		   ];
		   return response()->json($data);
		}
	}
	
}
