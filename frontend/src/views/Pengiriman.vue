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
          <button @click="openModalPaket" class="bg-[#3b5998] hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg font-bold text-sm transition-colors shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> 
            Pesanan Baru
          </button>
        </div>
      </div>
      
      <div class="px-6 sm:px-8 border-t border-gray-100">
        <nav class="flex space-x-8 overflow-x-auto" aria-label="Tabs">
          <button v-for="tab in allowedTabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[activeTab === tab.id ? 'border-[#3b5998] text-[#3b5998]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'group inline-flex items-center py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap']">
            <component :is="tab.icon" :class="[activeTab === tab.id ? 'text-[#3b5998]' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 h-5 w-5']" />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-6">
      <!-- <Overview v-if="activeTab === 'overview'" /> -->
      <AllPaket v-if="activeTab === 'all-paket'" />
      <Customer v-if="activeTab === 'customer'" ref="customerRef" />
      <Alamat v-if="activeTab === 'alamat'" />
    </div>

    <div v-if="isModalPaketOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeModalPaket"></div>
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-auto flex flex-col overflow-hidden max-h-[95vh]">
        
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-black text-gray-900">
              {{ modalStep === 1 ? '1. Cari Alamat & Rute (OSM)' : (modalStep === 2 ? '2. Data Pasien & Struk' : '3. Pembayaran') }}
            </h3>
            <div class="flex space-x-1 mt-2">
              <div class="h-1.5 w-10 rounded-full transition-colors" :class="modalStep >= 1 ? 'bg-blue-600' : 'bg-gray-200'"></div>
              <div class="h-1.5 w-10 rounded-full transition-colors" :class="modalStep >= 2 ? 'bg-blue-600' : 'bg-gray-200'"></div>
              <div class="h-1.5 w-10 rounded-full transition-colors" :class="modalStep >= 3 ? 'bg-blue-600' : 'bg-gray-200'"></div>
            </div>
          </div>
          <button @click="closeModalPaket" class="text-gray-400 hover:text-red-500 bg-white p-2 rounded-lg transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        
        <div class="p-6 overflow-y-auto custom-scrollbar">
          
          <div v-show="modalStep === 1" class="space-y-4">
            
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
              </div>
              <input 
                v-model="searchQueryAddress" 
                @keyup.enter="searchAddress"
                type="text" 
                placeholder="Ketik alamat (contoh: Kramat Jati, Jakarta) lalu tekan Enter..." 
                class="w-full border border-gray-300 rounded-xl py-3 pl-10 pr-24 outline-none focus:ring-2 focus:ring-[#3b5998] font-medium shadow-sm transition-shadow"
              >
              <button @click="searchAddress" class="absolute inset-y-1.5 right-1.5 bg-[#3b5998] hover:bg-blue-800 text-white text-xs font-bold px-3 rounded-lg">
                CARI
              </button>
            </div>

            <ul v-if="addressResults.length > 0" class="border border-gray-200 rounded-lg max-h-40 overflow-y-auto bg-white shadow-sm -mt-2">
              <li v-for="(result, index) in addressResults" :key="index" @click="selectAddress(result)" class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 text-sm flex items-start">
                <i class="fas fa-map-marker-alt text-red-500 mt-1 mr-2"></i>
                <span>{{ result.display_name }}</span>
              </li>
            </ul>

            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-inner relative z-0">
              <div id="leaflet-map" class="w-full h-56 bg-gray-100"></div>
              
              <div v-if="isCalculating" class="absolute inset-0 bg-white/70 backdrop-blur-sm flex flex-col items-center justify-center z-10">
                <svg class="animate-spin h-8 w-8 text-[#3b5998] mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="text-sm font-bold text-[#3b5998]">Menarik Garis Rute...</span>
              </div>
            </div>

            <div v-if="estimasiSelesai" class="mt-4 border border-emerald-200 rounded-xl p-4 bg-emerald-50/80 shadow-sm animate-fade-in">
              <div class="flex justify-between items-center border-b border-emerald-100 pb-2 mb-2">
                <span class="text-sm font-bold text-emerald-900"><i class="fas fa-route mr-1"></i> Jarak Tempuh Rute</span>
                <span class="text-sm font-black text-emerald-700">{{ formBaru.jarak_km }} km</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-gray-700">Total Tarif</span>
                <span class="text-2xl font-black text-[#3b5998]">{{ formatRupiah(formBaru.total_harga) }}</span>
              </div>
            </div>
          </div>

          <div v-if="modalStep === 2" class="space-y-4 animate-fade-in">
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Pasien</label>
              <input v-model="formBaru.nama" type="text" placeholder="Masukkan nama..." class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-[#3b5998]">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">No. Telepon / WhatsApp</label>
              <input v-model="formBaru.no_telp" type="number" placeholder="08..." class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-[#3b5998]">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nomor Struk Antrian</label>
              <input v-model="formBaru.no_struk" type="text" placeholder="Masukkan nomor struk..." class="w-full border border-gray-300 rounded-lg p-3 outline-none focus:ring-2 focus:ring-[#3b5998]">
            </div>
            
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Alamat Tujuan (Terkunci via Maps)</label>
              <p class="text-sm font-medium text-gray-800 line-clamp-2"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> {{ formBaru.alamat }}</p>
            </div>

            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 transition-colors">
              <label class="cursor-pointer block">
                <div class="mx-auto w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-2">
                  <i class="fas fa-camera text-xl"></i>
                </div>
                <span class="block text-sm font-bold text-gray-800">Upload / Foto Struk Antrian</span>
                <span class="block text-xs text-gray-500 mt-1">Wajib sebagai bukti pengambilan obat ke farmasi</span>
                <input type="file" @change="handleFileUpload" accept="image/*" capture="environment" class="hidden">
              </label>
              <div v-if="formBaru.foto_struk" class="mt-3 text-xs font-bold text-green-600 bg-green-50 py-1.5 px-3 rounded inline-block border border-green-200">
                <i class="fas fa-check-circle mr-1"></i> {{ formBaru.foto_struk.name }}
              </div>
            </div>
          </div>

          <div v-if="modalStep === 3" class="space-y-5 animate-fade-in">
            <div class="bg-gray-900 text-white p-5 rounded-xl text-center shadow-md border-b-4 border-[#3b5998]">
              <p class="text-xs text-gray-400 uppercase tracking-widest font-bold mb-1">Total Tagihan Final</p>
              <p class="text-3xl font-black">{{ formatRupiah(formBaru.total_harga) }}</p>
            </div>

            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Metode Pembayaran</label>
              <select v-model="formBaru.metode_pembayaran" class="w-full border border-gray-300 rounded-xl py-3 px-4 bg-white text-sm font-bold outline-none focus:ring-2 focus:ring-[#3b5998] shadow-sm appearance-none">
                <option value="Tunai / Cash (Sistem)">💵 Tunai / Cash (Bayar ke Stand)</option>
                <option value="QRIS / E-Wallet (Sistem)">📱 QRIS / E-Wallet digital</option>
                <option value="Transfer Bank">🏦 Transfer Bank (Virtual Account)</option>
              </select>
            </div>

            <div class="bg-green-50 border border-green-200 p-4 rounded-xl flex items-start shadow-sm">
              <i class="fas fa-shield-check text-green-600 mt-0.5 mr-3 text-lg"></i>
              <p class="text-xs text-green-800 font-medium leading-relaxed">
                Pembayaran akan langsung dicatat sebagai <strong>Lunas</strong>. Setelah disimpan, sistem akan otomatis membuatkan Resi dan Link Tracking pengiriman untuk pasien.
              </p>
            </div>
          </div>

        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center rounded-b-2xl">
          <button v-if="modalStep > 1" @click="modalStep--" type="button" class="px-5 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-100 transition-colors shadow-sm">
            Kembali
          </button>
          <div v-else></div> 
          
          <button v-if="modalStep === 1 && estimasiSelesai" @click="modalStep = 2" type="button" class="px-6 py-2.5 bg-[#3b5998] rounded-xl text-white font-bold hover:bg-blue-800 transition-colors shadow-md">
            Lanjut Isi Data <i class="fas fa-arrow-right ml-1"></i>
          </button>
          
          <button v-if="modalStep === 2" @click="cekValidasiPasien" type="button" class="px-6 py-2.5 bg-[#3b5998] rounded-xl text-white font-bold hover:bg-blue-800 transition-colors shadow-md">
            Lanjut Bayar <i class="fas fa-arrow-right ml-1"></i>
          </button>
          
          <button v-if="modalStep === 3" @click="submitPaket" :disabled="isSaving" type="button" class="px-6 py-2.5 bg-emerald-600 rounded-xl text-white font-black hover:bg-emerald-700 disabled:opacity-50 transition-all shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">
            {{ isSaving ? 'Memproses...' : 'Konfirmasi Selesai' }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, markRaw, nextTick, onMounted } from 'vue'
import axios from 'axios'

// Pastikan Leaflet (Open Source Map) di-load ke halaman Anda
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
  { id: 'customer', name: 'Customer', icon: UsersIcon, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] },
  { id: 'alamat', name: 'Alamat', icon: MapPinIcon, roles: ['superadmin', 'admin', 'farmasi', 'kurir'] },
]

const allowedTabs = computed(() => allTabs.filter(tab => tab.roles.includes(userRole.value)))
const activeTab = ref(allowedTabs.value.some(t => t.id === 'all-paket') ? 'all-paket' : allowedTabs.value[0]?.id || '')

// KOORDINAT RSPPN SOEDIRMAN SEBAGAI TITIK ASAL
const RUMAH_SAKIT_COORD = [-6.271362, 106.764780] 

// --- STATE WIZARD & MAPS ---
const isModalPaketOpen = ref(false)
const modalStep = ref(1)
const isCalculating = ref(false)
const estimasiSelesai = ref(false)
const isSaving = ref(false)

const searchQueryAddress = ref('')
const addressResults = ref([])

let L = null; // Leaflet Object
let mapInstance = null;
let originMarker = null;
let destMarker = null;
let routeLine = null;

const formBaru = ref({
  alamat: '', jarak_km: 0, total_harga: 0, 
  nama: '', no_telp: '', no_struk: '', // <-- Tambah no_struk
  foto_struk: null, metode_pembayaran: 'QRIS / E-Wallet (Sistem)'
})

const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)

// --- MENGGUNAKAN NOMINATIM (GRATIS) UNTUK PENCARIAN ALAMAT ---
// --- MENGGUNAKAN NOMINATIM (GRATIS) UNTUK PENCARIAN ALAMAT ---
const searchAddress = async () => {
  if (!searchQueryAddress.value) return;
  
  isCalculating.value = true; // Munculkan loading agar admin tahu sistem sedang mencari
  
  // Hapus hardcode tambahan kata, biarkan murni apa yang diketik user
  const query = encodeURIComponent(searchQueryAddress.value);
  
  try {
    // Tambahkan parameter &countrycodes=id agar fokus mencari di Indonesia saja
    const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=id&limit=5`);
    const data = await res.json();
    
    addressResults.value = data;
    
    if (data.length === 0) {
      alert("Alamat tidak ditemukan di database peta.\n\nTIPS:\n1. Coba persingkat (Contoh: 'Jalan Masjid Bendungan' saja).\n2. Perhatikan ejaan (Masjid vs Mesjid).");
    }
  } catch (error) { 
    console.error("Nominatim error", error); 
    alert("Terjadi kesalahan jaringan saat mencari alamat.");
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

// --- INISIALISASI PETA LEAFLET (GRATIS) ---
const initLeafletMap = async () => {
  // Load script Leaflet secara dinamis jika belum ada
  if (!window.L) {
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    document.head.appendChild(script);
    await new Promise(resolve => script.onload = resolve);
  }
  
  L = window.L;
  if (mapInstance) {
    mapInstance.remove(); // Hapus instance lama agar tidak error
  }

  mapInstance = L.map('leaflet-map').setView([RUMAH_SAKIT_COORD[0], RUMAH_SAKIT_COORD[1]], 14);
  
  // Gunakan tile map standard OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
  }).addTo(mapInstance);

  // Marker Rumah Sakit (Asal)
  originMarker = L.marker([RUMAH_SAKIT_COORD[0], RUMAH_SAKIT_COORD[1]]).addTo(mapInstance)
    .bindPopup('<b>RSPPN Soedirman</b><br>Titik Awal').openPopup();
}

// --- MENGGAMBAR RUTE MENGGUNAKAN OSRM (GRATIS) ---
const drawRoute = async (destLat, destLon) => {
  isCalculating.value = true;
  estimasiSelesai.value = false;

  try {
    // API OSRM Public Server (format: lng,lat)
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${RUMAH_SAKIT_COORD[1]},${RUMAH_SAKIT_COORD[0]};${destLon},${destLat}?overview=full&geometries=geojson`;
    
    const response = await fetch(osrmUrl);
    const data = await response.json();

    if (data.code === 'Ok') {
      const route = data.routes[0];
      const jarakKm = parseFloat((route.distance / 1000).toFixed(1));
      
      // Hapus marker/garis lama jika ada
      if (destMarker) mapInstance.removeLayer(destMarker);
      if (routeLine) mapInstance.removeLayer(routeLine);

      // Marker Tujuan
      destMarker = L.marker([destLat, destLon]).addTo(mapInstance)
        .bindPopup('<b>Tujuan Pasien</b>').openPopup();

      // Gambar Garis Rute (GeoJSON membalik lat/lng menjadi lng/lat array, Leaflet butuh lat/lng)
      const coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
      routeLine = L.polyline(coordinates, {color: '#3b5998', weight: 5}).addTo(mapInstance);
      
      // Fit bounds agar kedua marker terlihat jelas di peta
      mapInstance.fitBounds(routeLine.getBounds(), { padding: [30, 30] });

      // Hitung Tarif (Rules)
      let baseHarga = 20000;
      if (jarakKm > 5.0) {
        const extraKm = Math.ceil(jarakKm) - 5;
        baseHarga += (extraKm * 5000);
      }
      
      formBaru.value.jarak_km = jarakKm;
      formBaru.value.total_harga = baseHarga + 1500; // Harga + Biaya Admin
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
  formBaru.value = { alamat: '', jarak_km: 0, total_harga: 0, nama: '', no_telp: '', foto_struk: null, metode_pembayaran: 'QRIS / E-Wallet (Sistem)' }
}

const handleFileUpload = (e) => {
  if (e.target.files.length > 0) formBaru.value.foto_struk = e.target.files[0];
}

const cekValidasiPasien = () => {
  if (!formBaru.value.nama || !formBaru.value.no_telp) return alert("Nama & No. Telp wajib diisi!");
  if (!formBaru.value.foto_struk) return alert("Foto / Gambar struk antrian wajib dilampirkan!");
  modalStep.value = 3;
}

const submitPaket = async () => {
  isSaving.value = true
  
  const formData = new FormData()
  formData.append('nama', formBaru.value.nama)
  formData.append('no_telp', formBaru.value.no_telp)
  formData.append('no_struk', formBaru.value.no_struk)
  formData.append('alamat', formBaru.value.alamat)
  formData.append('jarak_km', formBaru.value.jarak_km)
  formData.append('total_harga', formBaru.value.total_harga)
  formData.append('metode_pembayaran', formBaru.value.metode_pembayaran)
  if (formBaru.value.foto_struk) formData.append('foto_struk', formBaru.value.foto_struk)

  try {
    // 1. Simpan ke Database & Tangkap Data Paket Barunya
    const response = await axios.post('http://localhost:8000/api/v1/packages', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    debugger
    // 2. Format Resi & Link Tracking
    const paketBaru = response.data.data;
    // Format resi menjadi: #PKT-0001-12345
    const kodeResi = '#PKT-' + String(paketBaru.id).padStart(4, '0') + '-' + formBaru.value.no_struk;
    const trackingLink = `${window.location.origin}/tracking/${kodeResi.replace('#', '')}`;

    // 3. Format Nomor Telepon +62
    let phoneWhatsApp = formBaru.value.no_telp.toString();
    if (phoneWhatsApp.startsWith('0')) {
      phoneWhatsApp = '+62' + phoneWhatsApp.substring(1);
    } else if (phoneWhatsApp.startsWith('62')) {
      phoneWhatsApp = '+' + phoneWhatsApp;
    }

    // 4. Racik Teks Pesan WhatsApp
    const waMessage = `Halo kak *${formBaru.value.nama}*,\n\nPesanan Jasa Antar Obat dari *RSPPN Soedirman* telah kami terima. Berikut rinciannya:\n\n📦 *No. Resi:* ${kodeResi}\n💵 *Total:* ${formatRupiah(formBaru.value.total_harga)}\n✅ *Status:* Lunas (${formBaru.value.metode_pembayaran})\n\nSilakan pantau pergerakan kurir dan status obat kakak secara real-time melalui link berikut:\n👇👇👇\n${trackingLink}\n\nTerima kasih! 🙏`;

    closeModalPaket()
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'all-paket', 10)
    
    alert("Pesanan dibuat! Mengarahkan ke WhatsApp untuk kirim link tracking...");

    // 5. Buka Tab Baru WhatsApp Web/App (URL API WA mengharuskan tanpa lambang +)
    const cleanPhoneUrl = phoneWhatsApp.replace('+', '');
    const waUrl = `https://wa.me/${cleanPhoneUrl}?text=${encodeURIComponent(waMessage)}`;
    window.open(waUrl, '_blank');

  } catch (error) { 
    console.error(error) 
    alert("Gagal memproses pesanan.");
  } finally { 
    isSaving.value = false 
  }
}

onMounted(() => {
  loadLeafletStyle();
});
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.3s ease-in-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
</style>