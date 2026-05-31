<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative min-h-[400px]">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center bg-gray-50/30 gap-4">
      <h2 class="text-lg font-bold text-gray-800 w-full sm:w-auto">Monitoring Pekerjaan (Work Orders)</h2>
      
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <input v-model="searchQuery" type="text" placeholder="Cari ID, Pasien, atau Kurir..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
        </div>
        <button @click="fetchWorks" class="text-sm text-[#3b5998] hover:underline flex items-center font-medium bg-blue-50 px-3 py-2 rounded-lg border border-blue-100 whitespace-nowrap">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh Data
        </button>
      </div>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all', notification.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>
    
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Work ID</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Paket & Pasien</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kurir & Armada</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-if="isLoading">
            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
              <svg class="animate-spin h-8 w-8 text-[#3b5998] mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Memuat rincian penugasan...
            </td>
          </tr>
          
          <tr v-for="work in paginatedWorks" :key="work.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">#WRK-{{ String(work.id).padStart(4, '0') }}</td>
            <td class="px-6 py-4">
              <div class="text-sm font-semibold text-gray-900">{{ work.package?.customer?.nama || 'Pasien Dihapus' }}</div>
              <div class="text-xs text-gray-500 mt-1 max-w-xs truncate">{{ work.package?.deskripsi_pesanan }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-bold text-[#3b5998]">{{ work.courier?.name || 'No Driver' }}</div>
              <div class="text-xs text-gray-500 mt-0.5">{{ work.vehicle?.nama_kendaraan }} ({{ work.vehicle?.plat_nomor || '-' }})</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm flex justify-center space-x-2">
              
              <span v-if="work.package?.status_pengiriman === 'Terkirim'" class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-bold text-xs border border-green-200">
                <i class="fas fa-check-circle mr-1"></i> SELESAI
              </span>
              
              <template v-else>
                <button @click="completeWork(work)" class="bg-green-50 hover:bg-green-100 text-green-600 p-1.5 rounded-lg border border-green-200" title="Tandai Selesai">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
                <button @click="openEditModal(work)" class="bg-blue-50 hover:bg-blue-100 text-blue-600 p-1.5 rounded-lg border border-blue-200" title="Ganti Kurir Darurat">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </button>
                <button @click="deleteWork(work.id)" class="bg-red-50 hover:bg-red-100 text-red-500 p-1.5 rounded-lg border border-red-200" title="Batalkan Pekerjaan">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </template>
            </td>
          </tr>

          <tr v-if="!isLoading && filteredWorks.length === 0">
            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
              {{ searchQuery ? `Tidak ada pekerjaan yang cocok dengan "${searchQuery}".` : 'Belum ada tugas kurir hari ini.' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="filteredWorks.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
      <span class="text-sm text-gray-500 font-medium">
        Menampilkan {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredWorks.length) }} dari {{ filteredWorks.length }} data
      </span>
      <div class="flex space-x-2">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors shadow-sm">
          Sebelumnya
        </button>
        <div class="flex items-center px-2 text-sm font-bold text-gray-700">
          {{ currentPage }} / {{ totalPages }}
        </div>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors shadow-sm">
          Selanjutnya
        </button>
      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="isEditModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Ganti Kurir / Armada</h3>
        </div>
        <div class="p-6">
          <form @submit.prevent="updateWork" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Pindah ke Kurir Baru</label>
              <select v-model="editForm.user_id" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
                <option v-for="usr in allUsers" :key="usr.id" :value="usr.id">{{ usr.name }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-2">Kendaraan penugasan ini akan otomatis berubah mengikuti motor milik kurir baru.</p>
            </div>
          </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="isEditModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium">Batal</button>
          <button @click="updateWork" :disabled="isSaving" class="px-4 py-2 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api/v1/works'
const works = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const notification = ref({ show: false, message: '', type: 'success' })

// --- State Pagination & Search ---
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 5 

const filteredWorks = computed(() => {
  if (!searchQuery.value) return works.value
  const query = searchQuery.value.toLowerCase()
  return works.value.filter(work => {
    const idMatch = String(work.id).padStart(4, '0').includes(query)
    const patientMatch = (work.package?.customer?.nama || '').toLowerCase().includes(query)
    const courierMatch = (work.courier?.name || '').toLowerCase().includes(query)
    const descMatch = (work.package?.deskripsi_pesanan || '').toLowerCase().includes(query)
    return idMatch || patientMatch || courierMatch || descMatch
  })
})

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredWorks.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)

const paginatedWorks = computed(() => filteredWorks.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
// ----------------------------------


// State Dropdown Data
const allUsers = ref([])

// State Modal Edit
const isEditModalOpen = ref(false)
const editForm = ref({ id: null, user_id: '' })

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => notification.value.show = false, 4000)
}

const fetchWorks = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    works.value = response.data.data
  } catch (error) {
    console.error("Gagal mengambil data:", error)
    showNotification("Gagal memuat database tugas", "error")
  } finally {
    isLoading.value = false
  }
}

const completeWork = async (work) => {
  if(!confirm(`Tandai paket untuk ${work.package?.customer?.nama || ''} selesai dikirim?\n\nIni akan merubah status paket menjadi "Terkirim" dan memasukkan Ongkos Kirim Rp ${work.harga_ongkos} ke laporan Finansial.`)) return
  
  try {
    const response = await axios.put(`${API_URL}/${work.id}`, { action: 'complete' })
    const index = works.value.findIndex(w => w.id === work.id)
    if(index !== -1) {
      works.value[index].package.status_pengiriman = 'Terkirim'
    }
    showNotification(response.data.message, 'success')
  } catch (error) {
    showNotification("Gagal menyelesaikan tugas", "error")
  }
}

const fetchDropdownData = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/v1/users')
    allUsers.value = response.data.data
  } catch (error) {
    console.error("Gagal memuat data kurir:", error)
  }
}

const openEditModal = async (work) => {
  if (allUsers.value.length === 0) await fetchDropdownData()
  editForm.value = { id: work.id, user_id: work.user_id }
  isEditModalOpen.value = true
}

const updateWork = async () => {
  isSaving.value = true
  try {
    const response = await axios.put(`${API_URL}/${editForm.value.id}`, editForm.value)
    const index = works.value.findIndex(w => w.id === editForm.value.id)
    if(index !== -1) {
      works.value[index] = response.data.data
    }
    isEditModalOpen.value = false
    showNotification("Berhasil memindahkan kurir/kendaraan!", "success")
  } catch (error) {
    showNotification(error.response?.data?.message || "Gagal memindahkan tugas", "error")
  } finally {
    isSaving.value = false
  }
}

const deleteWork = async (id) => {
  if(!confirm("Yakin ingin membatalkan penugasan kurir ini?\n\nStatus Paket akan dikembalikan menjadi 'Pesanan diverifikasi'.")) return
  try {
    await axios.delete(`${API_URL}/${id}`)
    
    works.value = works.value.filter(w => w.id !== id)
    if (paginatedWorks.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }

    showNotification("Pekerjaan dibatalkan", "success")
  } catch (error) {
    showNotification("Gagal menghapus tugas kerja", "error")
  }
}

onMounted(() => {
  fetchWorks()
})
</script>

<style scoped>
@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-modal-in { animation: modalIn 0.2s ease-out forwards; }
</style>