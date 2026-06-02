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
          <p class="text-sm text-gray-500 mt-1">Pastikan link yang Anda buka benar.</p>
        </div>

        <div v-else class="space-y-6">
          <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-center">
            <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Nomor Resi Pelacakan</p>
            <p class="text-2xl font-black text-[#3b5998] tracking-widest">#{{ resi }}</p>
            <div class="mt-3 pt-3 border-t border-blue-200/50 flex justify-between text-left">
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

          <div v-if="paket.work && paket.work.user" class="flex items-center p-4 bg-gray-50 border border-gray-100 rounded-2xl shadow-sm">
            <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(paket.work.user.name)}&background=random&color=fff`" class="w-12 h-12 rounded-full border-2 border-white shadow-sm mr-3">
            <div class="flex-1">
              <p class="text-xs font-bold text-gray-500 uppercase">Kurir Pengantar</p>
              <p class="text-sm font-bold text-gray-900">{{ paket.work.user.name }}</p>
              <p class="text-xs text-gray-500 font-medium">{{ paket.work.vehicle?.nama_kendaraan || 'Motor' }} • {{ paket.work.vehicle?.plat_nomor || '-' }}</p>
            </div>
            <a :href="`https://wa.me/62${(paket.work.user.no_telepon || '').replace(/^0+/, '')}`" class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center hover:bg-green-200 transition-colors">
              <i class="fas fa-phone-alt"></i>
            </a>
          </div>

          <div>
            <h3 class="text-sm font-bold text-gray-800 mb-4 px-2">Status Perjalanan Obat:</h3>
            <div class="relative px-4">
              <div class="absolute top-2 bottom-2 left-6 w-0.5 bg-gray-200"></div>

              <div class="relative z-10 flex items-start mb-6">
                <div :class="['w-5 h-5 rounded-full ring-4 ring-white flex-shrink-0 mt-0.5', paket.status_pengiriman.includes('Selesai') ? 'bg-green-500' : 'bg-blue-500 animate-pulse']"></div>
                <div class="ml-4">
                  <h4 class="text-sm font-black text-gray-900">{{ paket.status_pengiriman }}</h4>
                  <p class="text-xs text-gray-500 mt-0.5 font-medium">Update Terakhir: {{ new Date(paket.updated_at).toLocaleString('id-ID') }}</p>
                </div>
              </div>

              <div class="relative z-10 flex items-start mb-6 opacity-50">
                <div class="w-5 h-5 rounded-full bg-gray-300 ring-4 ring-white flex-shrink-0 mt-0.5"></div>
                <div class="ml-4">
                  <h4 class="text-sm font-bold text-gray-700">Pesanan Diterima</h4>
                  <p class="text-xs text-gray-500 mt-0.5">Sistem Jastar Obat</p>
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

const fetchTrackingData = async () => {
  isLoading.value = true
  try {
    // Mengekstrak ID asli dari resi (Contoh: "PKT-0005" menjadi 5)
    const paketId = parseInt(resi.value.replace('PKT-', ''), 10)
    
    // Pastikan Endpoint /packages/{id} ini bisa diakses tanpa Bearer Token (di Laravel)
    // ATAU Anda membuat endpoint khusus publik di Laravel: Route::get('/tracking/{id}')
    const response = await axios.get(`http://localhost:8000/api/v1/packages/${paketId}`)
    paket.value = response.data.data
  } catch (error) {
    console.error(error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => fetchTrackingData())
</script>