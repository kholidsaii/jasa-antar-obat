<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center py-10 px-4 sm:px-0">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden relative">
      
      <div class="bg-gradient-to-r from-[#2b4170] to-[#3b5998] p-6 text-center text-white relative">
        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
          <svg class="w-8 h-8 text-[#3b5998]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h1 class="text-xl font-black tracking-wider">JASTAR OBAT</h1>
        <p class="text-xs text-blue-200 font-medium mt-1">RSPPN Panglima Besar Soedirman</p>
      </div>

      <div class="p-6">
        <div v-if="isLoading" class="flex flex-col items-center justify-center py-10">
          <svg class="animate-spin h-8 w-8 text-[#3b5998] mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
          <p class="text-sm font-bold text-gray-500">Mencari data resi...</p>
        </div>

        <div v-else-if="!paket" class="text-center py-10">
          <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </div>
          <h2 class="text-lg font-bold text-gray-900">Resi Tidak Ditemukan</h2>
          <p class="text-sm text-gray-500 mt-1">Pastikan link atau nomor resi yang Anda buka benar.</p>
        </div>

        <div v-else class="space-y-6">
          
          <div :class="getCardThemeClass(paket.waktu_pengantaran)" class="rounded-2xl p-4 text-center shadow-sm relative overflow-hidden border transition-colors duration-300">
            
            <i class="fas absolute -right-4 -top-2 text-7xl opacity-[0.04]" :class="getWaktuIconClass(paket.waktu_pengantaran).split(' ')[0]"></i>

            <p :class="getTextLabelClass(paket.waktu_pengantaran)" class="text-xs font-bold uppercase tracking-widest mb-1 relative z-10">Nomor Resi Pelacakan</p>
            <p :class="getTextValueClass(paket.waktu_pengantaran)" class="text-2xl font-black tracking-widest mb-2 relative z-10">#{{ resi }}</p>
            
            <span v-if="paket.waktu_pengantaran" :class="getWaktuBadgeClass(paket.waktu_pengantaran)" class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-md border shadow-sm inline-block mb-1 relative z-10">
              <i class="fas" :class="getWaktuIconClass(paket.waktu_pengantaran)"></i> {{ paket.waktu_pengantaran === 'Segera' ? 'SEGERA DIANTAR' : 'DIANTAR ' + paket.waktu_pengantaran }}
            </span>

            <div class="mt-3 pt-3 border-t border-gray-200/60 flex justify-between text-left relative z-10">
              <div>
                <p class="text-[10px] text-gray-500 uppercase font-bold">Penerima</p>
                <p class="text-sm font-bold text-gray-900">{{ paket.customer?.nama }}</p>
              </div>
              <div class="text-right">
                <p class="text-[10px] text-gray-500 uppercase font-bold">Tagihan</p>
                <p class="text-sm font-black text-green-600">{{ paket.status_pembayaran === 'Lunas' ? 'LUNAS' : formatRupiah(paket.total_harga) }}</p>
              </div>
            </div>
          </div>

          <div v-if="paket.work && paket.work.user" class="flex items-center p-4 bg-white border border-gray-100 rounded-2xl shadow-sm">
            <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(paket.work.user.name)}&background=random&color=fff`" class="w-12 h-12 rounded-full border-2 border-gray-100 shadow-sm mr-3">
            <div class="flex-1">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kurir Pengantar</p>
              <p class="text-sm font-bold text-gray-900">{{ paket.work.user.name }}</p>
              <p class="text-[11px] text-gray-500 font-medium">{{ paket.work.vehicle?.nama_kendaraan || 'Motor' }} • {{ paket.work.vehicle?.plat_nomor || '-' }}</p>
            </div>
            <a v-if="paket.work.user.no_telepon" :href="`https://wa.me/62${(paket.work.user.no_telepon || '').replace(/^0+/, '')}`" class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center hover:bg-green-200 transition-colors shadow-sm">
              <i class="fas fa-phone-alt text-sm"></i>
            </a>
          </div>

          <div>
            <h3 class="text-sm font-bold text-gray-800 mb-4 px-2">Riwayat Perjalanan Obat:</h3>
            <div class="relative px-4">
              
              <div class="absolute top-2 bottom-6 left-6 w-0.5 bg-gray-200"></div>

              <div v-if="!paket.histories || paket.histories.length === 0" class="relative z-10 flex items-start mb-6">
                <div :class="['w-5 h-5 rounded-full ring-4 ring-white flex-shrink-0 mt-0.5 animate-pulse shadow-sm', getActiveDotColor(paket.waktu_pengantaran)]"></div>
                <div class="ml-4">
                  <h4 class="text-sm font-black text-gray-900">{{ paket.status_pengiriman }}</h4>
                  <p class="text-xs text-gray-500 mt-0.5 font-medium">Update: {{ new Date(paket.updated_at).toLocaleString('id-ID') }}</p>
                </div>
              </div>

              <div v-else v-for="(history, index) in paket.histories" :key="history.id" class="relative z-10 flex items-start mb-6" :class="{'opacity-60': index !== 0}">
                
                <div :class="[
                  'w-5 h-5 rounded-full ring-4 ring-white flex-shrink-0 mt-0.5 shadow-sm',
                  index === 0 ? (history.status_pengiriman.includes('Selesai') ? 'bg-green-500' : getActiveDotColor(paket.waktu_pengantaran) + ' animate-pulse') : 'bg-gray-300'
                ]"></div>
                
                <div class="ml-4 flex-1">
                  <h4 :class="['text-sm tracking-tight', index === 0 ? 'font-black text-gray-900' : 'font-bold text-gray-700']">
                    {{ history.status_pengiriman }}
                  </h4>
                  <p class="text-xs text-gray-500 mt-1 leading-relaxed font-medium">{{ history.keterangan || '-' }}</p>
                  <p class="text-[10px] text-gray-400 mt-1 font-semibold flex items-center">
                    <i class="far fa-clock mr-1"></i> {{ new Date(history.created_at).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' }) }}
                  </p>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const resi = ref(route.params.resi) // Menangkap "PKT-0005" dari URL
const paket = ref(null)
const isLoading = ref(true)

const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)

// FUNGSI WARNA TITIK TRACKING
const getActiveDotColor = (waktu) => {
  if (waktu === 'Segera') return 'bg-red-500'
  if (waktu === 'Malam') return 'bg-emerald-500'
  return 'bg-blue-500' // Besok & Default
}

// FUNGSI WARNA BADGE WAKTU DI ATAS
const getWaktuBadgeClass = (waktu) => {
  if (waktu === 'Segera') return 'bg-red-100 text-red-700 border-red-200'
  if (waktu === 'Malam') return 'bg-emerald-100 text-emerald-700 border-emerald-200'
  return 'bg-blue-100 text-blue-700 border-blue-200' // Besok & Default
}

// FUNGSI ICON BADGE
const getWaktuIconClass = (waktu) => {
  if (waktu === 'Segera') return 'fa-shipping-fast mr-1'
  if (waktu === 'Malam') return 'fa-moon mr-1'
  return 'fa-calendar-day mr-1' // Besok & Default
}

const fetchTrackingData = async () => {
  isLoading.value = true
  try {
    const resiParts = resi.value.split('-');
    const paketId = parseInt(resiParts[1], 10);
    
    const response = await axios.get(`http://localhost:8000/api/v1/packages/${paketId}`)
    paket.value = response.data.data
  } catch (error) {
    console.error("Gagal menarik data tracking:", error)
  } finally {
    isLoading.value = false
  }
}
// --- FUNGSI WARNA BACKGROUND KARTU ---
const getCardThemeClass = (waktu) => {
  if (waktu === 'Segera') return 'bg-red-50 border-red-100'
  if (waktu === 'Malam') return 'bg-emerald-50 border-emerald-100'
  return 'bg-blue-50 border-blue-100'
}

// --- FUNGSI WARNA TEKS LABEL (Kecil) ---
const getTextLabelClass = (waktu) => {
  if (waktu === 'Segera') return 'text-red-500'
  if (waktu === 'Malam') return 'text-emerald-500'
  return 'text-blue-500'
}

// --- FUNGSI WARNA TEKS RESI (Besar) ---
const getTextValueClass = (waktu) => {
  if (waktu === 'Segera') return 'text-red-700'
  if (waktu === 'Malam') return 'text-emerald-700'
  return 'text-[#3b5998]'
}
onMounted(() => fetchTrackingData())
</script>