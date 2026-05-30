<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
      <h2 class="text-lg font-bold text-gray-800">Daftar Karyawan Operasional</h2>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-6">
      <div v-if="isLoading" class="col-span-full text-center py-6 text-gray-400">Memuat database karyawan...</div>
      <div v-else-if="users.length === 0" class="col-span-full text-center py-6 text-gray-500">Karyawan belum terdaftar.</div>
      
      <div v-for="user in users" :key="user.id" class="border border-gray-200 rounded-xl p-4 flex items-center space-x-4 bg-white hover:border-blue-200 transition-all shadow-2xs">
        <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`" class="w-12 h-12 rounded-full border border-gray-100 shadow-2xs" alt="Avatar">
        <div class="flex-1 min-w-0">
          <h4 class="font-bold text-gray-900 truncate">{{ user.name }}</h4>
          <p class="text-xs text-gray-500 mt-0.5 truncate">{{ user.email }}</p>
          <span class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-800 border border-blue-100">
            {{ user.role || 'Kurir Internal' }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const users = ref([])
const isLoading = ref(true)

const fetchUsers = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/v1/users')
    users.value = response.data.data
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchUsers()
})
</script>