<template>
  <div class="flex h-screen">
    <!-- Sidebar List -->
    <div class="w-1/3 bg-gray-50 border-r border-gray-200 overflow-y-auto">
      <div class="p-4">
        <div class="mb-4">
          <input
            v-model="searchQuery"
            placeholder="Search notes..."
            class="w-full p-2 border rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
          />
        </div>

        <!-- Filter and Sort Controls -->
        <div class="mb-4 bg-white rounded-lg shadow-sm p-3">
          <!-- Sort Control and All Button -->
          <div class="flex items-center space-x-3 mb-3">
            <div class="flex items-center space-x-2">
              <span class="text-xs font-medium text-gray-700">Sort:</span>
              <select
                v-model="sortOrder"
                class="p-1 border border-gray-200 rounded text-xs focus:ring-1 focus:ring-blue-500"
              >
                <option value="desc">Newest First</option>
                <option value="asc">Oldest First</option>
              </select>
            </div>
            
            <button
            @click="selectedCategory = 'All'"
            :class="[
                'w-full px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 hover:shadow-sm',
                selectedCategory === 'All'
                ? getCategoryButtonClass('All', true)
                : getCategoryButtonClass('All', false)
            ]"
            >
            All
            </button>
          </div>

          <!-- Category Filters -->
          <div>
            <span class="block text-xs font-medium text-gray-700 mb-2">Category:</span>
            <div class="grid grid-cols-3 gap-2">
              <button
                v-for="category in ['Work', 'Personal', 'Shopping', 'Study', 'Health', 'Travel']"
                :key="category"
                @click="selectedCategory = category"
                :class="[
                  'px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 hover:shadow-sm',
                  selectedCategory === category
                    ? getCategoryButtonClass(category, true)
                    : getCategoryButtonClass(category, false)
                ]"
              >
                {{ category }}
              </button>
            </div>
          </div>
        </div>

        <!-- Notes List -->
        <div class="space-y-2">
          <div
            v-for="note in filteredNotes"
            :key="note.id"
            :class="[
              'p-3 bg-white rounded-lg shadow-sm cursor-pointer transition-all duration-200 hover:shadow-md border-l-4',
              selectedNote?.id === note.id ? 'ring-2 ring-blue-500 bg-blue-50' : '',
              getCategoryBorderClass(note.category)
            ]"
            @click="selectNote(note)"
          >
            <div class="flex justify-between items-start">
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <span
                    class="text-xs font-semibold px-2 py-1 rounded-full"
                    :class="getCategoryLabelClass(note.category)"
                  >
                    {{ note.category || 'N/A' }}
                  </span>
                  <span class="text-xs text-gray-500">
                    {{ formatDate(note.created_at) }}
                  </span>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 truncate mb-1">
                  {{ note.title }}
                </h3>
                <p class="text-xs text-gray-600 line-clamp-2">
                  {{ truncateText(note.body, 80) }}
                </p>
              </div>
              <button
                @click.stop="showDeleteDialog(note.id)"
                class="ml-2 text-red-500 hover:text-red-700 transition-colors"
                title="Delete note"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <p v-if="filteredNotes.length === 0" class="mt-4 text-gray-500 text-center text-sm">
          No notes found.
        </p>
      </div>
    </div>

    <!-- Editor/Display Panel -->
    <div class="flex-1 flex flex-col">
      <div v-if="!selectedNote" class="flex-1 flex items-center justify-center bg-gray-100">
        <div class="text-center text-gray-500">
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <p class="text-lg font-medium">Select a note to view or edit</p>
        </div>
      </div>

      <div v-else class="flex-1 flex flex-col">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 p-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center space-x-3">
              <span
                class="text-xs font-semibold px-2 py-1 rounded-full"
                :class="getCategoryLabelClass(selectedNote.category)"
              >
                {{ selectedNote.category || 'N/A' }}
              </span>
              <span class="text-sm text-gray-500">
                Created: {{ formatDate(selectedNote.created_at) }}
              </span>
            </div>
            <div class="flex items-center space-x-2">
              <button
                @click="isEditMode = !isEditMode"
                :class="[
                  'px-3 py-1 rounded text-sm font-medium transition-colors',
                  isEditMode 
                    ? 'bg-gray-500 text-white hover:bg-gray-600' 
                    : 'bg-blue-500 text-white hover:bg-blue-600'
                ]"
              >
                {{ isEditMode ? 'View' : 'Edit' }}
              </button>
              <button
                v-if="isEditMode"
                @click="saveNote"
                class="px-3 py-1 bg-green-500 text-white rounded text-sm font-medium hover:bg-green-600 transition-colors"
              >
                Save
              </button>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto">
          <!-- Edit Mode -->
          <div v-if="isEditMode" class="p-4">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
              <input
                v-model="editData.title"
                class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Note title"
              />
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select
                v-model="editData.category"
                class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="Work">Work</option>
                <option value="Personal">Personal</option>
                <option value="Shopping">Shopping</option>
                <option value="Study">Study</option>
                <option value="Health">Health</option>
                <option value="Travel">Travel</option>
              </select>
            </div>

            <!-- Rich Text Editor Toolbar -->
            <div class="mb-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
              <div class="border border-gray-300 rounded-t bg-gray-50 p-2 flex flex-wrap gap-1">
                <button
                  @click="formatText('bold')"
                  class="px-2 py-1 text-sm bg-white border rounded hover:bg-gray-100 font-bold"
                  title="Bold"
                >
                  B
                </button>
                <button
                  @click="formatText('italic')"
                  class="px-2 py-1 text-sm bg-white border rounded hover:bg-gray-100 italic"
                  title="Italic"
                >
                  I
                </button>
                <button
                  @click="formatText('underline')"
                  class="px-2 py-1 text-sm bg-white border rounded hover:bg-gray-100 underline"
                  title="Underline"
                >
                  U
                </button>
                <div class="border-l border-gray-300 h-6 mx-1"></div>
                <button
                  @click="formatText('insertUnorderedList')"
                  class="px-2 py-1 text-sm bg-white border rounded hover:bg-gray-100"
                  title="Bullet List"
                >
                  • List
                </button>
                <button
                  @click="formatText('insertOrderedList')"
                  class="px-2 py-1 text-sm bg-white border rounded hover:bg-gray-100"
                  title="Numbered List"
                >
                  1. List
                </button>
                <div class="border-l border-gray-300 h-6 mx-1"></div>
                <select
                  @change="formatText('fontSize', $event.target.value)"
                  class="text-sm bg-white border rounded px-1"
                >
                  <option value="">Font Size</option>
                  <option value="1">Small</option>
                  <option value="3">Normal</option>
                  <option value="5">Large</option>
                  <option value="7">Extra Large</option>
                </select>
              </div>
              <div
                ref="editor"
                contenteditable="true"
                v-html="editData.body"
                @input="updateContent"
                class="w-full min-h-64 p-3 border border-t-0 border-gray-300 rounded-b focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                style="min-height: 300px;"
              ></div>
            </div>
          </div>

          <!-- View Mode -->
          <div v-else class="p-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ selectedNote.title }}</h1>
            <div 
              class="prose max-w-none text-gray-700"
              v-html="selectedNote.body"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <Dialog 
      v-model:visible="deleteDialogVisible" 
      header="Confirm Deletion" 
      :style="{ width: '450px' }" 
      :modal="true"
      :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
    >
      <div class="flex align-items-center gap-3">
        <i class="pi pi-exclamation-triangle text-yellow-500" style="font-size: 2rem" />
        <span>Are you sure you want to delete this note? This action cannot be undone.</span>
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

    <!-- Toast -->
    <Toast />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, onMounted } from 'vue';
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
const selectedNote = ref(null);
const isEditMode = ref(false);
const deleteDialogVisible = ref(false);
const noteToDeleteId = ref<number | null>(null);
const editor = ref(null);

const editData = ref({
  title: '',
  body: '',
  category: 'Personal'
});

// Filter notes to only show type 'note'
const filteredNotes = computed(() => {
  let result = [...(props.notes || [])].filter(note => note.type === 'note');
  
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
  
  // Apply sorting
  return result.sort((a, b) => {
    const valueA = new Date(a.created_at || '');
    const valueB = new Date(b.created_at || '');
    return sortOrder.value === 'asc' ? valueA - valueB : valueB - valueA;
  });
});

// Auto-select most recent note when component mounts or notes change
const autoSelectMostRecentNote = () => {
  if (filteredNotes.value.length > 0 && !selectedNote.value) {
    selectNote(filteredNotes.value[0]); // First note is most recent due to desc sorting
  }
};

// Watch for changes in filtered notes to auto-select
watch(filteredNotes, () => {
  autoSelectMostRecentNote();
}, { immediate: false });

// Watch for props.notes changes to auto-select
watch(() => props.notes, () => {
  autoSelectMostRecentNote();
}, { immediate: true });

const selectNote = (note: any) => {
  selectedNote.value = note;
  isEditMode.value = false;
  editData.value = {
    title: note.title,
    body: note.body,
    category: note.category || 'Personal'
  };
};

const showDeleteDialog = (id: number) => {
  noteToDeleteId.value = id;
  deleteDialogVisible.value = true;
};

const confirmDelete = async () => {
  if (!noteToDeleteId.value) return;

  try {
    await axios.delete(`http://localhost:8000/api/notes/${noteToDeleteId.value}`);
    
    // If deleting currently selected note, clear selection
    if (selectedNote.value?.id === noteToDeleteId.value) {
      selectedNote.value = null;
      isEditMode.value = false;
    }
    
    emit('delete-note', noteToDeleteId.value);
    deleteDialogVisible.value = false;
    
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: 'Note deleted successfully',
      life: 3000
    });
  } catch (error) {
    console.error('Error deleting note:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to delete note',
      life: 3000
    });
  } finally {
    noteToDeleteId.value = null;
  }
};

const saveNote = async () => {
  if (!selectedNote.value) return;

  try {
    const updatedNote = {
      ...selectedNote.value,
      title: editData.value.title,
      body: editData.value.body,
      category: editData.value.category
    };

    await axios.put(`http://localhost:8000/api/notes/${selectedNote.value.id}`, updatedNote);
    
    selectedNote.value = updatedNote;
    emit('update-note', updatedNote);
    isEditMode.value = false;
    
    toast.add({
      severity: 'success',
      summary: 'Success',
      detail: 'Note updated successfully',
      life: 3000
    });
  } catch (error) {
    console.error('Error updating note:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to update note',
      life: 3000
    });
  }
};

const formatText = (command: string, value?: string) => {
  document.execCommand(command, false, value);
  editor.value?.focus();
};

const updateContent = () => {
  if (editor.value) {
    editData.value.body = editor.value.innerHTML;
  }
};

// Watch for edit mode changes to focus editor
watch(isEditMode, async (newValue) => {
  if (newValue) {
    await nextTick();
    editor.value?.focus();
  }
});

const formatDate = (date: string | undefined) => {
  return date ? new Date(date).toLocaleDateString() : 'N/A';
};

const truncateText = (text: string, maxLength: number) => {
  if (!text) return '';
  // Strip HTML tags for preview
  const strippedText = text.replace(/<[^>]*>/g, '');
  if (strippedText.length <= maxLength) return strippedText;
  return strippedText.substring(0, maxLength) + '...';
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

const getCategoryBorderClass = (category: string) => {
  const baseClasses = {
    'Work': 'border-l-red-500',
    'Personal': 'border-l-blue-500',
    'Shopping': 'border-l-green-500',
    'Study': 'border-l-yellow-500',
    'Health': 'border-l-purple-500',
    'Travel': 'border-l-indigo-500'
  };
  return baseClasses[category] || 'border-l-gray-500';
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Rich text editor styles */
[contenteditable="true"]:focus {
  outline: none;
}

/* Prose styles for content display */
.prose {
  line-height: 1.7;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  margin-top: 1.5em;
  margin-bottom: 0.5em;
  font-weight: bold;
}

.prose h1 { font-size: 1.875rem; }
.prose h2 { font-size: 1.5rem; }
.prose h3 { font-size: 1.25rem; }

.prose p {
  margin-bottom: 1em;
}

.prose ul, .prose ol {
  margin: 1em 0;
  padding-left: 2em;
}

.prose ul {
  list-style-type: disc;
}

.prose ol {
  list-style-type: decimal;
}

.prose li {
  margin: 0.5em 0;
}

.prose strong {
  font-weight: bold;
}

.prose em {
  font-style: italic;
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
</style>