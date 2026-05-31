<template>
  <div class="space-y-6 flex flex-col h-full">
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Ringkasan Arus Kas (Cash Flow)</h2>
        <p class="text-sm text-gray-500 mt-1">Pantau pergerakan saldo, pendapatan, dan pengeluaran operasional.</p>
      </div>
      <button @click="fetchTransactions" :disabled="isLoading" class="text-sm bg-gray-50 hover:bg-gray-100 text-gray-600 border border-gray-200 px-4 py-2 rounded-lg font-medium transition-colors flex items-center disabled:opacity-50">
        <svg :class="{'animate-spin': isLoading}" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        {{ isLoading ? 'Memuat...' : 'Refresh Data' }}
      </button>
    </div>

    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <div v-for="i in 3" :key="i" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 animate-pulse">
        <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
        <div class="h-8 bg-gray-200 rounded w-3/4"></div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-4 mr-4 bg-green-50 text-green-500 p-3 rounded-lg group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </div>
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Pendapatan (Masuk)</p>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ formatRupiah(totalPemasukan) }}</p>
        <p class="text-xs text-gray-400 mt-2">Akumulasi seluruh transaksi masuk</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-4 mr-4 bg-red-50 text-red-500 p-3 rounded-lg group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
        </div>
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Pengeluaran (Keluar)</p>
        <p class="text-3xl font-bold text-red-600 mt-2">{{ formatRupiah(totalPengeluaran) }}</p>
        <p class="text-xs text-gray-400 mt-2">BBM, Gaji, dan operasional lainnya</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden group">
        <div class="absolute right-0 top-0 mt-4 mr-4 bg-blue-50 text-blue-500 p-3 rounded-lg group-hover:scale-110 transition-transform">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Saldo Kas Bersih</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ formatRupiah(saldoBersih) }}</p>
        <p class="text-xs text-gray-400 mt-2">Pendapatan dikurangi pengeluaran</p>
      </div>
    </div>

    <div v-if="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 col-span-1">
        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Rasio Arus Kas</h3>
        
        <div class="mt-6 flex items-center justify-center">
          <div class="relative w-48 h-48 rounded-full border-8 border-gray-100 flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 bg-green-500" :style="`clip-path: polygon(0 0, 100% 0, 100% ${persentaseMasuk}%, 0 ${persentaseMasuk}%);`"></div>
            <div class="absolute inset-0 bg-red-500" :style="`clip-path: polygon(0 ${persentaseMasuk}%, 100% ${persentaseMasuk}%, 100% 100%, 0 100%);`"></div>
            <div class="absolute inset-2 bg-white rounded-full flex flex-col items-center justify-center z-10">
              <span class="text-sm text-gray-500 font-semibold">Margin</span>
              <span class="text-2xl font-bold" :class="saldoBersih >= 0 ? 'text-green-600' : 'text-red-600'">
                {{ persentaseMargin }}%
              </span>
            </div>
          </div>
        </div>

        <div class="mt-8 space-y-3">
          <div class="flex items-center justify-between text-sm">
            <span class="flex items-center text-gray-600"><span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span> Pemasukan</span>
            <span class="font-bold">{{ persentaseMasuk }}%</span>
          </div>
          <div class="flex items-center justify-between text-sm">
            <span class="flex items-center text-gray-600"><span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span> Pengeluaran</span>
            <span class="font-bold">{{ 100 - persentaseMasuk }}%</span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-100 col-span-1 lg:col-span-2 overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
          <h3 class="text-lg font-bold text-gray-800">5 Transaksi Terakhir</h3>
          <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded border border-gray-200">Real-time</span>
        </div>
        
        <div class="overflow-x-auto flex-1 p-4">
          <div v-if="recentTransactions.length === 0" class="text-center py-10 text-gray-500">
            Belum ada transaksi tercatat.
          </div>
          <div class="space-y-3">
            <div v-for="trx in recentTransactions" :key="trx.id" class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
              <div class="flex items-center">
                <div :class="['w-10 h-10 rounded-full flex items-center justify-center mr-4', trx.tipe === 'Uang Masuk' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600']">
                  <svg v-if="trx.tipe === 'Uang Masuk'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
                <div>
                  <p class="font-bold text-gray-800 text-sm">{{ trx.deskripsi }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(trx.created_at) }} • {{ trx.metode_pembayaran }}</p>
                </div>
              </div>
              <div :class="['font-bold text-sm', trx.tipe === 'Uang Masuk' ? 'text-green-600' : 'text-red-600']">
                {{ trx.tipe === 'Uang Masuk' ? '+' : '-' }} {{ formatRupiah(trx.nominal) }}
              </div>
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

const totalPemasukan = computed(() => {
  return transactions.value.filter(t => t.tipe === 'Uang Masuk').reduce((sum, t) => sum + Number(t.nominal), 0)
})

const totalPengeluaran = computed(() => {
  return transactions.value.filter(t => t.tipe === 'Uang Keluar').reduce((sum, t) => sum + Number(t.nominal), 0)
})

const saldoBersih = computed(() => totalPemasukan.value - totalPengeluaran.value)

const persentaseMasuk = computed(() => {
  const total = totalPemasukan.value + totalPengeluaran.value
  if (total === 0) return 50
  return Math.round((totalPemasukan.value / total) * 100)
})

const persentaseMargin = computed(() => {
  if (totalPemasukan.value === 0) return 0
  return Math.round((saldoBersih.value / totalPemasukan.value) * 100)
})

const recentTransactions = computed(() => transactions.value.slice(0, 5))

const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(API_URL)
    transactions.value = response.data.data
  } catch (error) {
    console.error('Error:', error)
  } finally {
    isLoading.value = false
  }
}

const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)
const formatDate = (dateString) => new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }).format(new Date(dateString))

onMounted(() => fetchTransactions())
</script>