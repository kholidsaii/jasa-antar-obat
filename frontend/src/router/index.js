import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Pengiriman from '../views/Pengiriman.vue'
import Teamwork from '../views/Teamwork.vue'
import Financial from '../views/Financial.vue'

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/pengiriman', name: 'Pengiriman', component: Pengiriman },
  { path: '/teamwork', name: 'Teamwork', component: Teamwork },
  { path: '/financial', name: 'Financial', component: Financial },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router