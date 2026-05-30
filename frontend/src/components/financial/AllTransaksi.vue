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
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm transition duration-150 ease-in-out"
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
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">ID / Tanggal</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Deskripsi Transaksi</th>
            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Tipe Arus Kas</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Metode</th>
            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Nominal</th>
            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        
        <tbody v-if="isLoading" class="bg-white divide-y divide-gray-200">
          <tr v-for="i in 5" :key="i" class="animate-pulse">
            <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-16 mb-2"></div><div class="h-3 bg-gray-200 rounded w-24"></div></td>
            <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-48"></div></td>
            <td class="px-6 py-4"><div class="h-6 bg-gray-200 rounded-full w-24 mx-auto"></div></td>
            <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-20"></div></td>
            <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-24 ml-auto"></div></td>
            <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-12 ml-auto"></div></td>
          </tr>
        </tbody>

        <tbody v-else-if="filteredTransactions.length === 0" class="bg-white">
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">Tidak ada data transaksi yang ditemukan.</td>
          </tr>
        </tbody>

        <tbody v-else class="bg-white divide-y divide-gray-100">
          <tr v-for="trx in filteredTransactions" :key="trx.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-bold text-gray-700">#TRX-{{ String(trx.id).padStart(5, '0') }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ formatDate(trx.created_at) }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-800 font-medium max-w-xs truncate" :title="trx.deskripsi">
              <span v-if="trx.work_id" class="mr-2 text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded border border-purple-200" title="Auto-Generated dari Sistem Pengiriman">SISTEM</span>
              {{ trx.deskripsi }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="getTipeClass(trx.tipe)" class="px-3 py-1 inline-flex text-xs font-bold rounded-full border">
                {{ trx.tipe }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
              {{ trx.metode_pembayaran }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold" :class="trx.tipe === 'Uang Masuk' ? 'text-green-600' : 'text-red-600'">
              {{ trx.tipe === 'Uang Masuk' ? '+' : '-' }} {{ formatRupiah(trx.nominal) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openEditModal(trx)" class="text-[#3b5998] hover:text-blue-900 mr-3 transition-colors" title="Edit Transaksi">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
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

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Edit Data Transaksi</h3>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="updateTransaction" class="space-y-5">
            <div class="flex space-x-4">
              <label class="flex-1 border rounded-lg p-3 cursor-pointer text-center font-semibold transition-all"
                :class="editForm.tipe === 'Uang Masuk' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-white border-gray-300 text-gray-500'">
                <input type="radio" v-model="editForm.tipe" value="Uang Masuk" class="hidden">
                Uang Masuk
              </label>
              <label class="flex-1 border rounded-lg p-3 cursor-pointer text-center font-semibold transition-all"
                :class="editForm.tipe === 'Uang Keluar' ? 'bg-red-50 border-red-500 text-red-700' : 'bg-white border-gray-300 text-gray-500'">
                <input type="radio" v-model="editForm.tipe" value="Uang Keluar" class="hidden">
                Uang Keluar
              </label>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode / Bank</label>
                <select v-model="editForm.metode_pembayaran" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-[#3b5998]">
                  <option value="Tunai / Cash">Tunai / Cash</option>
                  <option value="Transfer BCA">Transfer BCA</option>
                  <option value="Transfer Mandiri">Transfer Mandiri</option>
                  <option value="Transfer BRI">Transfer BRI</option>
                  <option value="QRIS">QRIS</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nominal (Rp)</label>
                <input v-model="editForm.nominal" type="number" min="1" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi / Kategori</label>
              <textarea v-model="editForm.deskripsi" rows="2" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]"></textarea>
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeEditModal" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-gray-700">Batal</button>
          <button @click="updateTransaction" :disabled="isSaving" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Update Transaksi' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="isDeleteModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden">
        <div class="p-6">
          <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Hapus Transaksi</h3>
          <p class="text-sm text-gray-500 text-center">
            Anda yakin ingin menghapus transaksi <strong>#TRX-{{ trxToDelete?.id ? String(trxToDelete.id).padStart(5, '0') : '' }}</strong>? Data yang dihapus akan mengubah laporan keuangan Anda.
          </p>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="isDeleteModalOpen = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium">Batal</button>
          <button @click="deleteTransaction" :disabled="isSaving" class="px-5 py-2.5 bg-red-600 rounded-lg text-white font-medium disabled:opacity-50">
            {{ isSaving ? 'Menghapus...' : 'Ya, Hapus' }}
          </button>
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
const isSaving = ref(false)
const searchQuery = ref('')
const notification = ref({ show: false, message: '', type: 'success' })

// State Modal
const isEditModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const trxToDelete = ref(null)
const editForm = ref({ id: null, tipe: '', metode_pembayaran: '', nominal: '', deskripsi: '' })

const filteredTransactions = computed(() => {
  if (!searchQuery.value) return transactions.value
  const query = searchQuery.value.toLowerCase()
  return transactions.value.filter(trx => {
    const desc = trx.deskripsi?.toLowerCase() || ''
    const method = trx.metode_pembayaran?.toLowerCase() || ''
    return desc.includes(query) || method.includes(query)
  })
})

const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    transactions.value = response.data.data 
  } catch (error) {
    showNotification('Gagal mengambil data transaksi dari server', 'error')
  } finally {
    isLoading.value = false
  }
}

// --- FUNGSI EDIT ---
const openEditModal = (trx) => {
  editForm.value = { ...trx }
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
}

const updateTransaction = async () => {
  isSaving.value = true
  try {
    const response = await axios.put(`${API_URL}/${editForm.value.id}`, editForm.value)
    
    // Update local state secara reaktif
    const index = transactions.value.findIndex(t => t.id === editForm.value.id)
    if (index !== -1) {
      transactions.value[index] = response.data.data
    }
    
    closeEditModal()
    showNotification('Transaksi berhasil diperbarui.', 'success')
  } catch (error) {
    showNotification('Gagal memperbarui transaksi', 'error')
  } finally {
    isSaving.value = false
  }
}

// --- FUNGSI DELETE ---
const confirmDelete = (trx) => {
  trxToDelete.value = trx
  isDeleteModalOpen.value = true
}

const deleteTransaction = async () => {
  if (!trxToDelete.value) return
  
  isSaving.value = true
  try {
    await axios.delete(`${API_URL}/${trxToDelete.value.id}`)
    transactions.value = transactions.value.filter(t => t.id !== trxToDelete.value.id)
    isDeleteModalOpen.value = false
    trxToDelete.value = null
    showNotification('Data transaksi berhasil dihapus.', 'success')
  } catch (error) {
    showNotification('Gagal menghapus transaksi', 'error')
  } finally {
    isSaving.value = false
  }
}

// --- FORMATTER ---
const formatRupiah = (angka) => {
  if (!angka) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka)
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(dateString))
}

const getTipeClass = (tipe) => {
  return tipe === 'Uang Masuk' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'
}

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => notification.value.show = false, 3000)
}

onMounted(() => fetchTransactions())
</script>

<style scoped>
@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-modal-in {
  animation: modalIn 0.2s ease-out forwards;
}
</style>