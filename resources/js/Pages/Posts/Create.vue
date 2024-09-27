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
  <AppLayout title="Create Post">
    <div class="m-8">
      
      <button @click="goBack" class="bg-gray-600 text-white px-4 py-2 rounded mb-4 flex items-center">
        <font-awesome-icon icon="arrow-left" class="mr-2" />Back
      </button>

      <h1 class="text-3xl font-bold mb-6">Create a New Post</h1>
      <form @submit.prevent="submit" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" enctype="multipart/form-data">
        <div class="mb-4">
          <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
          <input 
            id="title"
            v-model="form.title" 
            type="text" 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
            placeholder="Enter post title" 
            required
          />
        </div>

        <div class="mb-4">
          <label for="content" class="block text-gray-700 text-sm font-bold mb-2">Content:</label>
          <v-md-editor 
            v-model="form.content"
            height="400px" 
          />
        </div>

        <div class="mb-4">
          <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image:</label>
          <input 
            id="image"
            type="file" 
            @change="handleImageUpload" 
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            accept="image/*"
            required
          />
        </div>

        <div class="flex items-center justify-between">
          <button 
            type="submit" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          >
            Create Post
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script>
export default {
  methods: {
    goBack() {
      window.history.back();
    }
  },
};
</script>

<style scoped>
/* Global styles can be added here */
.container {
  max-width: 600px;
}
</style>
