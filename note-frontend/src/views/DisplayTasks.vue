<template>
  <div class="mx-auto p-4 relative"> <!-- Added relative for positioning context -->
    <h1 class="text-2xl font-bold mb-4">My Do List</h1>
    <div class="flex items-start"> <!-- Flex container to align button with list height -->
      <ToDoList 
        :notes="taskNotes" 
        @delete-note="handleDeleteNote" 
        @edit-note="EditTask" 
        @update-note="handleUpdateNote"
        class="flex-1" 
      />
      <!-- Add New Note Button at Upper Right Corner -->
      <RouterLink
        to="/add_task"
        class="absolute top-0 right-0 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors duration-200 mr-4 mt-4"
      >
        Add New Task
      </RouterLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import ToDoList from '../components/ToDoList.vue';
import axios from 'axios';

const notes = ref([]);
const router = useRouter();

// Computed property to filter only task type notes
const taskNotes = computed(() => {
  return notes.value.filter(note => note.type === 'task');
});

const fetchNotes = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/notes');
    notes.value = response.data;
  } catch (error) {
    console.error('Error fetching notes:', error);
  }
};

// Handle immediate note updates (for status changes)
const handleUpdateNote = (updatedNote: any) => {
  const index = notes.value.findIndex(note => note.id === updatedNote.id);
  if (index !== -1) {
    // Immediately update the note in local state
    notes.value[index] = { ...updatedNote };
    console.log('Note updated immediately in parent:', updatedNote);
  }
};

// Handle immediate note deletion
const handleDeleteNote = (deletedId: number) => {
  // Immediately remove from local state
  notes.value = notes.value.filter(note => note.id !== deletedId);
  console.log('Note deleted immediately in parent:', deletedId);
};

// Legacy delete function - keeping for backward compatibility but not using fetchNotes
const deleteNote = async (id: number) => {
  try {
    await axios.delete(`http://localhost:8000/api/notes/${id}`);
    // Use immediate update instead of refetching
    handleDeleteNote(id);
  } catch (error) {
    console.error('Error deleting note:', error);
  }
};

const EditTask = (note: any) => {
  router.push({ name: 'EditTask', params: { id: note.id }, props: { note } });
};

onMounted(fetchNotes);
</script>