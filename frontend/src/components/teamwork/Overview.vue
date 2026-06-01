<template>
  <div class="space-y-8 flex flex-col h-full animate-fade-in pb-12">
    
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 relative overflow-hidden group hover:shadow-md transition-all duration-300">
      <div class="absolute right-0 top-0 w-72 h-72 bg-gradient-to-br from-indigo-50 to-transparent rounded-full -translate-y-1/2 translate-x-1/3 opacity-60 pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>
      
      <div class="relative z-10">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center">
          <svg class="w-7 h-7 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
          Overview SDM & Armada
        </h2>
        <p class="text-sm text-gray-500 mt-2 font-medium max-w-2xl leading-relaxed">
          Monitor ketersediaan kurir, utilisasi armada kendaraan, serta sinkronisasi dispatch paket dari Farmasi menuju lapangan secara real-time.
        </p>
      </div>
      
      <div class="relative z-10 w-full sm:w-auto">
        <button @click="fetchDashboardData" :disabled="isLoading" class="w-full sm:w-auto flex items-center justify-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-300 shadow-sm disabled:opacity-50 group-hover:shadow hover:-translate-y-0.5">
          <svg :class="{'animate-spin': isLoading}" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          {{ isLoading ? 'Sinkronisasi Sistem...' : 'Refresh Monitor' }}
        </button>
      </div>
    </div>

    <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-5 rounded-2xl shadow-sm flex items-start animate-slide-down">
      <div class="flex-shrink-0 bg-red-100 p-2 rounded-full mt-0.5">
        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      <div class="ml-4">
        <h3 class="text-sm font-black text-red-800 tracking-wide">Kegagalan Koneksi Pusat</h3>
        <p class="text-xs font-medium text-red-600 mt-1">{{ errorMessage }}</p>
      </div>
    </div>

    <div v-if="!isLoading && paketPendingCount > 0" class="bg-amber-50 border border-amber-200 p-5 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-slide-down">
      <div class="flex items-start">
        <div class="flex-shrink-0 bg-amber-100 p-2.5 rounded-full mt-0.5">
          <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        </div>
        <div class="ml-4">
          <h3 class="text-sm font-black text-amber-800 tracking-wide uppercase">Tindakan Diperlukan: Alokasi Armada</h3>
          <p class="text-xs font-medium text-amber-700 mt-1 leading-relaxed">
            Terdapat <span class="font-black bg-amber-200 px-1.5 py-0.5 rounded text-amber-900">{{ paketPendingCount }} Paket</span> yang sudah selesai dikemas oleh Farmasi dan berstatus <span class="italic font-bold">"Pengemasan"</span>. Segera buatkan Work Order (Penugasan) untuk menyerahkan paket tersebut kepada Kurir.
          </p>
        </div>
      </div>
      <button @click="$parent.activeTab = 'pekerjaan'" class="w-full sm:w-auto shrink-0 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 flex justify-center items-center">
        Buka Tab Penugasan
        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
      </button>
    </div>

    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in 4" :key="'skel-tw-'+i" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-40">
        <div class="flex justify-between items-start mb-4">
          <div class="h-3 bg-gray-200 rounded-full w-1/2"></div>
          <div class="h-10 w-10 bg-gray-100 rounded-2xl"></div>
        </div>
        <div class="h-10 bg-gray-200 rounded-lg w-1/3 mb-3"></div>
        <div class="h-3 bg-gray-100 rounded-full w-2/3"></div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-8 -top-8 bg-indigo-50 w-32 h-32 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1.5">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">SDM Kurir / Driver</p>
            <p class="text-4xl font-black text-gray-900 tracking-tight">{{ totalKurir }}</p>
            <p class="text-[10px] text-indigo-700 font-bold bg-indigo-50 inline-block px-2.5 py-1 rounded-md mt-2 uppercase flex items-center">
              <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5 animate-pulse"></span>
              {{ kurirTerpakai }} Sedang Dinas
            </p>
          </div>
          <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-8 -top-8 bg-blue-50 w-32 h-32 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1.5">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Ketersediaan Armada</p>
            <div class="flex items-baseline space-x-2">
              <p class="text-4xl font-black text-blue-600 tracking-tight">{{ armadaTersedia }}</p>
              <p class="text-sm font-bold text-gray-400">/ {{ totalArmada }}</p>
            </div>
            <p class="text-[10px] text-blue-700 font-bold bg-blue-50 inline-block px-2.5 py-1 rounded-md mt-2 uppercase border border-blue-100">
              Unit Siap Pakai
            </p>
          </div>
          <div class="p-4 bg-blue-50 text-blue-600 rounded-2xl shadow-inner group-hover:-rotate-12 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"></path></svg>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-8 -top-8 bg-amber-50 w-32 h-32 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1.5">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Penugasan Berjalan</p>
            <p class="text-4xl font-black text-amber-500 tracking-tight">{{ activeWorks }}</p>
            <p class="text-[10px] text-amber-700 font-bold bg-amber-50 inline-block px-2.5 py-1 rounded-md mt-2 uppercase border border-amber-100">
              Work Order Aktif
            </p>
          </div>
          <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-8 -top-8 bg-emerald-50 w-32 h-32 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1.5">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Penugasan Selesai</p>
            <p class="text-4xl font-black text-emerald-500 tracking-tight">{{ completedWorks }}</p>
            <p class="text-[10px] text-emerald-700 font-bold bg-emerald-50 inline-block px-2.5 py-1 rounded-md mt-2 uppercase border border-emerald-100">
              Riwayat Sukses
            </p>
          </div>
          <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>

    </div>

    <div v-show="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-2">
      
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 lg:col-span-2 flex flex-col hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-8 opacity-5">
          <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div class="mb-8 flex justify-between items-start border-b border-gray-100 pb-5 relative z-10">
          <div>
            <h3 class="text-xl font-black text-gray-800 tracking-tight">Kinerja Distribusi Kurir</h3>
            <p class="text-xs text-gray-500 font-medium mt-1.5">Perbandingan jumlah paket yang sedang diantar vs berhasil diselesaikan.</p>
          </div>
          <div class="p-2.5 bg-indigo-50 rounded-xl border border-indigo-100">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
          </div>
        </div>
        <div class="flex-1 relative min-h-[300px] w-full z-10">
          <canvas id="kurirPerformanceChart"></canvas>
        </div>
      </div>

      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow duration-300">
        <div class="mb-8 flex justify-between items-start border-b border-gray-100 pb-5">
          <div>
            <h3 class="text-xl font-black text-gray-800 tracking-tight">Utilisasi Armada</h3>
            <p class="text-xs text-gray-500 font-medium mt-1.5">Rasio pemakaian kendaraan</p>
          </div>
          <div class="p-2.5 bg-blue-50 rounded-xl border border-blue-100">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
          </div>
        </div>
        <div class="flex-1 relative min-h-[250px] w-full flex items-center justify-center">
          <canvas id="vehicleStatusChart"></canvas>
          <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-4">
             <span class="text-4xl font-black text-gray-800">{{ totalArmada }}</span>
             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Total Unit</span>
          </div>
        </div>
      </div>

    </div>

    <div v-if="!isLoading" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow mt-4">
      <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
        <div>
          <h3 class="text-lg font-black text-gray-800 tracking-tight">Daftar Kendaraan Siap Pakai</h3>
          <p class="text-xs text-gray-500 font-medium mt-1">Gunakan armada ini untuk dispatch Work Order baru.</p>
        </div>
        <span class="flex items-center text-[10px] font-black bg-blue-50 border border-blue-100 text-blue-700 px-3 py-1.5 rounded-lg shadow-sm tracking-widest uppercase">
          Tersedia ({{ armadaTersedia }})
        </span>
      </div>
      
      <div class="overflow-x-auto p-2">
        <table class="min-w-full divide-y divide-gray-100">
          <thead>
            <tr>
              <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">Identitas Armada</th>
              <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">Kepemilikan</th>
              <th scope="col" class="px-6 py-4 text-right text-[11px] font-black text-gray-400 uppercase tracking-widest">Status / Ketersediaan</th>
            </tr>
          </thead>
          <tbody v-if="availableVehicles.length > 0" class="divide-y divide-gray-50 bg-white">
            <tr v-for="veh in availableVehicles.slice(0, 5)" :key="veh.id" class="hover:bg-blue-50/20 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <p class="text-sm font-black text-gray-800">{{ veh.nama_kendaraan }}</p>
                <p class="text-xs text-[#3b5998] font-bold mt-1 tracking-wide">{{ veh.plat_nomor }}</p>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="veh.jenis_kepemilikan === 'Kantor' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-gray-50 text-gray-600 border-gray-200'" class="px-2.5 py-1 inline-flex text-[10px] leading-4 font-bold rounded border uppercase tracking-wider">
                  {{ veh.jenis_kepemilikan }}
                </span>
                <p v-if="veh.user_id" class="text-[10px] text-gray-500 mt-1.5 font-medium"><i class="fas fa-user mr-1"></i> Terikat: {{ veh.user?.name || 'Kurir' }}</p>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <span class="bg-green-50 text-green-600 border-green-200 px-3 py-1.5 inline-flex text-xs leading-4 font-black rounded-lg border shadow-sm">
                  <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2 mt-1 animate-pulse"></span>
                  Tersedia
                </span>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="3" class="px-6 py-16 text-center flex flex-col items-center justify-center">
                <div class="bg-gray-50 p-4 rounded-full mb-3">
                  <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <span class="text-sm font-bold text-gray-600">Tidak ada kendaraan tersedia</span>
                <span class="text-xs text-gray-400 mt-1">Semua armada sedang digunakan atau dalam perbaikan.</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="availableVehicles.length > 5" class="bg-gray-50 px-6 py-3 border-t border-gray-100 text-center">
        <button @click="$parent.activeTab = 'kendaraan'" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest">Lihat Semua Armada ({{ availableVehicles.length }}) &rarr;</button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

// --- URL BERSIH RELATIVE PATH ---
const URL_USERS = '/users'
const URL_VEHICLES = '/vehicles'
const URL_WORKS = '/works'
const URL_PACKAGES = '/packages'

// --- STATE PENGELOLAAN DATA ---
const rawUsers = ref([])
const rawVehicles = ref([])
const rawWorks = ref([])
const rawPackages = ref([])

const isLoading = ref(true)
const errorMessage = ref('')

// Reference untuk Chart.js Canvas
let barChartInstance = null
let doughnutChartInstance = null

// --- KOMPUTASI METRIK (SDM & ARMADA) ---

// 1. Kurir & SDM
const listKurir = computed(() => rawUsers.value.filter(u => u.role === 'kurir'))
const totalKurir = computed(() => listKurir.value.length)
// Kurir terpakai = Kurir yang memiliki Work Order dengan status paket selain Terkirim/Dibatalkan
const kurirTerpakai = computed(() => {
  const activeUserIds = new Set()
  rawWorks.value.forEach(w => {
    if (w.package && !['Terkirim', 'Dibatalkan', 'Selesai'].includes(w.package.status_pengiriman)) {
      activeUserIds.add(w.user_id)
    }
  })
  return activeUserIds.size
})

// 2. Kendaraan
const totalArmada = computed(() => rawVehicles.value.length)
const armadaTersedia = computed(() => rawVehicles.value.filter(v => v.status === 'Tersedia').length)
const armadaDigunakan = computed(() => rawVehicles.value.filter(v => v.status === 'Sedang Digunakan').length)
const armadaPerbaikan = computed(() => rawVehicles.value.filter(v => v.status === 'Perbaikan').length)
const availableVehicles = computed(() => rawVehicles.value.filter(v => v.status === 'Tersedia'))

// 3. Work Orders (Penugasan)
const activeWorks = computed(() => {
  return rawWorks.value.filter(w => w.package && !['Terkirim', 'Dibatalkan', 'Selesai'].includes(w.package.status_pengiriman)).length
})
const completedWorks = computed(() => {
  return rawWorks.value.filter(w => w.package && ['Terkirim', 'Selesai'].includes(w.package.status_pengiriman)).length
})

// 4. Alert SOP Farmasi -> Admin
const paketPendingCount = computed(() => {
  return rawPackages.value.filter(p => p.status_pengiriman === 'Pengemasan').length
})

// 5. Agregasi Kinerja Kurir untuk Chart
const kurirPerformanceData = computed(() => {
  const perf = {}
  listKurir.value.forEach(kurir => {
    perf[kurir.name] = { terkirim: 0, diperjalanan: 0 }
  })
  
  rawWorks.value.forEach(w => {
    if (w.courier && w.package) {
      const name = w.courier.name
      const status = w.package.status_pengiriman
      
      if (!perf[name]) perf[name] = { terkirim: 0, diperjalanan: 0 } // Fallback
      
      if (['Terkirim', 'Selesai'].includes(status)) {
        perf[name].terkirim += 1
      } else if (status === 'Diperjalanan') {
        perf[name].diperjalanan += 1
      }
    }
  })
  return perf
})


// --- METODE PENGAMBILAN DATA (API CALL) ---
const fetchDashboardData = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const [resUsers, resVeh, resWorks, resPkg] = await Promise.all([
      axios.get(URL_USERS),
      axios.get(URL_VEHICLES),
      axios.get(URL_WORKS),
      axios.get(URL_PACKAGES)
    ])
    
    rawUsers.value = resUsers.data.data || []
    rawVehicles.value = resVeh.data.data || []
    rawWorks.value = resWorks.data.data || []
    rawPackages.value = resPkg.data.data || []
    
    // Tunggu DOM merender kanvas
    await nextTick()
    renderCharts()

  } catch (error) {
    console.error('Error fetching teamwork overview:', error)
    errorMessage.value = 'Gagal terhubung ke database operasional. Pastikan koneksi jaringan stabil.'
  } finally {
    isLoading.value = false
  }
}

// --- FUNGSI RENDER CHART.JS ---
const renderCharts = () => {
  if (barChartInstance) barChartInstance.destroy()
  if (doughnutChartInstance) doughnutChartInstance.destroy()

  const ctxBar = document.getElementById('kurirPerformanceChart')
  const ctxDoughnut = document.getElementById('vehicleStatusChart')

  if (!ctxBar || !ctxDoughnut) return

  // 1. GRAFIK BATANG (BAR CHART) - KINERJA KURIR
  const labels = Object.keys(kurirPerformanceData.value)
  const dataTerkirim = labels.map(name => kurirPerformanceData.value[name].terkirim)
  const dataDiperjalanan = labels.map(name => kurirPerformanceData.value[name].diperjalanan)

  barChartInstance = new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: labels.length > 0 ? labels : ['Belum Ada Kurir'],
      datasets: [
        {
          label: 'Selesai Diantar',
          data: labels.length > 0 ? dataTerkirim : [0],
          backgroundColor: '#10b981', // Emerald 500
          borderRadius: 6,
          barPercentage: 0.6
        },
        {
          label: 'Sedang Proses Jalan',
          data: labels.length > 0 ? dataDiperjalanan : [0],
          backgroundColor: '#f59e0b', // Amber 500
          borderRadius: 6,
          barPercentage: 0.6
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { position: 'top', align: 'end', labels: { boxWidth: 12, font: { size: 11, family: "'Inter', sans-serif", weight: 'bold' } } },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 }
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11, family: "'Inter', sans-serif", weight: '600' } } },
        y: { 
          beginAtZero: true, 
          stacked: true, // Stacked agar tinggi bar merepresentasikan total beban kerja
          grid: { borderDash: [4, 4], color: '#f1f5f9' }, 
          ticks: { stepSize: 1, font: { family: "'Inter', sans-serif" } } 
        }
      }
    }
  })

  // 2. GRAFIK DOUGHNUT - UTILISASI ARMADA
  doughnutChartInstance = new Chart(ctxDoughnut, {
    type: 'doughnut',
    data: {
      labels: ['Tersedia', 'Sedang Digunakan', 'Perbaikan'],
      datasets: [{
        data: [armadaTersedia.value, armadaDigunakan.value, armadaPerbaikan.value],
        backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444'], // Blue, Amber, Red
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverOffset: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '72%',
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20, font: { size: 11, family: "'Inter', sans-serif", weight: 'bold' } } },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 }
      }
    }
  })
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

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-down {
  animation: slideDown 0.4s ease-out forwards;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  height: 6px; width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>