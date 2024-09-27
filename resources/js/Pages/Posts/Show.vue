<template>
  <AppLayout :title="post.title">
    <div class="container mx-auto py-8">
      <!-- Flash Message Display -->
      <div v-if="flash" class="mb-4">
        <div v-if="flash.error" class="alert alert-error">
          {{ flash.error }}
        </div>
        <div v-if="flash.success" class="alert alert-success">
          {{ flash.success }}
        </div>
      </div>

      <!-- Back Button with FontAwesome Icon -->
      <button @click="goBack" class="bg-gray-600 text-white px-4 py-2 rounded mb-4 flex items-center">
        <font-awesome-icon icon="arrow-left" class="mr-2" /> <!-- Back Arrow Icon -->
        Back to Posts
      </button>

      <h1 class="text-4xl font-bold text-center mb-6 text-blue-600">{{ post.title }}</h1>
      <img :src="getImageUrl(post.image)" alt="Post image" class="w-full h-72 object-cover mb-4 rounded-lg shadow-md" />

      <!-- Render content as Markdown -->
      <div v-html="renderedContent" class="text-gray-700 mb-4"></div>

      <!-- Repost Button with FontAwesome Icon -->
      <button @click="repost" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 flex items-center">
        <font-awesome-icon icon="retweet" class="mr-2" /> <!-- Retweet Icon -->
        Repost
      </button>

      <div class="mt-8">
        <h2 class="text-2xl font-semibold mb-4">Comments</h2>
        <form @submit.prevent="addComment" class="mb-4">
          <textarea 
            v-model="comment" 
            class="w-full p-2 border border-gray-300 rounded" 
            placeholder="Add a comment..." 
            required
          ></textarea>
          <!-- Submit Button with FontAwesome Icon -->
          <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded flex items-center">
            <font-awesome-icon icon="paper-plane" class="mr-2" /> <!-- Paper Plane Icon -->
            Submit
          </button>
        </form>
      </div>

      <div class="mt-4">
        <ul>
          <li v-for="comment in post.comments" :key="comment.id" class="border-b py-2">
            <strong class="text-gray-800">{{ comment.user.name }}</strong>: {{ comment.body }}
          </li>
        </ul>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'; 
import { computed } from 'vue'; // Ensure this is present
import MarkdownIt from 'markdown-it'; // Import markdown-it

// Define props
const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
  flash: {
    type: Object,
    default: () => ({}), 
  },
});

// Create a MarkdownIt instance
const md = new MarkdownIt();

// Render Markdown content to HTML
const renderedContent = computed(() => {
  return md.render(props.post.content); // Convert Markdown to HTML using props.post
});

// Handle image uploads
const form = useForm({
  title: '',
  content: '',
  image: null,
});

const handleImageUpload = (e) => {
  form.image = e.target.files[0];
};

// Submit post
const submit = () => {
  form.post(route('posts.store'));
};

// Other methods as before...
</script>

<script>
export default {
  data() {
    return {
      comment: '',
      flashMessage: this.flash.success || this.flash.error || '', 
      flashType: this.flash.success ? 'alert-success' : this.flash.error ? 'alert-error' : '',
    };
  },
  mounted() {
    console.log('Flash data:', this.flash); 

    if (this.flashMessage) {
      setTimeout(() => {
        this.flashMessage = '';
      }, 5000); 
    }
  },
  methods: {
    getImageUrl(image) {
      return image ? `/storage/${image}` : ''; 
    },
    async addComment() {
      try {
        const payload = {
          body: this.comment,
          post: this.post,
        };
        await this.$inertia.post(`/posts/${this.post.id}/comments`, payload);
        this.comment = ''; 
      } catch (error) {
        console.error('Error adding comment:', error);
      }
    },
    async repost() {
      try {
        await this.$inertia.post(`/posts/${this.post.id}/repost`);
      } catch (error) {
        console.error('Error reposting:', error);
      }
    },
    goBack() {
      this.$inertia.visit('/posts');
    },
  },
};
</script>
