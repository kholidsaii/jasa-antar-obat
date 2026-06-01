<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Laporan Akuntansi (Laba / Rugi)</h2>
        <p class="text-sm text-gray-500 mt-1">Rekapitulasi pendapatan dan beban operasional perusahaan.</p>
      </div>
      <div class="flex space-x-3">
        <button @click="fetchTransactions" :disabled="isLoading" class="bg-white hover:bg-gray-100 border px-4 py-2 rounded-lg text-sm font-medium">Refresh</button>
        <button @click="printLaporan" class="bg-[#3b5998] hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
          <i class="fas fa-print mr-2"></i> Cetak PDF
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="p-10 flex justify-center">
       <svg class="animate-spin h-8 w-8 text-[#3b5998]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
    </div>

    <div v-else class="p-8 print:p-2 max-w-4xl mx-auto w-full">
      <div class="text-center border-b-2 border-gray-800 pb-6 mb-8">
        <h1 class="text-2xl font-black text-gray-900 tracking-wider">PROFIT & LOSS STATEMENT</h1>
        <p class="text-gray-600 font-medium">Jasta Obat - Pengiriman Obat</p>
        <p class="text-sm text-gray-400 mt-1">Periode: Semua Waktu (Hingga Hari Ini)</p>
      </div>

      <div class="mb-8">
        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">A. Pendapatan (Income)</h3>
        <div class="space-y-3">
          <div v-for="item in incomeCategories" :key="item.nama" class="flex justify-between text-sm items-center hover:bg-gray-50 p-2 rounded transition">
            <span class="text-gray-700"><i class="fas fa-arrow-right text-green-500 mr-2 text-xs"></i> {{ item.nama }}</span>
            <span class="font-medium text-gray-900">{{ formatRupiah(item.total) }}</span>
          </div>
          <div v-if="incomeCategories.length === 0" class="text-gray-400 text-sm p-2 italic">Belum ada catatan pendapatan.</div>
        </div>
        <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-300 bg-green-50/50 p-3 rounded-lg">
          <span class="font-bold text-gray-800">Total Pendapatan</span>
          <span class="font-bold text-green-700 text-lg">{{ formatRupiah(totalIncome) }}</span>
        </div>
      </div>

      <div class="mb-8">
        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">B. Beban Operasional (Expenses)</h3>
        <div class="space-y-3">
          <div v-for="item in expenseCategories" :key="item.nama" class="flex justify-between text-sm items-center hover:bg-gray-50 p-2 rounded transition">
            <span class="text-gray-700"><i class="fas fa-arrow-left text-red-500 mr-2 text-xs"></i> {{ item.nama }}</span>
            <span class="font-medium text-gray-900">{{ formatRupiah(item.total) }}</span>
          </div>
          <div v-if="expenseCategories.length === 0" class="text-gray-400 text-sm p-2 italic">Belum ada catatan beban pengeluaran.</div>
        </div>
        <div class="flex justify-between items-center mt-4 pt-3 border-t border-gray-300 bg-red-50/50 p-3 rounded-lg">
          <span class="font-bold text-gray-800">Total Beban Operasional</span>
          <span class="font-bold text-red-600 text-lg">{{ formatRupiah(totalExpense) }}</span>
        </div>
      </div>

      <div :class="['p-5 border-2 rounded-xl flex justify-between items-center', netProfit >= 0 ? 'border-blue-400 bg-blue-50' : 'border-red-400 bg-red-50']">
        <div>
          <span class="font-black text-gray-900 block text-lg">C. Laba Bersih (Net Profit)</span>
          <span class="text-xs text-gray-500 font-medium">Total Pendapatan dikurangi Total Beban</span>
        </div>
        <span :class="['font-black text-3xl tracking-tight', netProfit >= 0 ? 'text-[#3b5998]' : 'text-red-600']">
          {{ formatRupiah(netProfit) }}
        </span>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api/v1/transactions'
const transactions = ref([])
const isLoading = ref(true)

const getKategoriAkuntansi = (deskripsi, tipe) => {
  const text = deskripsi.toLowerCase()
  if (tipe === 'Uang Masuk') {
    if (text.includes('paket')) return 'Pendapatan Layanan Pengiriman (PKT)'
    if (text.includes('donasi') || text.includes('subsidi')) return 'Pendapatan Lain-lain'
    return 'Pendapatan Umum'
  } else {
    if (text.includes('bensin') || text.includes('bbm') || text.includes('parkir')) return 'Beban Transportasi / Bensin'
    if (text.includes('makan') || text.includes('minum') || text.includes('konsumsi')) return 'Beban Konsumsi Pegawai'
    if (text.includes('lakban') || text.includes('kertas') || text.includes('plastik') || text.includes('packing')) return 'Beban Perlengkapan (Supplies)'
    if (text.includes('gaji') || text.includes('upah') || text.includes('thr')) return 'Beban Gaji & Upah Pegawai'
    if (text.includes('service') || text.includes('bengkel') || text.includes('oli') || text.includes('ban')) return 'Beban Pemeliharaan Kendaraan'
    return 'Beban Lain-lain'
  }
}

const incomeCategories = computed(() => {
  // Hanya menghitung Uang Masuk operasional murni (Mengecualikan Amal & Mutasi)
  const incomes = transactions.value.filter(t => t.tipe === 'Uang Masuk' && t.metode_pembayaran !== 'Gratis / Amal')
  const groups = {}
  incomes.forEach(t => {
    const akun = getKategoriAkuntansi(t.deskripsi || '', 'Uang Masuk')
    if (!groups[akun]) groups[akun] = 0
    groups[akun] += Number(t.nominal)
  })
  return Object.keys(groups).map(key => ({ nama: key, total: groups[key] })).sort((a, b) => b.total - a.total)
})

const expenseCategories = computed(() => {
  // Hanya menghitung Uang Keluar operasional murni (Mengecualikan Mutasi Keluar)
  const expenses = transactions.value.filter(t => t.tipe === 'Uang Keluar')
  const groups = {}
  expenses.forEach(t => {
    const akun = getKategoriAkuntansi(t.deskripsi || '', 'Uang Keluar')
    if (!groups[akun]) groups[akun] = 0
    groups[akun] += Number(t.nominal)
  })
  return Object.keys(groups).map(key => ({ nama: key, total: groups[key] })).sort((a, b) => b.total - a.total)
})

const totalIncome = computed(() => incomeCategories.value.reduce((sum, item) => sum + item.total, 0))
const totalExpense = computed(() => expenseCategories.value.reduce((sum, item) => sum + item.total, 0))
const netProfit = computed(() => totalIncome.value - totalExpense.value)

const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    transactions.value = response.data.data
  } catch (error) { console.error('Error fetching accounting data:', error) } finally { isLoading.value = false }
}

const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
const printLaporan = () => { window.print() }

onMounted(() => fetchTransactions())
</script>

<style scoped>
@media print {
  body * { visibility: hidden; }
  .print\:p-2, .print\:p-2 * { visibility: visible; }
  .print\:p-2 { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>