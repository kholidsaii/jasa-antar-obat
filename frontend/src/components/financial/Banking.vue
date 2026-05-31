<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col relative min-h-[400px]">
    
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Dompet & Rekening (Banking)</h2>
        <p class="text-sm text-gray-500 mt-1">Pantau total saldo yang tersedia di berbagai metode pembayaran.</p>
      </div>
      <button @click="fetchTransactions" :disabled="isLoading" class="text-sm text-[#3b5998] hover:underline flex items-center font-medium">
        <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Refresh Data
      </button>
    </div>

    <div v-if="isLoading" class="p-10 flex justify-center">
       <svg class="animate-spin h-8 w-8 text-[#3b5998]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
    </div>

    <div v-else class="p-6 bg-gray-50/30">
      
      <div class="mb-8 bg-gradient-to-br from-[#2b4170] to-[#3b5998] rounded-2xl p-6 sm:p-8 shadow-lg text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5 mix-blend-overlay"></div>
        <div class="absolute bottom-0 right-10 w-32 h-32 rounded-full bg-white opacity-10 mix-blend-overlay"></div>
        
        <h3 class="text-sm font-semibold text-blue-100 uppercase tracking-wider mb-2">Grand Total Seluruh Aset</h3>
        <p class="text-4xl sm:text-5xl font-black mb-6 tracking-tight">{{ formatRupiah(grandTotalSaldo) }}</p>
        
        <div class="grid grid-cols-2 gap-4 border-t border-blue-400/30 pt-4">
          <div>
            <p class="text-xs text-blue-200 mb-1">Total Uang Masuk</p>
            <p class="text-lg font-bold text-green-300 flex items-center">
              <i class="fas fa-arrow-down mr-1.5 text-xs"></i> {{ formatRupiah(grandTotalMasuk) }}
            </p>
          </div>
          <div>
            <p class="text-xs text-blue-200 mb-1">Total Uang Keluar</p>
            <p class="text-lg font-bold text-red-300 flex items-center">
              <i class="fas fa-arrow-up mr-1.5 text-xs"></i> {{ formatRupiah(grandTotalKeluar) }}
            </p>
          </div>
        </div>
      </div>

      <h3 class="text-base font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Rincian Per Dompet / Metode</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <div v-for="(data, name) in groupedBalances" :key="name" 
             class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
          
          <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-3">
              <div :class="['w-10 h-10 rounded-full flex items-center justify-center bg-opacity-10', getDompetColor(name).bg]">
                <i :class="[getDompetIcon(name), getDompetColor(name).text, 'text-lg']"></i>
              </div>
              <h4 class="font-bold text-gray-800 text-sm">{{ name }}</h4>
            </div>
          </div>
          
          <div class="mb-4">
            <p class="text-xs text-gray-500 font-medium mb-1">Saldo Aktif</p>
            <p class="text-2xl font-black text-gray-900 tracking-tight">{{ formatRupiah(data.saldo) }}</p>
          </div>
          
          <div class="flex justify-between text-xs border-t border-gray-100 pt-3">
            <div class="text-gray-500">
              Masuk: <span class="font-bold text-green-600">{{ formatRupiah(data.masuk) }}</span>
            </div>
            <div class="text-gray-500">
              Keluar: <span class="font-bold text-red-500">{{ formatRupiah(data.keluar) }}</span>
            </div>
          </div>
          
          <div :class="['absolute bottom-0 left-0 h-1 w-full', getDompetColor(name).border]"></div>
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

const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
}

// Styling Dinamis
const getDompetIcon = (method) => {
  if(method.includes('Cash') || method.includes('Tunai')) return 'fas fa-money-bill-wave'
  if(method.includes('Bank') || method.includes('Transfer')) return 'fas fa-university'
  if(method.includes('QRIS') || method.includes('Wallet')) return 'fas fa-qrcode'
  return 'fas fa-wallet'
}

const getDompetColor = (method) => {
  if(method.includes('Cash') || method.includes('Tunai')) return { bg: 'bg-green-500', text: 'text-green-600', border: 'bg-green-500' }
  if(method.includes('Bank') || method.includes('Transfer')) return { bg: 'bg-blue-500', text: 'text-blue-600', border: 'bg-blue-500' }
  if(method.includes('QRIS') || method.includes('Wallet')) return { bg: 'bg-purple-500', text: 'text-purple-600', border: 'bg-purple-500' }
  return { bg: 'bg-gray-500', text: 'text-gray-600', border: 'bg-gray-400' }
}

// Kalkulasi Grand Total
const grandTotalMasuk = computed(() => transactions.value.filter(t => t.tipe === 'Uang Masuk').reduce((sum, t) => sum + Number(t.nominal), 0))
const grandTotalKeluar = computed(() => transactions.value.filter(t => t.tipe === 'Uang Keluar').reduce((sum, t) => sum + Number(t.nominal), 0))
const grandTotalSaldo = computed(() => grandTotalMasuk.value - grandTotalKeluar.value)

// Pemilahan berdasarkan 'metode_pembayaran'
const groupedBalances = computed(() => {
  const result = {
    'Tunai / Cash': { masuk: 0, keluar: 0, saldo: 0 },
    'Transfer Bank': { masuk: 0, keluar: 0, saldo: 0 },
    'QRIS / E-Wallet': { masuk: 0, keluar: 0, saldo: 0 }
  }

  transactions.value.forEach(trx => {
    // Fallback jika ada metode lain yang tidak standard
    const method = trx.metode_pembayaran || 'Tunai / Cash'
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

  // Hapus dompet yang benar-benar 0 transaksinya (opsional)
  // Object.keys(result).forEach(key => { if(result[key].masuk === 0 && result[key].keluar === 0) delete result[key] })

  return result
})

const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    transactions.value = response.data.data
  } catch (error) {
    console.error('Gagal mengambil data:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => fetchTransactions())
</script>