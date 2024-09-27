<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Repost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Transformers\PostTransformer;
use App\Transformers\UserFeedTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class PostController extends Controller
{
    protected $fractal;

    public function __construct()
    {
        $this->fractal = new Manager();
    }

    public function index()
    {
        // Fetch posts with the related user model
        $posts = Post::with('user')->latest()->get();

        // Create a Fractal resource for the collection of posts
        $resource = new Collection($posts, new PostTransformer());

        // Transform the resource to an array
        $data = $this->fractal->createData($resource)->toArray();

        // Return the transformed data to the Inertia view
        return inertia('Posts/Index', ['posts' => $data['data']]);
    }

    public function userPosts()
    {
        // Fetch user's original posts
        $userPosts = Post::where('user_id', auth()->id())->latest()->get()->map(function ($post) {
            $post->isRepost = false; // Mark as own post
            return $post;
        });

        // Fetch reposts and mark them
        $reposts = Repost::where('user_id', auth()->id())
            ->with('post') // Ensure the related post data is included
            ->latest()
            ->get()
            ->map(function ($repost) {
                $post = $repost->post;
                $post->isRepost = true; // Mark as repost
                return $post;
            });

        // Combine user posts and reposts
        $posts = $userPosts->concat($reposts)->sortByDesc('created_at');

        $resource = new Collection($posts, new UserFeedTransformer());
        $data = $this->fractal->createData($resource)->toArray();

        return inertia('Posts/UserFeed', [
            'posts' => $data['data'],
            'flash' => [
                'success' => session('success'), // Flash success message
                'error' => session('error'),     // Flash error message
            ],
        ]);
    }

    public function create()
    {
        return inertia('Posts/Create');
    }

    public function store(Request $request)
    {
        try {
            // Validate incoming request data
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // Store the image if it exists
            $imagePath = $request->file('image') ? $request->file('image')->store('images', 'public') : null;

            // Create a new post
            Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'image' => $imagePath,
                'user_id' => auth()->id(),
            ]);

            return redirect('/posts')->with('success', 'Post created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error during post creation: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error during post creation: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Database error: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('General error during post creation: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $post = Post::with('comments.user')->findOrFail($id); // Make sure to include comments and user

        $resource = new Item($post, new PostTransformer());
        $data = $this->fractal->createData($resource)->toArray();

        return inertia('Posts/Show', [
            'post' => $data['data'],
            'flash' => [
                'success' => session('success'), // Flash success message
                'error' => session('error'),     // Flash error message
            ],
        ]);
    }

    public function edit($id)
    {
        $post = Post::with('comments.user')->findOrFail($id);
        return inertia('Posts/Edit', compact('post'));
    }

    public function update(Post $post, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->file('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image')->store('images', 'public');
        } else {
            $imagePath = $post->image;
        }

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
        ]);

        return redirect("/user-feed")->with('success', 'Post successfully updated.');
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect("/user-feed")->with('success', 'Post successfully deleted.');
    }

    public function repost(Post $post)
    {
        // Check if the authenticated user is trying to repost their own post
        if ($post->user_id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot repost your own post.');
        }

        // Check if the post has already been reposted by the authenticated user
        $existingRepost = Repost::where('user_id', auth()->id())
            ->where('post_id', $post->id)
            ->first();

        if ($existingRepost) {
            return redirect()->back()->with('error', 'You have already reposted this post.');
        }

        // Create the repost
        Repost::create([
            'user_id' => auth()->id(),
            'post_id' => $post->id,
        ]);

        return redirect('/user-feed')->with('success', 'Post successfully reposted.');
    }
}
