<template>
  <div class="max-w-7xl mx-auto pb-10">
    <div class="bg-white rounded-t-xl overflow-hidden shadow-sm border border-gray-100">
      <div class="h-48 w-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1449824913935-59a10b8d2000?q=80&w=2070&auto=format&fit=crop');"></div>
      <div class="px-6 sm:px-8 pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-12 sm:-mt-16 mb-4 sm:mb-0">
          <div class="bg-white p-1.5 rounded-2xl shadow-md border border-gray-100 inline-block">
            <img src="https://api.dicebear.com/7.x/bottts/svg?seed=Delivery&backgroundColor=e2e8f0" alt="Maskot" class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-100"/>
          </div>
          <div class="ml-5 pb-2">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">PENGIRIMAN</h1>
            <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">Manajemen Data & Operasional</p>
          </div>
        </div>
        
        <div class="flex space-x-3 pb-2" v-if="userRole !== 'kurir'">
          <button @click="openModalPaket" class="bg-[#3b5998] hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add Paket
          </button>
          <button @click="openModalCustomer" class="bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> Add Customer
          </button>
        </div>
      </div>
      
      <div class="px-6 sm:px-8 border-t border-gray-100">
        <nav class="flex space-x-8 overflow-x-auto" aria-label="Tabs">
          <button v-for="tab in allowedTabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[activeTab === tab.id ? 'border-[#3b5998] text-[#3b5998]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'group inline-flex items-center py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap']">
            <component :is="tab.icon" :class="[activeTab === tab.id ? 'text-[#3b5998]' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 h-5 w-5']" aria-hidden="true" />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-6">
      <Overview v-if="activeTab === 'overview'" />
      <AllPaket v-if="activeTab === 'all-paket'" />
      <Customer v-if="activeTab === 'customer'" ref="customerRef" />
      <Alamat v-if="activeTab === 'alamat'" />
    </div>

    <div v-if="isModalPaketOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeModalPaket"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[95vh]">
        
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Tambah Paket Pengiriman</h3>
            <p class="text-xs text-gray-500 mt-1">Sistem otomatis menghitung tarif berdasarkan jarak peta.</p>
          </div>
          <button @click="closeModalPaket" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="savePaket" class="space-y-5">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Customer (Pasien)</label>
              <select v-model="formPaket.customer_id" @change="handleCustomerSelect" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-[#3b5998] outline-none">
                <option disabled value="">-- Pilih Customer Terdaftar --</option>
                <option v-for="cust in customersList" :key="cust.id" :value="cust.id">{{ cust.nama }} - {{ cust.no_telp }}</option>
              </select>
            </div>
            
            <div v-show="formPaket.customer_id" class="border border-gray-200 rounded-xl p-4 bg-gray-50/50 relative overflow-hidden">
              <div v-if="isCalculating" class="absolute inset-0 bg-white/80 backdrop-blur-[2px] flex flex-col items-center justify-center z-10">
                <svg class="animate-spin h-8 w-8 text-[#3b5998] mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-bold text-[#3b5998]">Menghitung Rute & Harga...</span>
              </div>

              <div class="mb-4 bg-white p-3 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 font-bold mb-1">Alamat Tujuan Pengiriman</p>
                <p class="text-sm text-gray-800 font-medium leading-relaxed">{{ selectedCustomerAddress || '-' }}</p>
              </div>

              <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Jarak Tempuh Peta</span>
                  <span class="font-medium text-gray-900">{{ formPaket.jarak_km ? formPaket.jarak_km + ' km' : '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Rekomendasi Tarif Dasar</span>
                  <span class="font-medium text-gray-900">{{ formatRupiah(rincianHarga.baseHarga) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Biaya Admin Operasional</span>
                  <span class="font-medium text-gray-900">{{ formatRupiah(rincianHarga.admin) }}</span>
                </div>
              </div>
              
              <div class="pt-3 border-t border-gray-200">
                <label class="block text-sm font-bold text-gray-800 mb-1">Total Tagihan Keseluruhan (Rp)</label>
                <input 
                  v-model="formPaket.total_harga" 
                  type="number" 
                  :readonly="!isAdmin" 
                  :class="['w-full border rounded-lg p-2.5 font-black text-lg focus:outline-none focus:ring-2 focus:ring-[#3b5998]', isAdmin ? 'bg-white text-[#3b5998] border-blue-300' : 'bg-transparent border-transparent text-gray-700']"
                />
                <p v-if="isAdmin" class="text-[10px] text-blue-600 mt-1">Hak akses Admin: Anda dapat merubah total tagihan di atas secara manual.</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi Obat / Pesanan</label>
              <textarea v-model="formPaket.deskripsi_pesanan" rows="2" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] outline-none"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status Pembayaran</label>
                <select v-model="formPaket.status_pembayaran" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-[#3b5998] outline-none">
                  <option value="Belum Lunas">Belum Lunas</option>
                  <option value="Lunas">Lunas</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode (Kas Sistem)</label>
                <select v-model="formPaket.metode_pembayaran" @change="handleMetodeGratis" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-[#3b5998] outline-none">
                  <option value="Tunai / Cash (Sistem)">Tunai / Cash (Sistem)</option>
                  <option value="Transfer Bank (Sistem)">Transfer Bank (Sistem)</option>
                  <option value="QRIS / E-Wallet (Sistem)">QRIS / E-Wallet (Sistem)</option>
                  <option v-if="isAdmin" value="Gratis / Amal" class="text-purple-600 font-bold">Gratis / Amal (Khusus Admin)</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="closeModalPaket" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Batal</button>
          <button @click="savePaket" :disabled="!formPaket.customer_id || isSaving" type="button" class="px-5 py-2.5 bg-[#3b5998] rounded-lg text-white hover:bg-blue-800 disabled:opacity-50 font-medium shadow-sm">
            {{ isSaving ? 'Memproses...' : 'Buat Paket' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, markRaw, onMounted, nextTick } from 'vue'
import axios from 'axios'
import Overview from '../components/pengiriman/Overview.vue'
import AllPaket from '../components/pengiriman/AllPaket.vue'
import Customer from '../components/pengiriman/Customer.vue'
import Alamat from '../components/pengiriman/Alamat.vue'

const currentUser = JSON.parse(localStorage.getItem('user')) || {}
const userRole = ref(currentUser.role || '')
const isAdmin = computed(() => ['admin', 'superadmin'].includes(userRole.value))

const EyeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>` })
const CheckBadgeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>` })
const UsersIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>` })
const MapPinIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>` })

const allTabs = [
  { id: 'overview', name: 'Overview', icon: EyeIcon, roles: ['superadmin', 'admin'] },
  { id: 'all-paket', name: 'All Paket', icon: CheckBadgeIcon, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] },
  { id: 'customer', name: 'Customer', icon: UsersIcon, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] },
  { id: 'alamat', name: 'Alamat', icon: MapPinIcon, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] },
]

const allowedTabs = computed(() => allTabs.filter(tab => tab.roles.includes(userRole.value)))
const activeTab = ref(allowedTabs.value.some(t => t.id === 'all-paket') ? 'all-paket' : allowedTabs.value[0]?.id || '')

const RUMAH_SAKIT_COORD = [-6.271362, 106.764780] 
const isModalPaketOpen = ref(false)
const isCalculating = ref(false)
const isSaving = ref(false)
const customersList = ref([]) 
const selectedCustomerAddress = ref('')

// Referensi ke komponen Customer untuk memanggil fungsi dari dalam
const customerRef = ref(null)

const formPaket = ref({ customer_id: '', deskripsi_pesanan: '', status_pengiriman: 'Pesanan diverifikasi', status_pembayaran: 'Belum Lunas', metode_pembayaran: 'Tunai / Cash (Sistem)', jarak_km: null, total_harga: 0 })
const rincianHarga = ref({ baseHarga: 0, admin: 0 })

const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)

const fetchCustomersForDropdown = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/v1/customers')
    customersList.value = response.data.data
  } catch (error) { console.error("Gagal mengambil data:", error) }
}

const openModalPaket = async () => { 
  await fetchCustomersForDropdown() 
  isModalPaketOpen.value = true 
}

const closeModalPaket = () => { 
  isModalPaketOpen.value = false
  formPaket.value = { customer_id: '', deskripsi_pesanan: '', status_pengiriman: 'Pesanan diverifikasi', status_pembayaran: 'Belum Lunas', metode_pembayaran: 'Tunai / Cash (Sistem)', jarak_km: null, total_harga: 0 }
  rincianHarga.value = { baseHarga: 0, admin: 0 }
  selectedCustomerAddress.value = ''
}

// FUNGSI BARU: Pindah tab dan buka modal dari komponen anak
const openModalCustomer = async () => {
  activeTab.value = 'customer'
  // Tunggu Vue me-render komponen Customer terlebih dahulu
  await nextTick() 
  if (customerRef.value) {
    customerRef.value.openModal()
  }
}

const handleMetodeGratis = () => {
  if(formPaket.value.metode_pembayaran === 'Gratis / Amal') {
    alert("Sistem akan mencatat transaksi ini sebagai Sedekah/Amal senilai Total Tagihan.");
  }
}

const handleCustomerSelect = async () => {
  const customer = customersList.value.find(c => c.id == formPaket.value.customer_id)
  if(!customer) return;

  selectedCustomerAddress.value = customer.detail_alamat || customer.alamat;
  isCalculating.value = true;

  if (!customer.lat || !customer.lng) {
    alert("Koordinat tidak ditemukan di Database!");
    isCalculating.value = false;
    return;
  }

  try {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${RUMAH_SAKIT_COORD[1]},${RUMAH_SAKIT_COORD[0]};${customer.lng},${customer.lat}?overview=false`;
    const response = await fetch(osrmUrl);
    const data = await response.json();
    
    if (data.code === 'Ok') {
      const rute = data.routes[0];
      const jarakKm = parseFloat((rute.distance / 1000).toFixed(1));
      
      let baseHarga = 20000;
      if (jarakKm > 5.0) {
        const extraKm = Math.ceil(jarakKm) - 5;
        baseHarga += (extraKm * 5000);
      }
      const admin = 1500;
      
      rincianHarga.value = { baseHarga, admin }
      formPaket.value.jarak_km = jarakKm
      formPaket.value.total_harga = baseHarga + admin
    }
  } catch(e) { }
  isCalculating.value = false;
}

const savePaket = async () => {
  isSaving.value = true
  try {
    await axios.post('http://localhost:8000/api/v1/packages', formPaket.value)
    closeModalPaket()
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'all-paket', 10)
  } catch (error) { console.error(error) } finally { isSaving.value = false }
}

onMounted(() => { if(!['kurir'].includes(userRole.value)) fetchCustomersForDropdown() })
</script>