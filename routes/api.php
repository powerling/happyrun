<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Controllers'], function ($api) {
        $api->post('user/login', 'AuthController@authenticate');
        $api->post('user/register', 'AuthController@register');
		$api->get('user/test',function (){
		    return '1';
        });

		
		//策划
		$api->post('user/plotter/registerWithoutCode','PlotterController@registerWithoutCode');//没有验证码的注册
		$api->post('user/plotter/registerWithCode','PlotterController@registerWithCode');//有验证码的注册，给前端不写验证码用
		$api->get('user/plotter/modify','PlotterController@modify');
		$api->post('user/plotter/reset','PlotterController@reset');                 //重置密码
		$api->post('user/plotter/verifyResetCode','PlotterController@verifyResetCode'); //验证重置密码请求验证码正确性
		$api->post('user/plotter/resetRequest','PlotterController@resetRequest');   //重置密码请求会发送验证码。给前端不写验证码用
		$api->post('user/login','PlotterController@login');             //用户登录
		$api->post('user/plotter/registerVerifyCode','PlotterController@registerVerifyCode');       //发送注册验证码，给前端不写验证码用
        $api->post('user/plotter/modifyPic','PlotterController@modifyPic');     //修改策划人头像
        $api->post('user/plotter/modifyName','PlotterController@modifyName');
        $api->post('user/plotter/modifyPhone','PlotterController@modifyPhone');
        $api->post('user/plotter/modifyPassword','PlotterController@modifyPassword');
        $api->post('user/plotter/actionSetting','PlotterController@actionSetting');
        $api->post('user/actionInformation','PlotterController@actionInformation');




		
		//裁判
		$api->post('user/judger/modifyName','JudgerController@modifyName');//完成
		$api->post('user/judger/modifyPhone','JudgerController@modifyPhone');//完成
		$api->post('user/judger/modifyPassword','JudgerController@modifyPassword');//完成
		$api->post('user/judger/modifyPic','JudgerController@modifyPic');//完成
		$api->post('user/judger/placeCode','JudgerController@placeCode');//完成
		$api->post('user/judger/groupFinish','JudgerController@groupFinish');//完成

		
		//组长
		$api->post('user/actor/modifyName','ActorController@modifyName');//完成
		$api->post('user/actor/modifyPhone','ActorController@modifyPhone');//完成
		$api->post('user/actor/modifyPassword','ActorController@modifyPassword');//完成
		$api->post('user/actor/modifyPic','ActorController@modifyPic');//完成
		$api->post('user/startAction','ActorController@startAction');//完成
		$api->post('user/endAction','ActorController@endAction');//完成
		$api->post('user/actor/group','ActorController@group');//完成
		$api->post('user/uploadWay','ActorController@uploadWay');
        $api->post('user/wayInformation','ActorController@wayInformation');
        $api->post('user/modifyPlaceInformation','ActorController@modifyPlaceInformation');
        $api->post('user/modifyGrouper','ActorController@modifyGrouper');
        $api->post('user/modifyGroup','ActorController@modifyGroup');
        $api->post('user/matchWay','ActorController@matchWay');
        $api->post('user/deleteWay','ActorController@deleteWay');
        $api->post('user/appointAction','ActorController@appointAction');
        $api->post('user/actionHistory','ActorController@actionHistory');
        $api->post('user/actor/checkDuty','ActorController@checkDuty');
        $api->post('user/actor/groupActor','ActorController@groupActor');
        $api->post('user/modifyWay','ActorController@modifyWay');
        $api->post('user/modifyAction','ActorController@modifyAction');
        $api->post('user/checkDuty','ActorController@checkDuty');


		//救援
		$api->post('user/saver/modifyPic','SaveController@modifyPic');          //完成
		$api->post('user/saver/modifyName','SaveController@modifyName');        //完成
		$api->post('user/saver/modifyPhone','SaveController@modifyPhone');      //完成
		$api->post('user/saver/modifyPassword','SaveController@modifyPassword');//完成
        $api->post('user/saver/setPassword','SaveController@resetPassword');

		$api->post('/user/verify','PlotterController@verify');
		$api->get('/phpinfo','PlotterController@phpinfo');
		$api->get('/push','ActorController@push');

		
       /* $api->get('posts', 'PostController@index');
        $api->get('posts/{id}', 'PostController@show');*/
    });

    $api->group(['namespace' => 'App\Api\Controllers','middleware'=>'jwt.auth'], function ($api) {
        $api->get('posts', 'PostController@index');
        $api->get('posts/{id}', 'PostController@show');
        $api->get('me','AuthController@getAuthenticatedUser');
    });

});

