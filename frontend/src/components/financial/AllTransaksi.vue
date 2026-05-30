<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Buku Besar Transaksi</h2>
        <p class="text-sm text-gray-500 mt-1">Riwayat semua arus kas operasional Jasta Obat.</p>
      </div>
      
      <div class="relative w-full sm:w-80">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Cari deskripsi atau metode..." 
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm transition duration-150 ease-in-out"
        >
      </div>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all', notification.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID / Tanggal</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi Transaksi</th>
            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe Arus Kas</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Metode</th>
            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nominal</th>
            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        
        <tbody v-if="isLoading" class="bg-white divide-y divide-gray-200">
          <tr v-for="i in 5" :key="i" class="animate-pulse">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="h-4 bg-gray-200 rounded w-16 mb-2"></div>
              <div class="h-3 bg-gray-200 rounded w-24"></div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap"><div class="h-4 bg-gray-200 rounded w-48"></div></td>
            <td class="px-6 py-4 text-center"><div class="h-6 bg-gray-200 rounded-full w-24 mx-auto"></div></td>
            <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-20"></div></td>
            <td class="px-6 py-4 text-right"><div class="h-4 bg-gray-200 rounded w-24 ml-auto"></div></td>
            <td class="px-6 py-4 text-right"><div class="h-4 bg-gray-200 rounded w-12 ml-auto"></div></td>
          </tr>
        </tbody>

        <tbody v-else-if="filteredTransactions.length === 0" class="bg-white">
          <tr>
            <td colspan="6" class="px-6 py-12 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-gray-500 font-medium">Tidak ada data transaksi yang ditemukan.</p>
            </td>
          </tr>
        </tbody>

        <tbody v-else class="bg-white divide-y divide-gray-100">
          <tr v-for="trx in filteredTransactions" :key="trx.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-bold text-gray-700">#TRX-{{ String(trx.id).padStart(5, '0') }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ formatDate(trx.created_at) }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-800 font-medium max-w-xs truncate" :title="trx.deskripsi">
              {{ trx.deskripsi }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="getTipeClass(trx.tipe)" class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border">
                <svg v-if="trx.tipe === 'Uang Masuk'" class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <svg v-else class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                {{ trx.tipe }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                {{ trx.metode_pembayaran }}
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold" :class="trx.tipe === 'Uang Masuk' ? 'text-green-600' : 'text-red-600'">
              {{ trx.tipe === 'Uang Masuk' ? '+' : '-' }} {{ formatRupiah(trx.nominal) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="confirmDelete(trx)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Transaksi">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
      <p class="text-sm text-gray-500">
        Menampilkan <span class="font-medium">{{ filteredTransactions.length }}</span> dari <span class="font-medium">{{ transactions.length }}</span> total transaksi.
      </p>
      <button @click="fetchTransactions" class="text-sm text-[#3b5998] hover:underline flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Refresh Data
      </button>
    </div>

    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity" @click="isDeleteModalOpen = false"></div>
      
      <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-md sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-bold text-gray-900">Hapus Riwayat Transaksi</h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Anda yakin ingin menghapus transaksi <strong>#TRX-{{ trxToDelete?.id ? String(trxToDelete.id).padStart(5, '0') : '' }}</strong> ({{ formatRupiah(trxToDelete?.nominal) }})? 
                  Data yang dihapus akan mempengaruhi laporan keuangan (Accounting) Anda.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button @click="deleteTransaction" :disabled="isSaving" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
            {{ isSaving ? 'Menghapus...' : 'Ya, Hapus Transaksi' }}
          </button>
          <button @click="isDeleteModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Batal
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// --- CONFIGURATION ---
const API_URL = 'http://localhost:8000/api/v1/transactions' 

// --- STATE ---
const transactions = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const searchQuery = ref('')
const notification = ref({ show: false, message: '', type: 'success' })

// Modal Hapus State
const isDeleteModalOpen = ref(false)
const trxToDelete = ref(null)

// --- COMPUTED PROPERTIES ---
// Filter pencarian berdasarkan deskripsi atau metode pembayaran
const filteredTransactions = computed(() => {
  if (!searchQuery.value) return transactions.value
  const query = searchQuery.value.toLowerCase()
  return transactions.value.filter(trx => {
    const desc = trx.deskripsi?.toLowerCase() || ''
    const method = trx.metode_pembayaran?.toLowerCase() || ''
    return desc.includes(query) || method.includes(query)
  })
})

// --- METHODS ---

// 1. Fetch Data dari API
const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    transactions.value = response.data.data 
  } catch (error) {
    console.error('Error fetching transactions:', error)
    showNotification('Gagal mengambil data transaksi dari server', 'error')
  } finally {
    isLoading.value = false
  }
}

// 2. Persiapan Delete
const confirmDelete = (trx) => {
  trxToDelete.value = trx
  isDeleteModalOpen.value = true
}

// 3. Eksekusi Delete
const deleteTransaction = async () => {
  if (!trxToDelete.value) return
  
  isSaving.value = true
  try {
    await axios.delete(`${API_URL}/${trxToDelete.value.id}`)
    
    // Hapus data dari local state UI tanpa perlu reload
    transactions.value = transactions.value.filter(t => t.id !== trxToDelete.value.id)
    
    isDeleteModalOpen.value = false
    trxToDelete.value = null
    showNotification('Data transaksi berhasil dihapus.', 'success')
  } catch (error) {
    console.error('Error deleting transaction:', error)
    showNotification('Gagal menghapus transaksi', 'error')
  } finally {
    isSaving.value = false
  }
}

// --- UTILITIES / FORMATTERS ---

// Format Rupiah
const formatRupiah = (angka) => {
  if (!angka) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(angka)
}

// Format Tanggal (e.g., 24 Mei 2026, 14:30)
const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

// Warna Badge Uang Masuk / Keluar
const getTipeClass = (tipe) => {
  if (tipe === 'Uang Masuk') {
    return 'bg-green-50 text-green-700 border-green-200'
  }
  return 'bg-red-50 text-red-700 border-red-200'
}

// Tampilkan Toast
const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => {
    notification.value.show = false
  }, 3000)
}

// --- LIFECYCLE ---
onMounted(() => {
  fetchTransactions()
})
</script>