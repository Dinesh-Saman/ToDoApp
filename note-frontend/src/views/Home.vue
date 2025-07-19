<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Note Taking App</h1>
    <NoteForm @note-created="fetchNotes" />
    <NoteList :notes="notes" @delete-note="deleteNote" @edit-note="EditTask" />
    <RouterView />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import NoteForm from '../components/NoteForm.vue';
import NoteList from '../components/NoteList.vue';
import axios from 'axios';

export default {
  components: { NoteForm, NoteList },
  setup() {
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

    const deleteNote = async (id) => {
      try {
        await axios.delete(`http://localhost:8000/api/notes/${id}`);
        fetchNotes();
      } catch (error) {
        console.error('Error deleting note:', error);
      }
    };

    const EditTask = async (note) => {
      // Fetch the latest note data to ensure consistency
      try {
        const response = await axios.get(`http://localhost:8000/api/notes/${note.id}`);
        router.push({ name: 'EditTask', params: { id: note.id }, props: { note: response.data } });
      } catch (error) {
        console.error('Error fetching note for edit:', error);
      }
    };

    onMounted(fetchNotes);

    return { notes, fetchNotes, deleteNote, EditTask };
  },
};
</script>

<style scoped>
.container {
  max-width: 800px;
}
</style>