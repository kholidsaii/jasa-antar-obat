<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Database Customer</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola data pasien penerima layanan antar obat.</p>
      </div>
      <div class="relative w-full sm:w-72">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input v-model="searchQuery" type="text" placeholder="Cari nama atau telepon..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-[#3b5998] sm:text-sm outline-none">
      </div>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all', notification.type === 'success' ? 'bg-green-500' : (notification.type === 'warning' ? 'bg-yellow-500' : 'bg-red-500')]">
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
            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Alamat & Detail Patokan</th>
            <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        
        <tbody v-if="filteredCustomers.length === 0" class="bg-white">
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-gray-500 font-medium">Tidak ada data customer yang ditemukan.</td>
          </tr>
        </tbody>
        <tbody v-else class="bg-white divide-y divide-gray-100">
          <tr v-for="(customer, index) in paginatedCustomers" :key="customer.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ startIndex + index + 1 }}</td>
            <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-semibold text-gray-900">{{ customer.nama }}</div></td>
            <td class="px-6 py-4 whitespace-nowrap"><span class="px-2.5 py-1 inline-flex text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ customer.no_telp }}</span></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ customer.jenis_kelamin === 'Laki-laki' ? 'L' : 'P' }} <span v-if="customer.umur">({{ customer.umur }} thn)</span></td>
            <td class="px-6 py-4 text-sm max-w-xs whitespace-normal">
               <p class="text-gray-900 font-medium truncate" :title="customer.alamat">{{ customer.alamat }}</p>
               <p v-if="customer.detail_alamat" class="text-gray-500 text-xs mt-1 italic break-words line-clamp-2" :title="customer.detail_alamat">📍 {{ customer.detail_alamat }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openEditModal(customer)" class="text-[#3b5998] hover:text-blue-900 mr-3" title="Edit Data">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
              </button>
              <button @click="confirmDelete(customer)" class="text-red-500 hover:text-red-700" title="Hapus Data">
                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="filteredCustomers.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
      <span class="text-sm text-gray-500 font-medium">
        Menampilkan {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredCustomers.length) }} dari {{ filteredCustomers.length }} data
      </span>
      <div class="flex space-x-2">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 font-medium transition-colors shadow-sm">Sebelumnya</button>
        <div class="flex items-center px-2 text-sm font-bold text-gray-700">{{ currentPage }} / {{ totalPages }}</div>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 font-medium transition-colors shadow-sm">Selanjutnya</button>
      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-5 border-b border-gray-100">
          <h3 class="text-lg font-bold text-gray-900">Edit Data Customer</h3>
        </div>
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="updateCustomer" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
              <input v-model="editForm.nama" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] outline-none">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telephone</label>
              <input v-model="editForm.no_telp" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                <select v-model="editForm.jenis_kelamin" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white">
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Umur (Tahun)</label>
                <input v-model="editForm.umur" type="number" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none">
              </div>
            </div>

            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-2">
              <label class="block text-sm font-bold text-blue-900 mb-1">1. Alamat Jalan Utama (Untuk Pelacakan Peta)</label>
              <textarea v-model="editForm.alamat" rows="2" required class="w-full border border-blue-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
              <label class="block text-sm font-bold text-gray-700 mb-1">2. Detail Patokan & RT/RW (Khusus Panduan Kurir)</label>
              <textarea v-model="editForm.detail_alamat" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-gray-400 outline-none"></textarea>
            </div>
          </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="closeEditModal" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
          <button @click="updateCustomer" :disabled="isSaving" type="button" class="px-5 py-2.5 bg-[#3b5998] rounded-lg text-white hover:bg-blue-800 disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Update Data' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="isDeleteModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-xl font-bold text-center mb-4">Hapus Customer</h3>
        <div class="flex justify-end space-x-3 mt-6">
          <button @click="isDeleteModalOpen = false" class="px-5 py-2.5 bg-gray-100 rounded-lg">Batal</button>
          <button @click="deleteCustomer" class="px-5 py-2.5 bg-red-600 text-white rounded-lg">Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api/v1/customers' 

const customers = ref([])
const isSaving = ref(false)
const searchQuery = ref('')
const notification = ref({ show: false, message: '', type: 'success' })

// --- State Pagination ---
const currentPage = ref(1)
const itemsPerPage = 5 

const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  const query = searchQuery.value.toLowerCase()
  return customers.value.filter(customer => customer.nama.toLowerCase().includes(query) || customer.no_telp.includes(query))
})

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredCustomers.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)
const paginatedCustomers = computed(() => filteredCustomers.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
// ------------------------

const isEditModalOpen = ref(false)
const editForm = ref({ id: null, nama: '', no_telp: '', jenis_kelamin: '', umur: null, alamat: '', detail_alamat: '', lat: null, lng: null })

const isDeleteModalOpen = ref(false)
const customerToDelete = ref(null)


const getCoordinatesFromAddress = async (alamatUtama) => {
  try {
    const query = encodeURIComponent(`${alamatUtama}, Jakarta, Indonesia`);
    const response = await axios.get(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1`);
    if (response.data && response.data.length > 0) return { lat: response.data[0].lat, lng: response.data[0].lon }
    return null;
  } catch (error) { return null; }
}

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => notification.value.show = false, 4000)
}

const fetchCustomers = async () => {
  try {
    const response = await axios.get(API_URL)
    customers.value = response.data.data 
  } catch (error) { showNotification('Gagal mengambil data server', 'error') }
}

const openEditModal = (customer) => {
  editForm.value = { ...customer }
  isEditModalOpen.value = true
}
const closeEditModal = () => { isEditModalOpen.value = false }

const updateCustomer = async () => {
  if (!editForm.value.nama || !editForm.value.alamat) return alert("Nama dan Alamat Utama wajib diisi!");
  isSaving.value = true
  try {
    const coords = await getCoordinatesFromAddress(editForm.value.alamat);
    if (coords) { editForm.value.lat = coords.lat; editForm.value.lng = coords.lng; } 
    else { showNotification('Peta gagal melacak. Coba sederhanakan Alamat Utama.', 'warning') }

    const response = await axios.put(`${API_URL}/${editForm.value.id}`, editForm.value)
    const index = customers.value.findIndex(c => c.id === editForm.value.id)
    if (index !== -1) customers.value[index] = response.data.data
    
    closeEditModal()
    if(coords) showNotification('Data berhasil diupdate!', 'success')
  } catch (error) { showNotification('Gagal mengupdate data', 'error') } finally { isSaving.value = false }
}

const confirmDelete = (customer) => {
  customerToDelete.value = customer
  isDeleteModalOpen.value = true
}
const deleteCustomer = async () => {
  if (!customerToDelete.value) return
  isSaving.value = true
  try {
    await axios.delete(`${API_URL}/${customerToDelete.value.id}`)
    customers.value = customers.value.filter(c => c.id !== customerToDelete.value.id)
    if (paginatedCustomers.value.length === 0 && currentPage.value > 1) currentPage.value--
    isDeleteModalOpen.value = false
    showNotification('Data dihapus!', 'success')
  } catch (error) { showNotification('Gagal dihapus', 'error') } finally { isSaving.value = false }
}

onMounted(() => fetchCustomers())
</script>