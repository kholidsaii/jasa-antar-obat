<template>
  <!-- 
    =======================================================================
    OVERVIEW PENGIRIMAN - MODUL LOGISTIK & FARMASI
    =======================================================================
    Fokus pada pelacakan siklus hidup resi paket, performa distribusi, 
    serta pengawasan piutang (pembayaran COD/Transfer).
  -->
  <div class="space-y-8 flex flex-col h-full animate-fade-in pb-10">
    
    <!-- ========================================== -->
    <!-- 1. HEADER & KENDALI REFRESH                -->
    <!-- ========================================== -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5 relative overflow-hidden group">
      <div class="absolute right-0 top-0 w-64 h-64 bg-gradient-to-br from-blue-50 to-transparent rounded-full -translate-y-1/2 translate-x-1/3 opacity-50 pointer-events-none"></div>
      
      <div class="relative z-10">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">Ringkasan Operasional Logistik</h2>
        <p class="text-sm text-gray-500 mt-1.5 font-medium">Pemantauan siklus distribusi paket, antrean farmasi, dan status tagihan pasien secara real-time.</p>
      </div>
      
      <button @click="fetchDashboardData" :disabled="isLoading" class="relative z-10 w-full sm:w-auto flex items-center justify-center bg-white hover:bg-blue-50 text-[#3b5998] border border-blue-200 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-300 shadow-sm disabled:opacity-50 group-hover:shadow-md">
        <svg :class="{'animate-spin': isLoading}" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        {{ isLoading ? 'Sinkronisasi Data...' : 'Refresh Monitor' }}
      </button>
    </div>

    <!-- PESAN ERROR JIKA KONEKSI GAGAL -->
    <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-5 rounded-xl shadow-sm flex items-center">
      <div class="flex-shrink-0 bg-red-100 p-2 rounded-full">
        <svg class="h-6 w-6 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
      </div>
      <p class="ml-4 text-sm font-bold text-red-800 tracking-wide">{{ errorMessage }}</p>
    </div>

    <!-- SKELETON LOADING (PRE-FETCH) -->
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in 4" :key="'skel-'+i" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-40">
        <div class="h-4 bg-gray-200 rounded-full w-1/2 mb-5"></div>
        <div class="h-10 bg-gray-200 rounded-lg w-1/3 mb-3"></div>
        <div class="h-3 bg-gray-100 rounded-full w-2/3"></div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. KARTU METRIK UTAMA (TOP FUNNEL)         -->
    <!-- ========================================== -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <!-- Metrik 1: Total Paket -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-blue-50 w-28 h-28 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Total Registrasi</p>
            <p class="text-4xl font-black text-gray-900 tracking-tight">{{ totalPaket }}</p>
            <p class="text-[10px] text-blue-600 font-bold bg-blue-50 inline-block px-2.5 py-1 rounded-md mt-2 uppercase">Semua Siklus Paket</p>
          </div>
          <div class="p-4 bg-blue-50 text-[#3b5998] rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          </div>
        </div>
      </div>

      <!-- Metrik 2: Antrean Farmasi / Admin -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-orange-50 w-28 h-28 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Antrean Proses</p>
            <p class="text-4xl font-black text-orange-500 tracking-tight">{{ antreanCount }}</p>
            <p class="text-[10px] text-orange-700 font-bold bg-orange-50 inline-block px-2.5 py-1 rounded-md mt-2 uppercase">Farmasi & Admin</p>
          </div>
          <div class="p-4 bg-orange-50 text-orange-500 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>

      <!-- Metrik 3: Diperjalanan (Kurir) -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-purple-50 w-28 h-28 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Tahap Distribusi</p>
            <p class="text-4xl font-black text-purple-600 tracking-tight">{{ diperjalananCount }}</p>
            <p class="text-[10px] text-purple-700 font-bold bg-purple-50 inline-block px-2.5 py-1 rounded-md mt-2 uppercase">Sedang Diantar</p>
          </div>
          <div class="p-4 bg-purple-50 text-purple-600 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
          </div>
        </div>
      </div>

      <!-- Metrik 4: Selesai / Terkirim -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-green-50 w-28 h-28 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Distribusi Selesai</p>
            <p class="text-4xl font-black text-green-500 tracking-tight">{{ terkirimCount }}</p>
            <p class="text-[10px] text-green-700 font-bold bg-green-50 inline-block px-2.5 py-1 rounded-md mt-2 uppercase">Diterima Pasien</p>
          </div>
          <div class="p-4 bg-green-50 text-green-500 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>

    </div>

    <!-- ========================================== -->
    <!-- 3. GRAFIK ANALITIK PENGIRIMAN & PEMBAYARAN -->
    <!-- ========================================== -->
    <div v-show="!isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-4">
      
      <!-- Grafik 1: Siklus Status Pengiriman (Bar Chart) -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow">
        <div class="mb-6 flex justify-between items-start border-b border-gray-100 pb-4">
          <div>
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Pergerakan Alur Logistik</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Distribusi volume paket di setiap pos kerja</p>
          </div>
          <div class="p-2 bg-blue-50 rounded-lg">
            <svg class="w-5 h-5 text-[#3b5998]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
          </div>
        </div>
        <div class="flex-1 relative min-h-[280px] w-full">
          <canvas id="pengirimanBarChart"></canvas>
        </div>
      </div>

      <!-- Grafik 2: Rasio Pelunasan (Doughnut Chart) -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow">
        <div class="mb-6 flex justify-between items-start border-b border-gray-100 pb-4">
          <div>
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Rasio Status Tagihan</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Komparasi Piutang (COD) dan Pembayaran Lunas</p>
          </div>
          <div class="p-2 bg-emerald-50 rounded-lg">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
        <div class="flex-1 relative min-h-[280px] w-full flex items-center justify-center">
          <canvas id="pembayaranPieChart"></canvas>
          <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-4">
             <span class="text-3xl font-black text-gray-800">{{ persentaseLunas }}%</span>
             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Lunas</span>
          </div>
        </div>
      </div>

    </div>

    <!-- ========================================== -->
    <!-- 4. AREA BAWAH: PROGRESS BAR & LIVE FEED    -->
    <!-- ========================================== -->
    <div v-if="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Progress Bar Pembayaran Detail -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 col-span-1 hover:shadow-md transition-shadow flex flex-col justify-between">
        <div>
          <h3 class="text-lg font-black text-gray-800 tracking-tight border-b border-gray-100 pb-4 mb-6">Detail Penagihan</h3>
          
          <div class="space-y-6">
            <!-- Bar Lunas -->
            <div>
              <div class="flex justify-between items-end mb-2">
                <div>
                  <p class="text-sm font-bold text-gray-800">Lunas / Terbayar</p>
                  <p class="text-[10px] text-gray-400 font-medium">Pembayaran di muka / selesai COD</p>
                </div>
                <span class="text-green-600 font-black text-lg">{{ lunasCount }} <span class="text-xs text-gray-400 font-medium">/ {{ totalPaket }}</span></span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden border border-gray-200">
                <div class="bg-green-500 h-full rounded-full transition-all duration-1000 ease-out relative" :style="`width: ${persentaseLunas}%`">
                  <div class="absolute inset-0 bg-white/20 w-full h-full animate-[pulse_2s_ease-in-out_infinite]"></div>
                </div>
              </div>
            </div>

            <!-- Bar Piutang -->
            <div>
              <div class="flex justify-between items-end mb-2">
                <div>
                  <p class="text-sm font-bold text-gray-800">Piutang Berjalan</p>
                  <p class="text-[10px] text-gray-400 font-medium">Belum dibayar oleh pasien</p>
                </div>
                <span class="text-red-600 font-black text-lg">{{ belumLunasCount }} <span class="text-xs text-gray-400 font-medium">/ {{ totalPaket }}</span></span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden border border-gray-200">
                <div class="bg-red-500 h-full rounded-full transition-all duration-1000 ease-out" :style="`width: ${persentaseBelumLunas}%`"></div>
              </div>
            </div>
            
            <!-- Bar Batal -->
            <div>
              <div class="flex justify-between items-end mb-2">
                <div>
                  <p class="text-sm font-bold text-gray-800">Dibatalkan</p>
                  <p class="text-[10px] text-gray-400 font-medium">Pengiriman diretur/batal</p>
                </div>
                <span class="text-gray-600 font-black text-lg">{{ batalCount }} <span class="text-xs text-gray-400 font-medium">/ {{ totalPaket }}</span></span>
              </div>
            </div>

          </div>
        </div>

        <!-- Alert/Warning Tagihan -->
        <div class="mt-8 bg-amber-50/80 p-5 rounded-2xl border border-amber-200 flex items-start shadow-sm">
          <svg class="w-6 h-6 text-amber-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          <div>
            <h4 class="text-xs font-black text-amber-800 uppercase tracking-widest mb-1">Standard Operating Procedure</h4>
            <p class="text-[11px] font-medium text-amber-700 leading-relaxed">
              Pastikan armada lapangan menagih uang muka <span class="font-bold bg-amber-200 px-1 rounded text-amber-900">COD</span> sebelum mengubah status paket dari <span class="italic font-semibold">Diperjalanan</span> menjadi <span class="italic font-semibold">Terkirim</span>.
            </p>
          </div>
        </div>
      </div>

      <!-- Live Feed Tabel: 5 Pesanan Terakhir -->
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 col-span-1 lg:col-span-2 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
        <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Log Pendaftaran Resi Baru</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">5 entri pesanan terakhir yang masuk ke dalam antrean</p>
          </div>
          <span class="flex items-center text-[10px] font-black bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg shadow-sm tracking-widest uppercase">
            <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span> Live
          </span>
        </div>
        
        <div class="overflow-x-auto flex-1 p-2">
          <table class="min-w-full divide-y divide-gray-100">
            <thead>
              <tr>
                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">ID Tagihan</th>
                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">Identitas Pasien</th>
                <th scope="col" class="px-6 py-4 text-right text-[11px] font-black text-gray-400 uppercase tracking-widest">Posisi Saat Ini</th>
              </tr>
            </thead>
            <tbody v-if="recentPackages.length > 0" class="divide-y divide-gray-50">
              <tr v-for="pkg in recentPackages" :key="pkg.id" class="hover:bg-blue-50/30 transition-colors group cursor-default">
                <td class="px-6 py-5 whitespace-nowrap text-sm font-black text-gray-700">
                  <span class="bg-gray-100 px-2 py-1 rounded text-gray-600 font-bold border border-gray-200 group-hover:bg-white transition-colors">
                    #PKT-{{ String(pkg.id).padStart(4, '0') }}
                  </span>
                </td>
                <td class="px-6 py-5 whitespace-nowrap">
                  <p class="text-sm font-bold text-[#3b5998]">{{ pkg.customer?.nama || 'Data Dihapus' }}</p>
                  <p class="text-[10px] text-gray-500 mt-1 font-medium"><i class="fas fa-phone-alt mr-1 text-gray-400"></i> {{ pkg.customer?.no_telp || 'N/A' }}</p>
                </td>
                <td class="px-6 py-5 whitespace-nowrap text-right">
                  <span :class="getStatusBadge(pkg.status_pengiriman)" class="px-3 py-1.5 inline-flex text-[10px] font-black rounded-lg border uppercase tracking-widest shadow-sm">
                    {{ pkg.status_pengiriman }}
                  </span>
                </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td colspan="3" class="px-6 py-20 text-center flex flex-col items-center justify-center">
                  <div class="bg-gray-50 p-4 rounded-full mb-3">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                  </div>
                  <span class="text-sm font-bold text-gray-500">Database Antrean Kosong</span>
                  <span class="text-xs text-gray-400 mt-1">Belum ada pesanan obat yang diterbitkan oleh Farmasi hari ini.</span>
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
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

// --- URL BERSIH TANPA LOCALHOST ---
const API_URL = '/packages'

// --- STATE PENGELOLAAN DATA ---
const packages = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

// Reference untuk Chart.js Canvas
let barChartInstance = null
let pieChartInstance = null

// --- KOMPUTASI METRIK DASHBOARD ---

// 1. Total Seluruh Paket
const totalPaket = computed(() => packages.value.length)

// 2. Metrik Berdasarkan Status Pengiriman
const terkirimCount = computed(() => packages.value.filter(p => p.status_pengiriman === 'Terkirim' || p.status_pengiriman === 'Selesai').length)
const diperjalananCount = computed(() => packages.value.filter(p => p.status_pengiriman === 'Diperjalanan').length)
const batalCount = computed(() => packages.value.filter(p => p.status_pengiriman === 'Dibatalkan').length)
const antreanCount = computed(() => {
  const antreanStatuses = ['Pesanan diverifikasi', 'Pengemasan', 'Menunggu Driver']
  return packages.value.filter(p => antreanStatuses.includes(p.status_pengiriman)).length
})

// 3. Metrik Keuangan / Pembayaran
const lunasCount = computed(() => packages.value.filter(p => p.status_pembayaran === 'Lunas').length)
const belumLunasCount = computed(() => packages.value.filter(p => p.status_pembayaran === 'Belum Lunas').length)

const persentaseLunas = computed(() => totalPaket.value === 0 ? 0 : Math.round((lunasCount.value / totalPaket.value) * 100))
const persentaseBelumLunas = computed(() => totalPaket.value === 0 ? 0 : Math.round((belumLunasCount.value / totalPaket.value) * 100))

// 4. Feed Log Resi (5 Terbaru)
const recentPackages = computed(() => packages.value.slice(0, 5))

// --- METODE PENGAMBILAN DATA (API CALL) ---
const fetchDashboardData = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const response = await axios.get(API_URL)
    packages.value = response.data.data || []
    
    // Tunggu DOM selesai merender div skeleton menjadi canvas asli
    await nextTick()
    renderCharts()

  } catch (error) {
    console.error('Error fetching dashboard pengiriman:', error)
    errorMessage.value = 'Kehilangan koneksi ke server pusat. Modul gagal merender data terbaru.'
  } finally {
    isLoading.value = false
  }
}

// --- FUNGSI RENDER CHART.JS ---
const renderCharts = () => {
  // Hancurkan objek chart lama saat direfresh agar tidak gltich
  if (barChartInstance) barChartInstance.destroy()
  if (pieChartInstance) pieChartInstance.destroy()

  const ctxBar = document.getElementById('pengirimanBarChart')
  const ctxPie = document.getElementById('pembayaranPieChart')

  if (!ctxBar || !ctxPie) return

  // 1. GRAFIK BATANG (BAR CHART) - SIKLUS PENGIRIMAN
  const countVerif = packages.value.filter(p => p.status_pengiriman === 'Pesanan diverifikasi').length
  const countPack = packages.value.filter(p => p.status_pengiriman === 'Pengemasan').length
  const countWait = packages.value.filter(p => p.status_pengiriman === 'Menunggu Driver').length

  barChartInstance = new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: ['Diverifikasi', 'Pengemasan', 'Mnng Driver', 'Diperjalanan', 'Terkirim', 'Batal'],
      datasets: [{
        label: 'Volume Berkas',
        data: [countVerif, countPack, countWait, diperjalananCount.value, terkirimCount.value, batalCount.value],
        backgroundColor: [
          'rgba(226, 232, 240, 0.8)', // abu (Verif)
          'rgba(254, 240, 138, 0.8)', // kuning (Pack)
          'rgba(255, 237, 213, 0.8)', // orange (Wait)
          'rgba(219, 234, 254, 0.8)', // biru (Jalan)
          'rgba(220, 252, 231, 0.8)', // hijau (Terkirim)
          'rgba(254, 226, 226, 0.8)'  // merah (Batal)
        ],
        borderColor: ['#94a3b8', '#ca8a04', '#c2410c', '#2563eb', '#16a34a', '#dc2626'],
        borderWidth: 1,
        borderRadius: 6,
        barPercentage: 0.6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 }
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10, family: "'Inter', sans-serif", weight: '600' } } },
        y: { beginAtZero: true, border: { dash: [4, 4] }, ticks: { stepSize: 1, font: { family: "'Inter', sans-serif" } } }
      }
    }
  })

  // 2. GRAFIK DOUGHNUT - RASIO TAGIHAN
  pieChartInstance = new Chart(ctxPie, {
    type: 'doughnut',
    data: {
      labels: ['Lunas', 'Belum Lunas / Piutang'],
      datasets: [{
        data: [lunasCount.value, belumLunasCount.value],
        backgroundColor: ['#22c55e', '#ef4444'],
        borderWidth: 2,
        borderColor: '#ffffff',
        hoverOffset: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '75%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20, font: { size: 11, family: "'Inter', sans-serif", weight: 'bold' } } },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 }
      }
    }
  })
}

// --- UI HELPER: PEWARNAAN BADGE TABEL ---
const getStatusBadge = (status) => {
  switch(status) {
    case 'Pesanan diverifikasi': return 'bg-gray-100 text-gray-600 border-gray-200'
    case 'Pengemasan': return 'bg-yellow-50 text-yellow-700 border-yellow-200'
    case 'Menunggu Driver': return 'bg-orange-50 text-orange-700 border-orange-200'
    case 'Diperjalanan': return 'bg-blue-50 text-blue-700 border-blue-200'
    case 'Terkirim': return 'bg-green-50 text-green-700 border-green-200'
    case 'Dibatalkan': return 'bg-red-50 text-red-700 border-red-200 line-through'
    default: return 'bg-gray-100 text-gray-800'
  }
}

// --- LIFECYCLE VUE ---
onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
/* Transisi Kemunculan Komponen */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

/* Custom Scrollbar untuk Tabel Feed */
.custom-scrollbar::-webkit-scrollbar {
  height: 6px; width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f8fafc; 
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>