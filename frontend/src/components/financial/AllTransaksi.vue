<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative min-h-[500px]">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Buku Besar Transaksi</h2>
        <p class="text-sm text-gray-500 mt-1">Riwayat semua arus kas operasional (Pemasukan & Pengeluaran).</p>
      </div>
      
      <div class="relative w-full sm:w-80">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Cari deskripsi, nominal, metode..." 
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm transition duration-150"
        >
      </div>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all', notification.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>

    <div class="overflow-x-auto flex-1">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase whitespace-nowrap">Tanggal & Waktu</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Deskripsi Transaksi</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase whitespace-nowrap">Tipe</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase whitespace-nowrap">Metode</th>
            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase whitespace-nowrap">Nominal (Rp)</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase whitespace-nowrap">Aksi</th>
          </tr>
        </thead>
        
        <tbody v-if="isLoading" class="bg-white divide-y divide-gray-200">
          <tr v-for="i in 5" :key="i" class="animate-pulse">
            <td colspan="6" class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-full"></div></td>
          </tr>
        </tbody>
        
        <tbody v-else-if="filteredTransactions.length === 0" class="bg-white">
          <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">Tidak ada data transaksi.</td></tr>
        </tbody>

        <tbody v-else class="bg-white divide-y divide-gray-100">
          <tr v-for="trx in paginatedTransactions" :key="trx.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">{{ formatDate(trx.created_at) }}</td>
            <td class="px-6 py-4 text-sm text-gray-800 break-words min-w-[200px]">{{ trx.deskripsi }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="getTipeClass(trx.tipe)" class="px-3 py-1 inline-flex text-xs font-bold rounded-full border">
                <i :class="trx.tipe === 'Uang Masuk' ? 'fas fa-arrow-down mr-1' : 'fas fa-arrow-up mr-1'"></i> {{ trx.tipe }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-600">
              {{ trx.metode_pembayaran || 'Tunai / Cash' }}
            </td>
            <td :class="['px-6 py-4 whitespace-nowrap text-right text-sm font-bold', trx.tipe === 'Uang Masuk' ? 'text-green-600' : 'text-red-600']">
              {{ formatRupiah(trx.nominal) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
              <button @click="openEditModal(trx)" class="text-[#3b5998] hover:text-blue-900 bg-blue-50 p-1.5 rounded-lg border border-blue-200 mr-2" title="Edit Transaksi">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              </button>
              
              <button @click="confirmDelete(trx)" class="text-red-500 hover:text-red-700 bg-red-50 p-1.5 rounded-lg border border-red-200" title="Hapus Transaksi">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginasi -->
    <div v-if="filteredTransactions.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center gap-4">
      <span class="text-sm text-gray-500 font-medium">Menampilkan {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredTransactions.length) }} dari {{ filteredTransactions.length }} data</span>
      <div class="flex space-x-2">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50">Prev</button>
        <span class="px-2 text-sm font-bold text-gray-700 mt-2">{{ currentPage }} / {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50">Next</button>
      </div>
    </div>

    <!-- MODAL EDIT TRANSAKSI -->
    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isEditModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-3">Edit Transaksi Kas</h3>
        
        <form @submit.prevent="updateTransaksi" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold mb-1">Tipe Transaksi</label>
              <!-- Jika Transaksi dari sistem Paket, tipe uang tidak bisa diubah -->
              <select v-model="editForm.tipe" :disabled="editForm.deskripsi.includes('#PKT-')" class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] disabled:bg-gray-100">
                <option value="Uang Masuk">Uang Masuk</option>
                <option value="Uang Keluar">Uang Keluar</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">Metode Pembayaran</label>
              <select v-model="editForm.metode_pembayaran" class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]">
                <option value="Tunai / Cash">Tunai / Cash</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1">Nominal (Rp)</label>
            <input v-model="editForm.nominal" type="number" required min="0" class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]">
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1">Deskripsi</label>
            <textarea v-model="editForm.deskripsi" :disabled="editForm.deskripsi.includes('#PKT-')" rows="2" class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] disabled:bg-gray-100"></textarea>
          </div>
        </form>
        
        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
          <button @click="isEditModalOpen = false" class="px-5 py-2.5 bg-gray-100 rounded-lg">Batal</button>
          <button @click="updateTransaksi" :disabled="isSaving" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg hover:bg-blue-800 disabled:opacity-50">Update</button>
        </div>
      </div>
    </div>

    <!-- MODAL DELETE -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isDeleteModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg></div>
        <h3 class="text-lg font-bold mb-2">Hapus Transaksi?</h3>
        <p class="text-sm text-gray-500 mb-6">Penghapusan ini tidak dapat dibatalkan dan akan memengaruhi neraca buku besar.</p>
        <div class="flex justify-center space-x-3">
          <button @click="isDeleteModalOpen = false" class="px-5 py-2 bg-gray-100 rounded-lg font-medium">Batal</button>
          <button @click="deleteTransaksi" class="px-5 py-2 bg-red-600 text-white rounded-lg font-medium">Ya, Hapus</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api/v1/transactions'
const transactions = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const notification = ref({ show: false, message: '', type: 'success' })

// --- State Pagination & Search ---
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 7

const filteredTransactions = computed(() => {
  if (!searchQuery.value) return transactions.value
  const query = searchQuery.value.toLowerCase()
  return transactions.value.filter(trx => 
    trx.deskripsi.toLowerCase().includes(query) || 
    trx.nominal.toString().includes(query) ||
    (trx.metode_pembayaran || '').toLowerCase().includes(query)
  )
})

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredTransactions.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)
const paginatedTransactions = computed(() => filteredTransactions.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

// --- Modal States ---
const isEditModalOpen = ref(false)
const editForm = ref({ id: '', tipe: '', metode_pembayaran: '', nominal: '', deskripsi: '' })

const isDeleteModalOpen = ref(false)
const trxToDelete = ref(null)

const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    transactions.value = response.data.data
  } catch (error) { console.error('Error:', error) } finally { isLoading.value = false }
}

const openEditModal = (trx) => {
  editForm.value = { ...trx }
  isEditModalOpen.value = true
}

const updateTransaksi = async () => {
  isSaving.value = true
  try {
    const response = await axios.put(`${API_URL}/${editForm.value.id}`, editForm.value)
    const index = transactions.value.findIndex(t => t.id === editForm.value.id)
    if (index !== -1) transactions.value[index] = response.data.data
    isEditModalOpen.value = false
    showNotification('Transaksi berhasil diperbarui', 'success')
  } catch (error) {
    showNotification('Gagal memperbarui transaksi', 'error')
  } finally { isSaving.value = false }
}

const confirmDelete = (trx) => {
  trxToDelete.value = trx
  isDeleteModalOpen.value = true
}

const deleteTransaksi = async () => {
  isSaving.value = true
  try {
    await axios.delete(`${API_URL}/${trxToDelete.value.id}`)
    transactions.value = transactions.value.filter(t => t.id !== trxToDelete.value.id)
    if(paginatedTransactions.value.length === 0 && currentPage.value > 1) currentPage.value--
    isDeleteModalOpen.value = false
    showNotification('Data dihapus', 'success')
  } catch (error) { showNotification('Gagal menghapus', 'error') } finally { isSaving.value = false }
}

// --- Helpers ---
const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
const formatDate = (dateString) => new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(dateString))
const getTipeClass = (tipe) => tipe === 'Uang Masuk' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'
const showNotification = (message, type) => {
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