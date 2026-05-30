<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Pemetaan & Rute Alamat</h2>
        <p class="text-sm text-gray-500 mt-1">Daftar alamat tujuan untuk paket yang sedang diproses atau diperjalanan.</p>
      </div>
      
      <div class="flex space-x-3">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari jalan atau area..." 
            class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm"
          >
        </div>
        
        <button @click="fetchPackages" :disabled="isLoading" class="bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center disabled:opacity-50">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh
        </button>
      </div>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all absolute top-0 left-1/2 transform -translate-x-1/2 mt-4 rounded-lg shadow-lg z-50', notification.type === 'success' ? 'bg-green-600' : 'bg-red-600']">
      <div class="flex items-center">
        <svg v-if="notification.type === 'success'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ notification.message }}
      </div>
    </div>

    <div class="flex flex-col lg:flex-row h-[600px] divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
      
      <div class="w-full lg:w-3/5 h-64 lg:h-full bg-gray-50 relative flex flex-col">
        <iframe 
          class="w-full h-full border-0" 
          frameborder="0" 
          scrolling="no" 
          marginheight="0" 
          marginwidth="0" 
          src="https://www.openstreetmap.org/export/embed.html?bbox=106.82%2C-6.49%2C106.88%2C-6.45&layer=mapnik" 
          style="border: 1px solid black">
        </iframe>
        
        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-gray-900/60 to-transparent">
          <div class="bg-white/90 backdrop-blur-sm p-3 rounded-lg shadow-sm border border-white/20 inline-flex items-center">
            <span class="flex h-3 w-3 relative mr-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-500"></span>
            </span>
            <span class="text-sm font-bold text-gray-800">Tracking Area Aktif</span>
          </div>
        </div>
      </div>

      <div class="w-full lg:w-2/5 h-full flex flex-col bg-white">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <h3 class="font-bold text-gray-700">Daftar Rute ({{ filteredActiveRoutes.length }})</h3>
          <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full">Diproses / Diperjalanan</span>
        </div>
        
        <div v-if="isLoading" class="flex-1 p-4 overflow-y-auto space-y-4">
          <div v-for="i in 4" :key="i" class="border border-gray-100 p-4 rounded-xl animate-pulse">
            <div class="flex justify-between mb-2">
              <div class="h-4 bg-gray-200 rounded w-1/3"></div>
              <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            </div>
            <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
            <div class="h-16 bg-gray-100 rounded w-full"></div>
          </div>
        </div>

        <div v-else-if="filteredActiveRoutes.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
          <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
          </div>
          <h4 class="text-base font-bold text-gray-700 mb-1">Tidak Ada Rute Aktif</h4>
          <p class="text-sm text-gray-500">Semua paket sudah terkirim atau tidak ada data yang cocok dengan pencarian Anda.</p>
        </div>

        <div v-else class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
          <div 
            v-for="pkg in filteredActiveRoutes" 
            :key="pkg.id" 
            class="border border-gray-100 hover:border-blue-200 bg-white p-4 rounded-xl shadow-sm hover:shadow transition-all group relative"
          >
            <div class="absolute top-4 right-4">
               <span :class="getStatusBadgeClass(pkg.status_pengiriman)" class="px-2 py-1 text-[10px] font-bold uppercase rounded-md border">
                 {{ pkg.status_pengiriman }}
               </span>
            </div>

            <div class="pr-20 mb-3">
              <h4 class="font-bold text-gray-900 text-sm">#PKT-{{ String(pkg.id).padStart(4, '0') }}</h4>
              <p class="text-sm font-semibold text-[#3b5998] mt-1">{{ pkg.customer?.nama || 'Unknown' }}</p>
              <div class="flex items-center text-xs text-gray-500 mt-1">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                {{ pkg.customer?.no_telp || '-' }}
              </div>
            </div>

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 flex items-start mt-2">
              <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
              <div class="flex-1">
                <p class="text-sm text-gray-700 leading-relaxed">{{ pkg.customer?.alamat || 'Alamat tidak tersedia' }}</p>
              </div>
            </div>

            <div class="mt-4 flex gap-2">
              <button 
                @click="copyAddress(pkg.customer?.alamat)" 
                class="flex-1 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors flex items-center justify-center"
              >
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                Copy Alamat
              </button>
              <a 
                :href="'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(pkg.customer?.alamat)" 
                target="_blank"
                class="flex-1 bg-blue-50 border border-blue-100 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors flex items-center justify-center"
              >
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Buka di Maps
              </a>
            </div>
          </div>
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
const searchQuery = ref('')
const notification = ref({ show: false, message: '', type: 'success' })

// --- COMPUTED PROPERTIES ---

// 1. Filter hanya rute yang "Aktif" (Belum terkirim)
const activeRoutes = computed(() => {
  return packages.value.filter(pkg => pkg.status_pengiriman !== 'Terkirim')
})

// 2. Terapkan fitur pencarian (Berdasarkan alamat atau nama)
const filteredActiveRoutes = computed(() => {
  if (!searchQuery.value) return activeRoutes.value
  
  const query = searchQuery.value.toLowerCase()
  return activeRoutes.value.filter(pkg => {
    const alamat = pkg.customer?.alamat?.toLowerCase() || ''
    const nama = pkg.customer?.nama?.toLowerCase() || ''
    return alamat.includes(query) || nama.includes(query)
  })
})

// --- METHODS ---

// Ambil Data Paket dari Backend
const fetchPackages = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    packages.value = response.data.data 
  } catch (error) {
    console.error('Error fetching packages for routes:', error)
    showNotification('Gagal memuat data rute', 'error')
  } finally {
    isLoading.value = false
  }
}

// Fungsi Copy Alamat ke Clipboard
const copyAddress = async (alamat) => {
  if (!alamat) return showNotification('Alamat kosong', 'error')
  
  try {
    await navigator.clipboard.writeText(alamat)
    showNotification('Alamat berhasil disalin ke clipboard!', 'success')
  } catch (err) {
    // Fallback untuk browser lama
    const textArea = document.createElement("textarea")
    textArea.value = alamat
    document.body.appendChild(textArea)
    textArea.select()
    document.execCommand("Copy")
    textArea.remove()
    showNotification('Alamat disalin (Fallback mode)', 'success')
  }
}

// Helper: Warna Badge
const getStatusBadgeClass = (status) => {
  switch(status) {
    case 'Pesanan diverifikasi': return 'bg-gray-100 text-gray-600 border-gray-200'
    case 'Pengemasan': return 'bg-yellow-50 text-yellow-700 border-yellow-200'
    case 'Menunggu Driver': return 'bg-orange-50 text-orange-700 border-orange-200'
    case 'Diperjalanan': return 'bg-blue-50 text-blue-700 border-blue-200'
    default: return 'bg-gray-100 text-gray-800'
  }
}

// Menampilkan Toast Alert
const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => {
    notification.value.show = false
  }, 3000)
}

// --- LIFECYCLE ---
onMounted(() => {
  fetchPackages()
})
</script>

<style scoped>
/* Kustomisasi Scrollbar agar rapi */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9; 
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>