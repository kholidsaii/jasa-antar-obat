<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative min-h-[400px]">
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center bg-gray-50/30 gap-4">
      <div>
        <h2 class="text-lg font-bold text-gray-800">Database Semua Karyawan</h2>
        <p class="text-sm text-gray-500 mt-0.5">Pantau status, jabatan, dan beban kerja.</p>
      </div>
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <input v-model="searchQuery" type="text" placeholder="Cari nama, email, atau role..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#3b5998]">
        </div>
        <button @click="fetchUsers" :disabled="isLoading" class="text-sm text-[#3b5998] hover:underline flex items-center font-medium whitespace-nowrap">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh Data
        </button>
      </div>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all', notification.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-6">
      <div v-if="isLoading" class="col-span-full text-center py-10 text-gray-400">
        <svg class="animate-spin h-8 w-8 text-[#3b5998] mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        Memuat database karyawan...
      </div>
      
      <div v-else-if="filteredUsers.length === 0" class="col-span-full text-center py-10 text-gray-500">
        Tidak ada data karyawan yang cocok dengan pencarian.
      </div>

      <div v-for="user in paginatedUsers" :key="user.id" class="relative border border-gray-200 rounded-xl p-5 flex flex-col bg-white hover:border-[#3b5998] hover:shadow-md transition-all group">
        
        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-2 bg-white/90 p-1 rounded-lg shadow-sm">
          <button @click="openInfoModal(user)" class="text-green-600 hover:text-green-800 bg-green-50 p-1.5 rounded" title="Detail Riwayat">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
          </button>
          <button @click="openEditModal(user)" class="text-blue-500 hover:text-blue-700 bg-blue-50 p-1.5 rounded" title="Edit Karyawan">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          </button>
          <button @click="confirmDelete(user)" class="text-red-500 hover:text-red-700 bg-red-50 p-1.5 rounded" title="Hapus Karyawan">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          </button>
        </div>

        <div class="flex items-center space-x-4 mb-4">
          <img :src="user.foto ? 'http://localhost:8000/storage/' + user.foto : `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random&color=fff`" 
               class="w-14 h-14 rounded-full border-2 border-gray-100 shadow-sm object-cover" alt="Avatar">
          <div class="flex-1 min-w-0">
            <h4 class="font-bold text-gray-900 truncate text-base">{{ user.name }}</h4>
            <p class="text-xs text-gray-500 truncate mt-0.5"><i class="fas fa-envelope mr-1"></i>{{ user.email }}</p>
            <p v-if="user.no_telepon" class="text-xs text-gray-500 truncate mt-0.5 font-mono"><i class="fas fa-phone mr-1"></i>{{ user.no_telepon }}</p>
            <span class="inline-block mt-1.5 px-2 py-0.5 text-[10px] font-bold uppercase rounded-md border"
              :class="{
                'bg-blue-50 text-blue-700 border-blue-200': user.role === 'kurir',
                'bg-purple-50 text-purple-700 border-purple-200': user.role === 'admin',
                'bg-green-50 text-green-700 border-green-200': user.role === 'farmasi',
                'bg-red-50 text-red-700 border-red-200': user.role === 'superadmin'
              }">
              {{ user.role }}
            </span>
          </div>
        </div>

        <div class="mt-auto border-t border-gray-100 pt-3">
          <template v-if="user.role === 'kurir'">
            <div v-if="user.works_count > 0" class="flex items-center justify-between bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-100">
              <span class="text-xs font-semibold text-yellow-800 flex items-center">
                <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2 animate-pulse"></span> Sedang Bertugas
              </span>
              <span class="text-xs font-bold text-yellow-900">{{ user.works_count }} Paket</span>
            </div>
            <div v-else class="flex items-center bg-green-50 px-3 py-2 rounded-lg border border-green-100">
              <span class="text-xs font-semibold text-green-800 flex items-center">
                <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Standby (Kosong)
              </span>
            </div>
          </template>
          <template v-else>
            <div class="flex items-center px-3 py-2 rounded-lg border" :class="user.is_online ? 'bg-green-50 border-green-100' : 'bg-gray-50 border-gray-200'">
              <span class="text-xs font-semibold flex items-center" :class="user.is_online ? 'text-green-800' : 'text-gray-500'">
                <span class="w-2 h-2 rounded-full mr-2" :class="user.is_online ? 'bg-green-500' : 'bg-gray-400'"></span>
                {{ user.is_online ? 'Sedang Online' : 'Offline' }}
              </span>
            </div>
          </template>
        </div>
      </div>
    </div>

    <div v-if="filteredUsers.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
      <span class="text-sm text-gray-500 font-medium">Menampilkan {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredUsers.length) }} dari {{ filteredUsers.length }} data</span>
      <div class="flex space-x-2">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 transition-colors shadow-sm">Sebelumnya</button>
        <div class="flex items-center px-2 text-sm font-bold text-gray-700">{{ currentPage }} / {{ totalPages }}</div>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 transition-colors shadow-sm">Selanjutnya</button>
      </div>
    </div>

    <div v-if="isInfoModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isInfoModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm mx-auto overflow-hidden">
        
        <div class="bg-gradient-to-r from-[#2b4170] to-[#3b5998] p-6 text-center text-white relative">
          <button @click="isInfoModalOpen = false" class="absolute top-4 right-4 text-white/70 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
          
          <img :src="selectedUser?.foto ? 'http://localhost:8000/storage/' + selectedUser.foto : `https://ui-avatars.com/api/?name=${encodeURIComponent(selectedUser?.name)}&background=random&color=fff`" 
               class="w-24 h-24 rounded-full border-4 border-white shadow-md object-cover mx-auto mb-3">
          <h3 class="text-xl font-bold">{{ selectedUser?.name }}</h3>
          <p class="text-blue-200 text-sm font-medium uppercase tracking-widest">{{ selectedUser?.role }}</p>
        </div>

        <div class="p-6 space-y-4 bg-gray-50/50">
          <div class="flex items-center text-sm text-gray-700">
            <i class="fas fa-envelope w-6 text-center text-gray-400"></i>
            <span class="font-medium">{{ selectedUser?.email }}</span>
          </div>
          <div class="flex items-center text-sm text-gray-700">
            <i class="fas fa-phone-alt w-6 text-center text-gray-400"></i>
            <span class="font-medium font-mono">{{ selectedUser?.no_telepon || 'Tidak ada No Telp' }}</span>
          </div>
          <div class="flex items-center text-sm text-gray-700">
            <i class="fas fa-circle w-6 text-center" :class="selectedUser?.is_online ? 'text-green-500' : 'text-gray-400'"></i>
            <span class="font-medium">{{ selectedUser?.is_online ? 'Sedang Online' : 'Sedang Offline' }}</span>
          </div>
        </div>

        <div v-if="selectedUser?.role === 'kurir'" class="px-6 pb-6 pt-2 bg-gray-50/50">
          <div class="border-t border-gray-200 pt-4 mt-2">
            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 text-center">Data Kinerja Kurir</h4>
            <div class="grid grid-cols-2 gap-3">
              <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-3 text-center">
                <p class="text-2xl font-black text-yellow-600">{{ selectedUser?.works_count || 0 }}</p>
                <p class="text-[10px] font-bold text-yellow-800 uppercase mt-1">Paket Aktif</p>
              </div>
              <div class="bg-green-50 border border-green-100 rounded-lg p-3 text-center">
                <p class="text-2xl font-black text-green-600">{{ selectedUser?.history_count || 0 }}</p>
                <p class="text-[10px] font-bold text-green-800 uppercase mt-1">Total Selesai</p>
              </div>
            </div>
            <p class="text-[10px] text-gray-400 text-center mt-3">Total Selesai mencakup seluruh paket yang telah diantarkan semenjak kurir bekerja.</p>
          </div>
        </div>

      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeEditModal"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Update Data Karyawan</h3>
          <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[70vh]">
          <form @submit.prevent="updateUser" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <input v-model="formEdit.name" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon</label>
                <input v-model="formEdit.no_telepon" type="text" placeholder="0812xxxxxx" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
              <input v-model="formEdit.email" type="email" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Role / Jabatan</label>
                <select v-model="formEdit.role" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-[#3b5998]">
                  <option value="kurir">Kurir / Driver</option>
                  <option value="farmasi">Farmasi / Apoteker</option>
                  <option value="admin">Admin Operasional</option>
                  <option value="superadmin">Super Administrator</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Reset Password (Opsional)</label>
                <input v-model="formEdit.password" type="password" placeholder="Kosongkan jika tidak diubah" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ganti Foto Profil (Opsional)</label>
              <div 
                @dragover.prevent="isDraggingEdit = true" 
                @dragleave.prevent="isDraggingEdit = false" 
                @drop.prevent="handleDropEdit" 
                @click="$refs.fileInputEdit.click()"
                :class="[
                  'border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center min-h-[120px]', 
                  isDraggingEdit ? 'border-[#3b5998] bg-blue-50' : 'border-gray-300 bg-white hover:bg-gray-50'
                ]"
              >
                <input type="file" accept="image/*" class="hidden" ref="fileInputEdit" @change="handleFileSelectEdit">
                
                <div v-if="previewEditFoto" class="relative">
                  <img :src="previewEditFoto" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md mx-auto">
                  <span class="text-xs text-[#3b5998] hover:underline mt-2 block font-medium">Klik atau drop untuk ganti</span>
                </div>
                
                <div v-else>
                  <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                  <p class="text-sm text-gray-500"><span class="font-semibold text-[#3b5998]">Klik untuk upload</span> atau drag and drop</p>
                  <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG</p>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeEditModal" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-gray-700">Batal</button>
          <button @click="updateUser" :disabled="isUpdating" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
            {{ isUpdating ? 'Memproses...' : 'Update Data' }}
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
          <h3 class="text-xl font-bold text-gray-900 mb-2">Nonaktifkan Karyawan</h3>
          <p class="text-sm text-gray-500">Yakin menghapus <strong>{{ userToDelete?.name }}</strong>?</p>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="isDeleteModalOpen = false" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium">Batal</button>
          <button @click="deleteUser" :disabled="isSaving" class="px-5 py-2.5 bg-red-600 rounded-lg text-white font-medium hover:bg-red-700 disabled:opacity-50">
            {{ isSaving ? 'Memproses...' : 'Ya, Hapus' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'

const users = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const isUpdating = ref(false)
const notification = ref({ show: false, message: '', type: 'success' })

const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 6 

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query) ||
    user.role.toLowerCase().includes(query)
  )
})

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredUsers.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)
const paginatedUsers = computed(() => filteredUsers.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

const isDeleteModalOpen = ref(false)
const userToDelete = ref(null)

const isInfoModalOpen = ref(false)
const selectedUser = ref(null)

const isEditModalOpen = ref(false)
const formEdit = ref({ id: '', name: '', email: '', role: '', no_telepon: '', password: '', foto: null })

const isDraggingEdit = ref(false)
const previewEditFoto = ref(null)
const fileInputEdit = ref(null)

const handleDropEdit = (e) => {
  isDraggingEdit.value = false
  const file = e.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    formEdit.value.foto = file
    previewEditFoto.value = URL.createObjectURL(file) 
  }
}

const handleFileSelectEdit = (e) => {
  const file = e.target.files[0]
  if (file) {
    formEdit.value.foto = file
    previewEditFoto.value = URL.createObjectURL(file) 
  }
}

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => notification.value.show = false, 4000)
}

const fetchUsers = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/users')
    users.value = response.data.data
  } catch (error) {
    showNotification('Gagal memuat database karyawan', 'error')
  } finally { isLoading.value = false }
}

const openInfoModal = (user) => {
  selectedUser.value = user
  isInfoModalOpen.value = true
}

const openEditModal = (user) => {
  formEdit.value = { 
    id: user.id, 
    name: user.name, 
    email: user.email, 
    role: user.role, 
    no_telepon: user.no_telepon || '', 
    password: '', 
    foto: null 
  }
  previewEditFoto.value = user.foto ? 'http://localhost:8000/storage/' + user.foto : null
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
  previewEditFoto.value = null 
}

const updateUser = async () => {
  isUpdating.value = true
  try {
    const formData = new FormData()
    formData.append('name', formEdit.value.name)
    formData.append('email', formEdit.value.email)
    formData.append('role', formEdit.value.role)
    if (formEdit.value.no_telepon) formData.append('no_telepon', formEdit.value.no_telepon)
    if (formEdit.value.password) formData.append('password', formEdit.value.password)
    if (formEdit.value.foto) formData.append('foto', formEdit.value.foto)
    
    formData.append('_method', 'PUT')

    await axios.post(`/users/${formEdit.value.id}`, formData, { 
      headers: { 'Content-Type': 'multipart/form-data' } 
    })
    
    showNotification('Data karyawan berhasil diupdate!', 'success')
    closeEditModal()
    fetchUsers() 
  } catch (error) {
    showNotification(error.response?.data?.message || 'Gagal mengupdate karyawan.', 'error')
  } finally {
    isUpdating.value = false
  }
}

const confirmDelete = (user) => {
  if (user.works_count > 0) return alert("Kurir ini masih memiliki paket yang harus diantar!")
  userToDelete.value = user
  isDeleteModalOpen.value = true
}

const deleteUser = async () => {
  if (!userToDelete.value) return
  isSaving.value = true
  try {
    await axios.delete(`/users/${userToDelete.value.id}`)
    users.value = users.value.filter(u => u.id !== userToDelete.value.id)
    if (paginatedUsers.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }
    isDeleteModalOpen.value = false
    showNotification('Karyawan berhasil dihapus!', 'success')
  } catch (error) {
    showNotification('Gagal menghapus karyawan.', 'error')
  } finally { isSaving.value = false }
}

onMounted(() => fetchUsers())
</script>