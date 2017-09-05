<?php
namespace App\Api\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Overtrue\EasySms\EasySms;


class PlotterController extends BaseController
{
	
	//不包含验证码的注册(完成)
	public function registerWithoutCode(Request $request)
    {
		$name =$request->get('name');
		$phone=$request->get('phone');
		$pwd=$request->get('password');
        $select = DB::table('act_user')->where('account',$phone)->get();
        if(count($select)>0){
            return response()->json([
                'data' => false,
                'msg' => '帐号已经存在!'
            ]);
        }
        DB::table('act_code')->insert(['phone'=>$phone,'code' => '0000']);
		$plotter = DB::table('act_plotter')->insert(['name'=>$name,'phone'=>$phone,'pic' => 'http://orwi9xvus.bkt.clouddn.com/head_defalut.jpg']);
		$res = DB::table('act_user')->where('account',$phone)->update(['password'=>md5($pwd)]);
        if($res){
			return response()->json([
			    'data'=>true,
                'msg' => '注册成功！'
            ]);
		}else{
			DB::table('act_plotter')->where('id',$plotter['id'])->delete();
			DB::table('act_user')->where('account',$phone)->delete();
            DB::table('act_code')->where('phone',$phone)->delete();
			return response()->json([
			    'data' => false,
                'msg' => '注册失败！'
            ]);
		}
	}

	//包含验证码的注册（完成）
    public function registerWithCode(Request $request)
    {
        $name =$request->get('name');
        $phone=$request->get('phone');
        $pwd=$request->get('password');
        $code=$request->get('code');
        $select = DB::table('act_user')->where('account',$phone)->get();
        if(count($select)>0){
            return response()->json([
                'data' => false,
                'msg' => '帐号已经存在!'
            ]);
        }
        $result = DB::table('act_code')->where(['phone'=> $phone,'code' => $code])->get();
        if(count($result)>0){
            DB::table('act_plotter')->insert(['name'=>$name,'phone'=>$phone,'pic' => 'http://orwi9xvus.bkt.clouddn.com/head_defalut.jpg']);
            DB::table('act_user')->where('account',$phone)->update(['password'=>md5($pwd)]);
            return response()->json([
                'data'=>true,
                'msg' => '注册成功！'
            ]);
        }else{
            DB::table('act_plotter')->where('phone',$phone)->delete();
            DB::table('act_user')->where('account',$phone)->delete();
            return response()->json([
                'data' => false,
                'msg' => '注册失败！'
            ]);
        }
    }

	//登录（完成）
	public function login(Request $request){
		$account = $request->input('account');
		$password = $request->input('password');

		$info = DB::table('act_plotter')->where('phone',$account)->first();
		$user = DB::table('act_user')->where('account',$account)->first();
//		return $user->password;
		if(count($user)>0 && $user->password === md5($password)){
			$data = [
		      'code'=>1,
		      'msg'=>'登录成功！',
		      'data'=>[
		          'id' => $user->id,
                  'account' => $user->account,
                  'type' => $user->type,
                  'pid' => $user->pid,
                  'pic' => $info->pic,
                  'sex' => $info->sex,
                  'name' => $info->name
              ]
		   ];
			return response()->json($data);
		}else{
			$data = [
		      'code'=>0,
		      'msg'=>'用户名或密码错误，登录失败！',
		      'data'=>null
		   ];
			return response()->json($data);
		}
	}

    //注册时手机验证发送验证码（完成）
    public function registerVerifyCode(Request $request){
	    $code = $this->verifyPhone($request->get('phone'));
        DB::table('act_code')->insert(['phone'=>$request->get('phone'),'code' => $code]);
    }


    //发验证码，并更新act_code数据库（完成）
    public function resetVerify(Request $request){
        $code = $this->verifyPhone($request->get('phone'));
        DB::table('act_code')->where(['phone'=>$request->get('phone')])->update(['code' => $code]);
    }

    //发送验证码（完成）
    public function verifyPhone($phone){
        $code = rand(1000,9999);
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'aliyun'
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'aliyun' => [
                    'access_key_id' => 'LTAIU5b01XSkYQWs',
                    'access_key_secret' => 'ogRkdJsX05irgK6N6FWsgQk3v6S0gL',
                    'sign_name' => '悦跑happyrun',
                ],
            ],
        ];

        $easySms = new EasySms($config);

        $easySms->send($phone, [
            'template' => 'SMS_92710002',
            'data' => [
                'code' => $code
            ],
        ]);
        return $code;
    }

    //发送重置密码请求，会发验证码（完成）
    public function resetRequest(Request $request){
        $phone = $request->get('phone');
        $data = DB::table('act_plotter')->where('phone',$phone)->get();
        if(count($data)>0){
            $code = $this->verifyPhone($phone);
            DB::table('act_code')->where(['phone' => $phone])->update(['code'=> $code]);
        }else{
            return response()->json([
                'result' => false,
                'msg' => '该帐号尚未注册！'
            ]);
        }
    }


    //验证重置密码验证码的正确性（完成）
    public function verifyResetCode(Request $request){
        $code = $request->get('code');
        $phone = $request->get('phone');
        $select = DB::table('act_code')->where(['phone'=>$phone,'code' =>$code])->get();
        $data = [
            'result' => false,
            'msg' => '验证码输入错误！'
        ];
        if(count($select)>0){
            $data = [
                'result' => true,
                'msg' => '验证码输入正确！'
            ];
            return response()->json($data);
        }
        return response()->json($data);
    }


    //重置密码（完成）
    public function reset(Request $request){
        $newPassword = $request->get('newPassword');
        $phone = $request->get('phone');
        $data = [
            'result' => false,
            'msg' => '操作错误！'
        ];
        $result = DB::table('act_user')->where(['account' => $phone])->update(['password' => md5($newPassword)]);
        if(count($result)>0){
            $data = [
                'result' => true,
                'msg' => '密码重置成功!'
            ];
            return  response()->json($data);
        }
        return response()->json($data);
    }

    //修改策划人员头像(完成)
    public function modifyPic(Request $request){
        $phone = $request->get('phone');
        $disk = Storage::disk('qiniu');
        $path_pre = 'http://ovqzh14i2.bkt.clouddn.com/';
        $path = $disk->put('avatars', $request->file('pic'));
        DB::table('act_plotter')->where(['phone' => $phone])->update(['pic'=>$path_pre.$path]);
        return response()->json([
            'result' => true,
            'msg' => '修改成功！',
            'img_url' => $path_pre.$path
        ]);
//		$data = Storage::put('avatars',$request->file('pic'));
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
//                    $images = url('storage/app/head/'.$fileName.'');//这里是图片的存放路径(可更改)
////                    $this->outputSmall($fileName,$format);
//                }else{
//                    return response()->json(array('msg'=>'上传头像无效','code'=>0));
//                }
//                $data = [
//                    'code'=>1,
//                    'data'=>['head'=>url($path)]
//                ];
//            }else{
//                $data = [
//                    'code'=>0,
//                    'msg'=>'未获取图片信息'
//                ];
//            }
//        }
    }

    //修改????
    public function modify(Request $request){
        $name = $request->input('name');
        $password = $request->input('password');
        $phone = $request->input('phone');

        $modifyName = DB::table('act_plotter')->where('phone',$phone)->update([['name'=>$name],['phone']=>$phone]);
        $modifyPass = DB::table('act_user')->where('account',$phone)->update(['password'=>md5($password)]);

        if($modifyName||$modifyPass){
            $data = [
                'code'=>1,
                'msg'=>'修改成功！',
                //'data'=>null
            ];
            return response()->json($data);
        }else{
            $data = [
                'code'=>0,
                'msg'=>'修改失败！',
                //'data'=>null
            ];
            return response()->json($data);
        }
    }


    //修改策划人用户名（完成）
	public function modifyName(Request $request)
	{
		$name = $request->get('name');
		$phone = $request->get('phone');
		
		$modifyName = DB::table('act_plotter')->where('phone',$phone)->update(['name'=>$name]);
		
		if($modifyName){
            $info = DB::table('act_plotter')->where('phone',$phone)->first();
            $user = DB::table('act_user')->where('account',$phone)->first();
			$data = [
		      'code'=>1,
		      'msg'=>'修改成功',
                'data'=>[
                    'id' => $user->id,
                    'account' => $user->account,
                    'type' => $user->type,
                    'pid' => $user->pid,
                    'pic' => $info->pic,
                    'sex' => $info->sex,
                    'name' => $info->name
                ]
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
	
	
	//修改策划人电话(完成)
	public function modifyPhone(Request $request){
		$phone = $request->get('phone');
		$pid =$request->get('pid');
		$modifyPhone = DB::table('act_plotter')->where('phone',$phone)->first();
		if(count($modifyPhone)>0){
		    return response()->json([
		        'result' => false,
                'msg' =>  '该手机号已被使用！'
            ]);
        }
        $result = DB::table('act_plotter')->where('id',$pid)->update(['phone'=> $phone]);
		if($result){
            $info = DB::table('act_plotter')->where('phone',$phone)->first();
            $user = DB::table('act_user')->where('account',$phone)->first();
            $data = [
                'result'=> true,
                'msg'=>'修改成功',
                'data'=>[
                    'id' => $user->id,
                    'account' => $user->account,
                    'type' => $user->type,
                    'pid' => $user->pid,
                    'pic' => $info->pic,
                    'sex' => $info->sex,
                    'name' => $info->name
                ]
        ];
		   return response()->json($data);
		}else{
			$data = [
		      'result'=>false,
		      'msg'=>'修改失败',
		      'data'=>null
		   ];
		   return response()->json($data);
		}
	}
	
	
	//修改策划人密码(完成)
	public function modifyPassword(Request $request){
		$password = $request->get('password');
		$phone = $request->get('phone');
		
		$modifyPassword = DB::table('act_user')->where('account',$phone)->update(['password'=>md5($password)]);
		
		if($modifyPassword){
			$data = [
		      'result'=>true,
		      'msg'=>'修改成功',
		      //'data'=>
		   ];
		   return response()->json($data);
		}else{
			$data = [
		      'result'=>false,
		      'msg'=>'修改失败',
		      //'data'=>
		   ];
		   return response()->json($data);
		}
	}
	

	//活动信息设定
	public function actionSetting(Request $request){
		
	}
	
	
	
}
