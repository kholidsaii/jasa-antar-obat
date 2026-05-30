<template>
  <div class="max-w-7xl mx-auto pb-10">
    
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
          <div class="ml-5 pb-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight">FINANCIAL</h1>
            <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">Kejujuran Kunci Keberhasilan</p>
          </div>
        </div>

        <div class="flex space-x-3 pb-2">
          <button @click="openModalTransaksi" class="bg-[#3b5998] hover:bg-blue-800 text-white px-6 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-md hover:shadow-lg flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Transaksi
          </button>
        </div>
      </div>

      <div class="px-6 sm:px-8 border-t border-gray-100 bg-gray-50/50">
        <nav class="flex space-x-8 overflow-x-auto custom-scrollbar">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id 
                ? 'border-[#3b5998] text-[#3b5998]' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'group inline-flex items-center py-4 border-b-2 font-medium text-sm transition-colors whitespace-nowrap outline-none'
            ]"
          >
            <component 
              :is="tab.icon" 
              :class="[activeTab === tab.id ? 'text-[#3b5998]' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 h-5 w-5 transition-colors']" 
            />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-6 relative min-h-[400px]">
      <Transition name="fade" mode="out-in">
        <Overview v-if="activeTab === 'overview'" />
        <AllTransaksi v-else-if="activeTab === 'all-transaksi'" />
        <Accounting v-else-if="activeTab === 'accounting'" />
        <Banking v-else-if="activeTab === 'banking'" />
      </Transition>
    </div>

    <div v-if="isModalTransaksiOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModalTransaksi"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh] animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Catat Transaksi Baru</h3>
          <button @click="closeModalTransaksi" class="text-gray-400 hover:text-gray-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="saveTransaksi" class="space-y-5">
            <div class="flex space-x-4">
              <label 
                class="flex-1 border rounded-lg p-3 cursor-pointer text-center font-semibold transition-all"
                :class="formTransaksi.tipe === 'Uang Masuk' ? 'bg-green-50 border-green-500 text-green-700' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
              >
                <input type="radio" v-model="formTransaksi.tipe" value="Uang Masuk" class="hidden">
                <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Uang Masuk
              </label>
              
              <label 
                class="flex-1 border rounded-lg p-3 cursor-pointer text-center font-semibold transition-all"
                :class="formTransaksi.tipe === 'Uang Keluar' ? 'bg-red-50 border-red-500 text-red-700' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
              >
                <input type="radio" v-model="formTransaksi.tipe" value="Uang Keluar" class="hidden">
                <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                Uang Keluar
              </label>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Metode / Bank</label>
                <select v-model="formTransaksi.metode_pembayaran" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-2 focus:ring-[#3b5998] transition-all">
                  <option value="Tunai / Cash">Tunai / Cash</option>
                  <option value="Transfer BCA">Transfer BCA</option>
                  <option value="Transfer Mandiri">Transfer Mandiri</option>
                  <option value="Transfer BRI">Transfer BRI</option>
                  <option value="QRIS">QRIS</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nominal (Rp)</label>
                <input v-model="formTransaksi.nominal" type="number" min="1" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] transition-all">
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi / Kategori (Accounting)</label>
              <textarea v-model="formTransaksi.deskripsi" rows="2" required placeholder="Contoh: Pendapatan Ongkir, Beli BBM, Gaji Kurir..." class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] transition-all"></textarea>
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeModalTransaksi" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-100 transition-colors">Batal</button>
          <button @click="saveTransaksi" :disabled="isSaving" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50 transition-colors flex items-center">
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSaving ? 'Menyimpan...' : 'Simpan Transaksi' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, markRaw } from 'vue'
import axios from 'axios'

// --- IMPORT SUB KOMPONEN ---
import Overview from '../components/financial/Overview.vue'
import AllTransaksi from '../components/financial/AllTransaksi.vue'
import Accounting from '../components/financial/Accounting.vue'
import Banking from '../components/financial/Banking.vue'

// --- SETUP IKON SVG TABS ---
const ChartIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>` })
const ListIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>` })
const BookIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>` })
const BankIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>` })

const tabs = [
  { id: 'overview', name: 'Overview', icon: ChartIcon },
  { id: 'all-transaksi', name: 'All Transaksi', icon: ListIcon },
  { id: 'accounting', name: 'Accounting', icon: BookIcon },
  { id: 'banking', name: 'Banking', icon: BankIcon },
]

// --- STATE MANAGEMENT ---
const activeTab = ref('all-transaksi')
const isModalTransaksiOpen = ref(false)
const isSaving = ref(false)

const formTransaksi = ref({ 
  tipe: 'Uang Masuk', 
  metode_pembayaran: 'Tunai / Cash', 
  nominal: '', 
  deskripsi: '' 
})

// --- METHODS ---
const openModalTransaksi = () => { isModalTransaksiOpen.value = true }
const closeModalTransaksi = () => {
  isModalTransaksiOpen.value = false
  formTransaksi.value = { tipe: 'Uang Masuk', metode_pembayaran: 'Tunai / Cash', nominal: '', deskripsi: '' }
}

const saveTransaksi = async () => {
  if (!formTransaksi.value.nominal || !formTransaksi.value.deskripsi) {
    return alert('Harap isi Nominal dan Deskripsi Transaksi.')
  }
  
  isSaving.value = true
  try {
    await axios.post('http://localhost:8000/api/v1/transactions', formTransaksi.value)
    closeModalTransaksi()
    
    // Force refresh tab agar data terbaru langsung muncul
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'all-transaksi', 10)
    
  } catch (error) {
    console.error("Error Saving Transaction:", error)
    alert('Terjadi kesalahan saat menyimpan transaksi.')
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
/* Transisi Vue standar untuk pergerakan Tab */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(5px);
}

/* Animasi Muncul Pop-up */
@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-modal-in {
  animation: modalIn 0.2s ease-out forwards;
}

/* Kustomisasi Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
  height: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 4px;
}
</style>