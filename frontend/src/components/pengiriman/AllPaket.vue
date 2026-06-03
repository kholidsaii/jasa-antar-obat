<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative min-h-[500px]">
    
    <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <!-- <h2 class="text-lg sm:text-xl font-black text-gray-900">Daftar Semua Paket</h2>   -->
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Monitor status pesanan obat secara real-time.</p>
      </div>
      
      <div v-if="userRole === 'kurir' && pesananMenunggu.length > 0" class="w-full bg-indigo-50 border-l-4 border-indigo-500 p-3.5 sm:p-4 rounded-r-lg shadow-sm flex justify-between items-center animate-pulse">
        <div>
          <h3 class="text-indigo-800 font-bold text-xs sm:text-sm"><i class="fas fa-bell mr-1.5"></i> Orderan Baru Siap!</h3>
          <p class="text-indigo-600 text-[11px] sm:text-xs mt-1">Ada <strong>{{ pesananMenunggu.length }} paket</strong> dari farmasi menunggu diantar.</p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto mt-2 sm:mt-0">
        <div class="relative w-full sm:w-72">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <i class="fas fa-search text-gray-400 text-sm"></i>
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari ID, struk, atau pasien..." 
            class="block w-full pl-10 pr-3 py-3 sm:py-2 border border-gray-300 rounded-xl sm:rounded-lg text-sm outline-none focus:ring-2 focus:ring-[#3b5998] transition-all shadow-sm"
          >
        </div>
        <button @click="fetchPackages" :disabled="isLoading" class="w-full sm:w-auto flex justify-center items-center text-sm bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 px-5 py-3 sm:py-2 rounded-xl sm:rounded-lg font-bold transition-all shadow-sm active:scale-95 disabled:opacity-50">
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

      <div v-else-if="filteredPackages.length === 0" class="p-12 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="text-gray-300 mb-3"><i class="fas fa-box-open text-5xl"></i></div>
        <p class="text-gray-500 font-bold text-sm">Tidak ada data pesanan.</p>
      </div>

      <div v-else class="space-y-4">
        <div v-for="pkg in paginatedPackages" :key="pkg.id" class="bg-white rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row items-start md:items-center gap-4 sm:gap-6 border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all group">

          <div class="shrink-0 hidden sm:block">
            <div class="w-14 h-14 rounded-full border-2 border-blue-100 p-1">
              <div class="w-full h-full bg-blue-50 rounded-full flex items-center justify-center text-[#3b5998] text-xl font-black uppercase">
                {{ pkg.customer?.nama ? pkg.customer.nama.charAt(0) : '?' }}
              </div>
            </div>
          </div>

          <div class="flex-1 w-full">
            <div class="flex flex-wrap items-center gap-2 mb-1">
              <h3 class="text-gray-900 text-base sm:text-lg font-black">{{ pkg.customer?.nama || 'Dihapus' }}</h3>
              
              <span :class="getWaktuResiClass(pkg.waktu_pengantaran)" class="text-[10px] font-bold border px-2.5 py-0.5 rounded-md uppercase tracking-wider shadow-sm flex items-center">
                 <i v-if="pkg.waktu_pengantaran === 'Segera'" class="fas fa-shipping-fast mr-1"></i>
                 <i v-else-if="pkg.waktu_pengantaran === 'Malam'" class="fas fa-moon mr-1"></i>
                 <i v-else class="fas fa-calendar-day mr-1"></i>
                #PKT-{{ String(pkg.id).padStart(4, '0') }}{{ pkg.no_struk ? '-' + pkg.no_struk : '' }}
              </span>
            </div>
            
            <p class="text-gray-500 text-xs sm:text-sm mb-4 flex items-center line-clamp-1 font-medium">
              <i class="fas fa-map-marker-alt mr-1.5 text-red-400"></i> 
              {{ pkg.deskripsi_pesanan }} <span class="mx-2 text-gray-300">|</span> <i class="fas fa-phone-alt mr-1.5 text-gray-400"></i> {{ pkg.customer?.no_telp || '-' }}
            </p>

            <div class="relative w-full sm:w-5/6 mt-2">
              <div class="flex justify-between items-end mb-1.5">
                <span class="text-[10px] text-gray-400 font-bold tracking-widest uppercase">Status Pengiriman</span>
                <span class="text-[10px] font-black uppercase" :class="pkg.status_pengiriman === '9. Cancel / Pending' ? 'text-red-500' : 'text-[#3b5998]'">
                  {{ pkg.status_pengiriman }}
                </span>
              </div>
              <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                <div class="h-full transition-all duration-500 rounded-full"
                     :class="pkg.status_pengiriman === '9. Cancel / Pending' ? 'bg-red-500' : 'bg-gradient-to-r from-blue-400 to-[#3b5998]'"
                     :style="{ width: getProgressPercentage(pkg.status_pengiriman) + '%' }">
                </div>
              </div>
            </div>
          </div>

          <div class="w-full md:w-auto shrink-0 bg-gray-50 rounded-xl p-3 sm:p-4 border border-gray-200 flex flex-row md:flex-col justify-between md:justify-center items-center md:items-end gap-3 mt-2 md:mt-0 min-w-[190px]">
            
            <div class="flex flex-col md:items-end">
              <div v-if="pkg.status_pembayaran === 'Lunas'" class="bg-emerald-100 text-emerald-700 border border-emerald-200 text-[9px] font-black px-2 py-1 rounded-md uppercase tracking-wider mb-2 flex items-center shadow-sm">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span> LUNAS
              </div>
              <div v-else class="bg-red-50 text-red-600 border border-red-200 text-[9px] font-black px-2 py-1 rounded-md uppercase tracking-wider mb-2 shadow-sm">
                BELUM LUNAS
              </div>

              <span class="text-[9px] sm:text-[10px] text-gray-400 font-bold tracking-widest mt-1">TOTAL TAGIHAN</span>
              <span class="text-[#3b5998] text-lg sm:text-xl font-black mt-0.5 tracking-tight">{{ pkg.total_harga ? formatRupiah(pkg.total_harga) : '-' }}</span>
            </div>

           <div class="flex gap-1.5 md:mt-2">
              <button @click="copyTrackingLink(pkg)" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-500 hover:text-white flex items-center justify-center transition-colors shadow-sm" title="Salin Link">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
              </button>
              <button @click="openEditModal(pkg)" class="w-8 h-8 rounded-lg bg-blue-50 text-[#3b5998] border border-blue-200 hover:bg-[#3b5998] hover:text-white flex items-center justify-center transition-colors shadow-sm" title="Update Status">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              </button>
              <button v-if="['superadmin', 'admin'].includes(userRole)" @click="confirmDelete(pkg)" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 border border-red-200 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors shadow-sm" title="Hapus">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </div>
            
          </div>

        </div>
      </div>
    </div>

    <div v-if="filteredPackages.length > 0" class="px-5 py-4 border-t border-gray-100 bg-white sm:bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
      <span class="text-[11px] sm:text-sm text-gray-500 font-bold uppercase tracking-wider text-center sm:text-left">
        Hal {{ currentPage }} dari {{ totalPages }} <span class="hidden sm:inline">• Total {{ filteredPackages.length }} Data</span>
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

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex flex-col justify-end sm:items-center sm:justify-center p-0 sm:p-4">
      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
      
      <div class="relative bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl w-full sm:max-w-lg mx-auto flex flex-col overflow-hidden max-h-[92vh] sm:max-h-[90vh] animate-slide-up sm:animate-modal-in">
        
        <div class="w-full flex justify-center pt-3 pb-2 sm:hidden cursor-grab active:cursor-grabbing" @click="closeEditModal">
          <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-white sm:bg-gray-50/50 flex justify-between items-center sticky top-0 z-10">
          <div>
            <h3 class="text-base sm:text-lg font-black text-gray-900 leading-tight">Update Status</h3>
            <p class="text-xs sm:text-sm font-bold text-[#3b5998] mt-0.5">#PKT-{{ String(editForm.id).padStart(4, '0') }}</p>
          </div>
          <button @click="closeEditModal" class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-full transition-colors shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar bg-gray-50/30">
          <form @submit.prevent="updatePackage" class="space-y-5">
            
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi Pesanan</label>
              <textarea v-model="editForm.deskripsi_pesanan" :disabled="userRole === 'kurir'" rows="2" required class="w-full border border-gray-300 rounded-xl sm:rounded-lg p-3 sm:p-2.5 focus:ring-2 focus:ring-[#3b5998] disabled:bg-gray-100 disabled:text-gray-500 outline-none text-sm font-medium shadow-sm transition-all"></textarea>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Posisi / Status</label>
                <select v-model="editForm.status_pengiriman" required class="w-full border border-gray-300 rounded-xl sm:rounded-lg p-3.5 sm:p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] bg-white text-sm font-bold shadow-sm transition-all">
                  <template v-if="userRole === 'farmasi'">
                    <option value="2. Stor Struk ke farmasi">2. Stor Struk ke farmasi</option>
                    <option value="3. Ambil paket obat farmasi">3. Ambil paket obat farmasi</option>
                  </template>
                  <template v-else-if="userRole === 'kurir'">
                    <option value="6. Diserahkan ke kurir">6. Diserahkan ke kurir</option>
                    <option value="7. Dalam perjalanan">7. Dalam perjalanan</option>
                    <option value="8. Sampai (Selesai)">8. Sampai (Selesai)</option>
                  </template>
                  <template v-else>
                    <option value="1. Verifikasi Jastar">1. Verifikasi Jastar</option>
                    <option value="2. Stor Struk ke farmasi">2. Stor Struk ke farmasi</option>
                    <option value="3. Ambil paket obat farmasi">3. Ambil paket obat farmasi</option>
                    <option value="4. Diserah paket obat jastar">4. Diserah paket obat jastar</option>
                    <option value="5. Sedang menunggu kurir">5. Sedang menunggu kurir</option>
                    <option value="6. Diserahkan ke kurir">6. Diserahkan ke kurir</option>
                    <option value="7. Dalam perjalanan">7. Dalam perjalanan</option>
                    <option value="8. Sampai (Selesai)">8. Sampai (Selesai)</option>
                    <option value="9. Cancel / Pending" class="text-red-600 font-black">9. Batal / Cancel</option>
                  </template>
                </select>
                <p v-if="editForm.status_pengiriman === '9. Cancel / Pending' && ['superadmin', 'admin', 'kurir'].includes(userRole)" class="text-[10px] font-bold text-red-500 mt-1.5 bg-red-50 px-2 py-1 rounded inline-block"><i class="fas fa-exclamation-triangle"></i> Cabut tugas kurir.</p>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pembayaran</label>
                <select v-model="editForm.status_pembayaran" :disabled="userRole === 'farmasi' || userRole === 'kurir'" required class="w-full border border-gray-300 rounded-xl sm:rounded-lg p-3.5 sm:p-2.5 disabled:bg-gray-100 disabled:text-gray-400 outline-none focus:ring-2 focus:ring-[#3b5998] bg-white text-sm font-bold shadow-sm transition-all">
                  <option value="Belum Lunas">Belum Lunas</option>
                  <option value="Lunas">Lunas</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Metode Bayar</label>
                <select v-model="editForm.metode_pembayaran" required class="w-full border border-gray-300 rounded-xl sm:rounded-lg p-3.5 sm:p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] bg-white text-sm font-bold shadow-sm transition-all">
                  <option value="Tunai / Cash (Sistem)">Tunai / Cash</option>
                  <option value="Transfer Bank (Sistem)">Transfer Bank</option>
                  <option value="QRIS / E-Wallet (Sistem)">QRIS / E-Wallet</option>
                  <option v-if="['superadmin', 'admin'].includes(userRole)" value="Gratis / Amal" class="text-purple-600 font-black">Gratis / Sedekah</option>
                </select>
              </div>
              
              <div v-if="['superadmin', 'admin'].includes(userRole)" class="sm:col-span-2 pt-4 border-t border-gray-200 mt-1">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Revisi Total Tagihan (Rp)</label>
                <input v-model="editForm.total_harga" type="number" class="w-full border-2 border-blue-200 bg-white text-[#3b5998] font-black text-xl rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-[#3b5998] shadow-sm transition-all">
              </div>
            </div>
          </form>
        </div>

        <div class="bg-white sm:bg-gray-50 px-5 sm:px-6 py-4 border-t border-gray-100 flex gap-3 pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] sm:shadow-none sm:rounded-b-2xl">
          <button @click="closeEditModal" type="button" class="w-1/3 sm:w-auto px-4 py-3.5 sm:py-2.5 bg-gray-100 hover:bg-gray-200 border border-transparent rounded-xl text-gray-700 font-bold transition-all active:scale-95 text-sm">
            Batal
          </button>
          <button @click="updatePackage" :disabled="isSaving" type="button" :class="['flex-1 px-4 py-3.5 sm:py-2.5 rounded-xl text-white font-black transition-all shadow-md active:scale-95 disabled:opacity-50 text-sm flex justify-center items-center', editForm.status_pengiriman === '9. Cancel / Pending' ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : 'bg-[#3b5998] hover:bg-blue-800 shadow-blue-200']">
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSaving ? 'Menyimpan...' : 'Simpan Status' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isDeleteModalOpen && ['superadmin', 'admin'].includes(userRole)" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="isDeleteModalOpen = false"></div>
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-auto flex flex-col overflow-hidden animate-modal-in">
        <div class="p-6 text-center">
          <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-50 rounded-full mb-4">
            <i class="fas fa-trash-alt text-2xl text-red-500"></i>
          </div>
          <h3 class="text-lg font-black text-gray-900 mb-2">Hapus Permanen Paket?</h3>
          <p class="text-sm text-gray-500 leading-relaxed">Anda yakin ingin menghapus resi <strong class="text-gray-800">#PKT-{{ packageToDelete?.id ? String(packageToDelete.id).padStart(4, '0') : '' }}</strong> secara permanen?</p>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex gap-3 border-t border-gray-100">
          <button @click="isDeleteModalOpen = false" type="button" class="flex-1 py-3 bg-white border border-gray-200 rounded-xl text-gray-700 font-bold active:scale-95 transition-all text-sm shadow-sm">Batal</button>
          <button @click="deletePackage" :disabled="isSaving" type="button" class="flex-1 py-3 bg-red-600 rounded-xl text-white font-black hover:bg-red-700 disabled:opacity-50 active:scale-95 transition-all text-sm shadow-md shadow-red-200">
            {{ isSaving ? 'Proses...' : 'Hapus' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

// -- Get Role User --
const currentUser = ref(JSON.parse(localStorage.getItem('user') || '{}'))
const userRole = ref(JSON.parse(localStorage.getItem('user'))?.role || '')

const packages = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const searchQuery = ref('')
const notification = ref({ show: false, message: '', type: 'success' })

// --- State Pagination & Search Filter Update ---
const currentPage = ref(1)
const itemsPerPage = 8 

const filteredPackages = computed(() => {
  if (!searchQuery.value) return packages.value
  const query = searchQuery.value.toLowerCase()
  return packages.value.filter(pkg => {
    const idStr = String(pkg.id).padStart(4, '0')
    const customerName = pkg.customer?.nama?.toLowerCase() || ''
    const desc = pkg.deskripsi_pesanan?.toLowerCase() || ''
    const noStruk = pkg.no_struk?.toLowerCase() || '' 
    return customerName.includes(query) || desc.includes(query) || idStr.includes(query) || noStruk.includes(query)
  })
})

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredPackages.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)
const paginatedPackages = computed(() => filteredPackages.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

const isEditModalOpen = ref(false)
const editForm = ref({ id: null, customer_id: null, deskripsi_pesanan: '', status_pengiriman: '', status_pembayaran: '', metode_pembayaran: '', customer: null })

const isDeleteModalOpen = ref(false)
const packageToDelete = ref(null)

const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
}
const getProgressPercentage = (status) => {
  if (!status) return 0;
  // Mengambil angka depan dari status (misal "1" dari "1. Verifikasi Jastar")
  const match = status.match(/^(\d)/);
  if (match) {
    const step = parseInt(match[1]);
    if (step === 9) return 100; // Jika batal, penuhi bar dengan warna merah
    // Total ada 8 step hingga selesai
    return (step / 8) * 100;
  }
  return 0;
}
const fetchPackages = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/packages')
    packages.value = response.data.data 
  } catch (error) { showNotification('Gagal mengambil data paket dari server', 'error') } 
  finally { isLoading.value = false }
}

const copyTrackingLink = (pkg) => {
  const resiStr = 'PKT-' + String(pkg.id).padStart(4, '0') + (pkg.no_struk ? '-' + pkg.no_struk : '')
  const trackingLink = `${window.location.origin}/tracking/${resiStr}`
  
  navigator.clipboard.writeText(trackingLink).then(() => {
    showNotification(`Link resi ${resiStr} disalin!`, 'success')
  }).catch(err => {
    alert("Gagal menyalin link. Browser Anda memblokir akses.")
  })
}

const openEditModal = (pkg) => {
  editForm.value = { ...pkg }
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
}

const pesananMenunggu = computed(() => {
  return packages.value.filter(p => ['3. Ambil paket obat farmasi', '4. Diserah paket obat jastar', '5. Sedang menunggu kurir'].includes(p.status_pengiriman));
});

const updatePackage = async () => {
  if (!editForm.value.deskripsi_pesanan) return
  isSaving.value = true
  try {
    const payload = {
      customer_id: editForm.value.customer_id,
      deskripsi_pesanan: editForm.value.deskripsi_pesanan,
      status_pengiriman: editForm.value.status_pengiriman,
      status_pembayaran: editForm.value.status_pembayaran,
      metode_pembayaran: editForm.value.metode_pembayaran,
      // PERBAIKAN 4: Tambahkan total harga agar ikut ter-update di database
      total_harga: editForm.value.total_harga 
    }
    
    const response = await axios.put(`/packages/${editForm.value.id}`, payload)
    
    // Auto Assign Khusus Kurir
    if (userRole.value === 'kurir' && editForm.value.status_pengiriman === '6. Diserahkan ke kurir') {
      try {
        const currentUserData = JSON.parse(localStorage.getItem('user'));
        const vehRes = await axios.get('/vehicles');
        const myVeh = vehRes.data.data.find(v => v.user_id === currentUserData.id);
        
        await axios.post('/works', {
          package_ids: [editForm.value.id],
          user_id: currentUserData.id,
          vehicle_id: myVeh ? myVeh.id : null
        });
      } catch (e) { 
        console.error("Gagal auto-assign Work Order", e); 
        alert("Peringatan: Paket berhasil diubah, tapi gagal masuk Penugasan.");
      }
    }

    const updatedPkg = response.data.data
    const index = packages.value.findIndex(p => p.id === updatedPkg.id)
    if (index !== -1) packages.value[index] = updatedPkg
    
    closeEditModal()
    showNotification('Status berhasil disimpan!', 'success')
  } catch (error) {
    showNotification('Gagal menyimpan status', 'error')
  } finally { 
    isSaving.value = false 
  }
}

const confirmDelete = (pkg) => {
  packageToDelete.value = pkg
  isDeleteModalOpen.value = true
}

const deletePackage = async () => {
  if (!packageToDelete.value) return
  isSaving.value = true
  try {
    await axios.delete(`/packages/${packageToDelete.value.id}`)
    packages.value = packages.value.filter(p => p.id !== packageToDelete.value.id)
    if (paginatedPackages.value.length === 0 && currentPage.value > 1) currentPage.value--
    isDeleteModalOpen.value = false
    showNotification('Paket dihapus!', 'success')
  } catch (error) { showNotification('Gagal menghapus', 'error') } 
  finally { isSaving.value = false }
}

const getStatusPengirimanClass = (status) => {
  switch(status) {
    case '1. Verifikasi Jastar': return 'bg-gray-100 text-gray-700 border-gray-300'
    case '2. Stor Struk ke farmasi': return 'bg-purple-50 text-purple-700 border-purple-200'
    case '3. Ambil paket obat farmasi': return 'bg-indigo-50 text-indigo-700 border-indigo-200'
    case '4. Diserah paket obat jastar': return 'bg-teal-50 text-teal-700 border-teal-200'
    case '5. Sedang menunggu kurir': return 'bg-orange-50 text-orange-700 border-orange-200'
    case '6. Diserahkan ke kurir': return 'bg-blue-50 text-blue-700 border-blue-200'
    case '7. Dalam perjalanan': return 'bg-yellow-50 text-yellow-700 border-yellow-200'
    case '8. Sampai (Selesai)': return 'bg-green-100 text-green-800 border-green-300'
    case '9. Cancel / Pending': return 'bg-red-50 text-red-700 border-red-200 line-through'
    default: return 'bg-gray-100 text-gray-800'
  }
}
// --- FUNGSI WARNA RESI BERDASARKAN WAKTU PENGANTARAN ---
const getWaktuResiClass = (waktu) => {
  // debugger
  if (waktu === 'Segera') return 'bg-red-50 text-red-700 border-red-200'
  if (waktu === 'Malam') return 'bg-emerald-50 text-emerald-700 border-emerald-200'
  return 'bg-blue-50 text-blue-700 border-blue-200' // Besok & Default
}
const getStatusPembayaranClass = (status) => {
  if (status === 'Lunas') return 'bg-green-100 text-green-800 border-green-200'
  return 'bg-red-50 text-red-700 border-red-200'
}

const showNotification = (message, type = 'success') => {
  notification.value = { show: true, message, type }
  setTimeout(() => { notification.value.show = false }, 3000)
}

onMounted(() => { fetchPackages() })
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

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
