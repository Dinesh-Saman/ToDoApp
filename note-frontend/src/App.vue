<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isLoggedIn = ref(false);

// Check auth status on component mount and when localStorage changes
onMounted(() => {
  checkAuthStatus();
  window.addEventListener('storage', checkAuthStatus);
});

const checkAuthStatus = () => {
  isLoggedIn.value = !!localStorage.getItem('authToken');
};

const logout = () => {
  localStorage.removeItem('authToken');
  localStorage.removeItem('user');
  isLoggedIn.value = false;
  router.push('/login');
};
</script>

<template>
  <div class="flex h-screen bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100">
    <!-- Enhanced Sidebar -->
    <aside class="w-72 bg-gradient-to-b from-purple-50 to-pink-50 shadow-2xl transform transition-all duration-500 ease-in-out hover:shadow-3xl border-r border-purple-200 relative z-10 flex flex-col">
      <div class="p-6 border-b border-purple-200">
        <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 flex items-center">
          <span class="mr-2">🌸</span>
          <span>TaskBloom</span>
        </h2>
        <p class="text-sm text-purple-500 mt-1">Where ideas blossom</p>
      </div>
      
      <nav class="mt-8 px-4 flex-1">
        <RouterLink 
          to="/display" 
          class="flex items-center px-6 py-4 text-gray-700 hover:bg-gradient-to-r from-purple-50 to-pink-50 hover:text-purple-800 rounded-xl transition-all duration-300 mb-2 group border border-transparent hover:border-purple-200"
          active-class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border-purple-300"
        >
          <span class="mr-4 text-xl group-hover:scale-110 transition-transform">✅</span>
          <span class="font-medium text-lg">My tasks</span>
          <span class="ml-auto bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">View</span>
        </RouterLink>
        
        <RouterLink 
          to="/notes" 
          class="flex items-center px-6 py-4 text-gray-700 hover:bg-gradient-to-r from-purple-50 to-pink-50 hover:text-purple-800 rounded-xl transition-all duration-300 mb-2 group border border-transparent hover:border-purple-200"
          active-class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border-purple-300"
        >
          <span class="mr-4 text-xl group-hover:scale-110 transition-transform">📖</span>
          <span class="font-medium text-lg">My Notes</span>
          <span class="ml-auto bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full">View</span>
        </RouterLink>
        
        <RouterLink 
          to="/add_task" 
          class="flex items-center px-6 py-4 text-gray-700 hover:bg-gradient-to-r from-purple-50 to-pink-50 hover:text-purple-800 rounded-xl transition-all duration-300 group border border-transparent hover:border-purple-200"
          active-class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border-purple-300"
        >
          <span class="mr-4 text-xl group-hover:scale-110 transition-transform">➕</span>
          <span class="font-medium text-lg">Create task</span>
          <span class="ml-auto bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full">Add</span>
        </RouterLink>

        <RouterLink 
          to="/add_note" 
          class="flex items-center px-6 py-4 text-gray-700 hover:bg-gradient-to-r from-purple-50 to-pink-50 hover:text-purple-800 rounded-xl transition-all duration-300 group border border-transparent hover:border-purple-200"
          active-class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 border-purple-300"
        >
          <span class="mr-4 text-xl group-hover:scale-110 transition-transform">🖋</span>
          <span class="font-medium text-lg">Create note</span>
          <span class="ml-auto bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full">Add</span>
        </RouterLink>
      </nav>
      
      <!-- Sidebar Footer -->
      <div class="p-6 border-t border-purple-200 bg-gradient-to-b from-white to-purple-50">
        <div class="flex justify-center space-x-4 mb-4">
          <a href="#" class="text-purple-500 hover:text-pink-600 transition-colors">
            <span class="text-xl">🌐</span>
          </a>
          <a href="#" class="text-purple-500 hover:text-pink-600 transition-colors">
            <span class="text-xl">📱</span>
          </a>
          <a href="#" class="text-purple-500 hover:text-pink-600 transition-colors">
            <span class="text-xl">📧</span>
          </a>
        </div>
        <p class="text-center text-xs text-purple-400">
          © 2023 TaskBloom. All petals reserved.
        </p>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 overflow-auto">
      <!-- Header -->
      <header class="bg-gradient-to-r from-purple-600 to-pink-500 text-white p-6 shadow-lg sticky top-0 z-20">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
          <div class="text-3xl font-extrabold tracking-wide animate-pulse-slow">
            <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full flex items-center">
              <span class="mr-2">🌼</span>
              <span>TaskBloom</span>
            </span>
          </div>
          <div class="flex space-x-4">
            <button 
              v-if="isLoggedIn"
              @click="logout"
              class="px-4 py-2 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all duration-200 flex items-center"
            >
              <span class="mr-2">🚪</span>
              Logout
            </button>
            <RouterLink
              v-else
              to="/login"
              class="px-4 py-2 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all duration-200 flex items-center"
            >
              <span class="mr-2">🔑</span>
              Login
            </RouterLink>
            
            <button class="px-4 py-2 bg-white bg-opacity-20 rounded-full hover:bg-opacity-30 transition-all duration-200 flex items-center">
              <span class="mr-2">⚙️</span>
              Settings
            </button>
          </div>
        </div>
      </header>

      <!-- Main Content - Natural flow -->
      <main class="p-6 bg-gradient-to-br from-white to-purple-50 min-h-screen">
        <RouterView />
      </main>

      <!-- Footer - At natural bottom of content -->
      <footer class="bg-gradient-to-r from-purple-100 to-pink-100 border-t border-purple-200 py-6 px-8">
        <div class="max-w-7xl mx-auto">
          <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
              <h3 class="text-lg font-semibold text-purple-800 flex items-center">
                <span class="mr-2">🌷</span>
                TaskBloom
              </h3>
              <p class="text-sm text-purple-600">Your digital garden for ideas</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
              <div>
                <h4 class="text-sm font-semibold text-purple-700 mb-2">Explore</h4>
                <ul class="space-y-2">
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">Features</a></li>
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">Templates</a></li>
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">Integrations</a></li>
                </ul>
              </div>
              <div>
                <h4 class="text-sm font-semibold text-purple-700 mb-2">Company</h4>
                <ul class="space-y-2">
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">About</a></li>
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">Blog</a></li>
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">Careers</a></li>
                </ul>
              </div>
              <div>
                <h4 class="text-sm font-semibold text-purple-700 mb-2">Support</h4>
                <ul class="space-y-2">
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">Help Center</a></li>
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">Contact Us</a></li>
                  <li><a href="#" class="text-purple-600 hover:text-pink-600 text-sm transition-colors">Privacy</a></li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="mt-8 pt-6 border-t border-purple-200 flex flex-col md:flex-row justify-between items-center">
            <p class="text-xs text-purple-500 mb-4 md:mb-0">
              © 2023 TaskBloom. All rights reserved.
            </p>
            <div class="flex space-x-6">
              <a href="#" class="text-purple-500 hover:text-pink-600 transition-colors">
                <span class="text-lg">🐦</span>
              </a>
              <a href="#" class="text-purple-500 hover:text-pink-600 transition-colors">
                <span class="text-lg">📸</span>
              </a>
              <a href="#" class="text-purple-500 hover:text-pink-600 transition-colors">
                <span class="text-lg">💼</span>
              </a>
              <a href="#" class="text-purple-500 hover:text-pink-600 transition-colors">
                <span class="text-lg">👾</span>
              </a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<style scoped>
.animate-pulse-slow {
  animation: pulse 3s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.9; transform: scale(1.02); }
}

.hover\:shadow-3xl:hover {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth scrolling for main content */
main {
  scroll-behavior: smooth;
}

/* Enhanced active link styling */
.router-link-active {
  font-weight: 600;
}
</style>