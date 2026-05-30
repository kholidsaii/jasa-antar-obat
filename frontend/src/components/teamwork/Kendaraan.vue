<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100">
      <h2 class="text-lg font-bold text-gray-800">Manajemen Armada Kendaraan RS</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nama Kendaraan</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Plat Nomor</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status Operasional</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-if="isLoading">
            <td colspan="3" class="px-6 py-4 text-center text-gray-400">Memuat status armada...</td>
          </tr>
          <tr v-else-if="vehicles.length === 0">
            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Belum ada armada kendaraan terdaftar.</td>
          </tr>
          <tr v-for="vehicle in vehicles" :key="vehicle.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ vehicle.nama_kendaraan }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono font-bold">{{ vehicle.plat_nomor }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="px-2.5 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold border border-green-200">Tersedia</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const vehicles = ref([])
const isLoading = ref(true)

const fetchVehicles = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/v1/vehicles')
    vehicles.value = response.data.data
  } catch (error) {
    console.error(error)
  }  finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchVehicles()
})
</script>