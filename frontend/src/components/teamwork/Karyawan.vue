<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative min-h-[400px]">
    
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
      <div>
        <h2 class="text-lg font-bold text-gray-800">Database Kurir Operasional</h2>
        <p class="text-sm text-gray-500 mt-0.5">Pantau status *standby* dan beban kerja masing-masing kurir.</p>
      </div>
      <button @click="fetchUsers" :disabled="isLoading" class="text-sm text-[#3b5998] hover:underline flex items-center font-medium">
        <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Refresh Data
      </button>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all', notification.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-6">
      <div v-if="isLoading" class="col-span-full text-center py-10 text-gray-400">
        <svg class="animate-spin h-8 w-8 text-[#3b5998] mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        Memuat database karyawan...
      </div>
      <div v-else-if="users.length === 0" class="col-span-full text-center py-10 text-gray-500">
        Belum ada kurir terdaftar. Silakan tambahkan melalui tombol "Add Karyawan" di atas.
      </div>
      
      <div v-for="user in users" :key="user.id" class="relative border border-gray-200 rounded-xl p-5 flex flex-col bg-white hover:border-[#3b5998] hover:shadow-md transition-all group">
        
        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-2 bg-white/90 p-1 rounded-lg">
          <button @click="openEditModal(user)" class="text-[#3b5998] hover:text-blue-900 bg-blue-50 p-1.5 rounded" title="Edit Karyawan">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          </button>
          <button @click="confirmDelete(user)" class="text-red-500 hover:text-red-700 bg-red-50 p-1.5 rounded" title="Nonaktifkan / Hapus">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          </button>
        </div>

        <div class="flex items-center space-x-4 mb-4">
          <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random&color=fff`" class="w-14 h-14 rounded-full border-2 border-gray-100 shadow-sm" alt="Avatar">
          <div class="flex-1 min-w-0">
            <h4 class="font-bold text-gray-900 truncate text-base">{{ user.name }}</h4>
            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ user.email }}</p>
          </div>
        </div>

        <div class="mt-auto border-t border-gray-100 pt-3">
          <div v-if="user.works_count > 0" class="flex items-center justify-between bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-100">
            <span class="text-xs font-semibold text-yellow-800 flex items-center">
              <span class="w-2 h-2 rounded-full bg-yellow-500 mr-2 animate-pulse"></span>
              Sedang Bertugas
            </span>
            <span class="text-xs font-bold text-yellow-900">{{ user.works_count }} Paket</span>
          </div>
          <div v-else class="flex items-center bg-green-50 px-3 py-2 rounded-lg border border-green-100">
            <span class="text-xs font-semibold text-green-800 flex items-center">
              <span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span>
              Standby (Kosong)
            </span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Update Profil Kurir</h3>
        </div>
        
        <div class="p-6">
          <form @submit.prevent="updateUser" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
              <input v-model="editForm.name" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] transition-all">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
              <input v-model="editForm.email" type="email" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] transition-all">
            </div>
            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
              <label class="block text-sm font-semibold text-blue-900 mb-1">Ganti Password (Opsional)</label>
              <input v-model="editForm.password" type="password" placeholder="Kosongkan jika tidak ingin ganti password" class="w-full border border-blue-200 rounded-md p-2 outline-none focus:ring-1 focus:ring-blue-500 text-sm">
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeEditModal" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50">Batal</button>
          <button @click="updateUser" :disabled="isSaving" class="px-4 py-2 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
          <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Nonaktifkan Kurir</h3>
          <p class="text-sm text-gray-500 text-center">
            Yakin ingin menghapus akses kurir <strong>{{ userToDelete?.name }}</strong>? Jika ia sedang bertugas, selesaikan tugasnya terlebih dahulu.
          </p>
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
import { ref, onMounted } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api/v1/users'

const users = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const notification = ref({ show: false, message: '', type: 'success' })

// Modal State
const isEditModalOpen = ref(false)
const editForm = ref({ id: null, name: '', email: '', password: '' })
const isDeleteModalOpen = ref(false)
const userToDelete = ref(null)

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => notification.value.show = false, 4000)
}

const fetchUsers = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    users.value = response.data.data
  } catch (error) {
    console.error("Gagal mengambil data user", error)
    showNotification('Gagal memuat database karyawan', 'error')
  } finally {
    isLoading.value = false
  }
}

const openEditModal = (user) => {
  editForm.value = { id: user.id, name: user.name, email: user.email, password: '' }
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
}

const updateUser = async () => {
  if (!editForm.value.name || !editForm.value.email) return alert('Nama dan Email wajib diisi!')
  
  isSaving.value = true
  try {
    const payload = {
      name: editForm.value.name,
      email: editForm.value.email,
    }
    if (editForm.value.password) payload.password = editForm.value.password

    const response = await axios.put(`${API_URL}/${editForm.value.id}`, payload)
    
    // Update local state
    const index = users.value.findIndex(u => u.id === editForm.value.id)
    if (index !== -1) {
      users.value[index] = response.data.data
    }
    
    closeEditModal()
    showNotification('Data karyawan berhasil diupdate!', 'success')
  } catch (error) {
    console.error("Update Error:", error)
    showNotification(error.response?.data?.message || 'Gagal mengupdate data', 'error')
  } finally {
    isSaving.value = false
  }
}

const confirmDelete = (user) => {
  if (user.works_count > 0) {
    alert("Kurir ini masih memiliki paket yang harus diantar! Pindahkan tugasnya terlebih dahulu sebelum menghapus akun ini.")
    return
  }
  userToDelete.value = user
  isDeleteModalOpen.value = true
}

const deleteUser = async () => {
  if (!userToDelete.value) return
  
  isSaving.value = true
  try {
    await axios.delete(`${API_URL}/${userToDelete.value.id}`)
    users.value = users.value.filter(u => u.id !== userToDelete.value.id)
    isDeleteModalOpen.value = false
    userToDelete.value = null
    showNotification('Akses kurir berhasil dihapus!', 'success')
  } catch (error) {
    console.error("Delete Error:", error)
    showNotification('Gagal menghapus kurir. Data mungkin terkait dengan riwayat operasional lama.', 'error')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchUsers()
})
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