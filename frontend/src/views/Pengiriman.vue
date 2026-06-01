<template>
  <div class="max-w-7xl mx-auto pb-10">
    
    <div class="bg-white rounded-t-xl overflow-hidden shadow-sm border border-gray-100">
      <div 
        class="h-48 w-full bg-cover bg-center relative" 
        style="background-image: url('https://images.unsplash.com/photo-1449824913935-59a10b8d2000?q=80&w=2070&auto=format&fit=crop');"
      >
        <div class="absolute inset-0 bg-blue-900/40 backdrop-blur-[1px]"></div>
      </div>
      
      <div class="px-6 sm:px-8 pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        
        <div class="flex flex-col sm:flex-row sm:items-end -mt-12 sm:-mt-16 mb-4 sm:mb-0 relative z-10">
          <div class="bg-white p-1.5 rounded-2xl shadow-lg border border-gray-100 inline-block shrink-0 w-max">
            <img 
              src="https://api.dicebear.com/7.x/bottts/svg?seed=Delivery&backgroundColor=e2e8f0" 
              alt="Maskot" 
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-50"
            />
          </div>
          
          <div class="mt-4 sm:mt-0 sm:ml-5 pb-1 sm:pb-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight">PENGIRIMAN</h1>
            <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">Manajemen Data Pasien & Paket Obat</p>
          </div>
        </div>
        
        <div class="flex space-x-3 mt-4 sm:mt-0 pb-2 w-full sm:w-auto overflow-x-auto" v-if="['superadmin', 'admin', 'farmasi'].includes(userRole)">
          <button @click="openModalPaket" class="flex-1 sm:flex-none justify-center bg-[#3b5998] hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-md hover:shadow-lg flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> 
            Buat Paket
          </button>
          <button @click="openModalCustomer" class="flex-1 sm:flex-none justify-center bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 px-6 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> 
            Pasien Baru
          </button>
        </div>
      </div>
      
      <div class="px-6 sm:px-8 border-t border-gray-100 bg-gray-50/30">
        <nav class="flex space-x-8 overflow-x-auto custom-scrollbar" aria-label="Tabs">
          <button v-for="tab in computedTabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id ? 'border-[#3b5998] text-[#3b5998]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 
              'group inline-flex items-center py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap outline-none'
            ]">
            <component :is="tab.icon" :class="[activeTab === tab.id ? 'text-[#3b5998]' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 h-5 w-5']" aria-hidden="true" />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-6 min-h-[500px] relative">
      <Transition name="fade" mode="out-in">
        <Overview v-if="activeTab === 'overview'" />
        <AllPaket v-else-if="activeTab === 'all-paket'" />
        <Customer v-else-if="activeTab === 'customer'" />
        <Alamat v-else-if="activeTab === 'alamat'" />
      </Transition>
    </div>

    <div v-if="isModalCustomerOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModalCustomer"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh] animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-extrabold text-gray-900">Registrasi Pasien Baru</h3>
            <p class="text-xs text-gray-500 mt-0.5">Pastikan alamat yang dimasukkan jelas dan akurat.</p>
          </div>
          <button @click="closeModalCustomer" class="text-gray-400 hover:text-gray-600 transition focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="saveCustomer" class="space-y-5">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap Pasien</label>
              <input v-model="formCustomer.nama" type="text" required placeholder="Contoh: Budi Santoso" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] outline-none transition-all">
            </div>
            
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon / WhatsApp</label>
              <input v-model="formCustomer.no_telp" type="text" required placeholder="081234567890" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] outline-none transition-all">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Kelamin</label>
                <select v-model="formCustomer.jenis_kelamin" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] bg-white outline-none transition-all">
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Umur (Tahun)</label>
                <input v-model="formCustomer.umur" type="number" min="0" placeholder="Opsional" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] outline-none transition-all">
              </div>
            </div>

            <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
              <label class="block text-sm font-extrabold text-blue-900 mb-1.5 flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                1. Alamat Jalan Utama (Untuk Maps)
              </label>
              <p class="text-[10px] text-blue-600 mb-2 leading-tight">Nama jalan, kelurahan, dan kecamatan. Hindari singkatan agar sistem rute otomatis dapat melacak koordinat pasien.</p>
              <textarea v-model="formCustomer.alamat" rows="2" required placeholder="Contoh: Jl. RC Veteran Raya, Bintaro, Pesanggrahan" class="w-full border border-blue-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
              <label class="block text-sm font-extrabold text-gray-700 mb-1.5">2. Detail Patokan & Nomor Rumah</label>
              <p class="text-[10px] text-gray-500 mb-2 leading-tight">Deskripsi tambahan khusus untuk dibaca oleh kurir di lapangan.</p>
              <textarea v-model="formCustomer.detail_alamat" rows="2" placeholder="Contoh: Rumah pagar hitam depan masjid, RT 01 RW 02 No. 15" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-gray-400 outline-none transition-all"></textarea>
            </div>
          </form>
        </div>
        
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="closeModalCustomer" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-100 transition-colors">Batal</button>
          <button @click="saveCustomer" :disabled="isSaving" type="button" class="px-5 py-2.5 bg-[#3b5998] rounded-lg text-white font-bold hover:bg-blue-800 disabled:opacity-50 transition-colors flex items-center shadow-sm">
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSaving ? 'Menyimpan...' : 'Simpan Customer' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isModalPaketOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModalPaket"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[95vh] animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-extrabold text-gray-900">Buat Tagihan Pengiriman</h3>
            <p class="text-xs text-gray-500 mt-1">Harga dihitung otomatis oleh sistem routing GPS.</p>
          </div>
          <button @click="closeModalPaket" class="text-gray-400 hover:text-gray-600 focus:outline-none transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="savePaket" class="space-y-6">
            
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-1.5">Pilih Customer (Pasien Tujuan)</label>
              <select v-model="formPaket.customer_id" @change="handleCustomerSelect" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-[#3b5998] outline-none transition-all shadow-sm">
                <option disabled value="">-- Pilih Customer Terdaftar --</option>
                <option v-for="cust in customersList" :key="cust.id" :value="cust.id">
                  {{ cust.nama }} - {{ cust.no_telp }}
                </option>
              </select>
            </div>
            
            <div v-show="formPaket.customer_id" class="border border-blue-100 rounded-xl p-5 bg-blue-50/30 relative overflow-hidden">
              
              <div v-if="isCalculating" class="absolute inset-0 bg-white/90 backdrop-blur-[2px] flex flex-col items-center justify-center z-10">
                <svg class="animate-spin h-8 w-8 text-[#3b5998] mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-extrabold text-[#3b5998] tracking-wide">Menghitung Rute & Harga...</span>
              </div>

              <div class="mb-4 bg-white p-3.5 rounded-lg border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 font-extrabold mb-1 uppercase tracking-wider flex items-center">
                  <svg class="w-3.5 h-3.5 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                  Tujuan Pengiriman
                </p>
                <p class="text-sm text-gray-800 font-semibold leading-relaxed">{{ selectedCustomerAddress || '-' }}</p>
              </div>

              <div class="flex flex-col justify-center">
                <h4 class="text-xs font-extrabold text-gray-800 mb-3 border-b border-gray-200 pb-2 uppercase tracking-wider">Rincian Biaya Sistem</h4>
                
                <div class="space-y-2.5 mb-4">
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500 font-medium">Jarak Tempuh Peta</span>
                    <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">{{ formPaket.jarak_km ? formPaket.jarak_km + ' km' : '-' }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500 font-medium">Tarif Dasar Jarak</span>
                    <span class="font-bold text-gray-900">{{ formatRupiah(rincianHarga.baseHarga) }}</span>
                  </div>
                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500 font-medium">Biaya Admin RS</span>
                    <span class="font-bold text-gray-900">{{ formatRupiah(rincianHarga.admin) }}</span>
                  </div>
                </div>

                <div class="flex justify-between items-center border-t border-blue-200 pt-3 mt-1">
                  <span class="font-extrabold text-gray-800 uppercase tracking-wide text-sm">Total Tagihan</span>
                  <span class="font-black text-2xl text-[#3b5998]">{{ formatRupiah(formPaket.total_harga) }}</span>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-1.5">Deskripsi Resep Obat / Pesanan</label>
              <textarea v-model="formPaket.deskripsi_pesanan" rows="3" required placeholder="Contoh: Paracetamol 500mg, Obat Batuk Sirup..." class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#3b5998] outline-none transition-all shadow-sm"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Status Pembayaran</label>
                <select v-model="formPaket.status_pembayaran" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-[#3b5998] outline-none transition-all shadow-sm">
                  <option value="Belum Lunas">Piutang / Belum Lunas</option>
                  <option value="Lunas">Sudah Lunas</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1.5">Metode Pembayaran</label>
                <select v-model="formPaket.metode_pembayaran" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-[#3b5998] outline-none transition-all shadow-sm">
                  <option value="Tunai / Cash">Tunai ke Kurir</option>
                  <option value="Transfer Bank">Transfer Bank</option>
                  <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="closeModalPaket" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 font-bold hover:bg-gray-100 transition-colors">Batal</button>
          <button @click="savePaket" :disabled="!formPaket.customer_id || isSaving || !formPaket.total_harga" type="button" class="px-5 py-2.5 bg-[#3b5998] rounded-lg text-white hover:bg-blue-800 disabled:opacity-50 font-bold transition-all shadow-sm flex items-center">
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSaving ? 'Memproses...' : 'Simpan & Rilis Paket' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, markRaw, onMounted, computed } from 'vue'
import axios from 'axios'

// --- PENGATURAN KOMPONEN ANAK ---
import Overview from '../components/pengiriman/Overview.vue'
import AllPaket from '../components/pengiriman/AllPaket.vue'
import Customer from '../components/pengiriman/Customer.vue'
import Alamat from '../components/pengiriman/Alamat.vue'

// --- CEK ROLE PENGGUNA SAAT INI ---
const currentUser = ref(JSON.parse(localStorage.getItem('user') || '{}'))
const userRole = computed(() => currentUser.value.role || 'guest')

// --- ICONS SVG (TABS) ---
const EyeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>` })
const CheckBadgeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>` })
const UsersIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>` })
const MapPinIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>` })

const tabs = [
  { id: 'overview', name: 'Dashboard', icon: EyeIcon },
  { id: 'all-paket', name: 'Database Paket', icon: CheckBadgeIcon },
  { id: 'customer', name: 'Buku Pasien', icon: UsersIcon },
  { id: 'alamat', name: 'Navigasi Kurir', icon: MapPinIcon },
]

// --- FILTER TABS BERDASARKAN ROLE (KEAMANAN UI) ---
const computedTabs = computed(() => {
  const role = userRole.value;
  if (role === 'farmasi') {
    return tabs.filter(t => ['all-paket', 'customer'].includes(t.id))
  } else if (role === 'kurir') {
    return tabs.filter(t => ['all-paket', 'alamat'].includes(t.id))
  }
  return tabs 
})

// Set Active Tab Default
const activeTab = ref(['admin', 'superadmin'].includes(userRole.value) ? 'overview' : 'all-paket')

const RUMAH_SAKIT_COORD = [-6.271362, 106.764780] 

// --- STATE MANAGEMENT MODALS & FORMS ---
const isModalCustomerOpen = ref(false)
const isModalPaketOpen = ref(false)
const isCalculating = ref(false)
const isSaving = ref(false)
const customersList = ref([]) 

const selectedCustomerAddress = ref('')
const formCustomer = ref({ nama: '', no_telp: '', jenis_kelamin: 'Laki-laki', umur: null, alamat: '', detail_alamat: '' })
const formPaket = ref({ customer_id: '', deskripsi_pesanan: '', status_pengiriman: 'Pesanan diverifikasi', status_pembayaran: 'Belum Lunas', metode_pembayaran: 'Tunai / Cash', jarak_km: null, total_harga: 0 })
const rincianHarga = ref({ baseHarga: 0, admin: 0 })

// --- FUNGSI FORMAT RUPIAH ---
const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
}

// --- FUNGSI API ---
const fetchCustomersForDropdown = async () => {
  try {
    const response = await axios.get('/customers')
    customersList.value = response.data.data
  } catch (error) { 
    console.error("Gagal mengambil data customer dari server:", error) 
  }
}

// --- LOGIKA MODAL PAKET & KALKULATOR HARGA ---
const openModalPaket = async () => { 
  await fetchCustomersForDropdown() 
  isModalPaketOpen.value = true 
}

const closeModalPaket = () => { 
  isModalPaketOpen.value = false
  formPaket.value = { customer_id: '', deskripsi_pesanan: '', status_pengiriman: 'Pesanan diverifikasi', status_pembayaran: 'Belum Lunas', metode_pembayaran: 'Tunai / Cash', jarak_km: null, total_harga: 0 }
  rincianHarga.value = { baseHarga: 0, admin: 0 }
  selectedCustomerAddress.value = ''
}

const handleCustomerSelect = async () => {
  const customer = customersList.value.find(c => c.id == formPaket.value.customer_id)
  if(!customer) return;

  selectedCustomerAddress.value = customer.alamat + (customer.detail_alamat ? ` (Patokan: ${customer.detail_alamat})` : '');
  isCalculating.value = true;
  formPaket.value.jarak_km = null;
  formPaket.value.total_harga = 0;
  rincianHarga.value = { baseHarga: 0, admin: 0 };

  let lat = parseFloat(customer.lat);
  let lng = parseFloat(customer.lng);

  if (!lat || !lng) {
    try {
      const query = encodeURIComponent(`${customer.alamat}, Jakarta, Indonesia`);
      const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1`);
      const data = await response.json();
      
      if (data && data.length > 0) {
        lat = parseFloat(data[0].lat);
        lng = parseFloat(data[0].lon);
      } else {
        alert("Peta gagal melacak lokasi persis. Tagihan harus disesuaikan secara manual.");
        isCalculating.value = false;
        return;
      }
    } catch(e) {
      alert("Koneksi GPS Nominatim terganggu.");
      isCalculating.value = false;
      return;
    }
  }

  try {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${RUMAH_SAKIT_COORD[1]},${RUMAH_SAKIT_COORD[0]};${lng},${lat}?overview=false`;
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
  } catch(e) { 
    alert("Koneksi ke server kalkulasi rute OSRM terputus.");
  }
  isCalculating.value = false;
}

// --- LOGIKA MODAL CUSTOMER ---
const openModalCustomer = () => { isModalCustomerOpen.value = true }
const closeModalCustomer = () => { 
  isModalCustomerOpen.value = false
  formCustomer.value = { nama: '', no_telp: '', jenis_kelamin: 'Laki-laki', umur: null, alamat: '', detail_alamat: '' }
}

const getCoordinatesFromAddress = async (alamatUtama) => {
  try {
    const query = encodeURIComponent(`${alamatUtama}, Jakarta, Indonesia`);
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1`);
    const data = await response.json();
    if (data && data.length > 0) {
      return { lat: data[0].lat, lng: data[0].lon }
    }
    return null;
  } catch (error) { return null; }
}

const saveCustomer = async () => {
  if(!formCustomer.value.nama || !formCustomer.value.alamat) return alert('Nama & Alamat Utama Wajib Diisi!');
  isSaving.value = true
  try {
    const coords = await getCoordinatesFromAddress(formCustomer.value.alamat);
    const payload = { ...formCustomer.value, lat: coords ? coords.lat : null, lng: coords ? coords.lng : null }
    
    await axios.post('/customers', payload)
    
    closeModalCustomer()
    alert('Data Pasien (Customer) Berhasil Disimpan ke Database!')
    
    if (activeTab.value === 'customer') {
      activeTab.value = ''
      setTimeout(() => activeTab.value = 'customer', 10)
    }
  } catch (error) { 
    console.error("Error Saving Customer:", error) 
  } finally { 
    isSaving.value = false 
  }
}

const savePaket = async () => {
  if(!formPaket.value.customer_id || !formPaket.value.deskripsi_pesanan) return alert('Harap isi form dengan lengkap!');
  isSaving.value = true
  try {
    await axios.post('/packages', formPaket.value)
    closeModalPaket()
    alert('Resi Tagihan dan Paket Berhasil Dibuat!')
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'all-paket', 10)
  } catch (error) { 
    console.error("Error Saving Package:", error) 
  } finally { 
    isSaving.value = false 
  }
}

onMounted(() => { 
  if(['superadmin', 'admin', 'farmasi'].includes(userRole.value)){
    fetchCustomersForDropdown() 
  }
})
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.fade-enter-from,
.fade-leave-to { opacity: 0; transform: translateY(5px); }

@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal-in { animation: modalIn 0.2s ease-out forwards; }

.custom-scrollbar::-webkit-scrollbar { height: 4px; width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
</style>