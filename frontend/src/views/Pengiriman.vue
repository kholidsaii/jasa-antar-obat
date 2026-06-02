<template>
  <div class="max-w-7xl mx-auto pb-10 sm:pb-12 bg-gray-50/50 min-h-screen">
    <div class="bg-white rounded-b-2xl sm:rounded-t-xl sm:rounded-b-none overflow-hidden shadow-sm border-b sm:border border-gray-100">
      
      <div class="h-32 sm:h-48 w-full bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1449824913935-59a10b8d2000?q=80&w=2070&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent sm:hidden"></div>
      </div>
      
      <div class="px-4 sm:px-8 pb-5 sm:pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-10 sm:-mt-16 mb-4 sm:mb-0 relative z-10">
          <div class="bg-white p-1 sm:p-1.5 rounded-xl sm:rounded-2xl shadow-md border border-gray-100 inline-block">
            <img src="https://api.dicebear.com/7.x/bottts/svg?seed=Delivery&backgroundColor=e2e8f0" alt="Maskot" class="w-20 h-20 sm:w-24 sm:h-24 rounded-lg sm:rounded-xl object-cover bg-gray-100"/>
          </div>
          <div class="ml-4 sm:ml-5 pb-1 sm:pb-2">
            <h1 class="text-xl sm:text-3xl font-black text-gray-900 leading-tight">PENGIRIMAN</h1>
            <p class="text-gray-500 text-xs sm:text-sm font-medium mt-0.5 sm:mt-1">Manajemen Data & Operasional</p>
          </div>
        </div>
        
        <div class="w-full sm:w-auto mt-2 sm:mt-0 relative z-10" v-if="userRole !== 'kurir'">
          <button @click="openModalPaket" class="w-full sm:w-auto justify-center bg-[#3b5998] hover:bg-blue-800 text-white px-6 py-3.5 sm:py-2.5 rounded-xl font-bold text-sm transition-all shadow-md active:scale-95 flex items-center">
            <svg class="w-5 h-5 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> 
            Buat Paket
          </button>
        </div>
      </div>
      
      <div class="border-t border-gray-100 bg-white">
        <nav class="flex overflow-x-auto hide-scrollbar scroll-smooth px-2 sm:px-8" aria-label="Tabs">
          <button v-for="tab in allowedTabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[activeTab === tab.id ? 'border-[#3b5998] text-[#3b5998]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'group inline-flex items-center px-4 py-3.5 sm:py-4 border-b-2 font-bold text-[13px] sm:text-sm transition-colors whitespace-nowrap flex-shrink-0']">
            <component :is="tab.icon" :class="[activeTab === tab.id ? 'text-[#3b5998]' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 h-5 w-5']" />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-4 sm:mt-6 px-0 sm:px-0">
      <!-- <Overview v-if="activeTab === 'overview'" /> -->
      <AllPaket v-if="activeTab === 'all-paket'" />
      <Customer v-if="activeTab === 'customer'" ref="customerRef" />
      <Alamat v-if="activeTab === 'alamat'" />
    </div>

    <div v-if="isModalPaketOpen" class="fixed inset-0 z-[100] flex flex-col justify-end sm:items-center sm:justify-center p-0 sm:p-4">
      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModalPaket"></div>
      
      <div class="relative bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl w-full sm:max-w-xl mx-auto flex flex-col overflow-hidden max-h-[92vh] sm:max-h-[95vh] animate-slide-up sm:animate-modal-in">
        
        <div class="w-full flex justify-center pt-3 pb-2 sm:hidden cursor-grab active:cursor-grabbing" @click="closeModalPaket">
          <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-white flex justify-between items-center sticky top-0 z-20">
          <div class="w-full pr-4">
            <h3 class="text-base sm:text-lg font-black text-gray-900 leading-tight truncate">
              {{ modalStep === 1 ? '1. Cari Alamat & Rute' : (modalStep === 2 ? '2. Data Pasien & Struk' : '3. Pembayaran Lunas') }}
            </h3>
            <div class="flex space-x-1.5 mt-2">
              <div class="h-1.5 flex-1 rounded-full transition-colors duration-300" :class="modalStep >= 1 ? 'bg-blue-600' : 'bg-gray-200'"></div>
              <div class="h-1.5 flex-1 rounded-full transition-colors duration-300" :class="modalStep >= 2 ? 'bg-blue-600' : 'bg-gray-200'"></div>
              <div class="h-1.5 flex-1 rounded-full transition-colors duration-300" :class="modalStep >= 3 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            </div>
          </div>
          <button @click="closeModalPaket" class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-full transition-colors shrink-0">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar flex-1 bg-gray-50/30 pb-20 sm:pb-6">
          
          <div v-show="modalStep === 1" class="space-y-4">
            <div class="relative z-20">
              <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
              </div>
              <input 
                v-model="searchQueryAddress" 
                @keyup.enter="searchAddress"
                type="text" 
                placeholder="Ketik jalan tujuan lalu Enter..." 
                class="w-full border border-gray-300 rounded-xl py-3.5 sm:py-3 pl-10 pr-20 outline-none focus:ring-2 focus:ring-[#3b5998] text-sm font-medium shadow-sm transition-shadow"
              >
              <button @click="searchAddress" class="absolute inset-y-1.5 right-1.5 bg-[#3b5998] hover:bg-blue-800 text-white text-xs font-bold px-4 rounded-lg active:scale-95 transition-transform">
                CARI
              </button>
            </div>

            <ul v-if="addressResults.length > 0" class="border border-gray-200 rounded-xl max-h-48 overflow-y-auto bg-white shadow-lg relative z-30 -mt-2 custom-scrollbar">
              <li v-for="(result, index) in addressResults" :key="index" @click="selectAddress(result)" class="p-3.5 hover:bg-blue-50 cursor-pointer border-b border-gray-50 text-[13px] sm:text-sm flex items-start transition-colors active:bg-blue-100">
                <i class="fas fa-map-marker-alt text-red-500 mt-1 mr-3 shrink-0"></i>
                <span class="leading-relaxed text-gray-700 font-medium">{{ result.display_name }}</span>
              </li>
            </ul>
            <div>
              <label class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 flex justify-between items-center">
                <span>Detail / Patokan Alamat</span>
                <span class="text-[9px] font-bold text-gray-500 bg-gray-200 px-2 py-0.5 rounded normal-case tracking-normal">Bantu Kurir Mencari</span>
              </label>
              <textarea v-model="formBaru.detail_alamat" rows="2" placeholder="Contoh: RT 02/05, pagar warna hitam, masuk gang sebelah indomaret..." class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-[#3b5998] text-sm shadow-sm transition-all"></textarea>
            </div>
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-inner relative z-0">
              <div id="leaflet-map" class="w-full h-48 sm:h-56 bg-gray-100"></div>
              
              <div v-if="isCalculating" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-10">
                <svg class="animate-spin h-8 w-8 text-[#3b5998] mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-bold text-[#3b5998]">Memproses Rute...</span>
              </div>
            </div>

            <div v-if="estimasiSelesai" class="mt-4 border border-emerald-200 rounded-xl p-4 bg-emerald-50 shadow-sm animate-fade-in">
              <div class="flex justify-between items-center border-b border-emerald-100 pb-2.5 mb-2.5">
                <span class="text-xs sm:text-sm font-bold text-emerald-900"><i class="fas fa-route mr-1.5 text-emerald-600"></i> Jarak Tempuh Rute</span>
                <span class="text-sm font-black text-emerald-700 bg-white px-2 py-0.5 rounded shadow-sm">{{ formBaru.jarak_km }} km</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-xs sm:text-sm font-bold text-gray-700">Total Tarif Pengiriman</span>
                <span class="text-xl sm:text-2xl font-black text-[#3b5998]">{{ formatRupiah(formBaru.total_harga) }}</span>
              </div>
            </div>
          </div>

          <div v-if="modalStep === 2" class="space-y-4 animate-fade-in">
            <div>
              <label class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama Pasien</label>
              <input v-model="formBaru.nama" type="text" placeholder="Ketik nama pasien..." class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-[#3b5998] text-sm shadow-sm transition-all">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">No. WA (Aktif)</label>
                <input v-model="formBaru.no_telp" type="number" placeholder="08..." class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-[#3b5998] text-sm shadow-sm transition-all">
              </div>
              <div>
                <label class="block text-[11px] sm:text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">No. Struk Antrian</label>
                <input v-model="formBaru.no_struk" type="text" placeholder="Misal: F-123" class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-[#3b5998] text-sm shadow-sm transition-all font-bold text-gray-800">
              </div>
            </div>
            
            <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200">
              <label class="block text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Tujuan (Titik Peta)</label>
              <p class="text-xs sm:text-sm font-semibold text-gray-800 line-clamp-3 leading-relaxed"><i class="fas fa-map-marker-alt text-red-500 mr-1.5"></i> {{ formBaru.alamat }}</p>
            </div>
            <div class="border-2 border-dashed border-blue-200 rounded-xl p-5 text-center bg-blue-50/30 hover:bg-blue-50 transition-colors cursor-pointer group relative overflow-hidden">
              <label class="cursor-pointer block w-full h-full relative z-10">
                <div class="mx-auto w-12 h-12 bg-white text-blue-600 rounded-full flex items-center justify-center mb-3 shadow-sm group-hover:scale-110 transition-transform">
                  <i class="fas fa-camera text-xl"></i>
                </div>
                <span class="block text-sm font-bold text-blue-900">Ketuk untuk Foto Struk</span>
                <span class="block text-[11px] text-blue-600 mt-1">Bukti fisik untuk ambil obat ke Farmasi</span>
                <input type="file" @change="handleFileUpload" accept="image/*" capture="environment" class="hidden">
              </label>
              <div v-if="formBaru.foto_struk" class="mt-4 text-[11px] sm:text-xs font-bold text-green-700 bg-green-100 py-2 px-3 rounded-lg inline-block border border-green-200 shadow-sm truncate max-w-full">
                <i class="fas fa-check-circle mr-1 text-green-600"></i> {{ formBaru.foto_struk.name }}
              </div>
            </div>
          </div>

          <div v-if="modalStep === 3" class="space-y-5 animate-fade-in">
            <div class="bg-gray-900 text-white p-6 rounded-2xl text-center shadow-lg border-b-4 border-[#3b5998] relative overflow-hidden">
              <div class="absolute -right-4 -top-4 opacity-10"><i class="fas fa-wallet text-9xl"></i></div>
              <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mb-2 relative z-10">Total Tagihan Final</p>
              <p class="text-3xl sm:text-4xl font-black relative z-10">{{ formatRupiah(formBaru.total_harga) }}</p>
            </div>

            <div class="bg-white p-1 rounded-xl shadow-sm border border-gray-100">
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 mt-2 px-3">Metode Pembayaran</label>
              <select v-model="formBaru.metode_pembayaran" class="w-full border-none rounded-xl py-3 px-3 text-sm font-bold outline-none focus:ring-0 bg-transparent appearance-none cursor-pointer text-gray-800">
                <option value="Tunai / Cash (Sistem)">💵 Tunai / Cash (Bayar ke Stand)</option>
                <option value="QRIS / E-Wallet (Sistem)">📱 QRIS / E-Wallet digital</option>
                <option value="Transfer Bank (Sistem)">🏦 Transfer Bank (Virtual Account)</option>
              </select>
              <div class="pointer-events-none absolute right-6 top-[55%] transform -translate-y-1/2">
                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
              </div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-start shadow-sm">
              <i class="fas fa-shield-check text-emerald-600 mt-0.5 mr-3 text-xl"></i>
              <p class="text-xs text-emerald-800 font-medium leading-relaxed">
                Pembayaran akan langsung dicatat <strong>LUNAS</strong>. Klik "Proses Pesanan" dan sistem otomatis mengirim URL Tracking ke WhatsApp pasien.
              </p>
            </div>
          </div>

        </div>

        <div class="bg-white sm:bg-gray-50 px-5 sm:px-6 py-4 border-t border-gray-100 flex gap-3 items-center sticky bottom-0 z-30 pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] sm:shadow-none sm:rounded-b-2xl">
          
          <button v-if="modalStep > 1" @click="modalStep--" type="button" class="w-1/3 sm:w-auto px-4 py-3.5 sm:py-2.5 bg-gray-100 border border-transparent rounded-xl text-gray-700 font-bold hover:bg-gray-200 transition-colors active:scale-95 text-sm">
            Kembali
          </button>
          
          <button v-if="modalStep === 1" @click="modalStep = 2" :disabled="!estimasiSelesai" type="button" class="w-full px-4 py-3.5 sm:py-2.5 bg-[#3b5998] rounded-xl text-white font-bold hover:bg-blue-800 transition-colors shadow-md disabled:opacity-50 disabled:active:scale-100 active:scale-95 text-sm">
            Lanjut Data Pasien <i class="fas fa-arrow-right ml-1"></i>
          </button>
          
          <button v-if="modalStep === 2" @click="cekValidasiPasien" type="button" class="flex-1 px-4 py-3.5 sm:py-2.5 bg-[#3b5998] rounded-xl text-white font-bold hover:bg-blue-800 transition-colors shadow-md active:scale-95 text-sm">
            Lanjut Bayar <i class="fas fa-arrow-right ml-1"></i>
          </button>
          
          <button v-if="modalStep === 3" @click="submitPaket" :disabled="isSaving" type="button" class="flex-1 px-4 py-3.5 sm:py-2.5 bg-emerald-600 rounded-xl text-white font-black hover:bg-emerald-700 disabled:opacity-50 transition-all shadow-lg shadow-emerald-200 active:scale-95 flex justify-center items-center text-sm">
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSaving ? 'Menyimpan...' : 'Proses Pesanan' }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, markRaw, nextTick, onMounted } from 'vue'
import axios from 'axios'

// Load Leaflet Styles for Map
const loadLeafletStyle = () => {
  if (!document.getElementById('leaflet-css')) {
    const link = document.createElement('link');
    link.id = 'leaflet-css';
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(link);
  }
}

// import Overview from '../components/pengiriman/Overview.vue'
import AllPaket from '../components/pengiriman/AllPaket.vue'
import Customer from '../components/pengiriman/Customer.vue'
import Alamat from '../components/pengiriman/Alamat.vue'

const userRole = ref(JSON.parse(localStorage.getItem('user'))?.role || '')

const EyeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>` })
const CheckBadgeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>` })
const UsersIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>` })
const MapPinIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>` })

const allTabs = [
  // { id: 'overview', name: 'Overview', icon: EyeIcon, roles: ['superadmin', 'admin'] },
  { id: 'all-paket', name: 'All Paket', icon: CheckBadgeIcon, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] },
  { id: 'customer', name: 'Data Customer', icon: UsersIcon, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] },
  { id: 'alamat', name: 'Alamat', icon: MapPinIcon, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] },
]

const allowedTabs = computed(() => allTabs.filter(tab => tab.roles.includes(userRole.value)))
const activeTab = ref(allowedTabs.value.some(t => t.id === 'all-paket') ? 'all-paket' : allowedTabs.value[0]?.id || '')

// KOORDINAT RS PUSAT
const RUMAH_SAKIT_COORD = [-6.271362, 106.764780] 

// --- STATE WIZARD & MAPS ---
const isModalPaketOpen = ref(false)
const modalStep = ref(1)
const isCalculating = ref(false)
const estimasiSelesai = ref(false)
const isSaving = ref(false)

const searchQueryAddress = ref('')
const addressResults = ref([])

let L = null; 
let mapInstance = null;
let originMarker = null;
let destMarker = null;
let routeLine = null;

const formBaru = ref({
  alamat: '',
  detail_alamat: '', // Tambahan untuk detail patokan alamat
  jarak_km: 0,
  total_harga: 0,
  nama: '',
  no_telp: '',
  no_struk: '', 
  foto_struk: null,
  metode_pembayaran: 'QRIS / E-Wallet (Sistem)'
})

const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)

// PENCARIAN NOMINATIM
const searchAddress = async () => {
  if (!searchQueryAddress.value) return;
  isCalculating.value = true;
  
  const query = encodeURIComponent(searchQueryAddress.value);
  try {
    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=id&limit=5`);
    const data = await res.json();
    addressResults.value = data;
    
    if (data.length === 0) {
      alert("Alamat tidak ditemukan. Coba ketik nama jalannya saja secara singkat.");
    }
  } catch (error) { 
    console.error("Nominatim error", error); 
  } finally {
    isCalculating.value = false;
  }
}

const selectAddress = (result) => {
  formBaru.value.alamat = result.display_name;
  searchQueryAddress.value = '';
  addressResults.value = [];
  drawRoute(result.lat, result.lon);
}

// INISIALISASI PETA
const initLeafletMap = async () => {
  if (!window.L) {
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    document.head.appendChild(script);
    await new Promise(resolve => script.onload = resolve);
  }
  
  L = window.L;
  if (mapInstance) {
    mapInstance.remove(); 
    mapInstance = null;
  }

  const mapElement = document.getElementById('leaflet-map');
  if(mapElement) {
      mapInstance = L.map('leaflet-map', { zoomControl: false }).setView([RUMAH_SAKIT_COORD[0], RUMAH_SAKIT_COORD[1]], 14);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OSM' }).addTo(mapInstance);
      originMarker = L.marker([RUMAH_SAKIT_COORD[0], RUMAH_SAKIT_COORD[1]]).addTo(mapInstance).bindPopup('<b>RSPPN Soedirman</b>').openPopup();
  }
}

// MENGGAMBAR RUTE OSRM
const drawRoute = async (destLat, destLon) => {
  isCalculating.value = true;
  estimasiSelesai.value = false;

  try {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${RUMAH_SAKIT_COORD[1]},${RUMAH_SAKIT_COORD[0]};${destLon},${destLat}?overview=full&geometries=geojson`;
    const response = await fetch(osrmUrl);
    const data = await response.json();

    if (data.code === 'Ok') {
      const route = data.routes[0];
      const jarakKm = parseFloat((route.distance / 1000).toFixed(1));
      
      if (destMarker) mapInstance.removeLayer(destMarker);
      if (routeLine) mapInstance.removeLayer(routeLine);

      destMarker = L.marker([destLat, destLon]).addTo(mapInstance).bindPopup('<b>Tujuan Pasien</b>').openPopup();

      const coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
      routeLine = L.polyline(coordinates, {color: '#3b5998', weight: 5}).addTo(mapInstance);
      mapInstance.fitBounds(routeLine.getBounds(), { padding: [30, 30] });

      let baseHarga = 20000;
      if (jarakKm > 5.0) {
        const extraKm = Math.ceil(jarakKm) - 5;
        baseHarga += (extraKm * 5000);
      }
      
      formBaru.value.jarak_km = jarakKm;
      formBaru.value.total_harga = baseHarga + 1500; 
      estimasiSelesai.value = true;
    }
  } catch (error) {
    alert("Gagal memuat rute dari server OSRM.");
  }
  isCalculating.value = false;
}

const openModalPaket = async () => { 
  isModalPaketOpen.value = true;
  await nextTick();
  initLeafletMap();
}

const closeModalPaket = () => { 
  isModalPaketOpen.value = false
  modalStep.value = 1
  estimasiSelesai.value = false
  searchQueryAddress.value = ''
  addressResults.value = []
  formBaru.value = { alamat: '', detail_alamat: '', jarak_km: 0, total_harga: 0, nama: '', no_telp: '', no_struk: '', foto_struk: null, metode_pembayaran: 'QRIS / E-Wallet (Sistem)' }
}

const handleFileUpload = (e) => {
  if (e.target.files.length > 0) formBaru.value.foto_struk = e.target.files[0];
}

const cekValidasiPasien = () => {
  if (!formBaru.value.nama || !formBaru.value.no_telp) return alert("Nama & No. Telp wajib diisi!");
  if (!formBaru.value.no_struk) return alert("Nomor Struk wajib diisi!");
  if (!formBaru.value.foto_struk) return alert("Foto / Gambar struk antrian wajib dilampirkan!");
  modalStep.value = 3;
}

// PENYIMPANAN DAN GENERATE WA +62
// PENYIMPANAN, AUTO ASSIGN KURIR, DAN GENERATE WA +62
const submitPaket = async () => {
  isSaving.value = true
  
  const formData = new FormData()
  formData.append('nama', formBaru.value.nama)
  formData.append('no_telp', formBaru.value.no_telp)
  formData.append('no_struk', formBaru.value.no_struk)
  
  // Menggabungkan Alamat Utama dengan Detail Patokan (Jika ada)
  const alamatFinal = formBaru.value.detail_alamat 
    ? `${formBaru.value.alamat} (Patokan: ${formBaru.value.detail_alamat})` 
    : formBaru.value.alamat;
  
  formData.append('alamat', alamatFinal)
  formData.append('jarak_km', formBaru.value.jarak_km)
  formData.append('total_harga', formBaru.value.total_harga)
  formData.append('metode_pembayaran', formBaru.value.metode_pembayaran)
  if (formBaru.value.foto_struk) formData.append('foto_struk', formBaru.value.foto_struk)

  try {
    // 1. CARI KURIR TERSEDIA (Ambil kurir dengan tanggungan paket paling sedikit)
    const { data: usersRes } = await axios.get('/users');
    const kurirList = usersRes.data.filter(u => u.role === 'kurir');
    kurirList.sort((a, b) => a.works_count - b.works_count);
    const kurirTerpilih = kurirList.length > 0 ? kurirList[0] : null;

    // 2. SIMPAN PAKET BARU
    const response = await axios.post('/packages', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    const paketBaru = response.data.data;

    // 3. AUTO ASSIGN KE KURIR (Jika ada kurir tersedia)
    let namaKurirText = "menunggu kurir";
    debugger
    if (kurirTerpilih) {
      await axios.post('/works', {
        package_ids: [paketBaru.id],
        user_id: kurirTerpilih.id,
        // TAMBAHAN: Tarik ID kendaraan si kurir kalau dia punya plat motor terdaftar
        vehicle_id: kurirTerpilih.vehicle ? kurirTerpilih.vehicle.id : null 
      });
      namaKurirText = `kurir *${kurirTerpilih.name}*`;
    }
    // 4. GENERATE WHATSAPP MESSAGE
    const kodeResi = '#PKT-' + String(paketBaru.id).padStart(4, '0') + '-' + formBaru.value.no_struk;
    const trackingLink = `${window.location.origin}/tracking/${kodeResi.replace('#', '')}`;

    let phoneWhatsApp = String(formBaru.value.no_telp).trim();
    if (phoneWhatsApp.startsWith('0')) {
      phoneWhatsApp = '62' + phoneWhatsApp.substring(1);
    } else if (phoneWhatsApp.startsWith('+62')) {
      phoneWhatsApp = phoneWhatsApp.substring(1); 
    } else if (!phoneWhatsApp.startsWith('62')) {
      phoneWhatsApp = '62' + phoneWhatsApp;
    }

    const waMessage = `Halo kak *${formBaru.value.nama}*,\n\nPesanan Jasa Antar Obat dari *RSPPN Soedirman* telah kami terima dan saat ini dialokasikan ke ${namaKurirText}. Berikut rinciannya:\n\n📦 *No. Resi:* ${kodeResi}\n💵 *Total:* ${formatRupiah(formBaru.value.total_harga)}\n✅ *Status:* Lunas (${formBaru.value.metode_pembayaran})\n\nSilakan pantau pergerakan kurir dan status obat kakak secara real-time melalui link berikut:\n👇👇👇\n${trackingLink}\n\nTerima kasih! 🙏`;

    closeModalPaket()
    
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'all-paket', 10)

    const waUrl = `https://wa.me/${phoneWhatsApp}?text=${encodeURIComponent(waMessage)}`;
    window.open(waUrl, '_blank');

  } catch (error) { 
    console.error(error) 
    alert("Gagal memproses pesanan.");
  } finally { 
    isSaving.value = false 
  }
}

onMounted(() => { loadLeafletStyle() });
</script>

<style scoped>
/* Sembunyikan scrollbar untuk area tab agar mulus di swipe di HP */
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

/* Animasi HP Bottom Sheet */
@keyframes slideUp {
  from { transform: translateY(100%); opacity: 0.5; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up { animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

/* Animasi Fade In standard */
.animate-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

/* Pengamanan padding di HP (Khusus iPhone Swipe Bar) */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
  .pb-safe { padding-bottom: calc(1rem + env(safe-area-inset-bottom)); }
}
</style>