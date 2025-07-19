<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl transform transition-all duration-300 w-full max-w-4xl overflow-hidden">
      <div class="flex flex-col md:flex-row">
        <!-- Form Section -->
        <div class="w-full md:w-1/2 p-6">
          <h1 class="text-3xl font-extrabold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500">Edit Note</h1>
          <form @submit.prevent="handleSubmit" class="space-y-6">
            <div>
              <label for="title" class="block text-base font-semibold text-gray-700">Title</label>
              <input
                v-model="form.title"
                id="title"
                type="text"
                maxlength="255"
                class="w-full p-3 border-2 border-gray-200 rounded-lg mt-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                placeholder="Enter note title"
                required
              />
              <div class="text-sm text-gray-500 text-right">
                {{ form.title ? form.title.length : 0 }}/255 characters
              </div>
            </div>
            <div>
              <label for="body" class="block text-base font-semibold text-gray-700">Content</label>
              <textarea
                v-model="form.body"
                id="body"
                class="w-full p-3 border-2 border-gray-200 rounded-lg mt-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                rows="4"
                placeholder="Write your note here..."
              ></textarea>
            </div>
            <div>
              <label for="category" class="block text-base font-semibold text-gray-700">Category</label>
              <select
                v-model="form.category"
                id="category"
                class="w-full p-3 border-2 border-gray-200 rounded-lg mt-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 appearance-none bg-white"
                required
              >
                <option value="" disabled class="text-gray-400">Select a category</option>
                <option value="Work" class="text-gray-800">Work</option>
                <option value="Personal" class="text-gray-800">Personal</option>
                <option value="Shopping" class="text-gray-800">Shopping</option>
                <option value="Study" class="text-gray-800">Study</option>
                <option value="Health" class="text-gray-800">Health</option>
                <option value="Travel" class="text-gray-800">Travel</option>
              </select>
            </div>
            <p v-if="error" class="text-red-500 text-sm font-medium bg-red-100 p-2 rounded-md">{{ error }}</p>
            <div class="flex justify-end space-x-4">
              <RouterLink 
                to="/display" 
                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors duration-200"
              >
                Cancel
              </RouterLink>
              <button 
                type="submit" 
                class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 transition-all duration-200"
              >
                Save Changes
              </button>
            </div>
          </form>
        </div>

        <!-- Image Section -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center p-6">
          <img 
            src="https://images.pexels.com/photos/1162519/pexels-photo-1162519.jpeg" 
            alt="Editing notes illustration"
            class="rounded-lg object-cover h-full w-full max-h-[500px] shadow-md"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const id = route.params.id as string;
const form = ref({
  title: '',
  body: '',
  category: ''
});
const error = ref('');

const fetchNote = async () => {
  try {
    const response = await axios.get(`http://localhost:8000/api/notes/${id}`);
    form.value = response.data;
  } catch (err) {
    console.error('Error fetching note:', err);
    error.value = 'Failed to load note data';
  }
};

const handleSubmit = async () => {
  try {
    await axios.put(`http://localhost:8000/api/notes/${id}`, form.value);
    router.push('/display');
  } catch (err) {
    const errorResponse = err as { response?: { status: number; data: any } };
    if (errorResponse.response) {
      const { status, data } = errorResponse.response;
      if (status === 422) {
        error.value = 'Validation failed: ' + Object.values(data.errors).flat().join(', ');
      } else {
        error.value = 'An error occurred: ' + data.message;
      }
    } else {
      error.value = 'Network error, please try again';
    }
  }
};

onMounted(fetchNote);
</script>