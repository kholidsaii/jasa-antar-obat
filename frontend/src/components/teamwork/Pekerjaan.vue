<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative min-h-[500px]">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Monitoring Penugasan (Work Order)</h2>
        <p class="text-sm text-gray-500 mt-1">Pantau status armada dan paket yang sedang diantarkan.</p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-72">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </div>
          <input v-model="searchQuery" type="text" placeholder="Cari Kurir..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-[#3b5998]">
        </div>
        <button @click="fetchWorks" :disabled="isLoading" class="text-sm bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center disabled:opacity-50 w-full sm:w-auto justify-center whitespace-nowrap">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh Data
        </button>
      </div>
    </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all', notification.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>
    
    <div class="overflow-x-auto flex-1">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kurir & Armada</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sedang Diantar</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Selesai Hari Ini</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          <tr v-if="isLoading">
            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
              <svg class="animate-spin h-8 w-8 text-[#3b5998] mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Memuat penugasan...
            </td>
          </tr>
          
          <tr v-for="group in paginatedGroupedWorks" :key="group.courier?.id || 'unassigned'" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <img :src="group.courier?.foto ? 'http://localhost:8000/storage/' + group.courier.foto : `https://ui-avatars.com/api/?name=${encodeURIComponent(group.courier?.name || 'Tanpa Kurir')}&background=random&color=fff`" class="w-10 h-10 rounded-full mr-3 border border-gray-200">
                <div>
                  <div class="text-sm font-bold text-[#3b5998]">{{ group.courier?.name || 'Belum Ada Kurir' }}</div>
                  <div class="text-xs text-gray-500 mt-0.5"><i class="fas fa-motorcycle text-gray-400 mr-1"></i> {{ group.vehicle?.nama_kendaraan || '-' }} ({{ group.vehicle?.plat_nomor || '-' }})</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span v-if="group.active.length > 0" class="px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-full font-bold text-xs border border-yellow-200 animate-pulse">
                {{ group.active.length }} Paket Aktif
              </span>
              <span v-else class="text-xs text-gray-400 font-medium">Standby / Kosong</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <span v-if="group.history.length > 0" class="px-3 py-1.5 bg-green-100 text-green-800 rounded-full font-bold text-xs border border-green-200">
                {{ group.history.length }} Diselesaikan
              </span>
              <span v-else class="text-xs text-gray-400 font-medium">Belum ada</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <button @click="openGroupDetail(group)" class="bg-white border border-gray-300 hover:bg-gray-50 text-[#3b5998] px-4 py-2 rounded-lg text-xs font-bold shadow-sm transition-colors flex items-center mx-auto">
                <i class="fas fa-eye mr-2"></i> Lihat Detail
              </button>
            </td>
          </tr>

          <tr v-if="!isLoading && filteredGroupedWorks.length === 0">
            <td colspan="4" class="px-6 py-12 text-center text-gray-500 font-medium">
              {{ searchQuery ? `Tidak ada penugasan kurir yang cocok.` : 'Belum ada tugas kurir hari ini.' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="filteredGroupedWorks.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
      <span class="text-sm text-gray-500 font-medium">
        Menampilkan {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredGroupedWorks.length) }} dari {{ filteredGroupedWorks.length }} kurir
      </span>
      <div class="flex space-x-2">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors shadow-sm">Sebelumnya</button>
        <div class="flex items-center px-2 text-sm font-bold text-gray-700">{{ currentPage }} / {{ totalPages }}</div>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors shadow-sm">Selanjutnya</button>
      </div>
    </div>

    <div v-if="isDetailModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeDetailModal"></div>
      <div class="relative bg-gray-50 rounded-xl shadow-2xl w-full max-w-3xl mx-auto flex flex-col overflow-hidden max-h-[90vh]">
        
        <div class="px-6 py-5 border-b border-gray-200 bg-white flex justify-between items-center z-10">
          <div>
            <h3 class="text-lg font-extrabold text-gray-900">Detail Penugasan Kurir</h3>
            <p class="text-sm text-[#3b5998] font-semibold">{{ currentDetailGroup?.courier?.name }} • {{ currentDetailGroup?.vehicle?.nama_kendaraan }}</p>
          </div>
          <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1 space-y-6 custom-scrollbar">
          
          <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="isExpandedActive = !isExpandedActive" class="w-full bg-yellow-50/50 hover:bg-yellow-50 p-4 flex justify-between items-center focus:outline-none transition-colors">
              <h4 class="font-bold text-gray-800 flex items-center">
                <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded mr-2 text-xs">Aktif</span> 
                📦 Sedang Diantar ({{ currentDetailGroup?.active.length || 0 }})
              </h4>
              <i :class="['fas text-gray-400 transition-transform duration-300', isExpandedActive ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </button>

            <transition name="fade-slide">
              <div v-show="isExpandedActive" class="p-4 bg-gray-50/30">
                <div v-if="currentDetailGroup?.active.length === 0" class="text-sm text-gray-500 italic text-center py-4">Kurir ini sedang tidak mengantar paket aktif.</div>
                
                <transition-group name="list" tag="div" class="space-y-4 relative">
                  <div v-for="work in currentDetailGroup?.active" :key="work.id" class="bg-white border border-gray-200 p-5 rounded-xl shadow-sm hover:shadow transition-shadow">
                    
                    <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                      <div>
                        <span class="text-xs font-bold text-gray-500 mb-1 block">#WRK-{{ String(work.id).padStart(4, '0') }}</span>
                        <p class="font-bold text-gray-900 text-base">{{ work.package?.customer?.nama || 'Unknown' }}</p>
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1 text-red-500"></i> {{ work.package?.customer?.alamat || '-' }}</p>
                      </div>
                      <span :class="[work.package?.status_pengiriman === '7. Dalam perjalanan' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-orange-100 text-orange-800 border-orange-200']" class="px-3 py-1.5 text-[10px] font-bold uppercase rounded-md border text-center whitespace-nowrap animate-pulse">
                        {{ work.package?.status_pengiriman }}
                      </span>
                    </div>

                    <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                      <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Paket / Obat:</p>
                      <p class="text-sm font-semibold text-gray-800">{{ work.package?.deskripsi_pesanan }}</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                      <button v-if="work.package?.status_pengiriman !== '7. Dalam perjalanan'" @click="markOtw(work)" :disabled="isSaving" class="flex-1 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-3 py-2 rounded-lg border border-yellow-200 text-xs font-bold transition">
                        <i class="fas fa-motorcycle mr-1"></i> Diperjalanan
                      </button>
                      <button @click="completeWork(work)" :disabled="isSaving" class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 px-3 py-2 rounded-lg border border-green-200 text-xs font-bold transition">
                        <i class="fas fa-check-circle mr-1"></i> Selesai
                      </button>
                      <button @click="openEditModal(work)" :disabled="isSaving" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg border border-blue-200 text-xs font-bold transition">
                        <i class="fas fa-exchange-alt mr-1"></i> Ganti
                      </button>
                      <button @click="markBatal(work)" :disabled="isSaving" class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg border border-red-200 text-xs font-bold transition">
                        <i class="fas fa-times mr-1"></i> Batal
                      </button>
                    </div>

                  </div>
                </transition-group>
              </div>
            </transition>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="isExpandedHistory = !isExpandedHistory" class="w-full bg-green-50/50 hover:bg-green-50 p-4 flex justify-between items-center focus:outline-none transition-colors">
              <h4 class="font-bold text-gray-800 flex items-center">
                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded mr-2 text-xs">Selesai</span> 
                ✅ Diselesaikan Hari Ini ({{ currentDetailGroup?.history.length || 0 }})
              </h4>
              <i :class="['fas text-gray-400 transition-transform duration-300', isExpandedHistory ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </button>

            <transition name="fade-slide">
              <div v-show="isExpandedHistory" class="p-4 bg-gray-50/30">
                <div v-if="currentDetailGroup?.history.length === 0" class="text-sm text-gray-500 italic text-center py-4">Belum ada paket yang diselesaikan hari ini.</div>
                
                <transition-group name="list" tag="div" class="space-y-3 relative">
                  <div v-for="work in currentDetailGroup?.history" :key="work.id" class="bg-white border border-green-100 p-4 rounded-xl flex justify-between items-center opacity-80 hover:opacity-100 transition-opacity">
                    <div>
                      <p class="font-bold text-gray-800 text-sm">{{ work.package?.customer?.nama }}</p>
                      <p class="text-xs text-gray-500 mt-1"><i class="fas fa-box text-gray-400 mr-1"></i> {{ work.package?.deskripsi_pesanan }}</p>
                      <p class="text-[10px] text-gray-400 mt-1">Selesai: {{ new Date(work.updated_at).toLocaleTimeString('id-ID') }}</p>
                    </div>
                    <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2.5 py-1 rounded border border-green-200 uppercase tracking-widest">Selesai</span>
                  </div>
                </transition-group>
              </div>
            </transition>
          </div>

        </div>
      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isEditModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Ganti Kurir / Armada</h3>
        </div>
        <div class="p-6">
          <form @submit.prevent="updateWork" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Pindah ke Kurir Baru</label>
              <select v-model="editForm.user_id" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
                <option v-for="usr in allUsers" :key="usr.id" :value="usr.id">{{ usr.name }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-2">Kendaraan penugasan otomatis mengikuti motor milik kurir baru.</p>
            </div>
          </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="isEditModalOpen = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium">Batal</button>
          <button @click="updateWork" :disabled="isSaving" class="px-4 py-2 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Simpan Perubahan' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api/v1/works'
const PACKAGE_API_URL = 'http://localhost:8000/api/v1/packages'

const works = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const notification = ref({ show: false, message: '', type: 'success' })

// --- LOGIKA GROUPING BERDASARKAN KURIR ---
const groupedWorks = computed(() => {
  const groups = {}
  const today = new Date().toLocaleDateString('en-CA') 

  works.value.forEach(work => {
    const courierId = work.user_id || 'unassigned'
    if (!groups[courierId]) {
      groups[courierId] = { courier: work.courier, vehicle: work.vehicle, active: [], history: [] }
    }

    const status = work.package?.status_pengiriman
    const workDate = new Date(work.updated_at).toLocaleDateString('en-CA')

    // PERBAIKAN: Gunakan status baru '8. Sampai (Selesai)' dan '9. Cancel / Pending'
    if (status === '8. Sampai (Selesai)') {
      if (workDate === today) groups[courierId].history.push(work)
    } else if (status !== '9. Cancel / Pending') {
      groups[courierId].active.push(work)
    }
  })
  return Object.values(groups).filter(g => g.active.length > 0 || g.history.length > 0)
})

// --- State Pagination & Search ---
const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 8 

const filteredGroupedWorks = computed(() => {
  if (!searchQuery.value) return groupedWorks.value
  const query = searchQuery.value.toLowerCase()
  return groupedWorks.value.filter(group => {
    const courierMatch = (group.courier?.name || '').toLowerCase().includes(query)
    const patientMatch = group.active.some(w => (w.package?.customer?.nama || '').toLowerCase().includes(query))
    return courierMatch || patientMatch
  })
})

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredGroupedWorks.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)
const paginatedGroupedWorks = computed(() => filteredGroupedWorks.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

// --- MODALS ---
const isDetailModalOpen = ref(false)
const isExpandedActive = ref(true)
const isExpandedHistory = ref(true)

const selectedCourierId = ref(null)

const currentDetailGroup = computed(() => {
  if (!selectedCourierId.value) return null
  return groupedWorks.value.find(g => (g.courier?.id || 'unassigned') === selectedCourierId.value)
})

const openGroupDetail = (group) => {
  selectedCourierId.value = group.courier?.id || 'unassigned'
  isDetailModalOpen.value = true
  isExpandedActive.value = true
  isExpandedHistory.value = true
}
const closeDetailModal = () => {
  isDetailModalOpen.value = false
  setTimeout(() => selectedCourierId.value = null, 300) 
}

const allUsers = ref([])
const isEditModalOpen = ref(false)
const editForm = ref({ id: null, user_id: '' })

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => notification.value.show = false, 4000)
}

const fetchWorks = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    works.value = response.data.data
  } catch (error) {
    showNotification("Gagal memuat database penugasan", "error")
  } finally {
    isLoading.value = false
  }
}

// --- API ACTIONS ---
const markOtw = async (work) => {
  isSaving.value = true
  try {
    // PERBAIKAN: Gunakan status baru '7. Dalam perjalanan'
    await axios.put(`${PACKAGE_API_URL}/${work.package.id}`, { status_pengiriman: '7. Dalam perjalanan' })
    const index = works.value.findIndex(w => w.id === work.id)
    if(index !== -1) works.value[index].package.status_pengiriman = '7. Dalam perjalanan'
  } catch (error) {
    showNotification("Gagal mengubah status", "error")
  } finally { isSaving.value = false }
}

const completeWork = async (work) => {
  if(!confirm(`Tandai paket untuk ${work.package?.customer?.nama || ''} selesai dikirim?`)) return
  isSaving.value = true
  try {
    // PERBAIKAN: Gunakan status baru '8. Sampai (Selesai)'
    await axios.put(`${PACKAGE_API_URL}/${work.package.id}`, { 
      status_pengiriman: '8. Sampai (Selesai)',
      status_pembayaran: 'Lunas' 
    })
    
    const index = works.value.findIndex(w => w.id === work.id)
    if(index !== -1) {
      works.value[index].package.status_pengiriman = '8. Sampai (Selesai)'
      works.value[index].updated_at = new Date().toISOString() 
    }
  } catch (error) {
    showNotification("Gagal menyelesaikan tugas", "error")
  } finally { isSaving.value = false }
}

const markBatal = async (work) => {
  if(!confirm("Yakin ingin membatalkan paket ini?")) return
  isSaving.value = true
  try {
    // PERBAIKAN: Gunakan status baru '9. Cancel / Pending'
    await axios.put(`${PACKAGE_API_URL}/${work.package.id}`, { status_pengiriman: '9. Cancel / Pending' })
    works.value = works.value.filter(w => w.id !== work.id)
  } catch (error) {
    showNotification("Gagal membatalkan paket", "error")
  } finally { isSaving.value = false }
}

const fetchDropdownData = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/v1/users')
    allUsers.value = response.data.data.filter(u => u.role === 'kurir')
  } catch (error) { console.error(error) }
}

const openEditModal = async (work) => {
  if (allUsers.value.length === 0) await fetchDropdownData()
  editForm.value = { id: work.id, user_id: work.user_id }
  isEditModalOpen.value = true
}

const updateWork = async () => {
  isSaving.value = true
  try {
    const response = await axios.put(`${API_URL}/${editForm.value.id}`, editForm.value)
    const index = works.value.findIndex(w => w.id === editForm.value.id)
    if(index !== -1) works.value[index] = response.data.data
    
    isEditModalOpen.value = false
    showNotification("Berhasil memindahkan kurir!", "success")
  } catch (error) {
    showNotification("Gagal memindahkan tugas", "error")
  } finally { isSaving.value = false }
}

onMounted(() => fetchWorks())
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f9fafb; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

/* Animasi untuk TransitionGroup Lists (Slide Out & Drop In) */
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.list-enter-from {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(40px); /* Saat selesai, paket melayang keluar ke kanan */
}
.list-leave-active {
  position: absolute;
  width: 100%;
  z-index: 0;
}

/* Animasi Collapse Accordion */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
  transform-origin: top;
}
.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: scaleY(0.9);
}
</style>