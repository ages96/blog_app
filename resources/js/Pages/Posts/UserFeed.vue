<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const form = useForm({
  title: '',
  content: '',
  image: null,
});

const handleImageUpload = (e) => {
  form.image = e.target.files[0];
};

const submit = () => {
  form.post(route('posts.store'));
};

</script>

<template>
  <AppLayout title="My Posts & Reposts">
    <div class="container mx-auto py-8">
      <h1 class="text-4xl font-bold text-center mb-8 text-blue-600">My Posts & Reposts</h1>

      <!-- Flash Messages -->
      <div v-if="flash.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
        <strong class="font-bold">Success:</strong>
        <span class="block sm:inline">{{ flash.success }}</span>
      </div>
      <div v-if="flash.error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
        <strong class="font-bold">Error:</strong>
        <span class="block sm:inline">{{ flash.error }}</span>
      </div>

      <button @click="goBack" class="bg-gray-600 text-white px-4 py-2 rounded mb-4 flex items-center">
        <font-awesome-icon icon="arrow-left" class="mr-2" />Back
      </button>

      <button @click="goCreate" class="bg-gray-600 text-white px-4 py-2 rounded mb-4 flex items-center">
        <font-awesome-icon icon="fa-plus" class="mr-2" />Create Blog
      </button>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="post in posts" 
          :key="post.id" 
          :class="post.isRepost ? 'bg-gray-100 border border-gray-300' : 'bg-white border border-blue-500'" 
          class="rounded-lg shadow-lg overflow-hidden transition duration-300 ease-in-out hover:shadow-xl"
        >
          <a :href="`/posts/${post.id}`" class="block">
            <img 
              v-if="post.image" 
              :src="getImageUrl(post.image)" 
              alt="Post image" 
              class="w-full h-48 object-cover" 
            />
            <div class="p-6">
              <h2 class="text-xl font-bold text-gray-800 hover:text-blue-600 transition-colors duration-200">
                {{ post.title }}
              </h2>
              <p class="text-gray-600 mt-2">
                {{ post.content.length > 100 ? post.content.substring(0, 100) + '...' : post.content }}
              </p>
              <span class="text-sm text-gray-500">{{ formatDate(post.created_at) }}</span>
              <div v-if="post.isRepost" class="mt-2">
                <span class="bg-blue-100 text-blue-600 text-xs font-bold py-1 px-2 rounded-full">Reposted</span>
              </div>
              <div class="flex justify-between items-center mt-4">
                <span class="text-blue-500 hover:underline">Read more</span>
                <!-- Edit Button -->
                <button @click.prevent="editPost(post.id)" class="text-blue-600 hover:underline">
                  Edit
                </button>
                <!-- Delete Button -->
                <button @click.prevent="deletePost(post.id)" class="text-red-600 hover:underline">
                  Delete
                </button>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
export default {
  props: {
    posts: {
      type: Array,
      required: true,
    },
    flash: {
      type: Object,
      required: false, // Optional, in case no flash messages are provided
    },
  },
  methods: {
    getImageUrl(image) {
      return image ? `/storage/${image}` : ''; // Handle the case when there is no image
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    goBack() {
      window.history.back();
    },
    goCreate() {
      this.$inertia.visit('/posts/create'); // Redirect to the create post page
    },
    editPost(postId) {
      this.$inertia.visit(`/posts/${postId}/edit`); // Redirect to the edit page of the specific post
    },
    deletePost(postId) {
      if (confirm('Are you sure you want to delete this post?')) {
        this.$inertia.delete(`/posts/${postId}`); // Send a delete request to the server
      }
    },
  },
};
</script>
