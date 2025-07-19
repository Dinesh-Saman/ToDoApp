<template>
  <form @submit.prevent="handleSubmit" class="mb-4 p-4 bg-white rounded shadow">
    <div class="mb-2">
      <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
      <input
        v-model="form.title"
        id="title"
        type="text"
        placeholder="Enter title"
        class="w-full p-2 border rounded mt-1"
        required
      />
    </div>
    <div class="mb-2">
      <label for="body" class="block text-sm font-medium text-gray-700">Body</label>
      <textarea
        v-model="form.body"
        id="body"
        placeholder="Enter note body"
        class="w-full p-2 border rounded mt-1"
        rows="3"
      ></textarea>
    </div>
    <div class="mb-2">
      <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
      <input
        v-model="form.category"
        id="category"
        type="text"
        placeholder="Enter category (optional)"
        class="w-full p-2 border rounded mt-1"
      />
    </div>
    <button
      type="submit"
      class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
    >
      Add Note
    </button>
  </form>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';

export default {
  emits: ['note-created'],
  setup(props, { emit }) {
    const form = ref({
      title: '',
      body: '',
      category: '',
    });

    const handleSubmit = async () => {
      try {
        await axios.post('http://localhost:8000/api/notes', form.value);
        emit('note-created');
        form.value = { title: '', body: '', category: '' }; // Reset form
      } catch (error) {
        console.error('Error adding note:', error);
        // Optionally, show an error message to the user
      }
    };

    return { form, handleSubmit };
  },
};
</script>

<style scoped>
.form-group {
  margin-bottom: 1rem;
}
label {
  display: block;
  margin-bottom: 0.5rem;
}
input,
textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 0.25rem;
}
button {
  padding: 0.5rem 1rem;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.25rem;
  cursor: pointer;
}
button:hover {
  background-color: #2563eb;
}
</style>