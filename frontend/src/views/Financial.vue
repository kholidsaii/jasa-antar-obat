<template>
  <!-- 
    =======================================================================
    MODUL FINANSIAL - PUSAT AKUNTANSI & ARUS KAS
    =======================================================================
    Modul ini dirancang khusus untuk memantau pendapatan dari pengiriman,
    mencatat pengeluaran operasional (bensin, gaji, dll), serta mengevaluasi 
    laba rugi (Profit & Loss) rumah sakit secara real-time.
  -->
  <div class="max-w-7xl mx-auto pb-12 animate-fade-in">
    
    <!-- ========================================== -->
    <!-- 1. HERO HEADER & CALL-TO-ACTION (CTA)      -->
    <!-- ========================================== -->
    <div class="bg-white rounded-t-3xl overflow-hidden shadow-sm border border-gray-100 relative group">
      
      <!-- Background Image dengan efek Parallax & Gradient -->
      <div 
        class="h-48 w-full bg-cover bg-center relative transition-transform duration-1000 group-hover:scale-105" 
        style="background-image: url('https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?q=80&w=2070&auto=format&fit=crop');"
      >
        <!-- Overlay Gradient Emerald/Green khas Keuangan -->
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-900/90 via-teal-900/70 to-transparent backdrop-blur-[1px]"></div>
      </div>
      
      <div class="px-6 sm:px-8 pb-6 flex flex-col lg:flex-row lg:items-end lg:justify-between relative">
        
        <!-- Bagian Kiri: Identitas Modul (Responsif: Stack di HP, Sejajar di PC) -->
        <div class="flex flex-col sm:flex-row sm:items-end -mt-12 sm:-mt-16 mb-6 lg:mb-0 relative z-10 w-full lg:w-auto">
          <div class="bg-white p-1.5 rounded-2xl shadow-xl border border-gray-100 inline-block shrink-0 w-max transition-transform duration-500 hover:-translate-y-2">
            <img 
              src="https://api.dicebear.com/7.x/bottts/svg?seed=Finance&backgroundColor=e2e8f0" 
              alt="Maskot Finansial" 
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-50"
            />
          </div>
          
          <div class="mt-4 sm:mt-0 sm:ml-5 pb-1 sm:pb-2">
            <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full text-[10px] sm:text-xs font-black tracking-widest uppercase border border-emerald-200 mb-2 inline-block shadow-sm">
              Divisi Akuntansi
            </span>
            <!-- Ukuran font responsif agar anti-tiban -->
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 leading-tight tracking-tight">MANAJEMEN FINANSIAL</h1>
            <p class="text-gray-500 text-xs sm:text-sm font-medium mt-1 max-w-lg leading-relaxed">
              Buku besar pencatatan arus kas operasional, pelacakan saldo metode pembayaran, dan evaluasi laba rugi (Profit & Loss).
            </p>
          </div>
        </div>
        
        <!-- Bagian Kanan: Tombol Aksi Cepat (Hanya Superadmin / Akuntan) -->
        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 pb-2 w-full lg:w-auto relative z-10 shrink-0" v-if="['superadmin'].includes(userRole)">
          <button @click="openModalTransaksi" class="flex-1 sm:flex-none justify-center bg-emerald-600 hover:bg-emerald-800 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 shadow-md hover:shadow-xl flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> 
            Catat Transaksi Manual
          </button>
        </div>
      </div>
      
      <!-- ========================================== -->
      <!-- 2. NAVIGATION TABS                         -->
      <!-- ========================================== -->
      <div class="px-6 sm:px-10 border-t border-gray-100 bg-gray-50/50">
        <nav class="flex space-x-8 overflow-x-auto custom-scrollbar" aria-label="Tabs">
          <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300', 
              'group inline-flex items-center py-4 border-b-[3px] font-bold text-sm transition-all whitespace-nowrap outline-none'
            ]">
            <component :is="tab.icon" :class="[activeTab === tab.id ? 'text-emerald-600' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2.5 h-5 w-5 transition-colors']" />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- 3. TAB CONTENTS (CHILD COMPONENTS)         -->
    <!-- ========================================== -->
    <div class="mt-6 relative min-h-[400px] w-full">
      <Transition name="fade-slide" mode="out-in">
        <div :key="activeTab" class="w-full">
          <Overview v-if="activeTab === 'overview'" />
          <AllTransaksi v-else-if="activeTab === 'all-transaksi'" />
          <Banking v-else-if="activeTab === 'banking'" />
          <Accounting v-else-if="activeTab === 'accounting'" />
        </div>
      </Transition>
    </div>

    <!-- ========================================== -->
    <!-- MODAL: CATAT TRANSAKSI KAS MANUAL          -->
    <!-- ========================================== -->
    <div v-if="isModalTransaksiOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <!-- Overlay Background -->
      <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" @click="closeModalTransaksi"></div>
      
      <!-- Modal Content -->
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh] animate-modal-in">
        
        <!-- Header Modal -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
          <div>
            <h3 class="text-xl font-black text-gray-900">Catat Transaksi Manual</h3>
            <p class="text-xs font-semibold text-gray-500 mt-1 uppercase tracking-wider">Input Arus Kas Eksternal</p>
          </div>
          <button @click="closeModalTransaksi" class="text-gray-400 hover:text-red-500 bg-white hover:bg-red-50 p-2 rounded-lg transition-colors focus:outline-none border border-transparent hover:border-red-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <!-- Body Modal -->
        <div class="p-6 overflow-y-auto space-y-6 bg-slate-50/30 custom-scrollbar">
          
          <!-- Notifikasi Peringatan SOP -->
          <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl flex items-start shadow-sm">
            <svg class="w-6 h-6 text-emerald-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-xs text-emerald-900 font-medium leading-relaxed">
              Gunakan form ini <strong>HANYA</strong> untuk mencatat pengeluaran operasional (seperti bensin, tambal ban, ATK) atau pendapatan di luar sistem paket. Pendapatan paket akan tercatat otomatis.
            </p>
          </div>

          <form @submit.prevent="saveTransaksi" id="formTransaksiManual" class="space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <!-- Tipe Transaksi -->
              <div>
                <label class="block text-sm font-black text-gray-800 uppercase tracking-wider mb-2">Tipe Arus Kas</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i :class="formTransaksi.tipe === 'Uang Masuk' ? 'fas fa-arrow-down text-green-500' : 'fas fa-arrow-up text-red-500'"></i>
                  </div>
                  <select v-model="formTransaksi.tipe" class="w-full border border-gray-300 rounded-xl py-3 pl-9 pr-4 bg-white text-sm font-bold outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm appearance-none cursor-pointer" :class="formTransaksi.tipe === 'Uang Masuk' ? 'text-green-700' : 'text-red-700'">
                    <option value="Uang Masuk">Uang Masuk (Debit)</option>
                    <option value="Uang Keluar">Uang Keluar (Kredit)</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-500">
                    <i class="fas fa-chevron-down text-xs"></i>
                  </div>
                </div>
              </div>
              
              <!-- Metode Pembayaran -->
              <div>
                <label class="block text-sm font-black text-gray-800 uppercase tracking-wider mb-2">Sumber / Dompet</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-wallet text-gray-400"></i>
                  </div>
                  <select v-model="formTransaksi.metode_pembayaran" class="w-full border border-gray-300 rounded-xl py-3 pl-9 pr-4 bg-white text-sm font-medium outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm appearance-none cursor-pointer">
                    <option value="Tunai / Cash">Tunai / Cash (Brankas)</option>
                    <option value="Transfer Bank">Transfer Bank (Mutasi)</option>
                    <option value="QRIS / E-Wallet">QRIS / E-Wallet digital</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-500">
                    <i class="fas fa-chevron-down text-xs"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Input Nominal Rupiah -->
            <div>
              <label class="block text-sm font-black text-gray-800 uppercase tracking-wider mb-2">Nominal Uang (Rp)</label>
              <div class="relative flex items-center">
                <span class="absolute left-4 font-black text-gray-400">Rp</span>
                <input 
                  v-model="formTransaksi.nominal" 
                  type="number" 
                  required 
                  placeholder="0" 
                  min="0" 
                  class="w-full border border-gray-300 rounded-xl py-3.5 pl-12 pr-4 outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm font-black text-xl text-gray-900 bg-white tracking-wider"
                >
              </div>
              <p class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-widest text-right">Tanpa titik atau koma</p>
            </div>

            <!-- Deskripsi Transaksi -->
            <div>
              <label class="block text-sm font-black text-gray-800 uppercase tracking-wider mb-2 flex justify-between">
                <span>Keterangan Jurnal</span>
                <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded">*Wajib</span>
              </label>
              <textarea 
                v-model="formTransaksi.deskripsi" 
                rows="3" 
                required 
                placeholder="Contoh: Pembelian Bensin Kurir A, Service Motor Vario, Beli Lakban Hitam..." 
                class="w-full border border-gray-300 rounded-xl p-3.5 outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-sm font-medium text-gray-800 leading-relaxed"
              ></textarea>
            </div>
            
          </form>
        </div>
        
        <!-- Footer Modal -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
          <button @click="closeModalTransaksi" type="button" class="px-6 py-2.5 bg-white border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-100 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-200">
            Batal
          </button>
          <button @click="saveTransaksi" form="formTransaksiManual" type="submit" :disabled="isSaving || !formTransaksi.nominal || !formTransaksi.deskripsi" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-800 disabled:opacity-50 transition-all shadow-md flex items-center focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSaving ? 'Mencatat...' : 'Simpan Transaksi' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, markRaw, computed } from 'vue'
import axios from 'axios'

// --- PENGATURAN KOMPONEN ANAK ---
import Overview from '../components/financial/Overview.vue'
import AllTransaksi from '../components/financial/AllTransaksi.vue'
import Banking from '../components/financial/Banking.vue'
import Accounting from '../components/financial/Accounting.vue'

// --- CEK ROLE PENGGUNA (Hanya Superadmin yang direkomendasikan mengolah finansial) ---
const currentUser = ref(JSON.parse(localStorage.getItem('user') || '{}'))
const userRole = computed(() => currentUser.value.role || 'guest')

// --- ICONS SVG (TABS) ---
const ChartIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>` })
const ListIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>` })
const BankIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>` })

const tabs = [
  { id: 'overview', name: 'Dashboard Keuangan', icon: ChartIcon },
  { id: 'all-transaksi', name: 'Buku Besar Kas', icon: ListIcon },
  { id: 'banking', name: 'Saldo & Dompet', icon: BankIcon },
  { id: 'accounting', name: 'Profit & Loss', icon: ChartIcon },
]

// Default Tab
const activeTab = ref('overview')

// --- STATE MANAGEMENT MODALS ---
const isModalTransaksiOpen = ref(false)
const isSaving = ref(false)

const formTransaksi = ref({
  tipe: 'Uang Keluar', // Default ke uang keluar karena sistem dominan mencatat beban operasional
  metode_pembayaran: 'Tunai / Cash',
  nominal: '',
  deskripsi: ''
})

// --- METODE FORM TRANSAKSI ---
const openModalTransaksi = () => { 
  isModalTransaksiOpen.value = true 
}

const closeModalTransaksi = () => {
  isModalTransaksiOpen.value = false
  formTransaksi.value = { tipe: 'Uang Keluar', metode_pembayaran: 'Tunai / Cash', nominal: '', deskripsi: '' }
}

const saveTransaksi = async () => {
  if (!formTransaksi.value.nominal || !formTransaksi.value.deskripsi) return alert("Nominal dan Deskripsi Wajib diisi!");
  
  isSaving.value = true
  try {
    // MENGGUNAKAN RELATIVE PATH (TIDAK ADA LOCALHOST)
    await axios.post('/transactions', formTransaksi.value)
    closeModalTransaksi()
    
    alert('Transaksi berhasil dibukukan ke dalam sistem akuntansi.')

    // Auto-refresh via trick (re-mount tab) agar data di tabel langsung muncul
    const currentTab = activeTab.value
    activeTab.value = ''
    setTimeout(() => activeTab.value = currentTab, 10)
  } catch (error) {
    console.error("Gagal menyimpan transaksi manual:", error)
    alert('Terjadi kesalahan pada server saat menyimpan transaksi.')
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
/* Transisi Vue standar untuk pergerakan Tab/Component */
.fade-slide-enter-active,
.fade-slide-leave-active { transition: opacity 0.3s ease, transform 0.3s ease; }
.fade-slide-enter-from { opacity: 0; transform: translateY(10px); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-10px); }

/* Animasi Kemunculan */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards; }

/* Animasi Muncul Pop-up Modal */
@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal-in { animation: modalIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards; }

/* Kustomisasi Scrollbar agar Elegan */
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>