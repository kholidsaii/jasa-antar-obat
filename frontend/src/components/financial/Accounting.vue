<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full relative">
    
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50/50">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Laporan Akuntansi (Laba / Rugi)</h2>
        <p class="text-sm text-gray-500 mt-1">Rekapitulasi pendapatan dan beban operasional (Profit & Loss Statement).</p>
      </div>
      
      <div class="flex space-x-3">
        <button @click="fetchTransactions" :disabled="isLoading" class="bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center disabled:opacity-50 shadow-sm">
          <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
          Refresh
        </button>
        <button @click="printLaporan" class="bg-[#3b5998] hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center shadow-sm">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
          Cetak PDF
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="p-8 space-y-6">
      <div class="h-8 bg-gray-200 rounded w-1/4 animate-pulse"></div>
      <div class="space-y-3"><div v-for="i in 3" :key="i" class="h-10 bg-gray-100 rounded w-full animate-pulse"></div></div>
      <div class="h-8 bg-gray-200 rounded w-1/4 animate-pulse mt-8"></div>
      <div class="space-y-3"><div v-for="i in 3" :key="i" class="h-10 bg-gray-100 rounded w-full animate-pulse"></div></div>
    </div>

    <div v-else class="p-8 overflow-y-auto print-area">
      <div class="text-center mb-10 pb-6 border-b-2 border-gray-800 border-double">
        <h1 class="text-2xl font-black text-gray-900 tracking-wider">JASTAR OBAT</h1>
        <h2 class="text-lg font-bold text-gray-700 mt-1">LAPORAN LABA RUGI KOMPREHENSIF</h2>
        <p class="text-sm text-gray-500 mt-1">Periode: Keseluruhan (Buku Besar Real-time)</p>
      </div>

      <div class="max-w-4xl mx-auto">
        <div class="mb-8">
          <h3 class="text-lg font-bold text-[#3b5998] mb-3 uppercase tracking-wider flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Pendapatan Operasional
          </h3>
          <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
              <tr v-if="incomeCategories.length === 0">
                <td class="py-3 text-gray-500 italic px-4">Belum ada catatan pendapatan.</td>
                <td class="py-3 text-right text-gray-500 italic">Rp 0</td>
              </tr>
              <tr v-for="(item, index) in incomeCategories" :key="index" class="hover:bg-gray-50 transition-colors">
                <td class="py-3 px-4 text-gray-700">{{ item.nama }}</td>
                <td class="py-3 text-right font-medium text-gray-900">{{ formatRupiah(item.total) }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="border-t border-b border-gray-300 bg-green-50/50">
                <td class="py-3 px-4 font-bold text-gray-900">TOTAL PENDAPATAN</td>
                <td class="py-3 text-right font-bold text-green-700 text-base">{{ formatRupiah(totalIncome) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="mb-8">
          <h3 class="text-lg font-bold text-red-600 mb-3 uppercase tracking-wider flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
            Beban Operasional
          </h3>
          <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-100">
              <tr v-if="expenseCategories.length === 0">
                <td class="py-3 text-gray-500 italic px-4">Belum ada catatan beban pengeluaran.</td>
                <td class="py-3 text-right text-gray-500 italic">Rp 0</td>
              </tr>
              <tr v-for="(item, index) in expenseCategories" :key="index" class="hover:bg-gray-50 transition-colors">
                <td class="py-3 px-4 text-gray-700">{{ item.nama }}</td>
                <td class="py-3 text-right font-medium text-gray-900">({{ formatRupiah(item.total) }})</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="border-t border-b border-gray-300 bg-red-50/50">
                <td class="py-3 px-4 font-bold text-gray-900">TOTAL BEBAN OPERASIONAL</td>
                <td class="py-3 text-right font-bold text-red-600 text-base">({{ formatRupiah(totalExpense) }})</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="mt-10 rounded-xl border-2 overflow-hidden shadow-sm" :class="netProfit >= 0 ? 'border-green-500' : 'border-red-500'">
          <div class="flex justify-between items-center p-6" :class="netProfit >= 0 ? 'bg-green-50' : 'bg-red-50'">
            <div>
              <h2 class="text-xl font-black text-gray-900 uppercase">
                {{ netProfit >= 0 ? 'Laba Bersih Operasional' : 'Rugi Bersih Operasional' }}
              </h2>
              <p class="text-sm mt-1" :class="netProfit >= 0 ? 'text-green-700' : 'text-red-700'">
                Pendapatan dikurangi total beban pengeluaran.
              </p>
            </div>
            <div class="text-3xl font-black" :class="netProfit >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatRupiah(netProfit) }}
            </div>
          </div>
        </div>
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

// --- FUNGSI AUTO-CATEGORIZER (AKUNTANSI) ---
// Membaca deskripsi bebas dari admin/sistem dan merubahnya jadi Kategori Baku
const getKategoriAkuntansi = (deskripsi, tipe) => {
  const desc = deskripsi.toLowerCase()
  if (tipe === 'Uang Masuk') {
    if (desc.includes('ongkir') || desc.includes('ongkos') || desc.includes('pengiriman') || desc.includes('pkt-')) return 'Pendapatan Jasa Distribusi / Ongkir'
    if (desc.includes('tip') || desc.includes('bonus')) return 'Pendapatan Tip / Bonus'
    return 'Pendapatan Operasional Lainnya'
  } else {
    if (desc.includes('bbm') || desc.includes('bensin') || desc.includes('solar') || desc.includes('pertamax')) return 'Beban Bahan Bakar (BBM)'
    if (desc.includes('gaji') || desc.includes('upah') || desc.includes('honor') || desc.includes('insentif')) return 'Beban Gaji & Insentif Karyawan'
    if (desc.includes('servis') || desc.includes('bengkel') || desc.includes('oli') || desc.includes('sparepart')) return 'Beban Pemeliharaan Kendaraan'
    if (desc.includes('makan') || desc.includes('minum') || desc.includes('konsumsi') || desc.includes('snack')) return 'Beban Konsumsi & Kesejahteraan'
    if (desc.includes('parkir') || desc.includes('tol') || desc.includes('retribusi')) return 'Beban Parkir & Tol'
    return 'Beban Operasional Lain-lain'
  }
}

// 1. Grouping Pendapatan (Berdasarkan Kategori Baku)
const incomeCategories = computed(() => {
  const incomes = transactions.value.filter(t => t.tipe === 'Uang Masuk')
  const groups = {}
  
  incomes.forEach(t => {
    const akun = getKategoriAkuntansi(t.deskripsi || '', 'Uang Masuk')
    if (!groups[akun]) groups[akun] = 0
    groups[akun] += Number(t.nominal)
  })

  return Object.keys(groups).map(key => ({ nama: key, total: groups[key] })).sort((a, b) => b.total - a.total)
})

// 2. Grouping Beban Pengeluaran (Berdasarkan Kategori Baku)
const expenseCategories = computed(() => {
  const expenses = transactions.value.filter(t => t.tipe === 'Uang Keluar')
  const groups = {}
  
  expenses.forEach(t => {
    const akun = getKategoriAkuntansi(t.deskripsi || '', 'Uang Keluar')
    if (!groups[akun]) groups[akun] = 0
    groups[akun] += Number(t.nominal)
  })

  return Object.keys(groups).map(key => ({ nama: key, total: groups[key] })).sort((a, b) => b.total - a.total)
})

// 3. Kalkulasi Total
const totalIncome = computed(() => incomeCategories.value.reduce((sum, item) => sum + item.total, 0))
const totalExpense = computed(() => expenseCategories.value.reduce((sum, item) => sum + item.total, 0))
const netProfit = computed(() => totalIncome.value - totalExpense.value)

// --- METHODS ---
const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    transactions.value = response.data.data
  } catch (error) {
    console.error('Error fetching accounting data:', error)
  } finally {
    isLoading.value = false
  }
}

const printLaporan = () => { window.print() }

const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
}

onMounted(() => { fetchTransactions() })
</script>

<style scoped>
@media print {
  body * { visibility: hidden; }
  .print-area, .print-area * { visibility: visible; }
  .print-area { position: absolute; left: 0; top: 0; width: 100%; padding: 20px; background: white; }
}
</style>