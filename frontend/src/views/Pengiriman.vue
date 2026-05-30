<template>
  <div class="max-w-7xl mx-auto pb-10">
    
    <div class="bg-white rounded-t-xl overflow-hidden shadow-sm border border-gray-100">
      <div 
        class="h-48 w-full bg-cover bg-center" 
        style="background-image: url('https://images.unsplash.com/photo-1449824913935-59a10b8d2000?q=80&w=2070&auto=format&fit=crop');"
      ></div>

      <div class="px-6 sm:px-8 pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-12 sm:-mt-16 mb-4 sm:mb-0">
          <div class="bg-white p-1.5 rounded-2xl shadow-md border border-gray-100 inline-block">
            <img 
              src="https://api.dicebear.com/7.x/bottts/svg?seed=Delivery&backgroundColor=e2e8f0" 
              alt="Maskot Pengiriman" 
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-100"
            />
          </div>
          <div class="ml-5 pb-2">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">PENGIRIMAN</h1>
            <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">Manajemen Data & Operasional</p>
          </div>
        </div>

        <div class="flex space-x-3 pb-2">
          <button @click="openModalPaket" class="bg-[#3b5998] hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Paket
          </button>
          <button @click="openModalCustomer" class="bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Add Customer
          </button>
        </div>
      </div>

      <div class="px-6 sm:px-8 border-t border-gray-100">
        <nav class="flex space-x-8 overflow-x-auto" aria-label="Tabs">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id 
                ? 'border-[#3b5998] text-[#3b5998]' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'group inline-flex items-center py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap'
            ]"
          >
            <component 
              :is="tab.icon" 
              :class="[
                activeTab === tab.id ? 'text-[#3b5998]' : 'text-gray-400 group-hover:text-gray-500',
                '-ml-0.5 mr-2 h-5 w-5'
              ]" 
              aria-hidden="true" 
            />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-6">
      <Overview v-if="activeTab === 'overview'" />
      <AllPaket v-if="activeTab === 'all-paket'" />
      <Customer v-if="activeTab === 'customer'" />
      <Alamat v-if="activeTab === 'alamat'" />
    </div>

    <div v-if="isModalCustomerOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModalCustomer"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-5 border-b border-gray-100">
          <h3 class="text-lg font-bold text-gray-900">Tambah Data Customer</h3>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="saveCustomer" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
              <input v-model="formCustomer.nama" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] outline-none">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telephone</label>
              <input v-model="formCustomer.no_telp" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Kelamin</label>
                <select v-model="formCustomer.jenis_kelamin" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] outline-none bg-white">
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Umur (Tahun)</label>
                <input v-model="formCustomer.umur" type="number" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] outline-none">
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
              <textarea v-model="formCustomer.alamat" rows="3" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] outline-none"></textarea>
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="closeModalCustomer" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
            Batal
          </button>
          <button @click="saveCustomer" :disabled="isSaving" type="button" class="px-5 py-2.5 bg-[#3b5998] rounded-lg text-white font-medium hover:bg-blue-800 transition-colors disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Simpan Customer' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isModalPaketOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModalPaket"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-5 border-b border-gray-100">
          <h3 class="text-lg font-bold text-gray-900">Tambah Paket Pengiriman</h3>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="savePaket" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Customer (Pasien)</label>
              <select v-model="formPaket.customer_id" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] outline-none bg-white">
                <option disabled value="">-- Pilih Customer Terdaftar --</option>
                <option v-for="cust in customersList" :key="cust.id" :value="cust.id">
                  {{ cust.nama }} - {{ cust.no_telp }}
                </option>
              </select>
              <p v-if="customersList.length === 0" class="text-xs text-red-500 mt-1">Data customer kosong. Tambah customer terlebih dahulu.</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Obat / Pesanan</label>
              <textarea v-model="formPaket.deskripsi_pesanan" rows="2" required placeholder="Contoh: Paracetamol, Vitamin C..." class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] outline-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Pembayaran</label>
                <select v-model="formPaket.status_pembayaran" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] outline-none bg-white">
                  <option value="Belum Lunas">Belum Lunas</option>
                  <option value="Lunas">Lunas</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Awal</label>
                <input v-model="formPaket.status_pengiriman" type="text" disabled class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-100 text-gray-500 font-medium">
              </div>
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="closeModalPaket" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
            Batal
          </button>
          <button @click="savePaket" :disabled="!formPaket.customer_id || isSaving" type="button" class="px-5 py-2.5 bg-[#3b5998] rounded-lg text-white font-medium hover:bg-blue-800 transition-colors disabled:opacity-50">
            {{ isSaving ? 'Menyimpan...' : 'Simpan Paket' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, markRaw, onMounted } from 'vue'
import axios from 'axios'

// Komponen Tab
import Overview from '../components/pengiriman/Overview.vue'
import AllPaket from '../components/pengiriman/AllPaket.vue'
import Customer from '../components/pengiriman/Customer.vue'
import Alamat from '../components/pengiriman/Alamat.vue'

// --- SETUP IKON ---
const EyeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>` })
const CheckBadgeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>` })
const UsersIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>` })
const MapPinIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>` })

const tabs = [
  { id: 'overview', name: 'Overview', icon: EyeIcon },
  { id: 'all-paket', name: 'All Paket', icon: CheckBadgeIcon },
  { id: 'customer', name: 'Customer', icon: UsersIcon },
  { id: 'alamat', name: 'Alamat', icon: MapPinIcon },
]

// --- STATE MANAGEMENT ---
const activeTab = ref('all-paket')
const isModalCustomerOpen = ref(false)
const isModalPaketOpen = ref(false)
const isSaving = ref(false)
const customersList = ref([]) // Untuk select dropdown di form Add Paket

const formCustomer = ref({
  nama: '', no_telp: '', jenis_kelamin: 'Laki-laki', umur: null, alamat: ''
})

const formPaket = ref({
  customer_id: '', deskripsi_pesanan: '', status_pengiriman: 'Pesanan diverifikasi', status_pembayaran: 'Belum Lunas'
})

// --- METHODS ---
const fetchCustomersForDropdown = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/v1/customers')
    customersList.value = response.data.data
  } catch (error) {
    console.error("Gagal mengambil data customer:", error)
  }
}

const openModalCustomer = () => { isModalCustomerOpen.value = true }
const closeModalCustomer = () => { 
  isModalCustomerOpen.value = false
  formCustomer.value = { nama: '', no_telp: '', jenis_kelamin: 'Laki-laki', umur: null, alamat: '' }
}

const openModalPaket = () => { 
  fetchCustomersForDropdown() // Reload list customer terbaru sebelum buka modal
  isModalPaketOpen.value = true 
}
const closeModalPaket = () => { 
  isModalPaketOpen.value = false
  formPaket.value = { customer_id: '', deskripsi_pesanan: '', status_pengiriman: 'Pesanan diverifikasi', status_pembayaran: 'Belum Lunas' }
}

// ACTION: Simpan Customer ke API
const saveCustomer = async () => {
  if(!formCustomer.value.nama || !formCustomer.value.alamat) return alert('Nama & Alamat Wajib Diisi!');
  
  isSaving.value = true
  try {
    await axios.post('http://localhost:8000/api/v1/customers', formCustomer.value)
    closeModalCustomer()
    alert('Data Customer Berhasil Disimpan di Database!')
    
    // Jika sedang buka tab customer, force refresh dengan merubah state tab sesaat
    if (activeTab.value === 'customer') {
      activeTab.value = ''
      setTimeout(() => activeTab.value = 'customer', 10)
    }
  } catch (error) {
    console.error(error)
    alert('Gagal menyimpan data customer!')
  } finally {
    isSaving.value = false
  }
}

// ACTION: Simpan Paket ke API
const savePaket = async () => {
  if(!formPaket.value.customer_id || !formPaket.value.deskripsi_pesanan) return alert('Pilih Customer & Isi Deskripsi!');
  
  isSaving.value = true
  try {
    await axios.post('http://localhost:8000/api/v1/packages', formPaket.value)
    closeModalPaket()
    alert('Data Paket Berhasil Dibuat di Database!')
    
    // Pindah otomatis ke tab all-paket dan force refresh
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'all-paket', 10)
  } catch (error) {
    console.error(error)
    alert('Gagal menyimpan data paket!')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchCustomersForDropdown()
})
</script>