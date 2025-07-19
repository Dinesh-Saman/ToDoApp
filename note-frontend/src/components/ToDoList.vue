<template>
  <div class="mt-4 relative">
    <div class="mb-4">
      <input
        v-model="searchQuery"
        placeholder="Search tasks..."
        class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
      />
    </div>

    <!-- Filter and Sort Controls -->
    <div class="flex items-center justify-between mb-4 bg-white rounded-lg shadow-md p-2 transition-all duration-300 hover:shadow-lg">
      <div class="flex flex-wrap gap-2">
        <!-- Status Filters -->
        <div class="flex items-center space-x-2 mr-2">
          <span class="text-sm font-medium text-gray-700">Status:</span>
          <button
            v-for="status in ['All', 'To Do', 'Done']"
            :key="status"
            @click="selectedStatus = status"
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium transition-all duration-200',
              selectedStatus === status
                ? getStatusButtonClass(status, true)
                : getStatusButtonClass(status, false)
            ]"
          >
            {{ status }}
          </button>
        </div>

        <!-- Divider -->
        <div class="border-l border-gray-300 h-6 my-auto"></div>

        <!-- Category Filters -->
        <div class="flex items-center space-x-2 ml-2">
          <span class="text-sm font-medium text-gray-700">Category:</span>
          <button
            v-for="category in ['All', 'Work', 'Personal', 'Shopping', 'Study', 'Health', 'Travel']"
            :key="category"
            @click="selectedCategory = category"
            :class="[
              'px-3 py-1 rounded-full text-sm font-medium transition-all duration-200',
              selectedCategory === category
                ? getCategoryButtonClass(category, true)
                : getCategoryButtonClass(category, false)
            ]"
          >
            {{ category }}
          </button>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <span class="text-sm font-medium text-gray-700">Sort by:</span>
        <select
          v-model="sortOrder"
          class="p-2 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 appearance-none bg-white text-gray-700 font-medium pr-8"
        >
          <option value="desc">Newest First</option>
          <option value="asc">Oldest First</option>
        </select>
        <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </span>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="note in filteredNotes"
        :key="note.id"
        class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-all duration-300 cursor-pointer hover:bg-gray-50"
        :class="{ 'bg-gray-50': note.isdone || note.completed }"
        @click="openModal(note)"
      >
        <div class="flex justify-between items-start mb-4">
          <span
            class="text-xs font-semibold px-2 py-1 rounded-full"
            :class="getCategoryLabelClass(note.category)"
          >
            {{ note.category || 'N/A' }}
          </span>
          <div class="flex items-center space-x-2">
            <input
              type="checkbox"
              :checked="note.isdone || note.completed"
              @click.stop="toggleNoteStatus(note)"
              class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
            />
            <button
              @click.stop="$emit('edit-note', note)"
              class="text-blue-500 hover:text-blue-700"
              title="Edit task"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
              </svg>
            </button>
            <button
              @click.stop="showDeleteDialog(note.id)"
              class="text-red-500 hover:text-red-700"
              title="Delete task"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>
        <h3 class="text-xl font-bold mb-2" :class="{ 'line-through text-gray-500': note.isdone || note.completed }">
          {{ note.title }}
        </h3>
        <p class="text-gray-600 mt-2 line-clamp-3" :class="{ 'line-through': note.isdone || note.completed }">
          {{ truncateText(note.body, 180) }}
        </p>
        <div class="text-right text-gray-500 text-sm mt-2">
          {{ formatDate(note.created_at) }}
        </div>
      </div>
    </div>

    <p v-if="filteredNotes.length === 0" class="mt-2 text-gray-500">No tasks found.</p>

    <!-- Modal for full task view -->
    <div v-if="selectedNote" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50" @click.self="closeModal">
      <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-start mb-4">
            <span
              class="text-xs font-semibold px-2 py-1 rounded-full"
              :class="getCategoryLabelClass(selectedNote.category)"
            >
              {{ selectedNote.category || 'N/A' }}
            </span>
            <div class="flex items-center space-x-2">
              <input
                type="checkbox"
                :checked="selectedNote.isdone || selectedNote.completed"
                @click.stop="toggleNoteStatus(selectedNote)"
                class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
              />
              <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
          
          <h2 class="text-2xl font-bold mb-4" :class="{ 'line-through text-gray-500': selectedNote.isdone || selectedNote.completed }">
            {{ selectedNote.title }}
          </h2>
          
          <div class="prose max-w-none">
            <p class="whitespace-pre-line" :class="{ 'line-through': selectedNote.isdone || selectedNote.completed }">{{ selectedNote.body }}</p>
          </div>
          
          <div class="mt-6 text-sm text-gray-500">
            Created: {{ formatDate(selectedNote.created_at) }}
          </div>
        </div>
        
        <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
          <RouterLink
            to="/add"
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors"
          >
            Add New Task
          </RouterLink>
          <button
            @click="$emit('edit-note', selectedNote)"
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
          >
            Edit
          </button>
          <button
            @click="showDeleteDialog(selectedNote.id)"
            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors"
          >
            Delete
          </button>
          <button
            @click="closeModal"
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- PrimeVue Delete Confirmation Dialog -->
    <Dialog 
      v-model:visible="deleteDialogVisible" 
      header="Confirm Deletion" 
      :style="{ width: '450px' }" 
      :modal="true"
      :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
    >
      <div class="flex align-items-center gap-3">
        <i class="pi pi-exclamation-triangle text-yellow-500" style="font-size: 2rem" />
        <span>Are you sure you want to delete this task? This action cannot be undone.</span>
      </div>
      <template #footer>
        <Button 
          label="Cancel" 
          icon="pi pi-times" 
          @click="deleteDialogVisible = false" 
          class="p-button-text" 
        />
        <Button 
          label="Delete" 
          icon="pi pi-trash" 
          @click="confirmDelete" 
          class="p-button-danger" 
          autofocus 
        />
      </template>
    </Dialog>

    <!-- PrimeVue Toast (for success/error messages) positioned at bottom-right -->
    <Toast position="bottom-right" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const props = defineProps<{
  notes: any[];
}>();
const emit = defineEmits(['edit-note', 'delete-note', 'update-note']);

const searchQuery = ref('');
const sortOrder = ref('desc');
const selectedCategory = ref('All');
const selectedStatus = ref('All');
const selectedNote = ref(null);
const deleteDialogVisible = ref(false);
const noteToDeleteId = ref<number | null>(null);

const showDeleteDialog = (id: number) => {
  noteToDeleteId.value = id;
  deleteDialogVisible.value = true;
};

const openModal = (note: any) => {
  selectedNote.value = note;
};

const closeModal = () => {
  selectedNote.value = null;
};

const filteredNotes = computed(() => {
  let result = [...(props.notes || [])];
  
  // Apply search filter
  if (searchQuery.value) {
    result = result.filter(note => {
      const category = note.category || 'N/A';
      return (
        note.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        note.body.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        category.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    });
  }
  
  // Apply category filter
  if (selectedCategory.value !== 'All') {
    result = result.filter(note => note.category === selectedCategory.value);
  }
  
  // Apply status filter
  if (selectedStatus.value !== 'All') {
    result = result.filter(note => {
      const isDone = note.isdone || note.completed || false;
      return selectedStatus.value === 'Done' ? isDone : !isDone;
    });
  }
  
  // Apply sorting
  return result.sort((a, b) => {
    const valueA = new Date(a.created_at || '');
    const valueB = new Date(b.created_at || '');
    return sortOrder.value === 'asc' ? valueA - valueB : valueB - valueA;
  });
});

const formatDate = (date: string | undefined) => {
  return date ? new Date(date).toLocaleDateString() : 'N/A';
};

const truncateText = (text: string, maxLength: number) => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};

const confirmDelete = async () => {
  if (!noteToDeleteId.value) return;

  try {
    // Make the API call to delete
    await axios.delete(`http://localhost:8000/api/notes/${noteToDeleteId.value}`);
    
    // Immediately emit to parent to update the local state
    emit('delete-note', noteToDeleteId.value);
    
    // Close modal if the deleted note was open
    if (selectedNote.value && selectedNote.value.id === noteToDeleteId.value) {
      closeModal();
    }
    
    // Close delete dialog
    deleteDialogVisible.value = false;
    
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: 'Task deleted successfully',
      life: 3000
    });
  } catch (error) {
    console.error('Error deleting task:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to delete task',
      life: 3000
    });
  } finally {
    noteToDeleteId.value = null;
  }
};

const toggleNoteStatus = async (note: any) => {
  console.log('toggleNoteStatus called for note:', note);
  
  // Get current status
  const currentStatus = note.isdone || note.completed || false;
  const newStatus = !currentStatus;
  
  // Create updated note object
  const updatedNote = { 
    ...note, 
    isdone: newStatus,
    completed: newStatus 
  };
  
  try {
    console.log('Sending PUT request with data:', updatedNote);
    
    // Immediately emit the updated note to parent for instant UI update
    emit('update-note', updatedNote);
    
    // Update selectedNote if it's currently open in modal
    if (selectedNote.value && selectedNote.value.id === note.id) {
      selectedNote.value = updatedNote;
    }
    
    // Make API call to persist changes
    await axios.put(`http://localhost:8000/api/notes/${note.id}`, updatedNote);
    
    // Show success toast
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: `Task marked as ${newStatus ? 'Done' : 'To Do'}`,
      life: 2000
    });
    
  } catch (error) {
    console.error('Error updating task status:', error);
    
    // Revert the change by emitting the original note back
    emit('update-note', note);
    
    // Revert selectedNote if needed
    if (selectedNote.value && selectedNote.value.id === note.id) {
      selectedNote.value = note;
    }
    
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to update task status. Changes reverted.',
      life: 3000
    });
  }
};

const getStatusButtonClass = (status: string, isActive: boolean) => {
  const baseClasses = {
    'All': isActive ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-800',
    'To Do': isActive ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-800',
    'Done': isActive ? 'bg-green-500 text-white' : 'bg-green-100 text-green-800'
  };
  return baseClasses[status] || 'bg-gray-100 text-gray-800';
};

const getCategoryButtonClass = (category: string, isActive: boolean) => {
  const baseClasses = {
    'Work': isActive ? 'bg-red-500 text-white' : 'bg-red-100 text-red-800',
    'Personal': isActive ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-800',
    'Shopping': isActive ? 'bg-green-500 text-white' : 'bg-green-100 text-green-800',
    'Study': isActive ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-800',
    'Health': isActive ? 'bg-purple-500 text-white' : 'bg-purple-100 text-purple-800',
    'Travel': isActive ? 'bg-indigo-500 text-white' : 'bg-indigo-100 text-indigo-800',
    'All': isActive ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-800'
  };
  return baseClasses[category] || 'bg-gray-100 text-gray-800';
};

const getCategoryLabelClass = (category: string) => {
  const baseClasses = {
    'Work': 'bg-red-200 text-red-800',
    'Personal': 'bg-blue-200 text-blue-800',
    'Shopping': 'bg-green-200 text-green-800',
    'Study': 'bg-yellow-200 text-yellow-800',
    'Health': 'bg-purple-200 text-purple-800',
    'Travel': 'bg-indigo-200 text-indigo-800'
  };
  return baseClasses[category] || 'bg-gray-200 text-gray-800';
};
</script>

<style scoped>
/* Existing styles */
.bg-white {
  background-color: #ffffff;
}
.p-4 {
  padding: 1rem;
}
.rounded-lg {
  border-radius: 0.5rem;
}
.shadow {
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
.hover\:shadow-lg:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.transition-shadow {
  transition: box-shadow 0.3s ease;
}
button {
  padding: 0.25rem 0.5rem;
  cursor: pointer;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Modal styles */
.fixed {
  position: fixed;
}
.inset-0 {
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
.z-50 {
  z-index: 50;
}
.bg-opacity-50 {
  background-color: rgba(0, 0, 0, 0.5);
}
.max-h-\[90vh\] {
  max-height: 90vh;
}
.overflow-y-auto {
  overflow-y: auto;
}

/* Mobile responsiveness */
@media (max-width: 640px) {
  .max-w-2xl {
    width: 95%;
  }
}

/* Filter and Sort Styles */
.appearance-none {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/* PrimeVue Dialog custom styles */
:deep(.p-dialog .p-dialog-header) {
  border-bottom: 1px solid #e5e7eb;
  padding: 1.25rem;
}

:deep(.p-dialog .p-dialog-content) {
  padding: 1.25rem;
}

:deep(.p-dialog .p-dialog-footer) {
  border-top: 1px solid #e5e7eb;
  padding: 1rem 1.25rem;
}

/* PrimeVue Button styles */
:deep(.p-button) {
  padding: 0.5rem 1rem;
  transition: all 0.2s;
}

:deep(.p-button.p-button-danger) {
  background-color: #ef4444;
  border-color: #ef4444;
}

:deep(.p-button.p-button-danger:hover) {
  background-color: #dc2626;
  border-color: #dc2626;
}

:deep(.p-button.p-button-text) {
  color: #4b5563;
  background-color: transparent;
  border-color: transparent;
}

:deep(.p-button.p-button-text:hover) {
  background-color: #f3f4f6;
  color: #1f2937;
}

/* PrimeVue Toast positioning styles */
:deep(.p-toast) {
  z-index: 9999;
}

:deep(.p-toast .p-toast-message) {
  margin: 0 0 1rem 0;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  border-radius: 0.375rem;
}

:deep(.p-toast.p-toast-bottom-right) {
  right: 1rem;
  bottom: 1rem;
}

/* Success toast styling */
:deep(.p-toast .p-toast-message.p-toast-message-success) {
  background-color: #10b981;
  border: 1px solid #059669;
  color: white;
}

/* Error toast styling */
:deep(.p-toast .p-toast-message.p-toast-message-error) {
  background-color: #ef4444;
  border: 1px solid #dc2626;
  color: white;
}

/* Info toast styling */
:deep(.p-toast .p-toast-message.p-toast-message-info) {
  background-color: #3b82f6;
  border: 1px solid #2563eb;
  color: white;
}

/* Corrected mobile responsiveness for filter buttons */
@media (max-width: 640px) {
  .flex.space-x-2 {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .flex.space-x-2 > * + * {
    margin-left: 0;
  }
}

/* Filter section styling */
.flex-wrap {
  align-items: center;
}

.border-l {
  border-left-width: 1px;
}

.h-6 {
  height: 1.5rem;
}

.my-auto {
  margin-top: auto;
  margin-bottom: auto;
}
</style>