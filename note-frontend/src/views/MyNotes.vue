<template>
  <div class="min-h-screen flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between bg-white border-b border-gray-200 p-4 flex-shrink-0">
      <h1 class="text-2xl font-bold text-gray-900">My Notes</h1>
      <!-- Add New Note Button -->
      <RouterLink
        to="/add_note"
        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 flex items-center space-x-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        <span>Add New Note</span>
      </RouterLink>
    </div>
    
    <!-- Main Content Area -->
    <div class="flex-1 overflow-y-auto pb-20">
      <NoteList 
        :notes="sortedNotes" 
        :selected-note-id="selectedNoteId"
        @delete-note="deleteNote" 
        @edit-note="EditNote" 
        @update-note="updateNote"
        @select-note="selectNote"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import NoteList from '../components/NoteList.vue';
import axios from 'axios';

const notes = ref([]);
const selectedNoteId = ref(null);
const router = useRouter();

// Sort notes by creation date (newest first)
const sortedNotes = computed(() => {
  return [...notes.value].sort((a, b) => {
    return new Date(b.created_at) - new Date(a.created_at);
  });
});

const fetchNotes = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/notes');
    notes.value = response.data;
    
    // Auto-select the most recent note if there are any notes
    if (notes.value.length > 0) {
      selectedNoteId.value = sortedNotes.value[0].id;
    }
  } catch (error) {
    console.error('Error fetching notes:', error);
  }
};

const selectNote = (id) => {
  selectedNoteId.value = id;
};

const deleteNote = (id: number) => {
  // Remove the note from the local array immediately (no need for API call here since NoteList already did it)
  notes.value = notes.value.filter(note => note.id !== id);
  
  // If we deleted the selected note, select the next newest one
  if (selectedNoteId.value === id) {
    if (sortedNotes.value.length > 0) {
      selectedNoteId.value = sortedNotes.value[0].id;
    } else {
      selectedNoteId.value = null;
    }
  }
};

const updateNote = (updatedNote: any) => {
  const index = notes.value.findIndex(note => note.id === updatedNote.id);
  if (index !== -1) {
    notes.value[index] = updatedNote;
  }
  
  // Keep the updated note selected
  selectedNoteId.value = updatedNote.id;
};

const EditNote = (note: any) => {
  router.push({ name: 'EditNote', params: { id: note.id }, props: { note } });
};

onMounted(fetchNotes);
</script>

<style scoped>
.min-h-screen {
  min-height: 100vh;
}
</style>