<template>
  <div class="space-y-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Overview Tim & Operasional</h2>
        <p class="text-sm text-gray-500 mt-1">Ringkasan status distribusi kurir dan armada kendaraan.</p>
      </div>
      <button @click="fetchStats" :disabled="isLoading" class="text-sm bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
        <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Refresh Stats
      </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="card in statsCards" :key="card.title" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ card.title }}</p>
        <p :class="['text-3xl font-bold mt-2', card.color]">{{ isLoading ? '...' : card.value }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const isLoading = ref(true)
const totalWorks = ref(0)
const totalCouriers = ref(0)
const totalVehicles = ref(0)

const statsCards = computed(() => [
  { title: 'Total Penugasan', value: totalWorks.value, color: 'text-gray-900' },
  { title: 'Kurir Terdaftar', value: totalCouriers.value, color: 'text-blue-600' },
  { title: 'Sedang Berjalan', value: totalWorks.value > 0 ? 1 : 0, color: 'text-orange-500' },
  { title: 'Armada Kendaraan', value: totalVehicles.value, color: 'text-green-500' },
])

const fetchStats = async () => {
  isLoading.value = true
  try {
    // Mengambil data paralel dari endpoint API v1
    const [resWorks, resUsers, resVehicles] = await Promise.all([
      axios.get('http://localhost:8000/api/v1/works'),
      axios.get('http://localhost:8000/api/v1/users'),
      axios.get('http://localhost:8000/api/v1/vehicles')
    ])
    totalWorks.value = resWorks.data.data?.length || 0
    totalCouriers.value = resUsers.data.data?.length || 0
    totalVehicles.value = resVehicles.data.data?.length || 0
  } catch (error) {
    console.error("Gagal memuat statistik teamwork:", error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchStats()
})
</script>