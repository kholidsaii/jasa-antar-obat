<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Database Customer</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola data pasien penerima layanan antar obat.</p>
      </div>
      <div class="relative w-full sm:w-72">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input v-model="searchQuery" type="text" placeholder="Cari nama atau telepon..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-[#3b5998] sm:text-sm outline-none transition duration-150">
      </div>
      </div>

    <div v-if="notification.show" :class="['px-6 py-3 text-sm font-medium text-white transition-all', notification.type === 'success' ? 'bg-green-500' : 'bg-red-500']">
      {{ notification.message }}
    </div>

    <div class="overflow-x-auto flex-1">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Nama Pasien</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Kontak & Info</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase">Alamat Rute & Patokan</th>
            <th v-if="userRole !== 'kurir'" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase">Aksi</th>
          </tr>
        </thead>
        <tbody v-if="isLoading" class="bg-white divide-y divide-gray-200">
          <tr v-for="i in 5" :key="i" class="animate-pulse">
            <td :colspan="userRole !== 'kurir' ? 4 : 3" class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-full"></div></td>
          </tr>
        </tbody>
        <tbody v-else-if="filteredCustomers.length === 0" class="bg-white">
          <tr><td :colspan="userRole !== 'kurir' ? 4 : 3" class="px-6 py-12 text-center text-gray-500 font-medium">Tidak ada data pasien.</td></tr>
        </tbody>
        <tbody v-else class="bg-white divide-y divide-gray-100">
          <tr v-for="customer in paginatedCustomers" :key="customer.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-bold text-gray-900">{{ customer.nama }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-600 font-mono"><i class="fas fa-phone-alt mr-1"></i> {{ customer.no_telp }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ customer.jenis_kelamin }} • {{ customer.umur }} Tahun</div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm font-bold text-gray-800 line-clamp-2 max-w-xs">{{ customer.alamat }}</div>
              <div class="text-xs text-gray-500 mt-0.5 max-w-xs truncate">Patokan: {{ customer.detail_alamat || '-' }}</div>
              
              <span v-if="customer.lat && customer.lng" class="text-[11px] font-bold text-green-600 mt-1.5 block bg-green-50 px-2 py-0.5 rounded w-fit">
                <i class="fas fa-check-circle mr-1"></i> Koordinat Tersimpan
              </span>
              <span v-else class="text-[11px] font-bold text-red-500 mt-1.5 block bg-red-50 px-2 py-0.5 rounded w-fit">
                <i class="fas fa-exclamation-triangle mr-1"></i> Gagal Dilacak
              </span>
            </td>
            
            <td v-if="userRole !== 'kurir'" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
              <button @click="openEditModal(customer)" class="text-[#3b5998] hover:text-blue-900 bg-blue-50 p-1.5 rounded-lg border border-blue-200 mr-2 transition-colors" title="Edit">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
              </button>
              <button @click="confirmDelete(customer)" class="text-red-500 hover:text-red-700 bg-red-50 p-1.5 rounded-lg border border-red-200 transition-colors" title="Hapus">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="filteredCustomers.length > 0" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center gap-4">
      <span class="text-sm text-gray-500 font-medium">Menampilkan {{ startIndex + 1 }} - {{ Math.min(endIndex, filteredCustomers.length) }} dari {{ filteredCustomers.length }} data</span>
      <div class="flex space-x-2">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50">Sebelumnya</button>
        <span class="px-2 text-sm font-bold text-gray-700 mt-2">{{ currentPage }} / {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50">Selanjutnya</button>
      </div>
    </div>

    <div v-if="isModalOpen || isEditModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isModalOpen ? closeModal() : closeEditModal()"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 overflow-y-auto max-h-[95vh]">
        <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
          <h3 class="text-lg font-bold text-gray-900">{{ isEditModalOpen ? 'Edit Data Customer' : 'Tambah Customer Baru' }}</h3>
          <button @click="isModalOpen ? closeModal() : closeEditModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        
        <form @submit.prevent="isEditModalOpen ? updateCustomer() : saveCustomer()" class="space-y-4">
          <div>
            <label class="block text-sm font-semibold mb-1">Nama Lengkap</label>
            <input v-model="(isEditModalOpen ? editForm : formCustomer).nama" type="text" required class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]">
          </div>
          <div>
            <label class="block text-sm font-semibold mb-1">No. Telephone</label>
            <input v-model="(isEditModalOpen ? editForm : formCustomer).no_telp" type="text" required class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold mb-1">Jenis Kelamin</label>
              <select v-model="(isEditModalOpen ? editForm : formCustomer).jenis_kelamin" required class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] bg-white">
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold mb-1">Umur (Tahun)</label>
              <input v-model="(isEditModalOpen ? editForm : formCustomer).umur" type="number" class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]">
            </div>
          </div>
          
          <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
            <label class="block text-sm font-bold text-gray-800 mb-1">1. Alamat Utama & Nama Jalan</label>
            <p class="text-[11px] text-gray-500 mb-2">Jalan, Kelurahan, Kecamatan, Kota. (Sistem melacak koordinat otomatis).</p>
            <textarea v-model="(isEditModalOpen ? editForm : formCustomer).alamat" rows="2" required placeholder="Contoh: Jl. RC Veteran Raya, Bintaro..." class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]"></textarea>
          </div>

          <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
            <label class="block text-sm font-bold text-blue-900 mb-1">2. Link Peta / Koordinat (Opsional)</label>
            <p class="text-[11px] text-blue-700 mb-2">Jika pelacakan meleset, paste URL OpenStreetMap di sini untuk akurasi.</p>
            <input v-model="(isEditModalOpen ? editForm : formCustomer).link_peta" type="text" placeholder="Contoh: https://www.openstreetmap.org/directions?from=..." class="w-full border border-blue-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]">
          </div>
          
          <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
            <label class="block text-sm font-bold text-gray-700 mb-1">3. Detail Patokan & RT/RW</label>
            <p class="text-[11px] text-gray-500 mb-2">Ciri-ciri rumah atau rute detail untuk kurir.</p>
            <textarea v-model="(isEditModalOpen ? editForm : formCustomer).detail_alamat" rows="2" required placeholder="Contoh: RT 02/07, Pagar Hitam..." class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]"></textarea>
          </div>

          <div class="pt-4 border-t flex justify-end space-x-3">
            <button type="button" @click="isModalOpen ? closeModal() : closeEditModal()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 transition-colors rounded-lg font-medium">Batal</button>
            <button type="submit" :disabled="isSaving" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
              {{ isSaving ? 'Menyimpan...' : 'Simpan Customer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="isDeleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="isDeleteModalOpen = false"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg></div>
        <h3 class="text-lg font-bold mb-2">Hapus Customer?</h3>
        <p class="text-sm text-gray-500 mb-6">Penghapusan ini bersifat permanen.</p>
        <div class="flex justify-center space-x-3">
          <button @click="isDeleteModalOpen = false" class="px-5 py-2 bg-gray-100 rounded-lg">Batal</button>
          <button @click="deleteCustomer" class="px-5 py-2 bg-red-600 text-white rounded-lg">Ya, Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

const currentUser = JSON.parse(localStorage.getItem('user')) || {}
const userRole = ref(currentUser.role || '')

const API_URL = 'http://127.0.0.1:8000/api/v1/customers'
const customers = ref([])
const isLoading = ref(true)
const isSaving = ref(false)
const notification = ref({ show: false, message: '', type: 'success' })

const searchQuery = ref('')
const currentPage = ref(1)
const itemsPerPage = 6 

const filteredCustomers = computed(() => {
  if (!searchQuery.value) return customers.value
  const query = searchQuery.value.toLowerCase()
  return customers.value.filter(c => c.nama.toLowerCase().includes(query) || c.no_telp.includes(query))
})

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredCustomers.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)
const paginatedCustomers = computed(() => filteredCustomers.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

const isModalOpen = ref(false)
const formCustomer = ref({ nama: '', no_telp: '', jenis_kelamin: 'Laki-laki', umur: null, alamat: '', link_peta: '', detail_alamat: '', lat: null, lng: null })

const isEditModalOpen = ref(false)
const editForm = ref({ id: '', nama: '', no_telp: '', jenis_kelamin: '', umur: null, alamat: '', link_peta: '', detail_alamat: '', lat: null, lng: null })

const isDeleteModalOpen = ref(false)
const customerToDelete = ref(null)

const showNotification = (message, type) => {
  notification.value = { show: true, message, type }
  setTimeout(() => notification.value.show = false, 3000)
}

const fetchCustomers = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    customers.value = response.data.data
  } catch (error) { showNotification('Gagal mengambil data', 'error') } finally { isLoading.value = false }
}

const openModal = () => isModalOpen.value = true
const closeModal = () => {
  isModalOpen.value = false
  formCustomer.value = { nama: '', no_telp: '', jenis_kelamin: 'Laki-laki', umur: null, alamat: '', link_peta: '', detail_alamat: '', lat: null, lng: null }
}

const openEditModal = (customer) => {
  editForm.value = { ...customer, link_peta: '' }
  isEditModalOpen.value = true
}
const closeEditModal = () => {
  isEditModalOpen.value = false
}

// Mengekspos fungsi openModal agar bisa dipanggil oleh parent (Pengiriman.vue)
defineExpose({
  openModal
})

const parseOSM = (input) => {
  if (!input) return null;
  const rawMatch = input.match(/^(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/);
  if (rawMatch) return { lat: rawMatch[1], lng: rawMatch[2] };
  const toMatch = input.match(/to=(-?\d+\.\d+)(?:%2C|,)(-?\d+\.\d+)/);
  if (toMatch) return { lat: toMatch[1], lng: toMatch[2] };
  const mlatMatch = input.match(/mlat=(-?\d+\.\d+)/);
  const mlonMatch = input.match(/mlon=(-?\d+\.\d+)/);
  if (mlatMatch && mlonMatch) return { lat: mlatMatch[1], lng: mlonMatch[1] };
  const mapMatch = input.match(/#map=\d+\/(-?\d+\.\d+)\/(-?\d+\.\d+)/);
  if (mapMatch) return { lat: mapMatch[1], lng: mapMatch[2] };
  return null;
}

const getCoordinatesFromAddress = async (alamat) => {
  try {
    const query = encodeURIComponent(`${alamat}, Jakarta, Indonesia`);
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1`);
    const data = await response.json();
    if (data && data.length > 0) return { lat: data[0].lat, lng: data[0].lon }
    return null;
  } catch (error) { return null; }
}

const getFinalCoordinates = async (alamat, link_peta) => {
  if (link_peta) {
    const coords = parseOSM(link_peta);
    if (coords) return coords;
    alert("Format Link OSM tidak valid! Sistem mencoba melacak dari Nama Jalan...");
  }
  return await getCoordinatesFromAddress(alamat);
}

const saveCustomer = async () => {
  if(!formCustomer.value.nama || !formCustomer.value.alamat) return alert('Nama & Alamat Wajib Diisi!');
  isSaving.value = true
  try {
    const coords = await getFinalCoordinates(formCustomer.value.alamat, formCustomer.value.link_peta);
    const payload = { ...formCustomer.value, lat: coords ? coords.lat : null, lng: coords ? coords.lng : null }
    const response = await axios.post(API_URL, payload)
    customers.value.unshift(response.data.data)
    closeModal()
    if(coords) showNotification('Customer & Titik Lokasi berhasil disimpan!', 'success')
    else showNotification('Customer tersimpan, namun titik lokasi gagal dilacak.', 'warning')
  } catch (error) { showNotification('Gagal menyimpan data', 'error') } finally { isSaving.value = false }
}

const updateCustomer = async () => {
  if(!editForm.value.nama || !editForm.value.alamat) return alert("Nama & Alamat Wajib Diisi!");
  isSaving.value = true
  try {
    let coords = { lat: editForm.value.lat, lng: editForm.value.lng };
    if (editForm.value.link_peta || !coords.lat) {
       const newCoords = await getFinalCoordinates(editForm.value.alamat, editForm.value.link_peta);
       if (newCoords) coords = newCoords;
    }
    const payload = { ...editForm.value, lat: coords.lat, lng: coords.lng }
    const response = await axios.put(`${API_URL}/${editForm.value.id}`, payload)
    const index = customers.value.findIndex(c => c.id === editForm.value.id)
    if (index !== -1) customers.value[index] = response.data.data
    closeEditModal()
    showNotification('Data Customer berhasil diupdate!', 'success')
  } catch (error) { showNotification('Gagal mengupdate data', 'error') } finally { isSaving.value = false }
}

const confirmDelete = (customer) => {
  customerToDelete.value = customer
  isDeleteModalOpen.value = true
}

const deleteCustomer = async () => {
  if (!customerToDelete.value) return
  isSaving.value = true
  try {
    await axios.delete(`${API_URL}/${customerToDelete.value.id}`)
    customers.value = customers.value.filter(c => c.id !== customerToDelete.value.id)
    if (paginatedCustomers.value.length === 0 && currentPage.value > 1) currentPage.value--
    isDeleteModalOpen.value = false
    showNotification('Customer berhasil dihapus.', 'success')
  } catch (error) { showNotification('Gagal menghapus data', 'error') } finally { isSaving.value = false }
}

onMounted(() => fetchCustomers())
</script>