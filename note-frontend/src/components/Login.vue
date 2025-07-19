<template>
  <div class="mx-auto p-6">
    <div class="bg-white rounded-xl shadow-2xl transform transition-all duration-300 hover:shadow-3xl overflow-hidden">
      <div class="flex flex-col md:flex-row">
        <!-- Form Section -->
        <div class="w-full md:w-1/2 p-6">
          <h1 class="text-3xl font-extrabold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500">Login to TaskBloom</h1>
          <form @submit.prevent="handleLogin" class="space-y-6">
            <div>
              <label for="email" class="block text-base font-semibold text-gray-700">Email</label>
              <input
                v-model="email"
                id="email"
                type="email"
                class="w-full p-3 border-2 border-gray-200 rounded-lg mt-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                placeholder="Enter your email"
                required
              />
            </div>
            <div>
              <label for="password" class="block text-base font-semibold text-gray-700">Password</label>
              <input
                v-model="password"
                id="password"
                type="password"
                class="w-full p-3 border-2 border-gray-200 rounded-lg mt-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                placeholder="Enter your password"
                required
              />
            </div>
            <p v-if="error" class="text-red-500 text-sm font-medium bg-red-100 p-2 rounded-md">{{ error }}</p>
            <div class="flex space-x-4">
              <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:from-purple-700 hover:to-pink-600 transition-all duration-200">Login</button>
              <RouterLink to="/" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors duration-200">Cancel</RouterLink>
            </div>
            
            <!-- Demo credentials hint -->
            <div class="text-center text-xs text-gray-500 mt-6">
              <p class="font-medium">Demo credentials:</p>
              <p>Email: <span class="font-mono bg-gray-100 px-2 py-1 rounded">admin@example.com</span></p>
              <p>Password: <span class="font-mono bg-gray-100 px-2 py-1 rounded">securepassword123</span></p>
            </div>
          </form>
        </div>

        <!-- Image Section -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center p-6">
          <img 
            src="https://cdn.pixabay.com/photo/2015/03/16/18/24/daisies-676368_960_720.jpg" 
            alt="Login illustration"
            class="rounded-lg object-cover h-full w-full max-h-[500px] shadow-md"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const email = ref('');
const password = ref('');
const error = ref('');

const handleLogin = async () => {
  try {
    // For demo purposes - you can remove this in production
    if (email.value === 'admin@example.com' && password.value === 'securepassword123') {
      console.log('Using hardcoded credentials for demo');
      
      const mockResponse = {
        data: {
          message: 'Login successful (demo)',
          token: 'demo-auth-token-12345',
          user: {
            id: 1,
            name: 'Admin User',
            email: 'admin@example.com'
          }
        }
      };
      
      handleAuthSuccess(mockResponse);
      return;
    }

    // Regular API login
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value,
    });
    
    handleAuthSuccess(response);

  } catch (err) {
    error.value = err.response?.data?.message || 'Invalid credentials or server error. Please try again.';
    console.error('Login error:', err.response?.data || err);
  }
};

const handleAuthSuccess = (response: any) => {
  const token = response.data.token;
  console.log('Received token:', token);
  
  localStorage.setItem('authToken', token);
  axios.defaults.headers.Authorization = `Bearer ${token}`;
  
  error.value = '';
  router.push('/display');
};
</script>

<style scoped>
/* Transition effects */
.transform {
  transition-property: transform;
}
.transition-all {
  transition-property: all;
}
.duration-200 {
  transition-duration: 200ms;
}
.duration-300 {
  transition-duration: 300ms;
}

/* Shadow effects */
.hover\:shadow-3xl:hover {
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Focus styles */
.focus\:ring-2:focus {
  --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
  --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
  box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-ring-inset);
}
.focus\:border-transparent:focus {
  border-color: transparent;
}

/* Gradient text */
.text-transparent {
  color: transparent;
}
.bg-clip-text {
  -webkit-background-clip: text;
  background-clip: text;
}
.bg-gradient-to-r {
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
}
.from-purple-600 {
  --tw-gradient-from: #9333ea;
  --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}
.to-pink-500 {
  --tw-gradient-to: #ec4899;
}
</style>