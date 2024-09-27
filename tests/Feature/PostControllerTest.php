<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Repost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_display_all_posts()
    {
        // Create some dummy posts along with associated users
        $posts = Post::factory()->count(3)->create();

        // Make a GET request to the /posts route
        $response = $this->get('/posts');

        // Assert that the status is 200
        $response->assertStatus(200);

        // Assert that the Inertia response contains the 'posts' data
        $response->assertInertia(function (AssertableInertia $page) use ($posts) {
            $page->component('Posts/Index')
                 ->has('posts', 3) // Expecting 3 posts
                 ->where('posts.0.title', $posts[0]->title) // Check first post's title
                 ->where('posts.1.title', $posts[1]->title); // Check second post's title
        });
    }

    /** @test */
    public function it_can_store_a_post()
    {
        $user = User::factory()->create();
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
            'image' => null, // For testing without an image
        ];

        $response = $this->actingAs($user)->post('/posts', $postData);

        $this->assertCount(1, Post::all());
        $this->assertDatabaseHas('posts', $postData + ['user_id' => $user->id]);
        $response->assertRedirect('/posts');
        $response->assertSessionHas('success', 'Post created successfully!');
    }

    /** @test */
    public function test_can_update_post()
    {
        $post = Post::factory()->create(); // Assuming you're using factories
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.update', $post->id), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'image' => null, // or include an image if needed
        ]);

        // Assert that the response status is 302 (redirect)
        $response->assertStatus(302);
        
        // Refresh the post model
        $post->refresh();
        
        // Ensure the post has been updated in the database
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);
    }

    /** @test */
    public function it_can_delete_a_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/posts/{$post->id}");

        $this->assertCount(0, Post::all());
        $response->assertRedirect('/user-feed');
        $response->assertSessionHas('success', 'Post successfully deleted.');
    }

    /** @test */
    public function test_can_repost_a_post()
    {
        // Create a user who will repost
        $user = User::factory()->create();

        // Create a post by another user
        $postOwner = User::factory()->create(); // Create a second user to own the post
        $post = Post::factory()->create(['user_id' => $postOwner->id]); // Use the second user's ID

        // Now act as the user and try to repost
        $response = $this->actingAs($user)->post('/posts/' . $post->id . '/repost');

        // Assert that the repost was successful and the correct message is shown
        $response->assertRedirect('/user-feed'); // Adjust this as necessary
        $this->assertDatabaseHas('reposts', [
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    /** @test */
    public function test_cannot_repost_own_post()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post by the user
        $post = Post::factory()->create(['user_id' => $user->id]);

        // Attempt to repost their own post
        $response = $this->actingAs($user)->post('/posts/' . $post->id . '/repost');

        // Assert that the response status is 302 (redirect) and has the correct error message
        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot repost your own post.');
    }

    /** @test */
    public function test_cannot_repost_already_reposted_post()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post by another user
        $postOwner = User::factory()->create(); // Create a second user to own the post
        $post = Post::factory()->create(['user_id' => $postOwner->id]); // Use the second user's ID

        // Simulate the user reposting the post
        Repost::create(['user_id' => $user->id, 'post_id' => $post->id]); // Initial repost

        // Attempt to repost the same post again
        $response = $this->actingAs($user)->post('/posts/' . $post->id . '/repost');

        // Assert that the response status is 302 (redirect) and has the correct error message
        $response->assertRedirect();
        $response->assertSessionHas('error', 'You have already reposted this post.');
    }
}
