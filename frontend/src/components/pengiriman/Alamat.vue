<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col relative">
    
    <div class="p-4 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="w-full sm:w-auto">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Pemetaan & Rute Alamat</h2>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar alamat tujuan untuk paket yang sedang diproses atau diperjalanan.</p>
      </div>
      
      <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari jalan atau area..." 
            class="block w-full pl-9 pr-3 py-2.5 sm:py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] text-sm shadow-sm"
          >
        </div>
        
        <button @click="fetchPackages" :disabled="isLoading" class="w-full sm:w-auto justify-center bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 px-4 py-2.5 sm:py-2 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center disabled:opacity-50 active:scale-95">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh
        </button>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row flex-1">
      
      <div class="w-full lg:w-3/5 h-[45vh] lg:h-[700px] relative z-0 order-1 lg:order-none bg-gray-50">
        <div id="map" class="w-full h-full z-0"></div>
        
        <div v-if="selectedRoute" class="absolute bottom-4 left-4 right-4 lg:bottom-6 lg:left-1/2 lg:transform lg:-translate-x-1/2 bg-white/95 backdrop-blur-sm p-4 sm:p-5 rounded-2xl shadow-2xl border border-blue-100 lg:w-[420px] z-[400] animate-slide-up">
          <div class="flex justify-between items-start mb-3 border-b border-gray-100 pb-3">
            <div class="flex-1 pr-2">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5 flex items-center">
                <i class="fas fa-user-circle mr-1.5"></i> Pasien Tujuan
              </p>
              <h3 class="font-black text-gray-900 text-sm sm:text-base leading-tight truncate">{{ selectedRoute.customer?.nama || 'Unknown' }}</h3>
              <p class="text-[11px] sm:text-xs text-gray-500 mt-1 font-medium flex items-center">
                <i class="fas fa-phone-alt text-gray-400 mr-1.5"></i> {{ selectedRoute.customer?.no_telp || '-' }}
              </p>
            </div>
            <div class="text-right shrink-0">
              <span class="text-[10px] sm:text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 px-2 py-0.5 rounded-md uppercase tracking-wider inline-block">
                #PKT-{{ String(selectedRoute.id).padStart(4, '0') }} {{ selectedRoute.no_struk ? '-' + selectedRoute.no_struk : '' }}
              </span>
              <br>
              <span :class="getStatusBadgeClass(selectedRoute.status_pengiriman)" class="px-2 py-1 text-[9px] sm:text-[10px] font-black uppercase tracking-wider rounded-md border shadow-sm inline-block mt-1 text-center truncate max-w-[100px]">
                {{ selectedRoute.status_pengiriman }}
              </span>
            </div>
          </div>
          
          <div class="grid grid-cols-3 gap-2 sm:gap-3">
            <div class="bg-gray-50 p-2 sm:p-2.5 rounded-xl border border-gray-100 text-center">
              <p class="text-[10px] text-gray-500 font-bold mb-0.5">Waktu</p>
              <p class="font-black text-gray-900 text-xs sm:text-sm">{{ selectedRoute.estimasiWaktu || '-' }}<span class="text-[9px] sm:text-[10px] font-medium ml-0.5">mnt</span></p>
            </div>
            <div class="bg-gray-50 p-2 sm:p-2.5 rounded-xl border border-gray-100 text-center">
              <p class="text-[10px] text-gray-500 font-bold mb-0.5">Jarak</p>
              <p class="font-black text-gray-900 text-xs sm:text-sm">{{ selectedRoute.jarakKm || selectedRoute.jarak_km || '-' }}<span class="text-[9px] sm:text-[10px] font-medium ml-0.5">km</span></p>
            </div>
            <div class="bg-blue-50 p-2 sm:p-2.5 rounded-xl border border-blue-200 text-center shadow-sm">
              <p class="text-[10px] text-blue-700 font-bold mb-0.5">Tagihan</p>
              <p class="font-black text-[#3b5998] text-xs sm:text-sm">{{ formatRupiahSingkat(selectedRoute.total_harga || selectedRoute.harga) }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="w-full lg:w-2/5 flex flex-col h-[55vh] lg:h-[700px] bg-white border-t lg:border-t-0 lg:border-l border-gray-100 order-2 lg:order-none z-10">
        <div class="p-3 sm:p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center shrink-0">
          <h3 class="font-bold text-gray-700 text-sm sm:text-base">Daftar Rute ({{ filteredActiveRoutes.length }})</h3>
          <span class="text-[10px] sm:text-xs font-bold bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full uppercase tracking-wider">Aktif Berjalan</span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-3 sm:space-y-4 custom-scrollbar bg-gray-50/30">
          <div v-if="paginatedRoutes.length === 0" class="text-center py-10">
            <p class="text-gray-500 text-sm font-medium">Semua pengiriman sudah selesai atau kosong.</p>
          </div>
          
          <div 
            v-for="pkg in paginatedRoutes" 
            :key="pkg.id" 
            @click="tampilkanRute(pkg)"
            :class="[
              selectedRoute?.id === pkg.id ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-gray-200 hover:border-blue-300',
              'border bg-white p-3.5 sm:p-4 rounded-xl shadow-sm hover:shadow-md transition-all group relative cursor-pointer'
            ]"
          >
            <div class="absolute top-3 sm:top-4 right-3 sm:right-4 max-w-[110px] sm:max-w-[130px] text-right">
               <span :class="getStatusBadgeClass(pkg.status_pengiriman)" class="px-2 py-1 text-[9px] font-black uppercase rounded-md border text-center truncate block shadow-sm">
                 {{ pkg.status_pengiriman }}
               </span>
            </div>

            <div class="pr-24 sm:pr-28 mb-3">
              <h4 class="font-bold text-gray-500 text-[10px] sm:text-[11px] uppercase tracking-widest">#PKT-{{ String(pkg.id).padStart(4, '0') }} {{ pkg.no_struk ? '-' + pkg.no_struk : '' }}</h4>
              <p class="text-sm sm:text-base font-black text-gray-900 mt-0.5 truncate">{{ pkg.customer?.nama || 'Unknown' }}</p>
            </div>

            <div class="bg-gray-50 p-2.5 sm:p-3 rounded-lg border border-gray-100 flex items-start mt-2 group-hover:bg-white transition-colors">
              <i class="fas fa-map-marker-alt text-red-500 mr-2 sm:mr-2.5 mt-0.5 flex-shrink-0 text-sm"></i>
              <div class="flex-1">
                <p class="text-[11px] sm:text-xs text-gray-700 leading-relaxed font-medium line-clamp-2" :title="pkg.customer?.alamat">{{ pkg.customer?.alamat || 'Alamat tidak tersedia' }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="filteredActiveRoutes.length > 0" class="p-3 border-t border-gray-100 bg-white flex justify-between items-center shrink-0">
          <button @click="prevPage" :disabled="currentPage === 1" class="text-[11px] sm:text-xs font-bold text-gray-700 bg-gray-50 border border-gray-200 px-4 py-2.5 sm:py-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 transition-colors active:scale-95 shadow-sm">Prev</button>
          <span class="text-[11px] sm:text-xs font-bold text-gray-500 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">{{ currentPage }} / {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage === totalPages" class="text-[11px] sm:text-xs font-bold text-gray-700 bg-gray-50 border border-gray-200 px-4 py-2.5 sm:py-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 transition-colors active:scale-95 shadow-sm">Next</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import axios from 'axios'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'

const API_URL = 'http://localhost:8000/api/v1/packages'

const packages = ref([])
const isLoading = ref(true)
const searchQuery = ref('')
const selectedRoute = ref(null)

let map = null
let routingLayer = null 
let markers = []

const RUMAH_SAKIT_COORD = [-6.271362, 106.764780] 

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

const hospitalIcon = L.icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
})

// --- FILTERING (Hanya yang belum selesai/batal) ---
const activeRoutes = computed(() => packages.value.filter(pkg => 
  pkg.status_pengiriman !== '8. Sampai (Selesai)' && 
  pkg.status_pengiriman !== '9. Cancel / Pending'
))

const filteredActiveRoutes = computed(() => {
  if (!searchQuery.value) return activeRoutes.value
  const query = searchQuery.value.toLowerCase()
  return activeRoutes.value.filter(pkg => {
    const alamat = pkg.customer?.alamat?.toLowerCase() || ''
    const nama = pkg.customer?.nama?.toLowerCase() || ''
    return alamat.includes(query) || nama.includes(query)
  })
})

const currentPage = ref(1)
const itemsPerPage = 6

watch(searchQuery, () => { currentPage.value = 1 })

const totalPages = computed(() => Math.max(1, Math.ceil(filteredActiveRoutes.value.length / itemsPerPage)))
const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage)
const endIndex = computed(() => startIndex.value + itemsPerPage)
const paginatedRoutes = computed(() => filteredActiveRoutes.value.slice(startIndex.value, endIndex.value))

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }
// --- FUNGSI WARNA & ICON RESI WAKTU PENGANTARAN ---
const getWaktuResiClass = (waktu) => {
  if (waktu === 'Segera') return 'bg-red-50 text-red-700 border-red-200'
  if (waktu === 'Malam') return 'bg-emerald-50 text-emerald-700 border-emerald-200'
  return 'bg-blue-50 text-blue-700 border-blue-200' // Besok & Default
}

const getWaktuIconClass = (waktu) => {
  if (waktu === 'Segera') return 'fa-shipping-fast'
  if (waktu === 'Malam') return 'fa-moon'
  return 'fa-calendar-day'
}
// --- API FETCH ---
const fetchPackages = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    packages.value = response.data.data
  } catch (error) {
    console.error('Error fetching packages:', error)
  } finally {
    isLoading.value = false
  }
}

// Murni Fetch OSRM
const hitungRuteOSRM = async (latAwal, lngAwal, latTujuan, lngTujuan) => {
  try {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${lngAwal},${latAwal};${lngTujuan},${latTujuan}?overview=full&geometries=geojson`;
    const response = await fetch(osrmUrl);
    const data = await response.json();
    
    if (data.code === 'Ok') {
      const rute = data.routes[0];
      return {
        geometry: rute.geometry,
        jarakKm: (rute.distance / 1000).toFixed(1),
        estimasiMenit: Math.round(rute.duration / 60)
      }
    }
  } catch (error) {
    console.error("OSRM Error:", error);
  }
  return null;
}

const formatRupiahSingkat = (angka) => {
  if (!angka) return '-';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
}

// --- KLIK KARTU -> AUTO FALLBACK TRACKING ---
// --- KLIK KARTU -> SMART FALLBACK TRACKING ---
const tampilkanRute = async (pkg) => {
  selectedRoute.value = pkg

  let custLat = pkg.customer?.lat;
  let custLng = pkg.customer?.lng;

  // FITUR BARU: Smart Fallback Pencarian Bertingkat!
  if (!custLat || !custLng) {
    
    let rawAddress = pkg.customer?.alamat || '';
    if (rawAddress.includes('(Patokan:')) {
      rawAddress = rawAddress.split('(Patokan:')[0].trim();
    }
    
    // 1. Bersihkan RT/RW dan Kodepos yang sering ditolak Nominatim
    let cleanAddress = rawAddress
      .replace(/RT\s*\d+[\/\\]RW\s*\d+/ig, '') 
      .replace(/RW\s*\d+/ig, '') 
      .replace(/\b\d{5}\b/g, ''); 

    let parts = cleanAddress.split(',').map(s => s.trim()).filter(s => s.length > 0);
    let found = false;

    try {
      // Percobaan 1: Cari Alamat utuh yang sudah dibersihkan (tanpa RT/RW)
      let q1 = encodeURIComponent(parts.join(', '));
      let res1 = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${q1}&limit=1`);
      let data1 = await res1.json();
      
      if (data1 && data1.length > 0) {
        custLat = data1[0].lat; custLng = data1[0].lon; found = true;
      } 
      // Percobaan 2: Jika gagal, ambil 2 bagian awal saja (Misal: Nama Jalan + Kelurahan) + Jakarta
      else if (parts.length > 1) {
        let q2 = encodeURIComponent(`${parts[0]}, ${parts[1]}, Jakarta`);
        let res2 = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${q2}&limit=1`);
        let data2 = await res2.json();
        if (data2 && data2.length > 0) {
           custLat = data2[0].lat; custLng = data2[0].lon; found = true;
        }
      }
      
      // Percobaan 3: Mentok banget, hanya cari Nama Jalannya saja di Jakarta
      if (!found && parts.length > 0) {
        let q3 = encodeURIComponent(`${parts[0]}, Jakarta`);
        let res3 = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${q3}&limit=1`);
        let data3 = await res3.json();
        if (data3 && data3.length > 0) {
           custLat = data3[0].lat; custLng = data3[0].lon; found = true;
        }
      }

      if (!found) {
        alert(`Sistem kesulitan melacak titik jalan:\n${rawAddress}\n\nArea ini mungkin belum terdaftar di OpenStreetMap.`);
        return;
      }
    } catch(e) {
      alert("Gagal menghubungi satelit peta otomatis.");
      return;
    }
  }

  // Bersihkan peta dari rute sebelumnya
  if (routingLayer) map.removeLayer(routingLayer)
  markers.forEach(m => map.removeLayer(m))
  markers = []

  // Titik RS
  const rsMarker = L.marker(RUMAH_SAKIT_COORD, { icon: hospitalIcon })
    .bindPopup('<b>RSPPN Panglima Besar Soedirman</b><br>Titik Awal Pengiriman')
    .addTo(map)
  markers.push(rsMarker)

  // Titik Tujuan
  const custCoord = [parseFloat(custLat), parseFloat(custLng)]
  const custMarker = L.marker(custCoord)
    .bindPopup(`
      <b style="color: #3b5998;">${pkg.customer?.nama}</b><br>
      <span style="font-size: 11px;">${pkg.customer?.alamat}</span>
    `)
    .addTo(map)
  markers.push(custMarker)

  const osrmData = await hitungRuteOSRM(RUMAH_SAKIT_COORD[0], RUMAH_SAKIT_COORD[1], custCoord[0], custCoord[1]);

  if (osrmData) {
    routingLayer = L.geoJSON(osrmData.geometry, {
      style: { color: '#3b5998', weight: 5, opacity: 0.8 }
    }).addTo(map);

    selectedRoute.value = {
      ...pkg,
      jarakKm: osrmData.jarakKm,
      estimasiWaktu: osrmData.estimasiMenit
    };

    map.fitBounds(routingLayer.getBounds(), { padding: [50, 50] });
    custMarker.openPopup();
  }
}

const initMap = () => {
  map = L.map('map').setView(RUMAH_SAKIT_COORD, 14)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map)
  
  L.marker(RUMAH_SAKIT_COORD, { icon: hospitalIcon })
    .bindPopup('<b>RSPPN Panglima Besar Soedirman</b>')
    .addTo(map)
}

const getStatusBadgeClass = (status) => {
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

onMounted(() => {
  initMap()
  fetchPackages()
})

onBeforeUnmount(() => {
  if (map) map.remove() 
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
#map { height: 100%; width: 100%; z-index: 1; }

@keyframes slideUp {
  from { transform: translateY(120%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up {
  animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

/* Animasi khusus saat tampilan mode desktop (Laptop) agar popup presisi di tengah */
@media (min-width: 1024px) {
  @keyframes slideUpDesktop {
    from { transform: translate(-50%, 120%); opacity: 0; }
    to { transform: translate(-50%, 0); opacity: 1; }
  }
  .animate-slide-up {
    animation: slideUpDesktop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  }
}
</style>