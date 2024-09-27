<?php 

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Post;

class UserFeedTransformer extends TransformerAbstract
{
    public function transform(Post $post)
    {
        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'title' => $post->title,
            'content' => $post->content,
            'image' => $post->image,
            'created_at' => $post->created_at->toIso8601String(), // Format as needed
            'updated_at' => $post->updated_at->toIso8601String(), // Format as needed
            'is_repost' => $post->isRepost ?? false,
        ];
    }
}
