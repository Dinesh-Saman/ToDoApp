import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import DisplayNotes from '../views/DisplayNotes.vue';
import AddNote from '../views/AddNote.vue';
import EditNote from '../components/EditNote.vue';
import Login from '../components/Login.vue';
import axios from 'axios'; // Import axios

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/display',
    name: 'DisplayNotes',
    component: DisplayNotes,
    meta: { requiresAuth: true },
  },
  {
    path: '/add',
    name: 'AddNote',
    component: AddNote,
    meta: { requiresAuth: true },
  },
  {
    path: '/edit/:id',
    name: 'EditNote',
    component: EditNote,
    props: (route) => ({ id: route.params.id }),
    meta: { requiresAuth: true },
  },
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/login',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('authToken');

  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else if (to.path === '/login' && token) {
    // Validate token with backend before redirecting
    try {
      await axios.get('http://localhost:8000/api/user', {
        headers: { Authorization: `Bearer ${token}` },
      });
      next('/display'); 
    } catch (error) {
      localStorage.removeItem('authToken'); 
      next();
    }
  } else {
    next();
  }
});

export default router;