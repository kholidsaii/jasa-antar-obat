import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Pengiriman from '../views/Pengiriman.vue'
import Teamwork from '../views/Teamwork.vue'
import Financial from '../views/Financial.vue'
import Laporan from '../views/Laporan.vue' // Import komponen Laporan

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/pengiriman', name: 'Pengiriman', component: Pengiriman },
  { path: '/teamwork', name: 'Teamwork', component: Teamwork },
  { path: '/financial', name: 'Financial', component: Financial },
  { path: '/laporan', name: 'Laporan', component: Laporan }, // Rute baru
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router