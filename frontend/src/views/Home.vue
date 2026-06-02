<template>
  <div class="max-w-7xl mx-auto pb-12 space-y-6 animate-fade-in dashboard-wrapper">
    
    <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-xl sm:text-2xl font-black text-gray-800 tracking-tight">Dashboard Operasional</h1>
        <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
          Selamat bertugas, <span class="font-bold text-[#3b5998]">{{ currentUser.name || 'User' }}</span> ({{ userRole.toUpperCase() }})
        </p>
      </div>
      
      <div class="flex flex-row gap-2 w-full sm:w-auto">
        <button @click="fetchGlobalDashboard" :disabled="isLoading" class="flex-1 sm:flex-none flex items-center justify-center bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-sm active:scale-95 disabled:opacity-50">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh
        </button>
        
        <button @click="handleLogout" class="flex-1 sm:flex-none flex items-center justify-center bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-sm active:scale-95">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          Logout
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="i in 4" :key="'skel-met-'+i" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 animate-pulse h-28"></div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 animate-pulse h-80"></div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 animate-pulse h-80"></div>
      </div>
    </div>

    <div v-else class="space-y-6">
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-if="['superadmin', 'admin', 'farmasi', 'kurir'].includes(userRole)" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Paket</p>
            <p class="text-3xl font-black text-gray-900">{{ totalPaketData }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-50 text-[#3b5998] rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          </div>
        </div>

        <div v-if="['superadmin', 'admin', 'farmasi', 'kurir'].includes(userRole)" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Diantar Kurir</p>
            <p class="text-3xl font-black text-purple-600">{{ totalDiantar }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          </div>
        </div>

        <div v-if="['superadmin', 'admin', 'farmasi', 'kurir'].includes(userRole)" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Selesai</p>
            <p class="text-3xl font-black text-green-500">{{ totalSelesai }}</p>
          </div>
          <div class="w-12 h-12 bg-green-50 text-green-500 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>

        <div v-if="userRole === 'superadmin'" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center">
          <div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Saldo Bersih</p>
            <p class="text-xl sm:text-2xl font-black text-emerald-600 truncate max-w-[120px] sm:max-w-xs" :title="formatRupiah(saldoBersih)">
              {{ formatRupiah(saldoBersih) }}
            </p>
          </div>
          <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 col-span-1 flex flex-col">
          <div class="mb-4">
            <h3 class="text-sm font-black text-gray-800 tracking-tight">Status Paket</h3>
            <p class="text-[11px] text-gray-500 font-medium">Distribusi posisi saat ini</p>
          </div>
          <div class="flex-1 relative min-h-[220px] flex items-center justify-center">
            <canvas id="homeStatusChart"></canvas>
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 col-span-1 lg:col-span-2 flex flex-col">
          <div class="mb-4">
            <h3 class="text-sm font-black text-gray-800 tracking-tight">Tren Operasional</h3>
            <p class="text-[11px] text-gray-500 font-medium">Siklus pengiriman real-time</p>
          </div>
          <div class="flex-1 relative min-h-[220px] w-full">
            <canvas id="homeTrendChart"></canvas>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import Chart from 'chart.js/auto'

const router = useRouter()

// ==========================================
// 1. PENGELOLAAN SESI & HAK AKSES
// ==========================================
const currentUser = ref(JSON.parse(localStorage.getItem('user') || '{}'))
const userRole = computed(() => currentUser.value.role || 'guest')

// ==========================================
// 2. STATE MANAJEMEN DATA GLOBAL
// ==========================================
const isLoading = ref(true)

const rawPackages = ref([])
const rawUsers = ref([])
const rawWorks = ref([])
const rawTransactions = ref([])

const totalPaketData = ref(0)
const totalDiantar = ref(0)
const totalSelesai = ref(0)
const saldoBersih = ref(0)

let chartStatusInstance = null
let chartTrendInstance = null

// ==========================================
// 3. API FETCHING & SINKRONISASI
// ==========================================
// ==========================================
// 3. API FETCHING & SINKRONISASI
// ==========================================
const fetchGlobalDashboard = async () => {
  isLoading.value = true
  const role = userRole.value;

  try {
    if (['superadmin', 'admin', 'farmasi', 'kurir'].includes(role)) {
      const resPackages = await axios.get('/packages')
      rawPackages.value = resPackages.data.data || []
      
      totalPaketData.value = rawPackages.value.length
      
      // Hitung paket yang dipegang kurir / diperjalanan
      totalDiantar.value = rawPackages.value.filter(p => 
        ['6. Diserahkan ke kurir', '7. Dalam perjalanan'].includes(p.status_pengiriman)
      ).length
      
      // Hitung paket selesai
      totalSelesai.value = rawPackages.value.filter(p => p.status_pengiriman === '8. Sampai (Selesai)').length
    }

    if (role === 'superadmin') {
      const resTransactions = await axios.get('/transactions')
      rawTransactions.value = resTransactions.data.data || []
      const pemasukan = rawTransactions.value.filter(t => t.tipe === 'Uang Masuk').reduce((acc, c) => acc + Number(c.nominal), 0)
      const pengeluaran = rawTransactions.value.filter(t => t.tipe === 'Uang Keluar').reduce((acc, c) => acc + Number(c.nominal), 0)
      saldoBersih.value = pemasukan - pengeluaran
    }

  } catch (error) {
    console.error("Gagal melakukan sinkronisasi dashboard:", error)
  } finally {
    // Matikan loading DULU agar wadah grafik (canvas) muncul di layar
    isLoading.value = false
    
    // Tunggu DOM (HTML) selesai dirender, BARU jalankan Chart-nya
    await nextTick()
    renderDashboardCharts()
  }
}

// ==========================================
// 4. VISUALISASI GRAFIK (CHART.JS)
// ==========================================
const renderDashboardCharts = () => {
  if (chartStatusInstance) chartStatusInstance.destroy()
  if (chartTrendInstance) chartTrendInstance.destroy()

  const ctxStatus = document.getElementById('homeStatusChart')
  const ctxTrend = document.getElementById('homeTrendChart')

  if (!ctxStatus || !ctxTrend) return

  // ---- Pengelompokan Data Sesuai Status Baru ----
  const countVerifikasi = rawPackages.value.filter(p => p.status_pengiriman === '1. Verifikasi Jastar').length
  const countFarmasi = rawPackages.value.filter(p => ['2. Stor Struk ke farmasi', '3. Ambil paket obat farmasi', '4. Diserah paket obat jastar'].includes(p.status_pengiriman)).length
  const countTungguKurir = rawPackages.value.filter(p => p.status_pengiriman === '5. Sedang menunggu kurir').length
  const countDiperjalanan = rawPackages.value.filter(p => ['6. Diserahkan ke kurir', '7. Dalam perjalanan'].includes(p.status_pengiriman)).length
  const countSelesai = rawPackages.value.filter(p => p.status_pengiriman === '8. Sampai (Selesai)').length
  const countBatal = rawPackages.value.filter(p => p.status_pengiriman === '9. Cancel / Pending').length

  chartStatusInstance = new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
      labels: ['Verifikasi', 'Proses Farmasi', 'Tunggu Kurir', 'Diperjalanan', 'Selesai', 'Batal'],
      datasets: [{
        data: [countVerifikasi, countFarmasi, countTungguKurir, countDiperjalanan, countSelesai, countBatal],
        backgroundColor: ['#e2e8f0', '#c084fc', '#fef08a', '#60a5fa', '#22c55e', '#ef4444'],
        borderWidth: 2,
        borderColor: '#ffffff',
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
      }
    }
  })

  chartTrendInstance = new Chart(ctxTrend, {
    type: 'bar',
    data: {
      labels: ['Verifikasi', 'Farmasi', 'Tunggu Kurir', 'Pengantaran', 'Selesai'],
      datasets: [
        {
          label: 'Volume Paket',
          data: [countVerifikasi, countFarmasi, countTungguKurir, countDiperjalanan, countSelesai],
          backgroundColor: '#3b5998',
          borderRadius: 4,
          barPercentage: 0.6
        },
        {
          label: 'Tren Arus',
          data: [countVerifikasi, countFarmasi, countTungguKurir, countDiperjalanan, countSelesai],
          type: 'line',
          borderColor: '#10b981',
          borderWidth: 2,
          tension: 0.3,
          fill: false,
          pointBackgroundColor: '#10b981'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } },
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { beginAtZero: true, grid: { borderDash: [4, 4] }, ticks: { stepSize: 1, font: { size: 10 } } }
      }
    }
  })
}

// ==========================================
// 5. UTILITAS & FORMATTER
// ==========================================
const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
}

const handleLogout = async () => {
  try {
    await axios.post('/logout')
  } catch (e) {
    console.log("Error logout:", e)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

onMounted(() => {
  fetchGlobalDashboard()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}
</style>