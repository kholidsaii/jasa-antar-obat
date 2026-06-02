<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative min-h-[500px]">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Daftar Semua Paket</h2>
        <p class="text-sm text-gray-500 mt-1">Monitor status pesanan obat dan pengiriman secara real-time.</p>
      </div>
      <div v-if="userRole === 'kurir' && pesananMenunggu.length > 0" class="mx-6 mt-6 mb-2 bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-lg shadow-sm flex justify-between items-center animate-pulse">
        <div>
          <h3 class="text-indigo-800 font-bold text-sm"><i class="fas fa-bell mr-2"></i> Orderan Baru Siap Diambil!</h3>
          <p class="text-indigo-600 text-xs mt-1">Ada <strong>{{ pesananMenunggu.length }} paket</strong> dari farmasi yang menunggu untuk diantar.</p>
        </div>
      </div>
      <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-72">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari ID, struk, obat, atau pasien..." 
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] transition duration-150"
          >
        </div>
        <button @click="fetchPackages" :disabled="isLoading" class="text-sm bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center disabled:opacity-50 w-full sm:w-auto justify-center whitespace-nowrap shadow-sm">
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
            <th scope="col" class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">ID Resi & Tracking</th>
            <th scope="col" class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Pasien</th>
            <th scope="col" class="px-5 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Paket / Obat</th>
            <th scope="col" class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Total Harga</th>
            <th scope="col" class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status Pengiriman</th>
            <th scope="col" class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status Pembayaran</th>
            <th scope="col" class="px-5 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Metode</th>
            <th scope="col" class="px-5 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
          </tr>
        </thead>
        
        <tbody v-if="isLoading" class="bg-white divide-y divide-gray-200">
          <tr v-for="i in 5" :key="i" class="animate-pulse">
            <td colspan="8" class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-full"></div></td>
          </tr>
        </tbody>

        <tbody v-else-if="filteredPackages.length === 0" class="bg-white">
          <tr>
            <td colspan="8" class="px-6 py-12 text-center text-gray-500 font-medium">Tidak ada data paket yang ditemukan.</td>
          </tr>
        </tbody>

        <tbody v-else class="bg-white divide-y divide-gray-100">
          <tr v-for="pkg in paginatedPackages" :key="pkg.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-5 py-4 whitespace-nowrap">
              <span class="text-sm font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded border border-gray-200 inline-block mb-1">
                #PKT-{{ String(pkg.id).padStart(4, '0') }}{{ pkg.no_struk ? '-' + pkg.no_struk : '' }}
              </span>
              <br>
              <a :href="`/tracking/PKT-${String(pkg.id).padStart(4, '0')}${pkg.no_struk ? '-' + pkg.no_struk : ''}`" target="_blank" class="text-[10px] text-blue-600 hover:text-blue-800 hover:underline flex items-center mt-1">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Lihat Tracking Publik
              </a>
            </td>

            <td class="px-5 py-4 whitespace-nowrap">
              <div class="text-sm font-semibold text-gray-900">{{ pkg.customer?.nama || 'Dihapus' }}</div>
              <div class="text-xs text-gray-500 mt-0.5"><i class="fas fa-phone-alt mr-1"></i> {{ pkg.customer?.no_telp || '-' }}</div>
            </td>
            <td class="px-5 py-4 text-sm text-gray-600 whitespace-normal min-w-[150px] max-w-[200px] break-words">
              {{ pkg.deskripsi_pesanan }}
            </td>
            <td class="px-5 py-4 whitespace-nowrap text-center text-sm font-bold text-[#3b5998]">
              {{ pkg.total_harga ? formatRupiah(pkg.total_harga) : '-' }}
            </td>
            <td class="px-5 py-4 whitespace-nowrap text-center">
              <span :class="getStatusPengirimanClass(pkg.status_pengiriman)" class="px-3 py-1.5 inline-flex text-xs leading-4 font-bold rounded-full border">
                {{ pkg.status_pengiriman }}
              </span>
            </td>
            <td class="px-5 py-4 whitespace-nowrap text-center">
              <span :class="getStatusPembayaranClass(pkg.status_pembayaran)" class="px-3 py-1.5 inline-flex text-xs leading-4 font-bold rounded-full border">
                {{ pkg.status_pembayaran }}
              </span>
            </td>
            <td class="px-5 py-4 whitespace-nowrap text-center text-sm text-gray-700 font-medium">
              <span class="uppercase tracking-wide border bg-gray-100 px-2 py-1 rounded">{{ pkg.metode_pembayaran || 'Tunai / Cash' }}</span>
            </td>
            <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium">
              
              <button @click="copyTrackingLink(pkg)" class="text-emerald-600 hover:text-emerald-900 mr-2 transition-colors bg-emerald-50 p-1.5 rounded-lg border border-emerald-200" title="Salin Link Tracking">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
              </button>

              <button @click="openEditModal(pkg)" class="text-[#3b5998] hover:text-blue-900 mr-2 transition-colors bg-blue-50 p-1.5 rounded-lg border border-blue-200" title="Update Status">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              </button>

              <button v-if="['superadmin', 'admin'].includes(userRole)" @click="confirmDelete(pkg)" class="text-red-500 hover:text-red-700 transition-colors bg-red-50 p-1.5 rounded-lg border border-red-200" title="Hapus Paket">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="filteredPackages.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
      <span class="text-sm text-gray-500 font-medium text-center sm:text-left">
        Menampilkan {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredPackages.length) }} dari {{ filteredPackages.length }} paket
      </span>
      <div class="flex items-center space-x-2 w-full sm:w-auto justify-between sm:justify-end">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 font-medium transition-colors shadow-sm flex-1 sm:flex-none">Sebelumnya</button>
        <span class="px-3 text-sm font-bold text-gray-700 whitespace-nowrap">{{ currentPage }} / {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 font-medium transition-colors shadow-sm flex-1 sm:flex-none">Selanjutnya</button>
      </div>
    </div>

    <div v-if="isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Update Status Paket</h3>
            <p class="text-sm text-gray-500">#PKT-{{ String(editForm.id).padStart(4, '0') }} - {{ editForm.customer?.nama }}</p>
          </div>
          <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="updatePackage" class="space-y-4">
            
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Obat / Pesanan</label>
              <textarea v-model="editForm.deskripsi_pesanan" :disabled="userRole === 'kurir'" rows="2" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] disabled:bg-gray-100 disabled:text-gray-500 outline-none"></textarea>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Pengiriman</label>
                <select v-model="editForm.status_pengiriman" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none bg-white">
                  
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
                    <option value="9. Cancel / Pending" class="text-red-600 font-bold">9. Cancel / Pending</option>
                  </template>
                </select>
                <p v-if="editForm.status_pengiriman === 'Dibatalkan' && ['superadmin', 'admin', 'kurir'].includes(userRole)" class="text-[10px] text-red-500 mt-1">Otomatis mencabut tugas kurir.</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Pembayaran</label>
                <select v-model="editForm.status_pembayaran" :disabled="userRole === 'farmasi' || userRole === 'kurir'" required class="w-full border border-gray-300 rounded-lg p-2.5 disabled:bg-gray-100 disabled:text-gray-500 outline-none bg-white">
                  <option value="Belum Lunas">Belum Lunas</option>
                  <option value="Lunas">Lunas</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Metode Pembayaran</label>
                <select v-model="editForm.metode_pembayaran" required class="w-full border rounded-lg p-2.5 outline-none bg-white">
                  <option value="Tunai / Cash (Sistem)">Tunai / Cash (Sistem)</option>
                  <option value="Transfer Bank (Sistem)">Transfer Bank (Sistem)</option>
                  <option value="QRIS / E-Wallet (Sistem)">QRIS / E-Wallet (Sistem)</option>
                  <option v-if="['superadmin', 'admin'].includes(userRole)" value="Gratis / Amal" class="text-purple-600 font-bold">Gratis / Amal</option>
                </select>
              </div>
              
              <div v-if="['superadmin', 'admin'].includes(userRole)" class="sm:col-span-2 pt-2 border-t mt-2">
                <label class="block text-sm font-bold text-gray-800 mb-1">Ubah Total Tagihan (Rp)</label>
                <input v-model="editForm.total_harga" type="number" class="w-full border border-blue-300 bg-white text-[#3b5998] font-black text-lg rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-[#3b5998]">
              </div>
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="closeEditModal" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
            Batal
          </button>
          <button @click="updatePackage" :disabled="isSaving" type="button" :class="['px-5 py-2.5 rounded-lg text-white font-medium transition-colors shadow-sm disabled:opacity-50', editForm.status_pengiriman === 'Dibatalkan' ? 'bg-red-600 hover:bg-red-700' : 'bg-[#3b5998] hover:bg-blue-800']">
            {{ isSaving ? 'Memproses...' : 'Update Status' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isDeleteModalOpen && ['superadmin', 'admin'].includes(userRole)" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="isDeleteModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden">
        <div class="p-6">
          <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Hapus Permanen Paket</h3>
          <p class="text-sm text-gray-500 text-center">Anda yakin ingin menghapus <strong>#PKT-{{ packageToDelete?.id ? String(packageToDelete.id).padStart(4, '0') : '' }}</strong>?</p>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="isDeleteModalOpen = false" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium">Batal</button>
          <button @click="deletePackage" :disabled="isSaving" type="button" class="px-5 py-2.5 bg-red-600 rounded-lg text-white font-medium hover:bg-red-700 disabled:opacity-50">
            {{ isSaving ? 'Menghapus...' : 'Ya, Hapus' }}
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

const API_URL = '/packages' 

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
    const noStruk = pkg.no_struk?.toLowerCase() || '' // Membaca kolom no_struk
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
// ------------------------

const isEditModalOpen = ref(false)
const editForm = ref({ id: null, customer_id: null, deskripsi_pesanan: '', status_pengiriman: '', status_pembayaran: '', metode_pembayaran: '', customer: null })

const isDeleteModalOpen = ref(false)
const packageToDelete = ref(null)

const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
}

const fetchPackages = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    packages.value = response.data.data 
  } catch (error) { showNotification('Gagal mengambil data paket dari server', 'error') } 
  finally { isLoading.value = false }
}

// Fitur Copy Link Baru
const copyTrackingLink = (pkg) => {
  const resiStr = 'PKT-' + String(pkg.id).padStart(4, '0') + (pkg.no_struk ? '-' + pkg.no_struk : '')
  const trackingLink = `${window.location.origin}/tracking/${resiStr}`
  
  navigator.clipboard.writeText(trackingLink).then(() => {
    showNotification(`Link pelacakan ${resiStr} berhasil disalin!`, 'success')
  }).catch(err => {
    alert("Gagal menyalin link. Browser Anda mungkin memblokir akses clipboard.")
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
      metode_pembayaran: editForm.value.metode_pembayaran
    }
    // Update data paketnya
    const response = await axios.put(`http://localhost:8000/api/v1/packages/${editForm.value.id}`, payload)
    
debugger
    if (editForm.value.status_pengiriman === '6. Diserahkan ke kurir') {
      try {
        const currentUserData = JSON.parse(localStorage.getItem('user'));
        
        // Cari kendaraan kurir tersebut
        const vehRes = await axios.get('http://localhost:8000/api/v1/vehicles');
        const myVeh = vehRes.data.data.find(v => v.user_id === currentUserData.id);
        
        // POST ke Works
        await axios.post('http://localhost:8000/api/v1/works', {
          package_ids: [editForm.value.id],
          user_id: currentUserData.id,
          vehicle_id: myVeh ? myVeh.id : null
        });
      } catch (e) { 
        console.error("Gagal auto-assign Work Order", e.response?.data || e); 
        alert("Peringatan: Paket berhasil diubah, tapi gagal masuk Penugasan. Cek kembali akses backend.");
      }
    }

    const updatedPkg = response.data.data
    const index = packages.value.findIndex(p => p.id === updatedPkg.id)
    if (index !== -1) packages.value[index] = updatedPkg
    
    closeEditModal()
    showNotification('Data Paket berhasil diupdate!', 'success')
  } catch (error) {
    showNotification('Gagal mengupdate paket', 'error')
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
    await axios.delete(`${API_URL}/${packageToDelete.value.id}`)
    packages.value = packages.value.filter(p => p.id !== packageToDelete.value.id)
    if (paginatedPackages.value.length === 0 && currentPage.value > 1) currentPage.value--
    isDeleteModalOpen.value = false
    showNotification('Paket berhasil dihapus!', 'success')
  } catch (error) { showNotification('Gagal menghapus paket', 'error') } 
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