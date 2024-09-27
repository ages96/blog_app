<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Post;

class PostTransformer extends TransformerAbstract
{
    /**
     * Transform a post model into a public representation.
     *
     * @param  Post  $post
     * @return array
     */
    public function transform(Post $post)
    {
        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'username'=>$post->user->name,
            'title' => $post->title,
            'content' => $this->formatContent($post->content),
            'image' => $post->image,
            'created_at' => $post->created_at->toDateTimeString(),
            'updated_at' => $post->updated_at->toDateTimeString(),
        ];
    }

    /**
     * Format content for public view.
     *
     * @param  string  $content
     * @return string
     */
    private function formatContent(string $content)
    {
        // Here, you can use a Markdown parser or any formatting needed
        // For example, you can strip tags or convert markdown to HTML
        return $content; // Modify this to format as required
    }
}
