import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Pengiriman from '../views/Pengiriman.vue'
import Teamwork from '../views/Teamwork.vue'
import Financial from '../views/Financial.vue'
import Laporan from '../views/Laporan.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/',
    name: 'Home',
    component: Home,
    meta: { requiresAuth: true } // Semua role bisa lihat home
  },
  {
    path: '/pengiriman',
    name: 'Pengiriman',
    component: Pengiriman,
    meta: { requiresAuth: true, roles: ['superadmin', 'admin', 'farmasi'] }
  },
  {
    path: '/teamwork',
    name: 'Teamwork',
    component: Teamwork,
    meta: { requiresAuth: true, roles: ['superadmin', 'admin'] }
  },
  {
    path: '/financial',
    name: 'Financial',
    component: Financial,
    meta: { requiresAuth: true, roles: ['superadmin', 'admin'] }
  },
  {
    path: '/laporan',
    name: 'Laporan',
    component: Laporan,
    meta: { requiresAuth: true, roles: ['superadmin', 'admin'] }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Middleware / Navigation Guard
router.beforeEach((to, from) => {
  const token = localStorage.getItem('token')
  let user = null
  
  if (token) {
    try {
      user = JSON.parse(localStorage.getItem('user'))
    } catch (e) {
      user = null
    }
  }

  // Jika route butuh login tapi tidak ada token
  if (to.matched.some(record => record.meta.requiresAuth) && !token) {
    return { name: 'Login' }
  } 
  // Jika user sudah login tapi mau ke halaman login
  else if (to.matched.some(record => record.meta.guest) && token) {
    return { name: 'Home' }
  }
  // Cek otorisasi Role
  else if (to.matched.some(record => record.meta.roles)) {
    const routeRoles = to.meta.roles
    if (user && routeRoles.includes(user.role)) {
      return true // Lanjutkan
    } else {
      alert('Anda tidak memiliki akses ke halaman ini.')
      return { name: 'Home' } // Lempar kembali ke home
    }
  } 
  
  return true // Lanjutkan secara default
})

export default router