<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
      <div>
        <h2 class="text-xl font-bold text-gray-800">Sistem Kas Ganda (Banking)</h2>
        <p class="text-sm text-gray-500 mt-1">Pemantauan Uang Sistem (Kurir) vs Uang Real (Bank).</p>
      </div>
      <button @click="fetchTransactions" class="text-sm bg-white border px-4 py-2 rounded-lg text-gray-600 font-medium hover:bg-gray-50 shadow-sm flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Refresh Data
      </button>
    </div>

    <div v-if="isLoading" class="p-10 flex justify-center"><svg class="animate-spin h-8 w-8 text-[#3b5998]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>

    <div v-else class="p-6 space-y-8 bg-gray-50/50">
      
      <div class="bg-white p-5 rounded-xl border border-yellow-200 shadow-sm">
        <div class="flex justify-between items-end border-b border-yellow-100 pb-3 mb-4">
          <div>
            <h3 class="text-lg font-black text-yellow-800"><i class="fas fa-wallet mr-2"></i>1. Kas Operasional (Sistem / Tangan Kurir)</h3>
            <p class="text-xs text-gray-500 mt-1">Uang hasil paket cash/qris di lapangan yang belum disetor ke bank.</p>
          </div>
          <div class="text-right">
            <p class="text-[10px] uppercase font-bold text-gray-400">Total Belum Disetor</p>
            <p class="text-2xl font-black text-yellow-600">{{ formatRupiah(summaryKasOperasional.saldo) }}</p>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="(data, name) in dompetOperasional" :key="name" class="bg-yellow-50/50 border border-yellow-100 p-4 rounded-lg">
            <h4 class="font-bold text-gray-800 text-sm mb-1">{{ name }}</h4>
            <p class="text-xl font-black text-gray-900 mb-2">{{ formatRupiah(data.saldo) }}</p>
            <div class="text-[10px] text-gray-500 space-y-1">
              <div class="flex justify-between"><span>Masuk (Ops):</span> <span class="text-green-600 font-bold">+{{ formatRupiah(data.masuk) }}</span></div>
              <div class="flex justify-between"><span>Keluar (Ops):</span> <span class="text-red-600 font-bold">-{{ formatRupiah(data.keluar) }}</span></div>
              <div class="flex justify-between"><span>Mutasi (Keluar):</span> <span class="text-blue-600 font-bold">-{{ formatRupiah(data.mutasi_keluar) }}</span></div>
            </div>
          </div>
          
          <div v-if="dompetBiayaAdmin" class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 bg-indigo-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-bl-lg">HAK SYAMIL</div>
            <h4 class="font-bold text-gray-800 text-sm mb-1">Biaya Admin (Sistem)</h4>
            <p class="text-xl font-black text-gray-900 mb-2">{{ formatRupiah(dompetBiayaAdmin.saldo) }}</p>
            <div class="text-[10px] text-gray-500 space-y-1">
              <div class="flex justify-between"><span>Terkumpul:</span> <span class="text-green-600 font-bold">+{{ formatRupiah(dompetBiayaAdmin.masuk) }}</span></div>
              <div class="flex justify-between"><span>Telah Dimutasi:</span> <span class="text-indigo-600 font-bold">-{{ formatRupiah(dompetBiayaAdmin.mutasi_keluar) }}</span></div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white p-5 rounded-xl border border-emerald-200 shadow-sm">
        <div class="flex justify-between items-end border-b border-emerald-100 pb-3 mb-4">
          <div>
            <h3 class="text-lg font-black text-emerald-800"><i class="fas fa-university mr-2"></i>2. Rekening Bank (Uang Real)</h3>
            <p class="text-xs text-gray-500 mt-1">Dana aman terverifikasi di dalam Bank Fisik.</p>
          </div>
          <div class="text-right">
            <p class="text-[10px] uppercase font-bold text-gray-400">Total Uang di Bank</p>
            <p class="text-2xl font-black text-emerald-600">{{ formatRupiah(summaryUangReal.saldo) }}</p>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-for="(data, name) in dompetReal" :key="name" class="bg-emerald-50/50 border border-emerald-100 p-4 rounded-lg">
            <h4 class="font-bold text-gray-800 text-sm mb-1">{{ name }}</h4>
            <p class="text-xl font-black text-gray-900 mb-2">{{ formatRupiah(data.saldo) }}</p>
            <div class="text-[10px] text-gray-500 space-y-1">
              <div class="flex justify-between"><span>Masuk (Langsung):</span> <span class="text-green-600 font-bold">+{{ formatRupiah(data.masuk) }}</span></div>
              <div class="flex justify-between"><span>Diterima Setoran:</span> <span class="text-blue-600 font-bold">+{{ formatRupiah(data.mutasi_masuk) }}</span></div>
              <div class="flex justify-between"><span>Tarik/Keluar:</span> <span class="text-red-600 font-bold">-{{ formatRupiah(data.keluar + data.mutasi_keluar) }}</span></div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-purple-50 border border-purple-200 p-5 rounded-xl shadow-sm">
        <h3 class="text-lg font-black text-purple-800 mb-1"><i class="fas fa-heart mr-2"></i>3. Rekap Dana Amal / Gratis</h3>
        <p class="text-xs text-purple-600 mb-3">Laporan akumulasi nilai sosial pelayanan gratis.</p>
        <p class="text-2xl font-black text-gray-900">{{ formatRupiah(dompetAmal?.saldo || 0) }}</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const transactions = ref([])
const isLoading = ref(true)

const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka || 0)

const groupedBalances = computed(() => {
  const result = {}
  transactions.value.forEach(trx => {
    const method = trx.metode_pembayaran || 'Lain-lain'
    if (!result[method]) {
      result[method] = { masuk: 0, keluar: 0, mutasi_masuk: 0, mutasi_keluar: 0, saldo: 0 }
    }
    const nom = Number(trx.nominal)
    
    // Klasifikasi Murni menggunakan data Native Tipe dari Database
    if (trx.tipe === 'Uang Masuk') { result[method].masuk += nom; result[method].saldo += nom; }
    else if (trx.tipe === 'Uang Keluar') { result[method].keluar += nom; result[method].saldo -= nom; }
    else if (trx.tipe === 'Mutasi Masuk') { result[method].mutasi_masuk += nom; result[method].saldo += nom; }
    else if (trx.tipe === 'Mutasi Keluar') { result[method].mutasi_keluar += nom; result[method].saldo -= nom; }
  })
  return result
})

const dompetOperasional = computed(() => {
  const result = {}
  Object.entries(groupedBalances.value).forEach(([key, val]) => {
    if (key.includes('(Sistem)') && !key.includes('Biaya Admin')) result[key] = val
  })
  return result
})

const dompetBiayaAdmin = computed(() => groupedBalances.value['Biaya Admin (Sistem)'])

const dompetReal = computed(() => {
  const result = {}
  Object.entries(groupedBalances.value).forEach(([key, val]) => {
    if (key.includes('Rek. Bank') || (!key.includes('(Sistem)') && !key.includes('Amal'))) result[key] = val
  })
  return result
})

const dompetAmal = computed(() => groupedBalances.value['Gratis / Amal'])

const summaryKasOperasional = computed(() => {
  let saldo = 0
  Object.values(dompetOperasional.value).forEach(v => saldo += v.saldo)
  if (dompetBiayaAdmin.value) saldo += dompetBiayaAdmin.value.saldo
  return { saldo }
})

const summaryUangReal = computed(() => {
  let saldo = 0
  Object.values(dompetReal.value).forEach(v => saldo += v.saldo)
  return { saldo }
})

const fetchTransactions = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('http://localhost:8000/api/v1/transactions')
    transactions.value = response.data.data
  } catch (e) { console.error(e) } finally { isLoading.value = false }
}

onMounted(() => fetchTransactions())
</script>