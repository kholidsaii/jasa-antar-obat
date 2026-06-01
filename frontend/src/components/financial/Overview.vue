<template>
  <!-- 
    =======================================================================
    EXECUTIVE FINANCIAL DASHBOARD - PUSAT ANALITIK KEUANGAN
    =======================================================================
    Dashboard ini memberikan pandangan 360 derajat terhadap arus kas (cash flow),
    margin keuntungan (profitability), distribusi metode pembayaran, serta 
    pemantauan piutang (tagihan belum lunas) secara real-time.
  -->
  <div class="space-y-8 flex flex-col h-full animate-fade-in pb-12">
    
    <!-- ========================================== -->
    <!-- 1. HEADER & ACTION CONTROLS                -->
    <!-- ========================================== -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 relative overflow-hidden group hover:shadow-md transition-all duration-300">
      <!-- Background Gradient Decoration -->
      <div class="absolute right-0 top-0 w-80 h-80 bg-gradient-to-br from-emerald-50 to-transparent rounded-full -translate-y-1/2 translate-x-1/3 opacity-60 pointer-events-none group-hover:scale-110 transition-transform duration-700"></div>
      
      <div class="relative z-10 w-full lg:w-2/3">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight flex items-center">
          <svg class="w-7 h-7 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Executive Financial Overview
        </h2>
        <p class="text-sm text-gray-500 mt-2 font-medium leading-relaxed">
          Monitor kesehatan finansial operasional pengiriman, evaluasi margin laba rugi, dan lacak penyelesaian piutang tagihan dari pasien.
        </p>
      </div>
      
      <div class="relative z-10 w-full lg:w-auto flex flex-col sm:flex-row gap-3">
        <button @click="fetchFinancialData" :disabled="isLoading" class="w-full sm:w-auto flex items-center justify-center bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-6 py-3 rounded-xl font-bold text-sm transition-all duration-300 shadow-sm disabled:opacity-50 group-hover:shadow hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
          <svg :class="{'animate-spin': isLoading}" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          {{ isLoading ? 'Kalkulasi Ulang...' : 'Refresh Buku Kas' }}
        </button>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 2. SMART FINANCIAL ALERTS (INSIGHTS)       -->
    <!-- ========================================== -->
    <!-- Alert Error Koneksi -->
    <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-5 rounded-2xl shadow-sm flex items-start animate-slide-down">
      <div class="flex-shrink-0 bg-red-100 p-2 rounded-full mt-0.5">
        <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      <div class="ml-4">
        <h3 class="text-sm font-black text-red-800 tracking-wide">Koneksi Database Keuangan Terputus</h3>
        <p class="text-xs font-medium text-red-600 mt-1">{{ errorMessage }}</p>
      </div>
    </div>

    <!-- Alert Piutang Tinggi (Jika ada piutang berlebih) -->
    <div v-if="!isLoading && belumLunasValue > 0" class="bg-amber-50 border border-amber-200 p-5 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 animate-slide-down">
      <div class="flex items-start">
        <div class="flex-shrink-0 bg-amber-100 p-2.5 rounded-full mt-0.5">
          <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div class="ml-4">
          <h3 class="text-sm font-black text-amber-800 tracking-wide uppercase">Peringatan Arus Kas (Piutang)</h3>
          <p class="text-xs font-medium text-amber-700 mt-1 leading-relaxed max-w-3xl">
            Terdapat tagihan mengendap sebesar <span class="font-black bg-amber-200 px-1.5 py-0.5 rounded text-amber-900">{{ formatRupiah(belumLunasValue) }}</span> dari <span class="font-bold">{{ belumLunasCount }} paket</span> yang belum dilunasi. Segera instruksikan armada lapangan untuk melakukan penagihan pembayaran.
          </p>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. SKELETON LOADING (PRE-FETCH)            -->
    <!-- ========================================== -->
    <div v-if="isLoading" class="space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="i in 4" :key="'skel-fin-'+i" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-40">
          <div class="h-3 bg-gray-200 rounded-full w-1/2 mb-5"></div>
          <div class="h-10 bg-gray-200 rounded-lg w-3/4 mb-3"></div>
          <div class="h-3 bg-gray-100 rounded-full w-2/3"></div>
        </div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-96"></div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-96"></div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 4. KARTU METRIK UTAMA (KPI KEUANGAN)       -->
    <!-- ========================================== -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <!-- Metrik 1: Total Pendapatan (Gross Income) -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-green-50 w-28 h-28 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1.5 w-full">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Pendapatan Kotor</p>
            <p class="text-3xl lg:text-4xl font-black text-gray-900 tracking-tight truncate" :title="formatRupiah(totalIncome)">
              {{ formatShortRupiah(totalIncome) }}
            </p>
            <div class="flex items-center mt-3">
              <span class="text-[10px] text-green-700 font-bold bg-green-50 px-2.5 py-1 rounded-md uppercase border border-green-100 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Semua Uang Masuk
              </span>
            </div>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-1.5 bg-green-500"></div>
      </div>

      <!-- Metrik 2: Total Beban (Gross Expense) -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-red-50 w-28 h-28 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1.5 w-full">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Beban Operasional</p>
            <p class="text-3xl lg:text-4xl font-black text-red-600 tracking-tight truncate" :title="formatRupiah(totalExpense)">
              {{ formatShortRupiah(totalExpense) }}
            </p>
            <div class="flex items-center mt-3">
              <span class="text-[10px] text-red-700 font-bold bg-red-50 px-2.5 py-1 rounded-md uppercase border border-red-100 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                Semua Uang Keluar
              </span>
            </div>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-1.5 bg-red-500"></div>
      </div>

      <!-- Metrik 3: Laba Bersih (Net Profit) -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 sm:col-span-2 lg:col-span-2">
        <div class="absolute -right-10 -top-10 bg-blue-50 w-40 h-40 rounded-full opacity-60 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-center relative z-10 h-full">
          <div class="space-y-1.5 w-2/3">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Laba Bersih Kas (Margin)</p>
            <p :class="['text-4xl lg:text-5xl font-black tracking-tight truncate', netProfit >= 0 ? 'text-[#3b5998]' : 'text-red-600']" :title="formatRupiah(netProfit)">
              {{ formatRupiah(netProfit) }}
            </p>
            <div class="flex items-center mt-3 space-x-2">
              <span :class="['text-[10px] font-bold px-2.5 py-1 rounded-md uppercase border flex items-center', netProfit >= 0 ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-red-50 text-red-700 border-red-100']">
                Pendapatan - Beban
              </span>
              <span class="text-[10px] font-black bg-gray-100 text-gray-600 px-2 py-1 rounded-md uppercase">
                Margin: {{ persentaseMargin }}%
              </span>
            </div>
          </div>
          
          <!-- Circular Margin Indicator -->
          <div class="relative w-20 h-20 shrink-0">
            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
              <path class="text-gray-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
              <path :class="netProfit >= 0 ? 'text-[#3b5998]' : 'text-red-500'" stroke-dasharray="100, 100" :stroke-dashoffset="100 - Math.min(Math.max(persentaseMargin, 0), 100)" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
              <span :class="['text-sm font-black', netProfit >= 0 ? 'text-[#3b5998]' : 'text-red-500']">{{ persentaseMargin }}%</span>
            </div>
          </div>
        </div>
        <div :class="['absolute bottom-0 left-0 w-full h-1.5', netProfit >= 0 ? 'bg-[#3b5998]' : 'bg-red-500']"></div>
      </div>

    </div>

    <!-- ========================================== -->
    <!-- 5. AREA GRAFIK ANALITIK KEUANGAN           -->
    <!-- ========================================== -->
    <div v-show="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-2">
      
      <!-- Grafik 1: Analisis Arus Kas (Kategori Pengeluaran - Bar Chart) -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 lg:col-span-2 flex flex-col hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
        <div class="mb-8 flex justify-between items-start border-b border-gray-100 pb-5 relative z-10">
          <div>
            <h3 class="text-xl font-black text-gray-800 tracking-tight">Postur Beban Operasional</h3>
            <p class="text-xs text-gray-500 font-medium mt-1.5">Klasifikasi pengeluaran tertinggi berdasarkan deskripsi transaksi.</p>
          </div>
          <div class="p-2.5 bg-red-50 rounded-xl border border-red-100">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
          </div>
        </div>
        
        <div v-if="expenseCategories.length === 0" class="flex-1 flex flex-col items-center justify-center text-center p-8 border-2 border-dashed border-gray-100 rounded-2xl">
          <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          <p class="text-sm font-bold text-gray-400">Belum Ada Pengeluaran</p>
        </div>
        
        <div v-else class="flex-1 relative min-h-[300px] w-full z-10">
          <canvas id="expenseBarChart"></canvas>
        </div>
      </div>

      <!-- Grafik 2: Metode Pembayaran (Doughnut Chart) -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col hover:shadow-md transition-shadow duration-300">
        <div class="mb-8 flex justify-between items-start border-b border-gray-100 pb-5">
          <div>
            <h3 class="text-xl font-black text-gray-800 tracking-tight">Metode Transaksi</h3>
            <p class="text-xs text-gray-500 font-medium mt-1.5">Distribusi preferensi dompet kas.</p>
          </div>
          <div class="p-2.5 bg-purple-50 rounded-xl border border-purple-100">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
        </div>
        
        <div class="flex-1 relative min-h-[250px] w-full flex items-center justify-center">
          <canvas id="paymentMethodChart"></canvas>
        </div>
      </div>

    </div>

    <!-- ========================================== -->
    <!-- 6. AREA BAWAH: LIVE FEED BUKU KAS          -->
    <!-- ========================================== -->
    <div v-if="!isLoading" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow mt-4">
      <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h3 class="text-lg font-black text-gray-800 tracking-tight">Log Transaksi Real-Time</h3>
          <p class="text-xs text-gray-500 font-medium mt-1">10 entri arus kas terakhir yang masuk ke dalam buku besar.</p>
        </div>
        <button @click="$parent.activeTab = 'all-transaksi'" class="text-xs font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-xl transition-colors uppercase tracking-widest border border-emerald-100 w-full sm:w-auto text-center">
          Lihat Buku Besar Lengkap &rarr;
        </button>
      </div>
      
      <div class="overflow-x-auto p-2">
        <table class="min-w-full divide-y divide-gray-100">
          <thead>
            <tr>
              <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">Detail & Waktu</th>
              <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">Klasifikasi Kas</th>
              <th scope="col" class="px-6 py-4 text-center text-[11px] font-black text-gray-400 uppercase tracking-widest">Metode / Dompet</th>
              <th scope="col" class="px-6 py-4 text-right text-[11px] font-black text-gray-400 uppercase tracking-widest">Nominal (IDR)</th>
            </tr>
          </thead>
          <tbody v-if="recentTransactions.length > 0" class="divide-y divide-gray-50 bg-white">
            <tr v-for="trx in recentTransactions" :key="trx.id" class="hover:bg-gray-50/80 transition-colors group">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div :class="['w-10 h-10 rounded-full flex items-center justify-center mr-4 shadow-sm border shrink-0', trx.tipe === 'Uang Masuk' ? 'bg-green-50 border-green-100 text-green-600' : 'bg-red-50 border-red-100 text-red-600']">
                    <i :class="trx.tipe === 'Uang Masuk' ? 'fas fa-arrow-down' : 'fas fa-arrow-up'"></i>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-800 max-w-xs truncate" :title="trx.deskripsi">{{ trx.deskripsi }}</p>
                    <p class="text-[10px] text-gray-400 mt-1 font-medium"><i class="far fa-clock mr-1"></i> {{ formatDate(trx.created_at) }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="trx.tipe === 'Uang Masuk' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200'" class="px-2.5 py-1 inline-flex text-[10px] leading-4 font-black rounded-lg border uppercase tracking-wider">
                  {{ trx.tipe }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="text-xs font-bold text-gray-600 bg-gray-100 border border-gray-200 px-3 py-1.5 rounded-lg">
                  {{ trx.metode_pembayaran || 'Tunai / Cash' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <span :class="['text-sm font-black', trx.tipe === 'Uang Masuk' ? 'text-green-600' : 'text-red-600']">
                  {{ trx.tipe === 'Uang Masuk' ? '+' : '-' }} {{ formatRupiah(trx.nominal) }}
                </span>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="4" class="px-6 py-16 text-center flex flex-col items-center justify-center">
                <div class="bg-gray-50 p-4 rounded-full mb-3">
                  <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <span class="text-sm font-bold text-gray-600">Buku Besar Kasong</span>
                <span class="text-xs text-gray-400 mt-1">Belum ada transaksi keluar atau masuk yang tercatat.</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

// --- URL BERSIH RELATIVE PATH ---
const API_URL = '/transactions'
const PACKAGE_API_URL = '/packages'

// --- STATE PENGELOLAAN DATA ---
const rawTransactions = ref([])
const rawPackages = ref([])

const isLoading = ref(true)
const errorMessage = ref('')

// Reference untuk Chart.js Canvas
let expenseBarChartInstance = null
let paymentMethodChartInstance = null

// --- UTILITAS ---
const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
const formatShortRupiah = (angka) => {
  if (angka >= 1000000000) return 'Rp ' + (angka / 1000000000).toFixed(1) + ' M'
  if (angka >= 1000000) return 'Rp ' + (angka / 1000000).toFixed(1) + ' Jt'
  return formatRupiah(angka)
}
const formatDate = (dateString) => {
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(dateString))
}

// --- KOMPUTASI METRIK KEUANGAN ---
const totalIncome = computed(() => rawTransactions.value.filter(t => t.tipe === 'Uang Masuk').reduce((sum, t) => sum + Number(t.nominal), 0))
const totalExpense = computed(() => rawTransactions.value.filter(t => t.tipe === 'Uang Keluar').reduce((sum, t) => sum + Number(t.nominal), 0))
const netProfit = computed(() => totalIncome.value - totalExpense.value)

const persentaseMargin = computed(() => {
  if (totalIncome.value === 0) return 0
  return Math.round((netProfit.value / totalIncome.value) * 100)
})

// Metrik Piutang (Mengambil data dari tabel Packages)
const belumLunasPackages = computed(() => rawPackages.value.filter(p => p.status_pembayaran === 'Belum Lunas' && p.status_pengiriman !== 'Dibatalkan'))
const belumLunasCount = computed(() => belumLunasPackages.value.length)
const belumLunasValue = computed(() => belumLunasPackages.value.reduce((sum, p) => sum + Number(p.total_harga || 0), 0))

// Log Transaksi (10 Terbaru)
const recentTransactions = computed(() => rawTransactions.value.slice(0, 10))

// --- KOMPUTASI CHART.JS ---
// 1. Kategorisasi Pengeluaran Cerdas
const getKategoriBeban = (deskripsi) => {
  const text = (deskripsi || '').toLowerCase()
  if (text.includes('bensin') || text.includes('bbm') || text.includes('parkir')) return 'Transportasi & BBM'
  if (text.includes('makan') || text.includes('minum') || text.includes('konsumsi')) return 'Konsumsi'
  if (text.includes('lakban') || text.includes('kertas') || text.includes('packing')) return 'Perlengkapan (ATK)'
  if (text.includes('gaji') || text.includes('upah') || text.includes('insentif')) return 'Gaji Pegawai'
  if (text.includes('service') || text.includes('bengkel') || text.includes('oli')) return 'Pemeliharaan Armada'
  return 'Beban Lainnya'
}

const expenseCategories = computed(() => {
  const expenses = rawTransactions.value.filter(t => t.tipe === 'Uang Keluar')
  const groups = {}
  expenses.forEach(t => {
    const akun = getKategoriBeban(t.deskripsi)
    if (!groups[akun]) groups[akun] = 0
    groups[akun] += Number(t.nominal)
  })
  return Object.keys(groups).map(key => ({ nama: key, total: groups[key] })).sort((a, b) => b.total - a.total).slice(0, 6) // Ambil Top 6
})

// 2. Rasio Metode Pembayaran
const paymentMethods = computed(() => {
  const methods = { 'Tunai / Cash': 0, 'Transfer Bank': 0, 'QRIS / E-Wallet': 0 }
  rawTransactions.value.forEach(t => {
    const m = t.metode_pembayaran || 'Tunai / Cash'
    if (methods[m] !== undefined) {
      methods[m] += 1
    } else {
      methods['Tunai / Cash'] += 1 // Fallback
    }
  })
  return methods
})

// --- METODE PENGAMBILAN DATA (API CALL) ---
const fetchFinancialData = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const [resTrx, resPkg] = await Promise.all([
      axios.get(API_URL),
      axios.get(PACKAGE_API_URL)
    ])
    
    rawTransactions.value = resTrx.data.data || []
    rawPackages.value = resPkg.data.data || []
    
    // Tunggu DOM merender kanvas
    await nextTick()
    renderCharts()

  } catch (error) {
    console.error('Error fetching financial overview:', error)
    errorMessage.value = 'Gagal memuat buku besar keuangan. Silakan periksa koneksi internet Anda.'
  } finally {
    isLoading.value = false
  }
}

// --- FUNGSI RENDER CHART.JS ---
const renderCharts = () => {
  if (expenseBarChartInstance) expenseBarChartInstance.destroy()
  if (paymentMethodChartInstance) paymentMethodChartInstance.destroy()

  const ctxBar = document.getElementById('expenseBarChart')
  const ctxDoughnut = document.getElementById('paymentMethodChart')

  if (!ctxBar || !ctxDoughnut) return

  // 1. GRAFIK BATANG (BAR CHART) - POSTUR BEBAN
  const labels = expenseCategories.value.map(c => c.nama)
  const dataBeban = expenseCategories.value.map(c => c.total)

  expenseBarChartInstance = new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: labels.length > 0 ? labels : ['Belum Ada Data'],
      datasets: [{
        label: 'Total Pengeluaran (Rp)',
        data: labels.length > 0 ? dataBeban : [0],
        backgroundColor: 'rgba(239, 68, 68, 0.85)', // Red 500
        borderColor: '#dc2626',
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
        tooltip: { 
          backgroundColor: 'rgba(15, 23, 42, 0.95)', 
          padding: 12, 
          cornerRadius: 8,
          callbacks: {
            label: function(context) {
              return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
            }
          }
        }
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10, family: "'Inter', sans-serif", weight: '600' } } },
        y: { 
          beginAtZero: true, 
          grid: { borderDash: [4, 4], color: '#f1f5f9' }, 
          ticks: { 
            font: { family: "'Inter', sans-serif" },
            callback: function(value) {
              return 'Rp ' + (value / 1000) + 'K'; // Shorten large numbers
            }
          } 
        }
      }
    }
  })

  // 2. GRAFIK DOUGHNUT - METODE TRANSAKSI
  paymentMethodChartInstance = new Chart(ctxDoughnut, {
    type: 'doughnut',
    data: {
      labels: ['Tunai / Cash', 'Transfer Bank', 'QRIS / E-Wallet'],
      datasets: [{
        data: [paymentMethods.value['Tunai / Cash'], paymentMethods.value['Transfer Bank'], paymentMethods.value['QRIS / E-Wallet']],
        backgroundColor: ['#22c55e', '#3b82f6', '#a855f7'], // Green, Blue, Purple
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
        legend: { position: 'bottom', labels: { boxWidth: 12, padding: 20, font: { size: 11, family: "'Inter', sans-serif", weight: 'bold' } } },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.95)', padding: 12, cornerRadius: 8 }
      }
    }
  })
}

// --- LIFECYCLE VUE ---
onMounted(() => {
  fetchFinancialData()
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