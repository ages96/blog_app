<template>
  <AppLayout title="Public Posts">
    <div class="container mx-auto py-8">
      <h1 class="text-4xl font-bold text-center mb-8 text-blue-600">
        <font-awesome-icon icon="globe" class="mr-2" /> Public Posts
      </h1>
      <!-- Back Button with FontAwesome Icon -->
      <button @click="goBack" class="bg-gray-600 text-white px-4 py-2 rounded mb-4 flex items-center">
        <font-awesome-icon icon="arrow-left" class="mr-2" /> <!-- Back Arrow Icon -->
        Back to Home
      </button>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="post in posts" 
          :key="post.id" 
          class="bg-white rounded-lg shadow-lg overflow-hidden transform transition-transform duration-300 hover:scale-105 cursor-pointer"
        >
          <a :href="`/posts/${post.id}`" class="block">
            <img 
              :src="getImageUrl(post.image)" 
              alt="Post image" 
              class="w-full h-48 object-cover transition-transform duration-300 hover:scale-110"
            />
            <div class="p-6">
              <h2 class="text-xl font-bold text-gray-800 hover:text-blue-600 transition-colors duration-200">
                {{ post.title }}
              </h2>
              <!-- Display Author Name -->
              <p class="text-gray-500 italic mb-2">by {{ post.username }}</p>
              <p class="text-gray-600 mt-2">{{ post.excerpt }}</p>
              <div class="flex justify-between items-center mt-4">
                <span class="text-sm text-gray-500">{{ formatDate(post.created_at) }}</span>
                <span class="text-blue-500 hover:underline">Read more</span>
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
  },
  methods: {
    getImageUrl(image) {
      return `/storage/${image}`;
    },
    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    },
    goBack() {
      this.$inertia.visit('/'); // Redirect to the home page
    }
  },
};
</script>

<style scoped>
/* Additional styles for container and layout */
.container {
  max-width: 1200px; /* Adjust max-width as needed */
}

h1 {
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}
</style>