<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
      <h2 class="text-lg font-bold text-gray-800">Monitoring Pekerjaan (Work Orders)</h2>
    </div>
    
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Work ID</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Paket / Pasien</th>
            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Kurir & Armada</th>
            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Ongkos Kirim</th>
            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-if="isLoading">
            <td colspan="5" class="px-6 py-10 text-center text-gray-400">Memuat rincian penugasan...</td>
          </tr>
          <tr v-else-if="works.length === 0">
            <td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada tugas kurir hari ini.</td>
          </tr>
          <tr v-for="work in works" :key="work.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">#WRK-{{ String(work.id).padStart(4, '0') }}</td>
            <td class="px-6 py-4">
              <div class="text-sm font-semibold text-gray-900">{{ work.package?.customer?.nama || 'Pasien Umum' }}</div>
              <div class="text-xs text-gray-500 mt-1 max-w-xs truncate">{{ work.package?.deskripsi_pesanan }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-bold text-[#3b5998]">{{ work.courier?.name || 'No Driver' }}</div>
              <div class="text-xs text-gray-500 mt-0.5">{{ work.vehicle?.nama_kendaraan }} ({{ work.vehicle?.plat_nomor || '-' }})</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600">
              Rp {{ Number(work.harga_ongkos).toLocaleString('id-ID') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
              <button @click="deleteWork(work.id)" class="text-red-500 hover:text-red-700 font-medium">Batalkan</button>
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

const works = ref([])
const isLoading = ref(true)

const fetchWorks = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/v1/works')
    works.value = response.data.data
  } catch (error) {
    console.error("Gagal mengambil data penugasan:", error)
  } finally {
    isLoading.value = false
  }
}

const deleteWork = async (id) => {
  if(!confirm("Yakin ingin membatalkan tugas kerja ini?")) return
  try {
    await axios.delete(`http://localhost:8000/api/v1/works/${id}`)
    works.value = works.value.filter(w => w.id !== id)
  } catch (error) {
    alert("Gagal menghapus tugas kerja.")
  }
}

onMounted(() => {
  fetchWorks()
})
</script>