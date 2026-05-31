<template>
  <div class="max-w-7xl mx-auto pb-10">
    
    <!-- Hero Header -->
    <div class="bg-white rounded-t-xl overflow-hidden shadow-sm border border-gray-100">
      <div 
        class="h-48 w-full bg-cover bg-center relative" 
        style="background-image: url('https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?q=80&w=2070&auto=format&fit=crop');"
      >
        <div class="absolute inset-0 bg-blue-900/30 backdrop-blur-[1px]"></div>
      </div>

      <div class="px-6 sm:px-8 pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-12 sm:-mt-16 mb-4 sm:mb-0">
          <div class="bg-white p-1.5 rounded-2xl shadow-lg border border-gray-100 inline-block">
            <img 
              src="https://api.dicebear.com/7.x/bottts/svg?seed=Finance&backgroundColor=e2e8f0" 
              alt="Maskot Financial" 
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-50"
            />
          </div>
          <div class="ml-5 pb-2 text-white sm:text-gray-900 drop-shadow-md sm:drop-shadow-none relative z-10 sm:z-auto top-10 sm:top-0">
            <h1 class="text-2xl sm:text-3xl font-bold leading-tight">FINANSIAL</h1>
            <p class="text-gray-200 sm:text-gray-500 text-sm sm:text-base font-medium mt-1">Keuangan & Akuntansi</p>
          </div>
        </div>
        
        <div class="flex space-x-3 pb-2 pt-14 sm:pt-0">
          <button @click="openModalTransaksi" class="bg-[#3b5998] hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition-colors shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Catat Transaksi Manual
          </button>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="px-6 sm:px-8 border-t border-gray-100 bg-white">
        <nav class="flex space-x-8 overflow-x-auto custom-scrollbar" aria-label="Tabs">
          <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id ? 'border-[#3b5998] text-[#3b5998]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'group inline-flex items-center py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap outline-none'
            ]">
            <component :is="tab.icon" 
              :class="[activeTab === tab.id ? 'text-[#3b5998]' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 h-5 w-5']" 
              aria-hidden="true" 
            />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <!-- TABS CONTENT -->
    <div class="mt-6 relative min-h-[400px]">
      <transition name="fade" mode="out-in">
        <Overview v-if="activeTab === 'overview'" />
        <AllTransaksi v-else-if="activeTab === 'all-transaksi'" />
        <Banking v-else-if="activeTab === 'banking'" />
        <Accounting v-else-if="activeTab === 'accounting'" />
      </transition>
    </div>

    <!-- MODAL CATAT TRANSAKSI MANUAL -->
    <div v-if="isModalTransaksiOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModalTransaksi"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-extrabold text-gray-900">Catat Transaksi Manual</h3>
            <p class="text-xs text-gray-500 mt-0.5">Input pemasukan/pengeluaran di luar sistem paket.</p>
          </div>
          <button @click="closeModalTransaksi" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="saveTransaksi" class="space-y-4">
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tipe Transaksi</label>
                <select v-model="formTransaksi.tipe" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-2 focus:ring-[#3b5998]">
                  <option value="Uang Masuk">Uang Masuk (Debit)</option>
                  <option value="Uang Keluar">Uang Keluar (Kredit)</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode / Dompet</label>
                <select v-model="formTransaksi.metode_pembayaran" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-2 focus:ring-[#3b5998]">
                  <option value="Tunai / Cash">Tunai / Cash</option>
                  <option value="Transfer Bank">Transfer Bank</option>
                  <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nominal (Rp)</label>
              <div class="relative">
                <span class="absolute left-3 top-2.5 text-gray-500 font-bold">Rp</span>
                <input v-model="formTransaksi.nominal" type="number" required placeholder="0" min="0" class="w-full border border-gray-300 rounded-lg p-2.5 pl-10 outline-none focus:ring-2 focus:ring-[#3b5998] font-bold text-gray-900">
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan / Deskripsi</label>
              <textarea v-model="formTransaksi.deskripsi" rows="3" required placeholder="Contoh: Pembelian Bensin Kurir, Beli Lakban..." class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998]"></textarea>
            </div>
            
          </form>
        </div>
        
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeModalTransaksi" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">Batal</button>
          <button @click="saveTransaksi" :disabled="isSaving || !formTransaksi.nominal || !formTransaksi.deskripsi" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 transition disabled:opacity-50 shadow-sm">
            {{ isSaving ? 'Memproses...' : 'Simpan Transaksi' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, markRaw } from 'vue'
import axios from 'axios'
import Overview from '../components/financial/Overview.vue'
import AllTransaksi from '../components/financial/AllTransaksi.vue'
import Banking from '../components/financial/Banking.vue'
import Accounting from '../components/financial/Accounting.vue'

// --- ICONS ---
const EyeIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>` })
const ListIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>` })
const BankIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>` })
const ChartIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>` })

const tabs = [
  { id: 'overview', name: 'Overview', icon: ChartIcon },
  { id: 'all-transaksi', name: 'All Transaksi', icon: ListIcon },
  { id: 'banking', name: 'Dompet & Banking', icon: BankIcon },
  { id: 'accounting', name: 'Laba / Rugi', icon: ChartIcon },
]

const activeTab = ref('all-transaksi')
const isModalTransaksiOpen = ref(false)
const isSaving = ref(false)

const formTransaksi = ref({
  tipe: 'Uang Keluar',
  metode_pembayaran: 'Tunai / Cash',
  nominal: '',
  deskripsi: ''
})

const openModalTransaksi = () => { isModalTransaksiOpen.value = true }
const closeModalTransaksi = () => {
  isModalTransaksiOpen.value = false
  formTransaksi.value = { tipe: 'Uang Keluar', metode_pembayaran: 'Tunai / Cash', nominal: '', deskripsi: '' }
}

const saveTransaksi = async () => {
  isSaving.value = true
  try {
    await axios.post('http://localhost:8000/api/v1/transactions', formTransaksi.value)
    closeModalTransaksi()
    
    // Auto-refresh via trick (re-mount tab)
    const currentTab = activeTab.value
    activeTab.value = ''
    setTimeout(() => activeTab.value = currentTab, 10)
  } catch (error) {
    console.error(error)
    alert('Terjadi kesalahan saat menyimpan transaksi.')
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.fade-enter-from,
.fade-leave-to { opacity: 0; transform: translateY(5px); }

@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal-in { animation: modalIn 0.2s ease-out forwards; }
.custom-scrollbar::-webkit-scrollbar { height: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>