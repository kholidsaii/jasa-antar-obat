import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Pengiriman from '../views/Pengiriman.vue'
import Teamwork from '../views/Teamwork.vue'
import Financial from '../views/Financial.vue'
import Laporan from '../views/Laporan.vue'
import Tracking from '../views/Tracking.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true } // Hanya untuk tamu (belum login)
  },
  { 
    path: '/tracking/:resi', 
    component: Tracking, 
    name: 'Tracking',
    meta: { requiresAuth: false } // Rute publik, bebas akses tanpa login
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
    meta: { requiresAuth: true, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] }
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
  const userStr = localStorage.getItem('user')
  let user = null
  
  if (token && userStr) {
    try {
      user = JSON.parse(userStr)
    } catch (e) {
      user = null
    }
  }

  // 1. Jika route butuh login TAPI tidak ada token
  if (to.meta.requiresAuth && !token) {
    return { name: 'Login' }
  } 
  
  // 2. Jika user sudah login TAPI mencoba akses halaman khusus Guest (seperti Login)
  if (to.meta.guest && token) {
    return { name: 'Home' }
  }
  
  // 3. Cek Otorisasi Role (Jika route memiliki batasan role tertentu)
  if (to.meta.roles) {
    // Pastikan data user ada dan role-nya diizinkan
    if (user && to.meta.roles.includes(user.role)) {
      return true // Akses diizinkan
    } else {
      alert('Akses Ditolak! Anda tidak memiliki izin untuk melihat halaman ini.')
      return { name: 'Home' } // Kembalikan ke dashboard
    }
  } 
  
  // 4. Lanjutkan secara default untuk route publik (Tracking) atau rute yang valid
  return true 
})

export default router