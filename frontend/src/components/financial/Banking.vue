<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
      <div>
        <h2 class="text-lg font-bold text-gray-800">Rekapitulasi Rekening & Kas (Banking)</h2>
        <p class="text-sm text-gray-500 mt-1">Estimasi saldo kas di tangan dan di masing-masing rekening bank.</p>
      </div>
      <button @click="fetchTransactions" class="text-sm text-[#3b5998] hover:underline flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Refresh Rekap
      </button>
    </div>

    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-[#3b5998] to-blue-800 text-white">
      <div class="flex flex-col md:flex-row md:items-center justify-between">
        <div class="mb-4 md:mb-0">
          <p class="text-blue-200 font-semibold text-sm uppercase tracking-wider mb-1">Total Keseluruhan Aset (Kas & Bank)</p>
          <h2 class="text-4xl font-black">{{ formatRupiah(grandTotalSaldo) }}</h2>
        </div>
        <div class="flex space-x-6 text-sm">
          <div>
            <p class="text-blue-200 mb-1">Total Uang Masuk</p>
            <p class="font-bold text-white flex items-center"><svg class="w-4 h-4 mr-1 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg> {{ formatRupiah(grandTotalMasuk) }}</p>
          </div>
          <div>
            <p class="text-blue-200 mb-1">Total Uang Keluar</p>
            <p class="font-bold text-white flex items-center"><svg class="w-4 h-4 mr-1 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg> {{ formatRupiah(grandTotalKeluar) }}</p>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isLoading" class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-for="i in 3" :key="i" class="h-32 bg-gray-100 rounded-xl animate-pulse"></div>
    </div>

    <div v-else class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 bg-white">
      
      <div v-for="(data, metode) in groupedBalances" :key="metode" 
           class="border border-gray-200 rounded-2xl p-5 relative overflow-hidden transition-all hover:shadow-md hover:border-blue-300">
        
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-blue-100 to-transparent rounded-full opacity-50 pointer-events-none"></div>
        <div class="absolute -left-6 -bottom-6 w-16 h-16 bg-gradient-to-tr from-gray-100 to-transparent rounded-full opacity-50 pointer-events-none"></div>

        <div class="relative z-10 flex justify-between items-start mb-4">
          <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-[#3b5998]/10 flex items-center justify-center mr-3">
              <svg v-if="metode.toLowerCase().includes('tunai') || metode.toLowerCase().includes('cash')" class="w-5 h-5 text-[#3b5998]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
              <svg v-else class="w-5 h-5 text-[#3b5998]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <h3 class="font-bold text-gray-800 tracking-wide">{{ metode }}</h3>
          </div>
        </div>

        <div class="relative z-10">
          <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wider">Saldo Terhitung</p>
          <p class="text-2xl font-extrabold" :class="data.saldo >= 0 ? 'text-gray-900' : 'text-red-600'">
            {{ formatRupiah(data.saldo) }}
          </p>
        </div>

        <div class="relative z-10 mt-4 flex items-center text-xs font-medium space-x-4 border-t border-gray-100 pt-3">
          <div class="flex items-center text-green-600" title="Total Uang Masuk">
             <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
             {{ formatRupiah(data.masuk) }}
          </div>
          <div class="flex items-center text-red-500" title="Total Uang Keluar">
             <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
             {{ formatRupiah(data.keluar) }}
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api/v1/transactions'
const transactions = ref([])
const isLoading = ref(true)

// --- COMPUTED: TOTAL KESELURUHAN (GRAND TOTAL) ---
const grandTotalMasuk = computed(() => transactions.value.filter(t => t.tipe === 'Uang Masuk').reduce((sum, t) => sum + Number(t.nominal), 0))
const grandTotalKeluar = computed(() => transactions.value.filter(t => t.tipe === 'Uang Keluar').reduce((sum, t) => sum + Number(t.nominal), 0))
const grandTotalSaldo = computed(() => grandTotalMasuk.value - grandTotalKeluar.value)

// --- COMPUTED: PENGELOMPOKAN SALDO PER BANK ---
const groupedBalances = computed(() => {
  const result = {}

  transactions.value.forEach(trx => {
    const method = trx.metode_pembayaran || 'Lainnya'
    if (!result[method]) {
      result[method] = { masuk: 0, keluar: 0, saldo: 0 }
    }
    const nom = Number(trx.nominal)
    
    if (trx.tipe === 'Uang Masuk') {
      result[method].masuk += nom
      result[method].saldo += nom
    } else if (trx.tipe === 'Uang Keluar') {
      result[method].keluar += nom
      result[method].saldo -= nom
    }
  })

  return result
})

// --- METHODS ---
const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    transactions.value = response.data.data
  } catch (error) {
    console.error('Error fetching banking data:', error)
  } finally {
    isLoading.value = false
  }
}

const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
  }).format(angka || 0)
}

onMounted(() => { fetchTransactions() })
</script>