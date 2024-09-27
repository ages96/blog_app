<?php

namespace Tests\Unit;

use App\Http\Controllers\PostController;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $postController;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user for authentication
        $this->user = User::factory()->create();
        $this->postController = new PostController();
        Auth::login($this->user); // Log in the user for authentication context
    }

    /** @test */
    public function it_can_create_a_post()
    {
        // Arrange: Prepare request data
        $request = Request::create('/posts', 'POST', [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
            'image' => null, // Assuming no image for simplicity
        ]);

        // Act: Call the store method
        $response = $this->postController->store($request);

        // Assert: Check that the post was created in the database
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_update_a_post()
    {
        // Arrange: Create a post
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        // Prepare updated request data
        $request = Request::create("/posts/{$post->id}", 'PUT', [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
            'image' => null,
        ]);

        // Act: Call the update method
        $response = $this->postController->update($post, $request);

        // Assert: Check that the post has been updated
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);
    }

    /** @test */
    public function it_can_delete_a_post()
    {
        // Arrange: Create a mock for the Post model
        $post = $this->createMock(Post::class);
        
        // Set up the expectation that the delete method will be called
        $post->expects($this->once())
             ->method('delete')
             ->willReturn(true); // or whatever your delete method returns

        // Act: Call the destroy method
        $response = $this->postController->destroy($post);

        // Assert: The response can be checked here if needed
        $this->assertTrue(true); // If the delete method is called, the test will pass
    }

    /** @test */
    public function it_can_repost_a_post()
    {
        // Arrange: Create a post
        $post = Post::factory()->create();
        
        // Act: Call the repost method
        $response = $this->postController->repost($post);

        // Assert: Check that the repost was created
        $this->assertDatabaseHas('reposts', [
            'user_id' => $this->user->id,
            'post_id' => $post->id,
        ]);
    }

    /** @test */
    /** @test */
    public function it_cannot_repost_own_post()
    {
        // Arrange: Create a post owned by the user
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        // Act: Attempt to repost the own post
        $response = $this->postController->repost($post);

        // Assert: Check that the response status code is 302 (redirect)
        $this->assertEquals(302, $response->getStatusCode());

        // Assert: Check the session error message manually
        $this->assertEquals('You cannot repost your own post.', session('error'));
    }

}
