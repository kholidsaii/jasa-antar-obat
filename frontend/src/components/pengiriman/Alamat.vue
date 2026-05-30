<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Pemetaan & Rute Alamat</h2>
        <p class="text-sm text-gray-500 mt-1">Daftar alamat tujuan untuk paket yang sedang diproses atau diperjalanan.</p>
      </div>
      
      <div class="flex space-x-3">
        <div class="relative w-full sm:w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Cari jalan atau area..." 
            class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm"
          >
        </div>
        
        <button @click="fetchPackages" :disabled="isLoading" class="bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center disabled:opacity-50">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh
        </button>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row h-[600px] divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
      
      <div class="w-full lg:w-3/5 h-64 lg:h-full bg-gray-50 relative flex flex-col">
        <div id="map" class="w-full h-full z-0"></div>
        
        <div v-if="selectedRoute" class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-white/95 backdrop-blur-sm p-4 rounded-xl shadow-lg border border-gray-200 w-11/12 max-w-sm z-[400]">
          <div class="flex justify-between items-center mb-2">
            <span class="text-xs font-bold text-gray-500 uppercase">Detail Perjalanan</span>
            <span class="text-xs font-bold text-[#3b5998] bg-blue-50 px-2 py-1 rounded">RSPPN -> Pasien</span>
          </div>
          <div class="grid grid-cols-2 gap-4 mt-3">
            <div>
              <p class="text-xs text-gray-500 mb-1">Estimasi Waktu</p>
              <p class="font-bold text-gray-900">{{ selectedRoute.estimasiWaktu || '-' }} Menit</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 mb-1">Estimasi Harga</p>
              <p class="font-bold text-green-600">{{ selectedRoute.harga ? formatRupiah(selectedRoute.harga) : '-' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 mb-1">Jarak Tempuh</p>
              <p class="font-bold text-gray-900">{{ selectedRoute.jarakKm || '-' }} km</p>
            </div>
          </div>
        </div>
      </div>

      <div class="w-full lg:w-2/5 h-full flex flex-col bg-white">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <h3 class="font-bold text-gray-700">Daftar Rute ({{ filteredActiveRoutes.length }})</h3>
          <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2.5 py-1 rounded-full">Diproses / Diperjalanan</span>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar">
          <div 
            v-for="pkg in filteredActiveRoutes" 
            :key="pkg.id" 
            @click="tampilkanRute(pkg)"
            :class="[
              selectedRoute?.id === pkg.id ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50/30' : 'border-gray-100 hover:border-blue-200',
              'border bg-white p-4 rounded-xl shadow-sm hover:shadow transition-all group relative cursor-pointer'
            ]"
          >
            <div class="absolute top-4 right-4">
               <span :class="getStatusBadgeClass(pkg.status_pengiriman)" class="px-2 py-1 text-[10px] font-bold uppercase rounded-md border">
                 {{ pkg.status_pengiriman }}
               </span>
            </div>

            <div class="pr-20 mb-3">
              <h4 class="font-bold text-gray-900 text-sm">#PKT-{{ String(pkg.id).padStart(4, '0') }}</h4>
              <p class="text-sm font-semibold text-[#3b5998] mt-1">{{ pkg.customer?.nama || 'Unknown' }}</p>
            </div>

            <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 flex items-start mt-2">
              <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
              <div class="flex-1">
                <p class="text-sm text-gray-700 leading-relaxed">{{ pkg.customer?.alamat || 'Alamat tidak tersedia' }}</p>
              </div>
            </div>
            
            <div v-if="pkg.jarakKm" class="mt-3 flex justify-between text-xs text-gray-600 border-t pt-2">
               <span><i class="fas fa-motorcycle mr-1 text-gray-400"></i> {{ pkg.estimasiWaktu }} min ({{ pkg.jarakKm }} km)</span>
               <span class="font-bold text-green-600">{{ formatRupiah(pkg.harga) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
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

// --- PUSAT KOORDINAT RSPPN (UPDATE BARU) ---
// Jl. RC. Veteran Raya No.18, RT.9/RW.3, Bintaro, Pesanggrahan, Jakarta Selatan
const RUMAH_SAKIT_COORD = [-6.271362, 106.764780] 

// Fix icon Leaflet di Vue
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

const activeRoutes = computed(() => packages.value.filter(pkg => pkg.status_pengiriman !== 'Terkirim'))

const filteredActiveRoutes = computed(() => {
  if (!searchQuery.value) return activeRoutes.value
  const query = searchQuery.value.toLowerCase()
  return activeRoutes.value.filter(pkg => {
    const alamat = pkg.customer?.alamat?.toLowerCase() || ''
    const nama = pkg.customer?.nama?.toLowerCase() || ''
    return alamat.includes(query) || nama.includes(query)
  })
})

const fetchPackages = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    packages.value = response.data.data
  } catch (error) {
    console.error('Error fetching packages for routes:', error)
  } finally {
    isLoading.value = false
  }
}

// Panggil OSRM API (Gratis)
const hitungRuteOSRM = async (latAwal, lngAwal, latTujuan, lngTujuan) => {
  try {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${lngAwal},${latAwal};${lngTujuan},${latTujuan}?overview=full&geometries=geojson`;
    const response = await axios.get(osrmUrl);
    
    if (response.data.code === 'Ok') {
      const rute = response.data.routes[0];
      return {
        geometry: rute.geometry,
        jarakKm: (rute.distance / 1000).toFixed(1),
        estimasiMenit: Math.round(rute.duration / 60)
      }
    }
  } catch (error) {
    console.error("Gagal mengambil rute OSRM:", error);
  }
  return null;
}

const hitungHarga = (jarakAsli) => {
  const jarakKm = parseFloat(jarakAsli);
  let totalHargaOngkir = 0;
  
  if (jarakKm <= 5.0) {
    // 0 - 5 KM = Harga Flat Rp 20.000
    totalHargaOngkir = 20000;
  } else {
    // Jika lebih dari 5 KM, bulatkan ke atas
    const jarakDibulatkan = Math.ceil(jarakKm);
    
    // Cari sisa KM yang harus dihitung
    const extraKm = jarakDibulatkan - 5;
    
    // Rp 20.000 + (Rp 5.000 per KM lebih)
    totalHargaOngkir = 20000 + (extraKm * 5000);
  }

  // Tambahkan biaya admin tetap Rp 1.500 ke total akhir
  const biayaAdmin = 1500;
  return totalHargaOngkir + biayaAdmin;
}

const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka)
}

const tampilkanRute = async (pkg) => {
  selectedRoute.value = pkg

  if (!pkg.customer?.lat || !pkg.customer?.lng) {
    alert("Koordinat pelanggan belum tersedia di database. Mohon update data alamat customer ini agar sistem bisa melacak rutenya.");
    return;
  }

  if (routingLayer) map.removeLayer(routingLayer)
  markers.forEach(m => map.removeLayer(m))
  markers = []

  // Titik Awal: RSPPN Bintaro
  const rsMarker = L.marker(RUMAH_SAKIT_COORD, { icon: hospitalIcon })
    .bindPopup('<b>RSPPN Panglima Besar Soedirman</b><br>Titik Awal Pengiriman')
    .addTo(map)
  markers.push(rsMarker)

  // Titik Tujuan: Customer
  const custCoord = [parseFloat(pkg.customer.lat), parseFloat(pkg.customer.lng)]
  const custMarker = L.marker(custCoord)
    .bindPopup(`
      <b style="color: #3b5998;">${pkg.customer.nama}</b><br>
      ${pkg.customer.alamat} <br>
      <span style="font-size: 11px; color: gray;">
        📍 Patokan: ${pkg.customer.detail_alamat || '-'}
      </span>
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
      estimasiWaktu: osrmData.estimasiMenit,
      harga: hitungHarga(osrmData.jarakKm)
    };

    map.fitBounds(routingLayer.getBounds(), { padding: [50, 50] });
    custMarker.openPopup();
  }
}

const initMap = () => {
  // Map diinisialisasi terfokus ke area Bintaro
  map = L.map('map').setView(RUMAH_SAKIT_COORD, 14)
  
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map)
  
  L.marker(RUMAH_SAKIT_COORD, { icon: hospitalIcon })
    .bindPopup('<b>RSPPN Panglima Besar Soedirman</b><br>Jl. RC. Veteran Raya No.18')
    .addTo(map)
}

const getStatusBadgeClass = (status) => {
  switch(status) {
    case 'Pesanan diverifikasi': return 'bg-gray-100 text-gray-600 border-gray-200'
    case 'Pengemasan': return 'bg-yellow-50 text-yellow-700 border-yellow-200'
    case 'Menunggu Driver': return 'bg-orange-50 text-orange-700 border-orange-200'
    case 'Diperjalanan': return 'bg-blue-50 text-blue-700 border-blue-200'
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
</style>