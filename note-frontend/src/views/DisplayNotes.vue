<template>
  <div class="mx-auto p-4 relative"> <!-- Added relative for positioning context -->
    <h1 class="text-2xl font-bold mb-4">Notes List</h1>
    <div class="flex items-start"> <!-- Flex container to align button with list height -->
      <NoteList :notes="notes" @delete-note="deleteNote" @edit-note="editNote" class="flex-1" />
      <!-- Add New Note Button at Upper Right Corner -->
      <RouterLink
        to="/add"
        class="absolute top-0 right-0 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200 mr-4 mt-4"
      >
        Add New Note
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import NoteList from '../components/NoteList.vue';
import axios from 'axios';

const notes = ref([]);
const router = useRouter();

const fetchNotes = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/notes');
    notes.value = response.data;
  } catch (error) {
    console.error('Error fetching notes:', error);
  }
};

const deleteNote = async (id: number) => {
  try {
    await axios.delete(`http://localhost:8000/api/notes/${id}`);
    fetchNotes();
  } catch (error) {
    console.error('Error deleting note:', error);
  }
};

const editNote = (note: any) => {
  router.push({ name: 'EditNote', params: { id: note.id }, props: { note } });
};

onMounted(fetchNotes);
</script>