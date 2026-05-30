<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
      <h2 class="text-lg font-bold text-gray-800">Manajemen Armada Kendaraan RS</h2>
      <button @click="fetchVehicles" class="text-sm text-[#3b5998] hover:underline font-medium">Refresh Data</button>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pemilik (Kurir)</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kendaraan</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Plat Nomor</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-for="vehicle in vehicles" :key="vehicle.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#3b5998]">{{ vehicle.user?.name || 'Tidak Diketahui' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ vehicle.nama_kendaraan }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono font-bold">{{ vehicle.plat_nomor }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span :class="vehicle.status === 'Tersedia' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'" class="px-2.5 py-1 text-xs rounded-full font-semibold border">{{ vehicle.status }}</span>
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

const fetchVehicles = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/v1/vehicles')
    vehicles.value = response.data.data
  } catch (error) { console.error(error) }
}
onMounted(() => fetchVehicles())
</script>