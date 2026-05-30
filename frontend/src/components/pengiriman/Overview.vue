<template>
  <div class="space-y-6 flex flex-col h-full">
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Ringkasan Operasional Pengiriman</h2>
        <p class="text-sm text-gray-500 mt-1">Data real-time berdasarkan aktivitas paket hari ini.</p>
      </div>
      <button @click="fetchDashboardData" :disabled="isLoading" class="text-sm bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center disabled:opacity-50">
        <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        {{ isLoading ? 'Memuat...' : 'Refresh Data' }}
      </button>
    </div>

    <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">{{ errorMessage }}</p>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in 4" :key="i" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 animate-pulse">
        <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
        <div class="h-8 bg-gray-200 rounded w-1/3"></div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-4 mr-4 bg-blue-50 text-blue-500 p-3 rounded-lg group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Paket</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ totalPaket }}</p>
        <p class="text-xs text-gray-400 mt-2">Semua status pengiriman</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-4 mr-4 bg-orange-50 text-orange-500 p-3 rounded-lg group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Antrean / Proses</p>
        <p class="text-3xl font-bold text-orange-600 mt-2">{{ antreanCount }}</p>
        <p class="text-xs text-gray-400 mt-2">Diverifikasi & Menunggu Driver</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-4 mr-4 bg-purple-50 text-purple-500 p-3 rounded-lg group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
        </div>
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Diperjalanan</p>
        <p class="text-3xl font-bold text-purple-600 mt-2">{{ diperjalananCount }}</p>
        <p class="text-xs text-gray-400 mt-2">Sedang diantar kurir</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-4 mr-4 bg-green-50 text-green-500 p-3 rounded-lg group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Selesai / Terkirim</p>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ terkirimCount }}</p>
        <p class="text-xs text-gray-400 mt-2">Paket diterima pasien</p>
      </div>

    </div>

    <div v-if="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 col-span-1">
        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Status Pembayaran</h3>
        
        <div class="space-y-5 mt-4">
          <div>
            <div class="flex justify-between text-sm font-medium mb-1">
              <span class="text-gray-700">Lunas</span>
              <span class="text-green-600">{{ lunasCount }} / {{ totalPaket }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div class="bg-green-500 h-2.5 rounded-full transition-all duration-1000" :style="`width: ${persentaseLunas}%`"></div>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-sm font-medium mb-1">
              <span class="text-gray-700">Belum Lunas (Piutang)</span>
              <span class="text-red-600">{{ belumLunasCount }} / {{ totalPaket }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div class="bg-red-500 h-2.5 rounded-full transition-all duration-1000" :style="`width: ${persentaseBelumLunas}%`"></div>
            </div>
          </div>
        </div>

        <div class="mt-8 bg-blue-50 p-4 rounded-lg border border-blue-100 flex items-start">
          <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <p class="text-xs text-blue-800 leading-relaxed">
            Pastikan kurir melakukan penagihan untuk status <span class="font-bold">Belum Lunas</span> sebelum merubah status pengiriman menjadi Terkirim.
          </p>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 col-span-1 lg:col-span-2 overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-800">5 Pesanan Terakhir</h3>
          <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">Real-time</span>
        </div>
        
        <div class="overflow-x-auto flex-1">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Paket</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pasien</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
              </tr>
            </thead>
            <tbody v-if="recentPackages.length > 0" class="bg-white divide-y divide-gray-100">
              <tr v-for="pkg in recentPackages" :key="pkg.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                  #PKT-{{ String(pkg.id).padStart(4, '0') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ pkg.customer?.nama || 'Unknown' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusPengirimanClass(pkg.status_pengiriman)" class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border">
                    {{ pkg.status_pengiriman }}
                  </span>
                </td>
              </tr>
            </tbody>
            <tbody v-else class="bg-white">
              <tr>
                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                  Belum ada pesanan yang terdaftar.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// --- CONFIGURATION ---
const API_URL = 'http://localhost:8000/api/v1/packages'

// --- STATE ---
const packages = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

// --- COMPUTED METRICS ---

// 1. Total Seluruh Paket
const totalPaket = computed(() => packages.value.length)

// 2. Paket Selesai / Terkirim
const terkirimCount = computed(() => {
  return packages.value.filter(p => p.status_pengiriman === 'Terkirim').length
})

// 3. Paket Sedang Diperjalanan
const diperjalananCount = computed(() => {
  return packages.value.filter(p => p.status_pengiriman === 'Diperjalanan').length
})

// 4. Antrean (Pesanan diverifikasi, Pengemasan, Menunggu Driver)
const antreanCount = computed(() => {
  const antreanStatuses = ['Pesanan diverifikasi', 'Pengemasan', 'Menunggu Driver']
  return packages.value.filter(p => antreanStatuses.includes(p.status_pengiriman)).length
})

// 5. Statistik Pembayaran (Lunas vs Belum Lunas)
const lunasCount = computed(() => {
  return packages.value.filter(p => p.status_pembayaran === 'Lunas').length
})

const belumLunasCount = computed(() => {
  return packages.value.filter(p => p.status_pembayaran === 'Belum Lunas').length
})

const persentaseLunas = computed(() => {
  if (totalPaket.value === 0) return 0
  return Math.round((lunasCount.value / totalPaket.value) * 100)
})

const persentaseBelumLunas = computed(() => {
  if (totalPaket.value === 0) return 0
  return Math.round((belumLunasCount.value / totalPaket.value) * 100)
})

// 6. Recent Packages (Ambil 5 paket terbaru berdasarkan array terbalik)
const recentPackages = computed(() => {
  // Asumsi API mengirim data terbaru di awal (latest()->get()), 
  // atau kita potong 5 teratas saja.
  return packages.value.slice(0, 5)
})


// --- METHODS ---

// Fetch data langsung dari API saat overview dibuka
const fetchDashboardData = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const response = await axios.get(API_URL)
    // Response mapping berdasarkan standar resource controller Laravel
    packages.value = response.data.data
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
    errorMessage.value = 'Gagal mengambil data dari server. Pastikan backend Laravel dan PostgreSQL berjalan.'
  } finally {
    isLoading.value = false
  }
}

// UI Helper: Pewarnaan Badge Status
const getStatusPengirimanClass = (status) => {
  switch(status) {
    case 'Pesanan diverifikasi': return 'bg-gray-100 text-gray-600 border-gray-200'
    case 'Pengemasan': return 'bg-yellow-50 text-yellow-700 border-yellow-200'
    case 'Menunggu Driver': return 'bg-orange-50 text-orange-700 border-orange-200'
    case 'Diperjalanan': return 'bg-blue-50 text-blue-700 border-blue-200'
    case 'Terkirim': return 'bg-green-50 text-green-700 border-green-200'
    default: return 'bg-gray-100 text-gray-800'
  }
}

// --- LIFECYCLE ---
onMounted(() => {
  fetchDashboardData()
})
</script>