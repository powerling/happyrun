<?php
namespace App\Api\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaveController extends BaseController
{
	
	//修改救援人员头像
	public function modifyPic(Request $request){
//		$image = $request->input('image');
		
		$data = Storage::put('avatars',$request->file('image'));
//        if($image){
//            $type = $image->getClientMimeType();
//            $size= $image->getClientSize();
//            //判断是否是图片类型
//            if($type == 'image/jpeg' || $type == 'image/png'){
//                if($size > 3145728){
//                    return response()->json(array('msg'=>'头像图片大小不能超过3M','code'=>0));
//                }
//                if ($image->isValid()) {//判断图片的格式
//                    switch ($type) {
//                        case 'image/jpeg':
//                            $format='.jpg';
//                            break;
//                        default:
//                            $format='.png';
//                            break;
//                    }
//                    $fileName=uniqid().$format;
//                    $path = $image->storeAs(
//                        'head', $fileName
//                    );
//                    $images[] = url('api/user/saver/modifyPic/'.$fileName.'');//这里是图片的存放路径(可更改)
//                    //$this->outputSmall($fileName,$format);
//
//                }else{
//                    return response()->json(array('msg'=>'上传头像无效','code'=>0));
//                }
//                $data = [
//                    'code'=>1,
//                    'data'=>['head'=>$images]
//                ];
//            }else{
//                $data = [
//                    'code'=>0,
//                    'msg'=>'未获取图片信息'
//                ];
//            }
//        }
        return response()->json($data);
    }
	
	
	//修改救援人用户名
	public function modifyName(Request $request)
	{
		$name = $request->input('name');
		$id = $request->input('id');
		
		$modifyName = DB::table('act_saver')->where('id',$id)->update(['name'=>$name]);
		
		if($modifyName){
			$data = [
		      'code'=>1,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_saver')->where('id',$id)->get()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>0,
		      'msg'=>'该id不存在，修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	//修改救援人员电话
	public function modifyPhone(Request $request){
		$phone = $request->input('phone');
		$id = $request->get('id');
		
		$modifyPhone = DB::table('act_saver')->where('id',$id)->update(['phone'=>$phone]);
		
		if($modifyPhone){
			$data = [
		      'code'=>1,
		      'msg'=>'修改成功',
		      'data'=>DB::table('act_saver')->where('id',$id)->get()
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'code'=>0,
		      'msg'=>'该id不存在，修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	//修改救援人员密码
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