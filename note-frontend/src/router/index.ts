import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import DisplayTasks from '../views/DisplayTasks.vue';
import AddTask from '../views/AddTask.vue';
import AddNote from '../views/AddNote.vue';
import EditTask from '../components/EditTask.vue';
import MyNotes from '../views/MyNotes.vue'
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
    name: 'DisplayTasks',
    component: DisplayTasks,
    meta: { requiresAuth: true },
  },
    {
    path: '/notes',
    name: 'MyNotes',
    component: MyNotes,
    meta: { requiresAuth: true },
  },
  {
    path: '/add_task',
    name: 'AddTask',
    component: AddTask,
    meta: { requiresAuth: true },
  },
    {
    path: '/add_note',
    name: 'AddNote',
    component: AddNote,
    meta: { requiresAuth: true },
  },
  {
    path: '/edit/:id',
    name: 'EditTask',
    component: EditTask,
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