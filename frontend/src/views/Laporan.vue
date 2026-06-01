<template>
  <div class="max-w-7xl mx-auto pb-12 space-y-8 animate-fade-in print-bg-white">
    
    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hide-on-print">
      <div 
        class="h-48 w-full bg-cover bg-center relative" 
        style="background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop');"
      >
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/80 to-transparent"></div>
      </div>

      <div class="px-6 sm:px-10 pb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-12 sm:-mt-16 mb-6 sm:mb-0">
          <div class="bg-white p-2 rounded-2xl shadow-xl border border-gray-100 inline-block z-10 shrink-0">
            <img 
              src="https://api.dicebear.com/7.x/bottts/svg?seed=Report&backgroundColor=e2e8f0" 
              alt="Maskot Laporan" 
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-50"
            />
          </div>
          <div class="ml-5 pb-2 text-gray-900 z-10">
            <h1 class="text-2xl sm:text-4xl font-black leading-tight tracking-tight">LAPORAN MANAJEMEN</h1>
            <p class="text-gray-500 text-sm sm:text-base font-semibold mt-1">Integrasi Pengiriman, Teamwork & Finansial</p>
          </div>
        </div>

        <div class="flex space-x-3 pb-2 w-full sm:w-auto">
          <button @click="fetchReportData" :disabled="isLoading" class="flex-1 sm:flex-none justify-center bg-gray-50 hover:bg-gray-100 text-[#3b5998] border border-gray-200 px-6 py-3 rounded-xl font-bold text-sm transition-all shadow-sm flex items-center disabled:opacity-50">
            <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Refresh
          </button>
          <button @click="printReport" :disabled="isLoading" class="flex-1 sm:flex-none justify-center bg-[#3b5998] hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all shadow-md hover:shadow-lg flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak PDF
          </button>
        </div>
      </div>
    </div>

    <div class="hidden print-header mb-8 text-center border-b-2 border-gray-800 pb-4">
      <h1 class="text-3xl font-black text-gray-900 uppercase">Laporan Evaluasi Operasional</h1>
      <p class="text-gray-500 font-medium mt-1">Sistem Layanan Jastar Obat - RS PPN</p>
      <p class="text-sm text-gray-400 mt-2">Dicetak pada: {{ new Date().toLocaleString('id-ID') }}</p>
    </div>

    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-4 gap-6 hide-on-print">
      <div v-for="i in 4" :key="i" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 animate-pulse h-32"></div>
    </div>

    <div v-show="!isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 print-grid">
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-5 mr-5 bg-blue-50 text-blue-500 p-3 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Paket Terdata</p>
        <p class="text-3xl font-black text-gray-900 mt-2">{{ packages.length }}</p>
        <p class="text-xs text-blue-600 font-semibold mt-2">Semua siklus pengiriman</p>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-5 mr-5 bg-green-50 text-green-500 p-3 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Berhasil Terkirim</p>
        <p class="text-3xl font-black text-green-600 mt-2">{{ successRate }}%</p>
        <p class="text-xs text-green-600 font-semibold mt-2">{{ packages.filter(p => p.status_pengiriman === 'Terkirim').length }} paket selesai</p>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-5 mr-5 bg-purple-50 text-purple-500 p-3 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kurir Aktif Bekerja</p>
        <p class="text-3xl font-black text-purple-600 mt-2">{{ Object.keys(courierStats).length }}</p>
        <p class="text-xs text-purple-600 font-semibold mt-2">Dari total {{ users.filter(u => u.role === 'kurir').length }} driver</p>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-5 mr-5 bg-emerald-50 text-emerald-500 p-3 rounded-xl"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Laba Bersih Kas</p>
        <p class="text-xl font-black text-gray-900 mt-2 truncate">{{ formatRupiah(netProfit) }}</p>
        <p class="text-xs text-emerald-600 font-semibold mt-2">Pemasukan - Beban</p>
      </div>
    </div>

    <div v-show="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-6 hide-on-print">
      
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-2">
        <div class="mb-4">
          <h3 class="text-lg font-extrabold text-gray-800">Grafik Alur Pengiriman Paket</h3>
          <p class="text-xs text-gray-500 font-medium">Berdasarkan siklus kerja Farmasi hingga Kurir</p>
        </div>
        <div class="h-64 w-full">
          <canvas ref="pengirimanChartRef"></canvas>
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="mb-4">
          <h3 class="text-lg font-extrabold text-gray-800">Statistik Arus Kas</h3>
          <p class="text-xs text-gray-500 font-medium">Perbandingan Pemasukan & Pengeluaran</p>
        </div>
        <div class="h-64 w-full flex items-center justify-center">
          <canvas ref="keuanganChartRef"></canvas>
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 lg:col-span-3">
        <div class="mb-4">
          <h3 class="text-lg font-extrabold text-gray-800">Kinerja Pengantaran Kurir (Top Driver)</h3>
          <p class="text-xs text-gray-500 font-medium">Distribusi beban kerja dan penyelesaian antar armada</p>
        </div>
        <div class="h-72 w-full">
          <canvas ref="kurirChartRef"></canvas>
        </div>
      </div>
    </div>

    <div v-show="!isLoading" class="space-y-8">
      
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-800 flex items-center">
            <span class="w-8 h-8 rounded bg-purple-100 text-purple-600 flex items-center justify-center mr-3 text-sm">1</span>
            Rekapitulasi Kinerja Kurir
          </h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-white">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Armada / Kurir</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Diselesaikan</th>
                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sedang Berjalan</th>
                <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Kontribusi Ongkos</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-if="Object.keys(courierStats).length === 0">
                <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">Belum ada aktivitas kurir tercatat.</td>
              </tr>
              <tr v-for="(stat, name) in courierStats" :key="name" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#3b5998]">{{ name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-black text-gray-800">{{ stat.terkirim }} Paket</td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-orange-500">{{ stat.diperjalanan }} Paket</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">{{ formatRupiah(stat.revenue) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-800 flex items-center">
            <span class="w-8 h-8 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center mr-3 text-sm">2</span>
            Buku Kas & Laba Rugi
          </h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 bg-white">
          <div class="p-6">
            <h4 class="text-sm font-bold text-green-600 uppercase tracking-wider mb-4 border-b border-gray-50 pb-2">Pendapatan (Uang Masuk)</h4>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-gray-600 text-sm font-medium">Total Volume Transaksi</span>
                <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">{{ incomeTransactions.length }} Trx</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600 text-sm font-medium">Omset Pembayaran Lunas</span>
                <span class="font-black text-green-600 text-lg">{{ formatRupiah(totalIncome) }}</span>
              </div>
            </div>
          </div>
          
          <div class="p-6">
            <h4 class="text-sm font-bold text-red-600 uppercase tracking-wider mb-4 border-b border-gray-50 pb-2">Beban (Uang Keluar)</h4>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-gray-600 text-sm font-medium">Total Volume Pengeluaran</span>
                <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">{{ expenseTransactions.length }} Trx</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-gray-600 text-sm font-medium">Total Beban Operasional</span>
                <span class="font-black text-red-600 text-lg">{{ formatRupiah(totalExpense) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-blue-50/50 p-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center">
          <div class="mb-2 sm:mb-0 text-center sm:text-left">
            <p class="text-sm font-black text-blue-900 uppercase tracking-widest">Laba Bersih Akhir</p>
            <p class="text-xs text-blue-600 font-medium mt-1">Sisa saldo riil hasil pengurangan pendapatan & beban</p>
          </div>
          <div class="text-3xl font-black px-4 py-2 rounded-xl bg-white border border-blue-100 shadow-sm" :class="netProfit >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ formatRupiah(netProfit) }}
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

// --- STATE DATA ---
const packages = ref([])
const works = ref([])
const transactions = ref([])
const users = ref([])
const isLoading = ref(true)

// --- REFERENSI CANVAS GRAFIK ---
const pengirimanChartRef = ref(null)
const keuanganChartRef = ref(null)
const kurirChartRef = ref(null)

let pengirimanChartInstance = null
let keuanganChartInstance = null
let kurirChartInstance = null

// --- LOGIKA KOMPUTASI (METRIK & TABEL) ---
const successRate = computed(() => {
  if (packages.value.length === 0) return 0
  const terkirim = packages.value.filter(p => p.status_pengiriman === 'Terkirim').length
  return Math.round((terkirim / packages.value.length) * 100)
})

// Evaluasi Keuangan
const incomeTransactions = computed(() => transactions.value.filter(t => t.tipe === 'Uang Masuk'))
const expenseTransactions = computed(() => transactions.value.filter(t => t.tipe === 'Uang Keluar'))
const totalIncome = computed(() => incomeTransactions.value.reduce((acc, curr) => acc + Number(curr.nominal), 0))
const totalExpense = computed(() => expenseTransactions.value.reduce((acc, curr) => acc + Number(curr.nominal), 0))
const netProfit = computed(() => totalIncome.value - totalExpense.value)

// Evaluasi Kinerja Kurir
const courierStats = computed(() => {
  const stats = {}
  works.value.forEach(w => {
    const name = w.courier?.name || 'Kurir Dihapus'
    const status = w.package?.status_pengiriman || ''
    
    if (!stats[name]) {
      stats[name] = { terkirim: 0, diperjalanan: 0, revenue: 0 }
    }
    
    if (status === 'Terkirim') {
      stats[name].terkirim += 1
      stats[name].revenue += Number(w.package?.total_harga || 0)
    } else {
      stats[name].diperjalanan += 1
    }
  })
  return stats
})

// --- FORMAT RUPIAH ---
const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
}

// --- FUNGSI PRINT / CETAK PDF ---
const printReport = () => {
  window.print()
}

// --- PENGAMBILAN DATA (API) ---
const fetchReportData = async () => {
  isLoading.value = true
  try {
    const [resPkg, resWork, resTrx, resUsr] = await Promise.all([
      axios.get('/packages'),
      axios.get('/works'),
      axios.get('/transactions'),
      axios.get('/users')
    ])
    
    packages.value = resPkg.data.data || []
    works.value = resWork.data.data || []
    transactions.value = resTrx.data.data || []
    users.value = resUsr.data.data || []

    // Setelah data ditarik, render grafik
    await nextTick()
    renderCharts()
  } catch (error) {
    console.error("Gagal menarik data sinkronisasi laporan:", error)
  } finally {
    isLoading.value = false
  }
}

// --- PEMBUATAN GRAFIK (CHART.JS) ---
const renderCharts = () => {
  // Hancurkan instansiasi lama jika ada (mencegah bug tumpang tindih)
  if (pengirimanChartInstance) pengirimanChartInstance.destroy()
  if (keuanganChartInstance) keuanganChartInstance.destroy()
  if (kurirChartInstance) kurirChartInstance.destroy()

  // 1. GRAFIK PENGIRIMAN (Bar Chart) - Memvisualisasikan Alur Farmasi -> Kurir
  if (pengirimanChartRef.value) {
    const statusKeys = ['Pesanan diverifikasi', 'Pengemasan', 'Menunggu Driver', 'Diperjalanan', 'Terkirim', 'Dibatalkan']
    const statusCounts = statusKeys.map(key => packages.value.filter(p => p.status_pengiriman === key).length)
    
    pengirimanChartInstance = new Chart(pengirimanChartRef.value, {
      type: 'bar',
      data: {
        labels: ['Diverifikasi', 'Pengemasan', 'Mnng Driver', 'Diperjalanan', 'Terkirim', 'Batal'],
        datasets: [{
          label: 'Jumlah Paket',
          data: statusCounts,
          backgroundColor: ['#e2e8f0', '#fef08a', '#ffedd5', '#dbeafe', '#dcfce7', '#fee2e2'],
          borderColor: ['#94a3b8', '#ca8a04', '#c2410c', '#2563eb', '#16a34a', '#dc2626'],
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
      }
    })
  }

  // 2. GRAFIK KEUANGAN (Doughnut Chart)
  if (keuanganChartRef.value) {
    keuanganChartInstance = new Chart(keuanganChartRef.value, {
      type: 'doughnut',
      data: {
        labels: ['Pemasukan', 'Pengeluaran'],
        datasets: [{
          data: [totalIncome.value, totalExpense.value],
          backgroundColor: ['#22c55e', '#ef4444'],
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    })
  }

  // 3. GRAFIK KINERJA KURIR (Horizontal Bar Chart)
  if (kurirChartRef.value) {
    const kurirLabels = Object.keys(courierStats.value)
    const kurirTerkirim = kurirLabels.map(name => courierStats.value[name].terkirim)
    const kurirJalan = kurirLabels.map(name => courierStats.value[name].diperjalanan)

    kurirChartInstance = new Chart(kurirChartRef.value, {
      type: 'bar',
      data: {
        labels: kurirLabels.length > 0 ? kurirLabels : ['Belum Ada Data'],
        datasets: [
          {
            label: 'Berhasil Terkirim',
            data: kurirLabels.length > 0 ? kurirTerkirim : [0],
            backgroundColor: '#3b5998',
            borderRadius: 4
          },
          {
            label: 'Sedang Diperjalanan',
            data: kurirLabels.length > 0 ? kurirJalan : [0],
            backgroundColor: '#fbbf24',
            borderRadius: 4
          }
        ]
      },
      options: {
        indexAxis: 'y', // Dibuat menyamping agar nama kurir terbaca jelas
        responsive: true, maintainAspectRatio: false,
        scales: {
          x: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } },
          y: { stacked: true }
        }
      }
    })
  }
}

// --- INISIALISASI ---
onMounted(() => {
  fetchReportData()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

/* CSS KHUSUS MODE CETAK (PRINT) */
@media print {
  /* Paksa background menjadi putih bersih dan hapus shadow/margin web */
  body, .print-bg-white { background-color: #ffffff !important; }
  .max-w-7xl { max-width: 100% !important; margin: 0 !important; padding: 0 !important; }
  
  /* Sembunyikan elemen navigasi, tombol, dan grafik interaktif saat dicetak */
  .hide-on-print { display: none !important; }
  
  /* Tampilkan header khusus print */
  .print-header { display: block !important; }
  
  /* Penyesuaian layout grid agar muat di kertas A4 */
  .print-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; margin-bottom: 2rem; }
  .print-grid > div { border: 1px solid #e5e7eb !important; box-shadow: none !important; padding: 1rem !important; }
  
  /* Hilangkan efek rounded melengkung tajam */
  .rounded-2xl { border-radius: 4px !important; }
  .shadow-sm, .shadow-md, .shadow-xl { box-shadow: none !important; }
  
  table { width: 100% !important; border-collapse: collapse !important; }
  th, td { border: 1px solid #e5e7eb !important; padding: 8px !important; font-size: 12px !important; }
}
</style>