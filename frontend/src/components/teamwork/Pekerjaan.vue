<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative min-h-[500px]">
    
    <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-lg sm:text-xl font-black text-gray-900">Monitoring Penugasan (Work Order)</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau status armada dan paket yang sedang diantarkan.</p>
      </div>
      
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-72">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400 text-sm"></i>
          </div>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Cari Kurir atau Pasien..." 
            class="block w-full pl-10 pr-3 py-3 sm:py-2 border border-gray-300 rounded-xl sm:rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#3b5998] transition-all shadow-sm"
          >
        </div>
        <button @click="fetchWorks" :disabled="isLoading" class="w-full sm:w-auto flex justify-center items-center text-sm bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 px-5 py-3 sm:py-2 rounded-xl sm:rounded-lg font-bold transition-all shadow-sm active:scale-95 disabled:opacity-50">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh Data
        </button>
      </div>
    </div>

    <div v-if="notification.show" :class="['px-5 py-3 text-xs sm:text-sm font-bold text-white transition-all text-center', notification.type === 'success' ? 'bg-emerald-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>
    
    <div class="flex-1 bg-gray-50/50 p-4 sm:p-5 border-t border-gray-100">
      
      <div v-if="isLoading" class="space-y-4">
        <div v-for="i in 4" :key="i" class="h-32 bg-white rounded-2xl border border-gray-100 animate-pulse shadow-sm"></div>
      </div>

      <div v-else-if="filteredGroupedWorks.length === 0" class="p-12 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="text-gray-300 mb-3"><i class="fas fa-motorcycle text-5xl"></i></div>
        <p class="text-gray-500 font-bold text-sm">
          {{ searchQuery ? 'Tidak ada penugasan kurir yang cocok.' : 'Belum ada tugas kurir hari ini.' }}
        </p>
      </div>

      <div v-else class="space-y-4">
        <div v-for="group in paginatedGroupedWorks" :key="'card-'+(group.courier?.id || 'unassigned')" class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all flex flex-col gap-4 group relative">
          
          <div class="flex flex-col md:flex-row items-start md:items-center gap-4 sm:gap-6">
            <div class="flex items-center gap-3 w-full md:w-1/3 lg:w-1/4 shrink-0">
              <div class="shrink-0">
                <img :src="group.courier?.foto ? 'http://localhost:8000/storage/' + group.courier.foto : `https://ui-avatars.com/api/?name=${encodeURIComponent(group.courier?.name || 'Tanpa Kurir')}&background=random&color=fff`" class="w-14 h-14 rounded-full border-2 border-blue-100 shadow-sm object-cover p-0.5">
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-black text-gray-900 text-base truncate">{{ group.courier?.name || 'Belum Ada Kurir' }}</h3>
                <p class="text-xs text-gray-500 font-medium flex items-center mt-0.5 truncate">
                  <i class="fas fa-motorcycle text-gray-400 mr-1.5"></i> {{ group.vehicle?.nama_kendaraan || '-' }} <span class="hidden sm:inline ml-1">({{ group.vehicle?.plat_nomor || '-' }})</span>
                </p>
              </div>
            </div>

            <div class="flex-1 w-full flex gap-3">
              <div class="flex-1 bg-yellow-50 border border-yellow-100 py-3 sm:py-2.5 px-4 rounded-xl flex justify-between items-center shadow-sm">
                <p class="text-[10px] sm:text-xs text-yellow-800 font-bold uppercase tracking-wider">Aktif</p>
                <p class="text-xl sm:text-2xl font-black text-yellow-600" :class="{'animate-pulse': group.active.length > 0}">{{ group.active.length }}</p>
              </div>
              <div class="flex-1 bg-green-50 border border-green-100 py-3 sm:py-2.5 px-4 rounded-xl flex justify-between items-center shadow-sm">
                <p class="text-[10px] sm:text-xs text-green-800 font-bold uppercase tracking-wider">Selesai</p>
                <p class="text-xl sm:text-2xl font-black text-green-600">{{ group.history.length }}</p>
              </div>
            </div>

            <div class="w-full md:w-auto shrink-0 md:min-w-[200px] mt-2 md:mt-0 border-t border-gray-100 md:border-t-0 pt-4 md:pt-0">
              <button @click="openGroupDetail(group)" class="w-full flex justify-center items-center bg-blue-50 hover:bg-[#3b5998] text-[#3b5998] hover:text-white py-3 sm:py-3.5 rounded-xl border border-blue-200 text-xs font-bold transition-all active:scale-95 shadow-sm uppercase tracking-wider">
                <i class="fas fa-eye mr-2"></i> Buka Detail
              </button>
            </div>
          </div>

          <div v-if="group.active.length > 0" class="pt-3 border-t border-gray-100">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Tugas Sedang Berjalan:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
              <div v-for="work in group.active" :key="work.id" class="bg-gray-50 border border-gray-200 p-3 rounded-xl hover:border-blue-300 transition-colors shadow-sm">
                 <div class="flex items-start justify-between gap-2 mb-1">
                    <p class="text-xs font-bold text-[#3b5998] line-clamp-1" :title="work.nama_pekerjaan">{{ work.nama_pekerjaan || 'Tugas Pengantaran' }}</p>
                    
                    <span :class="getWaktuResiClass(work.package?.waktu_pengantaran)" class="text-[8px] font-black border px-1.5 py-0.5 rounded shadow-sm shrink-0 flex items-center uppercase">
                      <i class="fas mr-1" :class="getWaktuIconClass(work.package?.waktu_pengantaran)"></i>
                      #PKT-{{ String(work.package?.id || 0).padStart(4, '0') }}
                    </span>
                 </div>
                 <p class="text-[11px] text-gray-600 line-clamp-2 leading-relaxed italic">{{ work.deskripsi || 'Tidak ada catatan/deskripsi khusus.' }}</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <div v-if="filteredGroupedWorks.length > 0" class="px-5 py-4 border-t border-gray-100 bg-white sm:bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
      <span class="text-[11px] sm:text-sm text-gray-500 font-bold uppercase tracking-wider text-center sm:text-left">
        Hal {{ currentPage }} dari {{ totalPages }} <span class="hidden sm:inline">• Total {{ filteredGroupedWorks.length }} Kurir Beroperasi</span>
      </span>
      <div class="flex items-center space-x-2 w-full sm:w-auto justify-between sm:justify-end">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-5 py-2.5 sm:py-2 text-xs sm:text-sm bg-white border border-gray-300 rounded-xl sm:rounded-lg hover:bg-gray-50 disabled:opacity-50 font-bold transition-all shadow-sm active:scale-95 flex-1 sm:flex-none text-gray-700">
          <i class="fas fa-chevron-left mr-1"></i> Prev
        </button>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-5 py-2.5 sm:py-2 text-xs sm:text-sm bg-white border border-gray-300 rounded-xl sm:rounded-lg hover:bg-gray-50 disabled:opacity-50 font-bold transition-all shadow-sm active:scale-95 flex-1 sm:flex-none text-gray-700">
          Next <i class="fas fa-chevron-right ml-1"></i>
        </button>
      </div>
    </div>

    <div v-if="isDetailModalOpen" class="fixed inset-0 z-[100] flex flex-col justify-end sm:items-center sm:justify-center p-0 sm:p-4">
      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeDetailModal"></div>
      
      <div class="relative bg-gray-50 rounded-t-3xl sm:rounded-2xl shadow-2xl w-full sm:max-w-3xl mx-auto flex flex-col overflow-hidden max-h-[92vh] sm:max-h-[90vh] animate-slide-up sm:animate-modal-in">
        
        <div class="w-full flex justify-center pt-3 pb-2 sm:hidden cursor-grab bg-white" @click="closeDetailModal">
          <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-200 bg-white flex justify-between items-center z-10 sticky top-0">
          <div class="pr-4">
            <h3 class="text-base sm:text-lg font-black text-gray-900 leading-tight">Detail Penugasan Kurir</h3>
            <p class="text-xs sm:text-sm text-[#3b5998] font-bold mt-0.5 truncate">{{ currentDetailGroup?.courier?.name }} • {{ currentDetailGroup?.vehicle?.nama_kendaraan }}</p>
          </div>
          <button @click="closeDetailModal" class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-full transition-colors shrink-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <div class="p-4 sm:p-6 overflow-y-auto flex-1 space-y-5 sm:space-y-6 custom-scrollbar pb-10 sm:pb-6">
          
          <div class="bg-white rounded-2xl sm:rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="isExpandedActive = !isExpandedActive" class="w-full bg-yellow-50/50 hover:bg-yellow-50 p-4 sm:p-5 flex justify-between items-center focus:outline-none transition-colors">
              <h4 class="font-black text-gray-800 flex items-center text-sm sm:text-base">
                <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-md mr-2 text-[10px] sm:text-xs uppercase tracking-wider">Aktif</span> 
                📦 Sedang Diantar ({{ currentDetailGroup?.active.length || 0 }})
              </h4>
              <i :class="['fas text-gray-400 transition-transform duration-300', isExpandedActive ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </button>

            <transition name="fade-slide">
              <div v-show="isExpandedActive" class="p-4 bg-gray-50/30">
                <div v-if="currentDetailGroup?.active.length === 0" class="text-sm text-gray-500 italic text-center py-4">Kurir ini sedang tidak mengantar paket aktif.</div>
                
                <transition-group name="list" tag="div" class="space-y-4 relative">
                  <div v-for="work in currentDetailGroup?.active" :key="work.id" class="bg-white border border-gray-200 p-4 sm:p-5 rounded-xl shadow-sm hover:shadow transition-shadow">
                    
                    <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-3">
                      <div class="pr-3">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                          <span :class="getWaktuResiClass(work.package?.waktu_pengantaran)" class="text-[9px] sm:text-[10px] font-black border px-2 py-0.5 rounded uppercase tracking-wider shadow-sm flex items-center">
                            <i class="fas mr-1" :class="getWaktuIconClass(work.package?.waktu_pengantaran)"></i>
                            #PKT-{{ String(work.package?.id || 0).padStart(4, '0') }}{{ work.package?.no_struk ? '-' + work.package?.no_struk : '' }}
                          </span>
                          <span v-if="work.nama_pekerjaan" class="text-[9px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded uppercase tracking-wider shadow-sm">{{ work.nama_pekerjaan }}</span>
                        </div>
                        <p class="font-black text-gray-900 text-sm sm:text-base leading-tight">{{ work.package?.customer?.nama || 'Unknown' }}</p>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed"><i class="fas fa-map-marker-alt mr-1 text-red-500"></i> {{ work.package?.customer?.alamat || '-' }}</p>
                      </div>
                      <span :class="[work.package?.status_pengiriman === '7. Dalam perjalanan' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-orange-100 text-orange-800 border-orange-200']" class="px-2 sm:px-3 py-1.5 text-[9px] sm:text-[10px] font-black uppercase tracking-wider rounded-md border text-center shrink-0">
                        {{ work.package?.status_pengiriman }}
                      </span>
                    </div>

                    <div class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                      <div v-if="work.deskripsi" class="mb-2 pb-2 border-b border-gray-200 border-dashed">
                        <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan Tugas:</p>
                        <p class="text-xs font-medium text-gray-700 italic">{{ work.deskripsi }}</p>
                      </div>
                      <p class="text-[10px] sm:text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Paket / Obat:</p>
                      <p class="text-xs sm:text-sm font-semibold text-gray-800 leading-relaxed">{{ work.package?.deskripsi_pesanan }}</p>
                    </div>

                    <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2 sm:gap-3">
                      <button v-if="work.package?.status_pengiriman !== '7. Dalam perjalanan'" @click="markOtw(work)" :disabled="isSaving" class="sm:flex-1 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 py-3 sm:py-2.5 rounded-xl sm:rounded-lg border border-yellow-200 text-xs font-black transition-all active:scale-95 shadow-sm">
                        <i class="fas fa-motorcycle mr-1.5"></i> Di Jalan
                      </button>
                      <button @click="completeWork(work)" :disabled="isSaving" class="sm:flex-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 py-3 sm:py-2.5 rounded-xl sm:rounded-lg border border-emerald-200 text-xs font-black transition-all active:scale-95 shadow-sm">
                        <i class="fas fa-check-circle mr-1.5"></i> Selesai
                      </button>
                      <button @click="openEditModal(work)" :disabled="isSaving" class="sm:flex-1 bg-blue-50 hover:bg-blue-100 text-[#3b5998] py-3 sm:py-2.5 rounded-xl sm:rounded-lg border border-blue-200 text-xs font-black transition-all active:scale-95 shadow-sm">
                        <i class="fas fa-exchange-alt mr-1.5"></i> Pindah
                      </button>
                      <button @click="markBatal(work)" :disabled="isSaving" class="sm:flex-1 bg-red-50 hover:bg-red-100 text-red-600 py-3 sm:py-2.5 rounded-xl sm:rounded-lg border border-red-200 text-xs font-black transition-all active:scale-95 shadow-sm">
                        <i class="fas fa-times mr-1.5"></i> Batal
                      </button>
                    </div>

                  </div>
                </transition-group>
              </div>
            </transition>
          </div>

          <div class="bg-white rounded-2xl sm:rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="isExpandedHistory = !isExpandedHistory" class="w-full bg-emerald-50/50 hover:bg-emerald-50 p-4 sm:p-5 flex justify-between items-center focus:outline-none transition-colors">
              <h4 class="font-black text-gray-800 flex items-center text-sm sm:text-base">
                <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md mr-2 text-[10px] sm:text-xs uppercase tracking-wider">Selesai</span> 
                ✅ Hari Ini ({{ currentDetailGroup?.history.length || 0 }})
              </h4>
              <i :class="['fas text-gray-400 transition-transform duration-300', isExpandedHistory ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
            </button>

            <transition name="fade-slide">
              <div v-show="isExpandedHistory" class="p-4 bg-gray-50/30">
                <div v-if="currentDetailGroup?.history.length === 0" class="text-sm text-gray-500 italic text-center py-4">Belum ada paket yang diselesaikan hari ini.</div>
                
                <transition-group name="list" tag="div" class="space-y-3 relative">
                  <div v-for="work in currentDetailGroup?.history" :key="'hist-'+work.id" class="bg-white border border-emerald-100 p-4 rounded-xl flex justify-between items-start sm:items-center opacity-80 hover:opacity-100 transition-opacity shadow-sm">
                    <div class="pr-3">
                      <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <p class="font-black text-gray-800 text-xs sm:text-sm">{{ work.package?.customer?.nama }}</p>
                        
                        <span :class="getWaktuResiClass(work.package?.waktu_pengantaran)" class="text-[8px] font-black border px-1.5 py-0.5 rounded uppercase tracking-wider shadow-sm flex items-center">
                          <i class="fas mr-1" :class="getWaktuIconClass(work.package?.waktu_pengantaran)"></i>
                          #PKT-{{ String(work.package?.id || 0).padStart(4, '0') }}
                        </span>

                        <span v-if="work.nama_pekerjaan" class="text-[8px] font-bold bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded uppercase border border-gray-300">{{ work.nama_pekerjaan }}</span>
                      </div>
                      <p class="text-[11px] sm:text-xs text-gray-500 mt-1 leading-snug line-clamp-1"><i class="fas fa-box text-gray-400 mr-1"></i> {{ work.package?.deskripsi_pesanan }}</p>
                      <p class="text-[10px] font-bold text-gray-400 mt-1.5">Selesai: {{ new Date(work.updated_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) }} WIB</p>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-black text-emerald-600 bg-emerald-50 px-2.5 py-1.5 rounded-md border border-emerald-200 uppercase tracking-widest shrink-0 mt-1 sm:mt-0">Selesai</span>
                  </div>
                </transition-group>
              </div>
            </transition>
          </div>

        </div>
      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[110] flex flex-col justify-end sm:items-center sm:justify-center p-0 sm:p-4">
      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="isEditModalOpen = false"></div>
      
      <div class="relative bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl w-full sm:max-w-md mx-auto flex flex-col overflow-hidden animate-slide-up sm:animate-modal-in">
        
        <div class="w-full flex justify-center pt-3 pb-2 sm:hidden cursor-grab" @click="isEditModalOpen = false">
          <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-white sm:bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-base sm:text-lg font-black text-gray-900">Pindah / Ganti Kurir</h3>
          <button @click="isEditModalOpen = false" class="text-gray-400 hover:text-red-500 bg-gray-50 p-2 rounded-full transition-colors shrink-0 sm:hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-5 sm:p-6 bg-gray-50/30">
          <form @submit.prevent="updateWork" class="space-y-4">
            <div>
              <label class="block text-xs sm:text-sm font-black text-gray-500 uppercase tracking-wider mb-2">Pindah ke Kurir Baru</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                  <i class="fas fa-user-circle text-gray-400"></i>
                </div>
                <select v-model="editForm.user_id" required class="w-full border border-gray-300 rounded-xl py-3.5 sm:py-3 pl-10 pr-4 outline-none focus:ring-2 focus:ring-[#3b5998] bg-white text-sm font-bold shadow-sm appearance-none cursor-pointer">
                  <option disabled value="">Pilih Kurir...</option>
                  <option v-for="usr in allUsers" :key="usr.id" :value="usr.id">{{ usr.name }}</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                  <i class="fas fa-chevron-down text-xs"></i>
                </div>
              </div>
              <p class="text-[10px] sm:text-xs text-gray-500 font-medium mt-2 bg-blue-50 p-2 rounded-lg border border-blue-100"><i class="fas fa-info-circle text-[#3b5998] mr-1"></i> Kendaraan otomatis akan diset sesuai armada milik kurir yang baru dipilih.</p>
            </div>
          </form>
        </div>
        
        <div class="bg-white sm:bg-gray-50 px-5 sm:px-6 py-4 border-t border-gray-100 flex gap-3 pb-safe">
          <button @click="isEditModalOpen = false" class="w-1/3 sm:w-auto px-4 py-3.5 sm:py-2.5 bg-gray-100 border border-transparent rounded-xl text-gray-700 font-bold hover:bg-gray-200 transition-colors active:scale-95 text-sm">
            Batal
          </button>
          <button @click="updateWork" :disabled="isSaving" class="flex-1 px-4 py-3.5 sm:py-2.5 bg-[#3b5998] text-white rounded-xl font-black hover:bg-blue-800 disabled:opacity-50 transition-all shadow-md active:scale-95 flex justify-center items-center text-sm">
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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

const API_URL = '/works'
const PACKAGE_API_URL = '/packages'

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
const itemsPerPage = 6 

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
  if(!confirm("Yakin ingin membatalkan paket ini? Tugas akan ditarik dari kurir.")) return
  isSaving.value = true
  try {
    await axios.put(`${PACKAGE_API_URL}/${work.package.id}`, { status_pengiriman: '9. Cancel / Pending' })
    works.value = works.value.filter(w => w.id !== work.id)
  } catch (error) {
    showNotification("Gagal membatalkan paket", "error")
  } finally { isSaving.value = false }
}

const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/users')
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
// --- FUNGSI WARNA & ICON RESI WAKTU PENGANTARAN ---
const getWaktuResiClass = (waktu) => {
  if (waktu === 'Segera') return 'bg-red-50 text-red-700 border-red-200'
  if (waktu === 'Malam') return 'bg-emerald-50 text-emerald-700 border-emerald-200'
  return 'bg-blue-50 text-blue-700 border-blue-200'
}

const getWaktuIconClass = (waktu) => {
  if (waktu === 'Segera') return 'fa-shipping-fast'
  if (waktu === 'Malam') return 'fa-moon'
  return 'fa-calendar-day'
}
onMounted(() => fetchWorks())
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

/* Animasi untuk TransitionGroup Lists */
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.list-enter-from {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(40px);
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

/* Animasi HP Bottom Sheet */
@keyframes slideUp {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up { animation: slideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal-in { animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

/* Pengamanan padding di HP (Khusus iPhone Swipe Bar) */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
  .pb-safe { padding-bottom: calc(1rem + env(safe-area-inset-bottom)); }
}
</style>