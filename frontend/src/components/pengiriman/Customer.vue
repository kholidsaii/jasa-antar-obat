<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Database Customer</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola data pasien penerima layanan antar obat.</p>
      </div>
      
      <div class="relative w-full sm:w-72">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Cari nama atau telepon..." 
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm transition duration-150 ease-in-out"
        >
      </div>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white', notification.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pasien</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Telp</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">L/P (Umur)</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Alamat Tujuan</th>
            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        
        <tbody v-if="isLoading" class="bg-white divide-y divide-gray-200">
          <tr v-for="i in 5" :key="i" class="animate-pulse">
            <td class="px-6 py-4 whitespace-nowrap"><div class="h-4 bg-gray-200 rounded w-6"></div></td>
            <td class="px-6 py-4 whitespace-nowrap"><div class="h-4 bg-gray-200 rounded w-32"></div></td>
            <td class="px-6 py-4 whitespace-nowrap"><div class="h-4 bg-gray-200 rounded w-24"></div></td>
            <td class="px-6 py-4 whitespace-nowrap"><div class="h-4 bg-gray-200 rounded w-16"></div></td>
            <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-full"></div></td>
            <td class="px-6 py-4 whitespace-nowrap text-right"><div class="h-4 bg-gray-200 rounded w-12 ml-auto"></div></td>
          </tr>
        </tbody>

        <tbody v-else-if="filteredCustomers.length === 0" class="bg-white">
          <tr>
            <td colspan="6" class="px-6 py-12 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              <p class="text-gray-500 font-medium">Tidak ada data customer yang ditemukan.</p>
            </td>
          </tr>
        </tbody>

        <tbody v-else class="bg-white divide-y divide-gray-100">
          <tr v-for="(customer, index) in filteredCustomers" :key="customer.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ index + 1 }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-semibold text-gray-900">{{ customer.nama }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800">
                {{ customer.no_telp }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ customer.jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }} <span v-if="customer.umur">({{ customer.umur }} thn)</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" :title="customer.alamat">
              {{ customer.alamat }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openEditModal(customer)" class="text-[#3b5998] hover:text-blue-900 mr-3 transition-colors" title="Edit Data">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
              <button @click="confirmDelete(customer)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Data">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
      <p class="text-sm text-gray-500">
        Menampilkan <span class="font-medium">{{ filteredCustomers.length }}</span> dari <span class="font-medium">{{ customers.length }}</span> total customer.
      </p>
      <button @click="fetchCustomers" class="text-sm text-[#3b5998] hover:underline flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Refresh Data
      </button>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeEditModal" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4 border-b pb-2">Edit Data Customer</h3>
            <form @submit.prevent="updateCustomer" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input v-model="editForm.nama" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">No. Telephone</label>
                <input v-model="editForm.no_telp" type="text" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm">
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                  <select v-model="editForm.jenis_kelamin" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm">
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Umur</label>
                  <input v-model="editForm.umur" type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm">
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                <textarea v-model="editForm.alamat" rows="3" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm"></textarea>
              </div>
            </form>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="updateCustomer" :disabled="isSaving" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#3b5998] text-base font-medium text-white hover:bg-blue-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
              {{ isSaving ? 'Menyimpan...' : 'Update Data' }}
            </button>
            <button @click="closeEditModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="isDeleteModalOpen = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Customer</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus data <strong>{{ customerToDelete?.nama }}</strong>? Data yang dihapus tidak dapat dikembalikan.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="deleteCustomer" :disabled="isSaving" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
              {{ isSaving ? 'Menghapus...' : 'Ya, Hapus' }}
            </button>
            <button @click="isDeleteModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Batal
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// --- CONFIGURATION ---
// Sesuaikan dengan port backend Laravel Anda
const API_URL = 'http://localhost:8000/api/v1/customers' 

// --- STATE ---
const customers = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const searchQuery = ref('')
const notification = ref({ show: false, message: '', type: 'success' })

// State Modal Edit
const isEditModalOpen = ref(false)
const editForm = ref({ id: null, nama: '', no_telp: '', jenis_kelamin: '', umur: null, alamat: '' })

// State Modal Delete
const isDeleteModalOpen = ref(false)
const customerToDelete = ref(null)

// --- COMPUTED PROPERTIES ---
const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  const lowerCaseQuery = searchQuery.value.toLowerCase()
  return customers.value.filter(customer => 
    customer.nama.toLowerCase().includes(lowerCaseQuery) ||
    customer.no_telp.includes(lowerCaseQuery)
  )
})

// --- METHODS: API CALLS ---

// 1. Ambil Data Customer (GET)
const fetchCustomers = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    // Response dari resource controller Laravel biasanya membungkus data di .data.data atau .data (sesuai format controller Anda)
    customers.value = response.data.data 
  } catch (error) {
    console.error('Error fetching customers:', error)
    showNotification('Gagal mengambil data dari server', 'error')
  } finally {
    isLoading.value = false
  }
}

// 2. Persiapan Edit Data
const openEditModal = (customer) => {
  editForm.value = { ...customer } // Clone objek agar tidak merubah data asli sebelum disave
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
  editForm.value = { id: null, nama: '', no_telp: '', jenis_kelamin: '', umur: null, alamat: '' }
}

// 3. Update Data (PUT)
const updateCustomer = async () => {
  if (!editForm.value.nama || !editForm.value.alamat) return
  
  isSaving.value = true
  try {
    await axios.put(`${API_URL}/${editForm.value.id}`, editForm.value)
    
    // Update local state agar tidak perlu fetch ulang semua data
    const index = customers.value.findIndex(c => c.id === editForm.value.id)
    if (index !== -1) {
      customers.value[index] = { ...editForm.value }
    }
    
    closeEditModal()
    showNotification('Data customer berhasil diupdate!', 'success')
  } catch (error) {
    console.error('Error updating customer:', error)
    showNotification('Gagal mengupdate data', 'error')
  } finally {
    isSaving.value = false
  }
}

// 4. Persiapan Hapus Data
const confirmDelete = (customer) => {
  customerToDelete.value = customer
  isDeleteModalOpen.value = true
}

// 5. Eksekusi Hapus (DELETE)
const deleteCustomer = async () => {
  if (!customerToDelete.value) return
  
  isSaving.value = true
  try {
    await axios.delete(`${API_URL}/${customerToDelete.value.id}`)
    
    // Hapus dari local state
    customers.value = customers.value.filter(c => c.id !== customerToDelete.value.id)
    
    isDeleteModalOpen.value = false
    customerToDelete.value = null
    showNotification('Data customer berhasil dihapus!', 'success')
  } catch (error) {
    console.error('Error deleting customer:', error)
    showNotification('Gagal menghapus data', 'error')
  } finally {
    isSaving.value = false
  }
}

// Helper: Tampilkan Notifikasi Toast
const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => {
    notification.value.show = false
  }, 3000) // Hilang otomatis setelah 3 detik
}

// --- LIFECYCLE HOOKS ---
onMounted(() => {
  fetchCustomers()
})
</script>