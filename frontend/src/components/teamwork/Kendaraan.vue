<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center bg-gray-50/30 gap-4">
      <h2 class="text-lg font-bold text-gray-800 w-full sm:w-auto">Manajemen Armada Kendaraan</h2>
      
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <input v-model="searchQuery" type="text" placeholder="Cari armada, plat, atau kurir..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
        </div>
        <div class="flex space-x-2 w-full sm:w-auto justify-end">
          <button @click="fetchVehicles" class="text-sm bg-white border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-50 transition shadow-sm">Refresh</button>
          <button @click="openAddModal" class="text-sm bg-[#3b5998] text-white px-3 py-2 rounded-lg hover:bg-blue-800 transition shadow-sm whitespace-nowrap">+ Tambah Kendaraan</button>
        </div>
      </div>
    </div>
    
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pemilik / Penanggung Jawab</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kendaraan</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Plat Nomor</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kepemilikan</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="vehicle in paginatedVehicles" :key="vehicle.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#3b5998]">{{ vehicle.user?.name || 'Inventaris Bebas' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ vehicle.nama_kendaraan }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono font-bold">{{ vehicle.plat_nomor }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ vehicle.jenis_kepemilikan }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="{
                'bg-green-100 text-green-800 border-green-200': vehicle.status === 'Tersedia',
                'bg-yellow-100 text-yellow-800 border-yellow-200': vehicle.status === 'Sedang Digunakan',
                'bg-red-100 text-red-800 border-red-200': vehicle.status === 'Perbaikan'
              }" class="px-2.5 py-1 text-xs rounded-full font-semibold border">{{ vehicle.status }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm flex justify-center space-x-2">
              <button @click="openEditModal(vehicle)" class="bg-blue-50 hover:bg-blue-100 text-blue-600 p-1.5 rounded-lg border border-blue-200 transition-colors" title="Edit Armada">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              </button>
              <button @click="confirmDelete(vehicle)" class="bg-red-50 hover:bg-red-100 text-red-500 p-1.5 rounded-lg border border-red-200 transition-colors" title="Hapus Armada">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
          
          <tr v-if="filteredVehicles.length === 0">
             <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
               {{ searchQuery ? `Tidak ada armada yang cocok dengan "${searchQuery}".` : 'Belum ada data armada kendaraan.' }}
             </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="filteredVehicles.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
      <span class="text-sm text-gray-500 font-medium">
        Menampilkan {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredVehicles.length) }} dari {{ filteredVehicles.length }} data
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

    <div v-if="isModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Input Data Kendaraan</h3>
        </div>
        <div class="p-6">
          <form @submit.prevent="saveVehicle" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Merek & Nama Kendaraan</label>
              <input v-model="form.nama_kendaraan" type="text" required placeholder="Contoh: Honda Vario 125" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Plat Nomor</label>
              <input v-model="form.plat_nomor" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998] uppercase">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kepemilikan</label>
                <select v-model="form.jenis_kepemilikan" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none">
                  <option value="Kantor">Inventaris Kantor</option>
                  <option value="Pribadi">Pribadi / Kurir</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tetapkan Ke Kurir?</label>
                <select v-model="form.user_id" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none">
                  <option value="">-- Bebas --</option>
                  <option v-for="usr in apiUsers" :key="usr.id" :value="usr.id">{{ usr.name }}</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="isModalOpen = false" class="px-5 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700">Batal</button>
          <button @click="saveVehicle" :disabled="isSaving" class="px-5 py-2 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Simpan Armada' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isEditModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Update Data Kendaraan</h3>
        </div>
        <div class="p-6">
          <form @submit.prevent="updateVehicle" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Merek & Nama Kendaraan</label>
              <input v-model="formEdit.nama_kendaraan" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Plat Nomor</label>
              <input v-model="formEdit.plat_nomor" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998] uppercase">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kepemilikan</label>
                <select v-model="formEdit.jenis_kepemilikan" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none">
                  <option value="Kantor">Inventaris Kantor</option>
                  <option value="Pribadi">Pribadi / Kurir</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tetapkan Ke Kurir?</label>
                <select v-model="formEdit.user_id" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none">
                  <option value="">-- Bebas --</option>
                  <option v-for="usr in apiUsers" :key="usr.id" :value="usr.id">{{ usr.name }}</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status Kendaraan</label>
              <select v-model="formEdit.status" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none">
                <option value="Tersedia">Tersedia (Siap Digunakan)</option>
                <option value="Sedang Digunakan">Sedang Digunakan</option>
                <option value="Perbaikan">Perbaikan (Bengkel)</option>
              </select>
            </div>
          </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="isEditModalOpen = false" class="px-5 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700">Batal</button>
          <button @click="updateVehicle" :disabled="isSaving" class="px-5 py-2 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Update Armada' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isDeleteModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden">
        <div class="p-6 text-center">
          <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Armada Kendaraan</h3>
          <p class="text-sm text-gray-500">Yakin ingin menghapus <strong>{{ vehicleToDelete?.nama_kendaraan }} ({{ vehicleToDelete?.plat_nomor }})</strong> dari database armada?</p>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="isDeleteModalOpen = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium">Batal</button>
          <button @click="deleteVehicle" :disabled="isSaving" class="px-5 py-2.5 bg-red-600 rounded-lg text-white font-medium hover:bg-red-700 disabled:opacity-50">
            {{ isSaving ? 'Memproses...' : 'Ya, Hapus' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue' // Tambahkan computed & watch
import axios from 'axios'

const vehicles = ref([])
const apiUsers = ref([])

// --- State Pagination & Search ---
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

const filteredVehicles = computed(() => {
  if (!searchQuery.value) return vehicles.value
  const query = searchQuery.value.toLowerCase()
  return vehicles.value.filter(vehicle => 
    vehicle.nama_kendaraan.toLowerCase().includes(query) ||
    vehicle.plat_nomor.toLowerCase().includes(query) ||
    (vehicle.user?.name || 'Inventaris Bebas').toLowerCase().includes(query)
  )
})

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredVehicles.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)

const paginatedVehicles = computed(() => filteredVehicles.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
// ----------------------------------

// State Add Modal
const isModalOpen = ref(false)
const form = ref({ nama_kendaraan: '', plat_nomor: '', jenis_kepemilikan: 'Kantor', user_id: '' })

// State Edit Modal
const isEditModalOpen = ref(false)
const formEdit = ref({ id: '', nama_kendaraan: '', plat_nomor: '', jenis_kepemilikan: '', user_id: '', status: '' })

// State Delete Modal
const isDeleteModalOpen = ref(false)
const vehicleToDelete = ref(null)

const isSaving = ref(false)

const fetchVehicles = async () => {
  try {
    const response = await axios.get('/vehicles')
    vehicles.value = response.data.data
  } catch (error) { console.error(error) }
}

const loadUsers = async () => {
  if(apiUsers.value.length === 0) {
    try {
      const resUsr = await axios.get('/users')
      apiUsers.value = resUsr.data.data.filter(u => u.role === 'kurir')
    } catch (error) { console.log(error) }
  }
}

const openAddModal = async () => {
  await loadUsers()
  isModalOpen.value = true
}

const saveVehicle = async () => {
  if(!form.value.nama_kendaraan || !form.value.plat_nomor) return alert('Lengkapi data!')
  isSaving.value = true
  try {
    const payload = { ...form.value }
    if (!payload.user_id) delete payload.user_id 
    
    await axios.post('/vehicles', payload)
    isModalOpen.value = false
    form.value = { nama_kendaraan: '', plat_nomor: '', jenis_kepemilikan: 'Kantor', user_id: '' }
    fetchVehicles()
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menyimpan kendaraan (Plat nomor mungkin duplikat)')
  } finally {
    isSaving.value = false
  }
}

const openEditModal = async (vehicle) => {
  await loadUsers()
  formEdit.value = { 
    id: vehicle.id,
    nama_kendaraan: vehicle.nama_kendaraan,
    plat_nomor: vehicle.plat_nomor,
    jenis_kepemilikan: vehicle.jenis_kepemilikan,
    user_id: vehicle.user_id || '',
    status: vehicle.status
  }
  isEditModalOpen.value = true
}

const updateVehicle = async () => {
  if(!formEdit.value.nama_kendaraan || !formEdit.value.plat_nomor) return alert('Lengkapi data!')
  isSaving.value = true
  try {
    const payload = { ...formEdit.value }
    if (!payload.user_id) payload.user_id = null 
    
    await axios.put(`/vehicles/${payload.id}`, payload)
    isEditModalOpen.value = false
    fetchVehicles()
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal mengupdate kendaraan')
  } finally {
    isSaving.value = false
  }
}

const confirmDelete = (vehicle) => {
  if (vehicle.status === 'Sedang Digunakan') {
    return alert('Kendaraan ini sedang berstatus "Sedang Digunakan" oleh kurir. Selesaikan pekerjaan kurir terlebih dahulu sebelum menghapus armada.')
  }
  vehicleToDelete.value = vehicle
  isDeleteModalOpen.value = true
}

const deleteVehicle = async () => {
  if(!vehicleToDelete.value) return
  isSaving.value = true
  try {
    await axios.delete(`/vehicles/${vehicleToDelete.value.id}`)
    
    vehicles.value = vehicles.value.filter(v => v.id !== vehicleToDelete.value.id)
    if (paginatedVehicles.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }

    isDeleteModalOpen.value = false
  } catch (error) {
    alert('Gagal menghapus kendaraan. Pastikan tidak ada data yang terikat.')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => fetchVehicles())
</script>