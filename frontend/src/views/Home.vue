<template>
  <div class="max-w-7xl mx-auto pb-10 space-y-8">
    
    <div class="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden relative">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-900 via-blue-800 to-transparent opacity-90 z-10"></div>
      <div 
        class="absolute inset-0 bg-cover bg-center text-white" 
        style="background-image: url('https://images.unsplash.com/photo-1538108176447-280586497d96?q=80&w=2074&auto=format&fit=crop');"
      ></div>
      
      <div class="relative p-6 sm:p-10 z-20 text-white max-w-2xl space-y-3">
        <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase">
          Rumah Sakit
        </span>
        <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-tight">
          Sistem Layanan Jastar Obat
        </h1>
        <p class="text-sm sm:text-base text-blue-100 font-medium max-w-md leading-relaxed">
          Pantau pengemasan farmasi, koordinasi kurir teamwork, dan akuntansi finansial dalam satu gerbang terintegrasi.
        </p>
        <p v-if="currentUser.name" class="mt-4 inline-block bg-white/20 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20 font-semibold">
          Selamat bekerja, {{ currentUser.name }}! ({{ userRole.toUpperCase() }})
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all">
        <div class="space-y-1">
          <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Antrean Farmasi</p>
          <p class="text-3xl font-black text-gray-900">{{ isLoading ? '...' : totalAntrean }}</p>
          <p class="text-xs text-amber-600 font-semibold flex items-center">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse mr-1.5"></span>
            Butuh Tindakan
          </p>
        </div>
        <div class="p-3.5 bg-amber-50 text-amber-600 rounded-xl group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all">
        <div class="space-y-1">
          <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sedang Diantar</p>
          <p class="text-3xl font-black text-gray-900">{{ isLoading ? '...' : totalDiantar }}</p>
          <p class="text-xs text-blue-600 font-semibold">Kurir di Lapangan</p>
        </div>
        <div class="p-3.5 bg-blue-50 text-[#3b5998] rounded-xl group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all">
        <div class="space-y-1">
          <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Karyawan</p>
          <p class="text-3xl font-black text-gray-900">{{ isLoading ? '...' : totalKurir }}</p>
          <p class="text-xs text-green-600 font-semibold">Internal</p>
        </div>
        <div class="p-3.5 bg-green-50 text-green-600 rounded-xl group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all">
        <div class="space-y-1 w-2/3">
          <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Saldo Kas Bersih</p>
          <p class="text-xl font-black text-gray-900 truncate" :title="formatRupiah(saldoBersih)">
            {{ isLoading ? '...' : formatRupiah(saldoBersih) }}
          </p>
          <p class="text-xs text-gray-500 truncate">Sesuai Buku Akuntansi</p>
        </div>
        <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-xl group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
      </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <div class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100 col-span-1 space-y-4">
        <h3 class="font-extrabold text-gray-800 border-b pb-2 text-sm tracking-wide uppercase">
          Akses Cepat Modul
        </h3>
        
        <div class="space-y-2">
          <router-link v-if="['superadmin', 'admin', 'farmasi'].includes(userRole)" to="/pengiriman" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/40 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#3b5998] flex items-center justify-center font-bold mr-3 text-sm group-hover:bg-[#3b5998] group-hover:text-white transition-colors">P</div>
            <div>
              <p class="text-sm font-bold text-gray-800">Modul Pengiriman</p>
              <p class="text-xs text-gray-400">Add Customer & Paket Obat</p>
            </div>
          </router-link>

          <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/teamwork" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-purple-200 hover:bg-purple-50/40 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold mr-3 text-sm group-hover:bg-purple-600 group-hover:text-white transition-colors">T</div>
            <div>
              <p class="text-sm font-bold text-gray-800">Modul Teamwork</p>
              <p class="text-xs text-gray-400">Penugasan Kerja Kurir Armada</p>
            </div>
          </router-link>

          <router-link v-if="userRole === 'superadmin'" to="/financial" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50/40 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center font-bold mr-3 text-sm group-hover:bg-green-600 group-hover:text-white transition-colors">F</div>
            <div>
              <p class="text-sm font-bold text-gray-800">Modul Financial</p>
              <p class="text-xs text-gray-400">Pencatatan Buku Kas & Bank</p>
            </div>
          </router-link>

          <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/laporan" class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-orange-200 hover:bg-orange-50/40 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center font-bold mr-3 text-sm group-hover:bg-orange-600 group-hover:text-white transition-colors">L</div>
            <div>
              <p class="text-sm font-bold text-gray-800">Modul Laporan</p>
              <p class="text-xs text-gray-400">Evaluasi Kinerja & Laba</p>
            </div>
          </router-link>

          <button @click="handleLogout" class="w-full flex items-center p-3 rounded-xl border border-red-100 hover:bg-red-50 text-red-600 transition-all text-left mt-4 group">
            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold mr-3 text-sm group-hover:bg-red-600 group-hover:text-white transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </div>
            <div>
              <p class="text-sm font-bold text-red-700">Logout</p>
              <p class="text-xs text-red-400">Keluar dari aplikasi</p>
            </div>
          </button>
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-xs border border-gray-100 col-span-1 lg:col-span-2 flex flex-col justify-between">
        <div>
          <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h3 class="font-extrabold text-gray-800 text-sm tracking-wide uppercase">
              Aktivitas Pengantaran Terkini
            </h3>
            <span class="text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-md">Live Feed</span>
          </div>
          
          <div class="space-y-3.5 max-h-[220px] overflow-y-auto custom-scrollbar">
            <div v-if="recentWorks.length === 0" class="text-center text-sm text-gray-500 py-10">
              Belum ada pergerakan kurir atau penugasan aktif saat ini.
            </div>
            
            <div v-for="work in recentWorks" :key="work.id" class="flex items-start text-sm border-b border-gray-50 pb-3 last:border-0 last:pb-0">
              <div class="w-2.5 h-2.5 bg-blue-500 rounded-full mt-1.5 mr-3 flex-shrink-0 animate-pulse"></div>
              <div class="flex-1">
                <p class="text-gray-800 font-semibold">
                  #WRK-{{ String(work.id).padStart(4, '0') }} ditugaskan ke <span class="text-[#3b5998] font-bold">{{ work.courier?.name }}</span>
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                  Tujuan Pasien: {{ work.package?.customer?.nama || 'Umum' }} • Ongkos Kirim: {{ formatRupiah(work.harga_ongkos) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="pt-4 border-t border-gray-50 flex justify-end">
          <button @click="fetchGlobalDashboard" class="text-xs text-[#3b5998] font-bold hover:underline flex items-center">
            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Refresh Dashboard
          </button>
        </div>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

// --- USER STATES ---
const currentUser = ref(JSON.parse(localStorage.getItem('user') || '{}'))
const userRole = computed(() => currentUser.value.role || 'guest')

// --- GLOBAL APP STATES ---
const isLoading = ref(true)
const totalAntrean = ref(0)
const totalDiantar = ref(0)
const totalKurir = ref(0)
const saldoBersih = ref(0)
const recentWorks = ref([])

// --- API AGGREGATION HANDLER ---
// --- API AGGREGATION HANDLER ---
const fetchGlobalDashboard = async () => {
  isLoading.value = true
  
  // Ambil role secara langsung untuk pengecekan
  const role = userRole.value;

  try {
    // 1. Data Paket (Bisa diakses Superadmin, Admin, Farmasi)
    if (['superadmin', 'admin', 'farmasi'].includes(role)) {
      const resPackages = await axios.get('/packages')
      const antreanStatuses = ['Pesanan diverifikasi', 'Pengemasan', 'Menunggu Driver']
      totalAntrean.value = resPackages.data.data?.filter(p => antreanStatuses.includes(p.status_pengiriman)).length || 0
      totalDiantar.value = resPackages.data.data?.filter(p => p.status_pengiriman === 'Diperjalanan').length || 0
    }

    // 2. Data Teamwork & User (Hanya Superadmin & Admin)
    if (['superadmin', 'admin'].includes(role)) {
      const resUsers = await axios.get('/users')
      const resWorks = await axios.get('/works')
      
      totalKurir.value = resUsers.data.data?.length || 0
      recentWorks.value = resWorks.data.data?.slice(0, 5) || []
    }

    // 3. Data Transaksi/Keuangan (Hanya Superadmin)
    if (role === 'superadmin') {
      const resTransactions = await axios.get('/transactions')
      const pemasukan = resTransactions.data.data?.filter(t => t.tipe === 'Uang Masuk').reduce((acc, c) => acc + Number(c.nominal), 0) || 0
      const pengeluaran = resTransactions.data.data?.filter(t => t.tipe === 'Uang Keluar').reduce((acc, c) => acc + Number(c.nominal), 0) || 0
      saldoBersih.value = pemasukan - pengeluaran
    }

  } catch (error) {
    console.error("Gagal melakukan sinkronisasi dashboard:", error)
  } finally {
    isLoading.value = false
  }
}

// --- LOGOUT HANDLER ---
const handleLogout = async () => {
  try {
    await axios.post('/logout')
  } catch (e) {
    console.log("Error during API logout:", e)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

// --- CURRENCY FORMATTER ---
const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(angka || 0)
}

// --- INITIALIZER ---
onMounted(() => {
  fetchGlobalDashboard()
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
</style>