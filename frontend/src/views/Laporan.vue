<template>
  <div class="max-w-7xl mx-auto pb-10">
    
    <div class="bg-white rounded-t-xl overflow-hidden shadow-sm border border-gray-100">
      <div 
        class="h-48 w-full bg-cover bg-center relative" 
        style="background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop');"
      >
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/60 to-transparent"></div>
      </div>

      <div class="px-6 sm:px-8 pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-12 sm:-mt-16 mb-4 sm:mb-0">
          <div class="bg-white p-1.5 rounded-2xl shadow-lg border border-gray-100 inline-block">
            <img 
              src="https://api.dicebear.com/7.x/bottts/svg?seed=Report&backgroundColor=e2e8f0" 
              alt="Maskot Laporan" 
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-50"
            />
          </div>
          <div class="ml-5 pb-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight">LAPORAN MANAJEMEN</h1>
            <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">Rekapitulasi Kinerja & Evaluasi Operasional</p>
          </div>
        </div>

        <div class="flex space-x-3 pb-2">
          <button @click="printReport" class="bg-[#3b5998] hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-md hover:shadow-lg flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak PDF / Print
          </button>
        </div>
      </div>
    </div>

    <div class="mt-6 space-y-8 print-area">
      
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
          <span class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center mr-3">1</span>
          Kinerja Pengiriman & Paket
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-center">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Total Pesanan</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ packages.length }}</p>
          </div>
          <div class="bg-green-50 p-4 rounded-lg border border-green-100 text-center">
            <p class="text-xs text-green-600 font-bold uppercase tracking-wide">Terkirim</p>
            <p class="text-2xl font-black text-green-700 mt-1">{{ packages.filter(p => p.status_pengiriman === 'Terkirim').length }}</p>
          </div>
          <div class="bg-orange-50 p-4 rounded-lg border border-orange-100 text-center">
            <p class="text-xs text-orange-600 font-bold uppercase tracking-wide">Diproses / Jalan</p>
            <p class="text-2xl font-black text-orange-700 mt-1">{{ packages.filter(p => ['Pengemasan', 'Menunggu Driver', 'Diperjalanan'].includes(p.status_pengiriman)).length }}</p>
          </div>
          <div class="bg-red-50 p-4 rounded-lg border border-red-100 text-center">
            <p class="text-xs text-red-600 font-bold uppercase tracking-wide">Piutang (Belum Lunas)</p>
            <p class="text-2xl font-black text-red-700 mt-1">{{ packages.filter(p => p.status_pembayaran === 'Belum Lunas').length }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
          <span class="w-8 h-8 rounded bg-purple-100 text-purple-600 flex items-center justify-center mr-3">2</span>
          Kinerja Kurir & Operasional
        </h2>
        
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Nama Kurir</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Total Tugas Diselesaikan</th>
                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total Ongkos Terkumpul</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="Object.keys(courierStats).length === 0">
                <td colspan="3" class="px-4 py-6 text-center text-gray-500 text-sm">Belum ada data tugas diselesaikan.</td>
              </tr>
              <tr v-for="(stat, name) in courierStats" :key="name" class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm font-bold text-gray-800">{{ name }}</td>
                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-600">{{ stat.count }} Tugas</td>
                <td class="px-4 py-3 text-right text-sm font-bold text-green-600">{{ formatRupiah(stat.revenue) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center border-b pb-3">
          <span class="w-8 h-8 rounded bg-green-100 text-green-600 flex items-center justify-center mr-3">3</span>
          Evaluasi Keuangan
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
          <div>
            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">Arus Kas Masuk (Pendapatan)</h4>
            <div class="space-y-3">
              <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                <span class="text-gray-600 text-sm">Total Transaksi Masuk</span>
                <span class="font-bold text-gray-900">{{ incomeTransactions.length }} Trx</span>
              </div>
              <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                <span class="text-gray-600 text-sm">Nominal Pendapatan</span>
                <span class="font-bold text-green-600">{{ formatRupiah(totalIncome) }}</span>
              </div>
            </div>
          </div>
          
          <div>
            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">Arus Kas Keluar (Beban)</h4>
            <div class="space-y-3">
              <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                <span class="text-gray-600 text-sm">Total Transaksi Keluar</span>
                <span class="font-bold text-gray-900">{{ expenseTransactions.length }} Trx</span>
              </div>
              <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                <span class="text-gray-600 text-sm">Nominal Beban</span>
                <span class="font-bold text-red-600">{{ formatRupiah(totalExpense) }}</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-8 bg-blue-50 border border-blue-100 rounded-lg p-5 flex justify-between items-center">
          <div>
            <p class="text-sm font-bold text-blue-800 uppercase tracking-wider">NET PROFIT / LABA BERSIH</p>
            <p class="text-xs text-blue-600 mt-0.5">Saldo akhir setelah dikurangi pengeluaran operasional</p>
          </div>
          <div class="text-2xl font-black" :class="netProfit >= 0 ? 'text-green-600' : 'text-red-600'">
            {{ formatRupiah(netProfit) }}
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// State Data
const packages = ref([])
const works = ref([])
const transactions = ref([])

// Fetching Data dari 3 Endpoint (Pengiriman, Teamwork, Financial)
const fetchReportData = async () => {
  try {
    const [resPkg, resWork, resTrx] = await Promise.all([
      axios.get('http://localhost:8000/api/v1/packages'),
      axios.get('http://localhost:8000/api/v1/works'),
      axios.get('http://localhost:8000/api/v1/transactions')
    ])
    
    packages.value = resPkg.data.data || []
    works.value = resWork.data.data || []
    transactions.value = resTrx.data.data || []
  } catch (error) {
    console.error("Gagal menarik data laporan:", error)
  }
}

// --- LOGIKA KOMPUTASI LAPORAN ---

// 1. Kinerja Kurir (Kelompokkan berdasarkan Kurir)
const courierStats = computed(() => {
  const stats = {}
  works.value.forEach(w => {
    // Pastikan data kurir ada
    const name = w.courier?.name || 'Kurir Dihapus'
    if (!stats[name]) {
      stats[name] = { count: 0, revenue: 0 }
    }
    stats[name].count += 1
    stats[name].revenue += Number(w.harga_ongkos)
  })
  return stats
})

// 2. Evaluasi Keuangan
const incomeTransactions = computed(() => transactions.value.filter(t => t.tipe === 'Uang Masuk'))
const expenseTransactions = computed(() => transactions.value.filter(t => t.tipe === 'Uang Keluar'))

const totalIncome = computed(() => incomeTransactions.value.reduce((acc, curr) => acc + Number(curr.nominal), 0))
const totalExpense = computed(() => expenseTransactions.value.reduce((acc, curr) => acc + Number(curr.nominal), 0))
const netProfit = computed(() => totalIncome.value - totalExpense.value)

// Format Mata Uang
const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
}

// Print Handler
const printReport = () => {
  window.print()
}

onMounted(() => {
  fetchReportData()
})
</script>

<style scoped>
/* CSS Khusus untuk Print Mode */
@media print {
  body * {
    visibility: hidden;
  }
  .print-area, .print-area * {
    visibility: visible;
  }
  .print-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    margin: 0;
    padding: 0;
    box-shadow: none;
  }
  /* Sembunyikan bayangan dan border melengkung saat diprint */
  .shadow-sm, .rounded-xl, .border {
    box-shadow: none !important;
    border-radius: 0 !important;
    border-color: #e5e7eb !important;
  }
}
</style>