<?php

namespace App\Api\Controllers;

use App\Api\Transformer\PostTransformer;
use App\Post;
use Illuminate\Http\Request;

class PostController extends BaseController
{
    public function index()
    {
        $posts =  Post::all();

        return $this->collection($posts,new PostTransformer());
    }

    public function show($id)
    {

        $post = Post::find($id);

        if(! $post){
            return $this->response->errorNotFound('Lesson not found');
        }
        $data = [
            'code'=>1,
            'data'=>"这是一条成功的数据"
        ];
//        return json_encode($data);
        return $this->item($post,new PostTransformer());
    }
	
	public function test(Request $request){
		return response()->json(['name'=>$request->get('name')]);
	}
}
