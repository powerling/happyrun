<?php


namespace App\Api\Transformer;

use App\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    public function transform(Post $post)
    {
        return [
            'title' => $post['title'],
            'content' => $post['body'],
            'is_free' => (boolean)$post['free']
        ];
    }
}