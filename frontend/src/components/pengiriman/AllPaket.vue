<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Daftar Semua Paket</h2>
        <p class="text-sm text-gray-500 mt-1">Monitor status pesanan obat dan pengiriman secara real-time.</p>
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
          placeholder="Cari deskripsi obat / nama pasien..." 
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
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ID Paket</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Customer / Pasien</th>
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi Pesanan</th>
            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status Pengiriman</th>
            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Pembayaran</th>
            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        
        <tbody v-if="isLoading" class="bg-white divide-y divide-gray-200">
          <tr v-for="i in 5" :key="i" class="animate-pulse">
            <td class="px-6 py-4 whitespace-nowrap"><div class="h-4 bg-gray-200 rounded w-16"></div></td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="h-4 bg-gray-200 rounded w-32 mb-2"></div>
              <div class="h-3 bg-gray-200 rounded w-24"></div>
            </td>
            <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-48"></div></td>
            <td class="px-6 py-4 text-center"><div class="h-6 bg-gray-200 rounded-full w-24 mx-auto"></div></td>
            <td class="px-6 py-4 text-center"><div class="h-6 bg-gray-200 rounded-full w-20 mx-auto"></div></td>
            <td class="px-6 py-4 whitespace-nowrap text-right"><div class="h-4 bg-gray-200 rounded w-12 ml-auto"></div></td>
          </tr>
        </tbody>

        <tbody v-else-if="filteredPackages.length === 0" class="bg-white">
          <tr>
            <td colspan="6" class="px-6 py-12 text-center">
              <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
              </svg>
              <p class="text-gray-500 font-medium">Tidak ada data paket yang ditemukan.</p>
            </td>
          </tr>
        </tbody>

        <tbody v-else class="bg-white divide-y divide-gray-100">
          <tr v-for="pkg in filteredPackages" :key="pkg.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
              #PKT-{{ String(pkg.id).padStart(4, '0') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-semibold text-gray-900">{{ pkg.customer?.nama || 'Customer Dihapus' }}</div>
              <div class="text-xs text-gray-500 mt-0.5"><i class="fas fa-phone-alt mr-1"></i> {{ pkg.customer?.no_telp || '-' }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" :title="pkg.deskripsi_pesanan">
              {{ pkg.deskripsi_pesanan }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="getStatusPengirimanClass(pkg.status_pengiriman)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border">
                {{ pkg.status_pengiriman }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="getStatusPembayaranClass(pkg.status_pembayaran)" class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border">
                {{ pkg.status_pembayaran }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openEditModal(pkg)" class="text-[#3b5998] hover:text-blue-900 mr-3 transition-colors" title="Update Status">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              </button>
              <button @click="confirmDelete(pkg)" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Paket">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
      <p class="text-sm text-gray-500">
        Menampilkan <span class="font-medium">{{ filteredPackages.length }}</span> dari <span class="font-medium">{{ packages.length }}</span> total paket.
      </p>
      <button @click="fetchPackages" class="text-sm text-[#3b5998] hover:underline flex items-center">
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
            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-1" id="modal-title">Update Status Paket</h3>
            <p class="text-sm text-gray-500 mb-4 border-b pb-3">#PKT-{{ String(editForm.id).padStart(4, '0') }} - {{ editForm.customer?.nama }}</p>
            
            <form @submit.prevent="updatePackage" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Deskripsi Obat / Pesanan</label>
                <textarea v-model="editForm.deskripsi_pesanan" rows="2" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm"></textarea>
              </div>
              
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Status Pengiriman</label>
                  <select v-model="editForm.status_pengiriman" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm">
                    <option value="Pesanan diverifikasi">Pesanan diverifikasi</option>
                    <option value="Pengemasan">Pengemasan</option>
                    <option value="Menunggu Driver">Menunggu Driver</option>
                    <option value="Diperjalanan">Diperjalanan</option>
                    <option value="Terkirim">Terkirim</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                  <select v-model="editForm.status_pembayaran" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm">
                    <option value="Belum Lunas">Belum Lunas</option>
                    <option value="Lunas">Lunas</option>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="updatePackage" :disabled="isSaving" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#3b5998] text-base font-medium text-white hover:bg-blue-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
              {{ isSaving ? 'Menyimpan...' : 'Update Status' }}
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
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Batalkan / Hapus Paket</h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Anda yakin ingin menghapus <strong>#PKT-{{ packageToDelete?.id ? String(packageToDelete.id).padStart(4, '0') : '' }}</strong>? Data yang dihapus tidak akan masuk ke laporan operasional.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="deletePackage" :disabled="isSaving" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
              {{ isSaving ? 'Menghapus...' : 'Ya, Hapus' }}
            </button>
            <button @click="isDeleteModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Tutup
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
const API_URL = 'http://localhost:8000/api/v1/packages' 

// --- STATE ---
const packages = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const searchQuery = ref('')
const notification = ref({ show: false, message: '', type: 'success' })

// State Modal Edit
const isEditModalOpen = ref(false)
const editForm = ref({ 
  id: null, 
  customer_id: null, 
  deskripsi_pesanan: '', 
  status_pengiriman: '', 
  status_pembayaran: '',
  customer: null // Info tambahan untuk display di modal
})

// State Modal Delete
const isDeleteModalOpen = ref(false)
const packageToDelete = ref(null)

// --- COMPUTED PROPERTIES ---
// Filter pencarian berdasarkan nama pasien atau deskripsi obat
const filteredPackages = computed(() => {
  if (!searchQuery.value) return packages.value
  const lowerCaseQuery = searchQuery.value.toLowerCase()
  return packages.value.filter(pkg => {
    const customerName = pkg.customer?.nama?.toLowerCase() || ''
    const desc = pkg.deskripsi_pesanan?.toLowerCase() || ''
    return customerName.includes(lowerCaseQuery) || desc.includes(lowerCaseQuery)
  })
})

// --- METHODS: API CALLS ---

// 1. Fetch Data Packages (GET)
const fetchPackages = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    // Karena kita pakai resource controller Laravel: response.data.data
    packages.value = response.data.data 
  } catch (error) {
    console.error('Error fetching packages:', error)
    showNotification('Gagal mengambil data paket dari server', 'error')
  } finally {
    isLoading.value = false
  }
}

// 2. Persiapan Edit
const openEditModal = (pkg) => {
  editForm.value = { ...pkg }
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
  editForm.value = { id: null, customer_id: null, deskripsi_pesanan: '', status_pengiriman: '', status_pembayaran: '', customer: null }
}

// 3. Eksekusi Update Status (PUT)
const updatePackage = async () => {
  if (!editForm.value.deskripsi_pesanan) return
  
  isSaving.value = true
  try {
    // Kita hanya mengirim field yang diperlukan untuk update (mengikuti validasi controller)
    const payload = {
      customer_id: editForm.value.customer_id,
      deskripsi_pesanan: editForm.value.deskripsi_pesanan,
      status_pengiriman: editForm.value.status_pengiriman,
      status_pembayaran: editForm.value.status_pembayaran
    }

    const response = await axios.put(`${API_URL}/${editForm.value.id}`, payload)
    
    // Update local state dengan data yang di-return API
    const updatedPkg = response.data.data
    const index = packages.value.findIndex(p => p.id === updatedPkg.id)
    if (index !== -1) {
      packages.value[index] = updatedPkg
    }
    
    closeEditModal()
    showNotification('Status Paket berhasil diupdate!', 'success')
  } catch (error) {
    console.error('Error updating package:', error)
    showNotification('Gagal mengupdate paket', 'error')
  } finally {
    isSaving.value = false
  }
}

// 4. Persiapan Delete
const confirmDelete = (pkg) => {
  packageToDelete.value = pkg
  isDeleteModalOpen.value = true
}

// 5. Eksekusi Delete (DELETE)
const deletePackage = async () => {
  if (!packageToDelete.value) return
  
  isSaving.value = true
  try {
    await axios.delete(`${API_URL}/${packageToDelete.value.id}`)
    
    // Hapus dari local state
    packages.value = packages.value.filter(p => p.id !== packageToDelete.value.id)
    
    isDeleteModalOpen.value = false
    packageToDelete.value = null
    showNotification('Paket berhasil dibatalkan/dihapus!', 'success')
  } catch (error) {
    console.error('Error deleting package:', error)
    showNotification('Gagal menghapus paket', 'error')
  } finally {
    isSaving.value = false
  }
}

// --- UI HELPERS ---

// Memberikan warna dinamis berdasarkan status pengiriman
const getStatusPengirimanClass = (status) => {
  switch(status) {
    case 'Pesanan diverifikasi': return 'bg-gray-100 text-gray-600 border-gray-200'
    case 'Pengemasan': return 'bg-yellow-50 text-yellow-700 border-yellow-200'
    case 'Menunggu Driver': return 'bg-orange-50 text-orange-700 border-orange-200'
    case 'Diperjalanan': return 'bg-blue-50 text-blue-700 border-blue-200'
    case 'Terkirim': return 'bg-green-50 text-green-700 border-green-200'
    default: return 'bg-gray-100 text-gray-800'
  }
}

// Memberikan warna dinamis berdasarkan status pembayaran
const getStatusPembayaranClass = (status) => {
  if (status === 'Lunas') {
    return 'bg-green-100 text-green-800 border-green-200'
  }
  return 'bg-red-50 text-red-700 border-red-200'
}

// Notifikasi Toast
const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => {
    notification.value.show = false
  }, 3000)
}

// --- LIFECYCLE ---
// Saat komponen pertama kali dimuat di layar, langsung tembak API untuk ambil data.
onMounted(() => {
  fetchPackages()
})
</script>