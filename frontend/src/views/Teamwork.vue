Tentu, mari kita sempurnakan file utama `Teamwork.vue`. Saya telah menambahkan kembali fitur **"Add Karyawan (Kurir)"** beserta modalnya, dan menghubungkan kedua form tersebut (Add Work & Add Karyawan) secara langsung ke API backend Laravel Anda.

Selain itu, saya juga menambahkan sistem *loading state* agar tombol *submit* berubah menjadi "Memproses..." saat data sedang dikirim, sehingga UI/UX-nya setara dengan aplikasi profesional berstandar industri.

Silakan *copy-paste* dan timpa seluruh isi file `C:/jasa antar obat/frontend/src/views/Teamwork.vue` Anda dengan kode lengkap berikut:

```vue
<template>
  <div class="max-w-7xl mx-auto pb-10">
    
    <div class="bg-white rounded-t-xl overflow-hidden shadow-sm border border-gray-100">
      <div 
        class="h-48 w-full bg-cover bg-center relative" 
        style="background-image: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2084&auto=format&fit=crop');"
      >
        <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px]"></div>
      </div>

      <div class="px-6 sm:px-8 pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-12 sm:-mt-16 mb-4 sm:mb-0">
          <div class="bg-white p-1.5 rounded-2xl shadow-lg border border-gray-100 inline-block">
            <img 
              src="https://api.dicebear.com/7.x/bottts/svg?seed=Teamwork&backgroundColor=e2e8f0" 
              alt="Maskot Teamwork" 
              class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-50"
            />
          </div>
          <div class="ml-5 pb-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight">TEAMWORK</h1>
            <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">Komunikasi, Koordinasi & Eksekusi Kurir</p>
          </div>
        </div>

        <div class="flex space-x-3 pb-2">
          <button @click="openModalWork" class="bg-[#3b5998] hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-md hover:shadow-lg flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Work (Tugas)
          </button>
          <button @click="openModalKaryawan" class="bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-sm hover:shadow-md flex items-center transform hover:-translate-y-0.5">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Add Karyawan
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
        <Pekerjaan v-else-if="activeTab === 'pekerjaan'" />
        <Karyawan v-else-if="activeTab === 'karyawan'" />
        <Kendaraan v-else-if="activeTab === 'kendaraan'" />
      </Transition>
    </div>

    <div v-if="isModalWorkOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModalWork"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh] animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Buat Pekerjaan Baru</h3>
          <button @click="closeModalWork" class="text-gray-400 hover:text-gray-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-6 overflow-y-auto">
          <form @submit.prevent="saveWork" class="space-y-5">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Paket (Dari Farmasi)</label>
              <select v-model="formWork.package_id" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
                <option disabled value="">-- Pilih Paket Siap Kirim --</option>
                <option v-for="pkt in apiPackages" :key="pkt.id" :value="pkt.id">
                  #PKT-{{ String(pkt.id).padStart(4, '0') }} - {{ pkt.customer?.nama }} ({{ pkt.deskripsi_pesanan }})
                </option>
              </select>
              <p v-if="apiPackages.length === 0" class="text-xs text-red-500 mt-1">Belum ada paket yang siap dikirim.</p>
            </div>
            
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Karyawan (Kurir)</label>
              <select v-model="formWork.user_id" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
                <option disabled value="">-- Pilih Kurir --</option>
                <option v-for="usr in apiUsers" :key="usr.id" :value="usr.id">{{ usr.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Kendaraan Armada</label>
              <select v-model="formWork.vehicle_id" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
                <option disabled value="">-- Pilih Kendaraan --</option>
                <option v-for="kend in apiVehicles" :key="kend.id" :value="kend.id">{{ kend.nama_kendaraan }} [{{ kend.plat_nomor }}]</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ongkos Kirim (Rp)</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500 font-bold">Rp</div>
                <input v-model="formWork.harga_ongkos" type="number" min="0" required class="w-full pl-10 border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
              </div>
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeModalWork" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-100 transition-colors">Batal</button>
          <button @click="saveWork" :disabled="isSavingWork" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50 transition-colors flex items-center">
            <svg v-if="isSavingWork" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSavingWork ? 'Memproses...' : 'Tugaskan Kurir' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isModalKaryawanOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="closeModalKaryawan"></div>
      
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-auto flex flex-col overflow-hidden animate-modal-in">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Registrasi Kurir Baru</h3>
          <button @click="closeModalKaryawan" class="text-gray-400 hover:text-gray-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-6">
          <form @submit.prevent="saveKaryawan" class="space-y-5">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
              <input v-model="formKaryawan.name" type="text" required placeholder="Contoh: Budi Santoso" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
              <input v-model="formKaryawan.email" type="email" required placeholder="budi@jastar.com" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password (Untuk Login Kurir)</label>
              <input v-model="formKaryawan.password" type="password" required placeholder="Minimal 6 karakter" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-[#3b5998] focus:border-[#3b5998] transition-all">
            </div>
          </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeModalKaryawan" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-100 transition-colors">Batal</button>
          <button @click="saveKaryawan" :disabled="isSavingKaryawan" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50 transition-colors flex items-center">
            <svg v-if="isSavingKaryawan" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSavingKaryawan ? 'Menyimpan...' : 'Simpan Karyawan' }}
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
import Overview from '../components/teamwork/Overview.vue'
import Pekerjaan from '../components/teamwork/Pekerjaan.vue'
import Karyawan from '../components/teamwork/Karyawan.vue'
import Kendaraan from '../components/teamwork/Kendaraan.vue'

// --- SETUP IKON SVG ---
const ChartIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>` })
const BriefcaseIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.896 1.95-2 1.95H5.75c-1.104 0-2-.856-2-1.95v-4.25M12 18.75v-7.5M12 11.25a3 3 0 100-6 3 3 0 000 6z" /></svg>` })
const UsersIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>` })
const TruckIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>` })

const tabs = [
  { id: 'overview', name: 'Overview', icon: ChartIcon },
  { id: 'pekerjaan', name: 'Pekerjaan', icon: BriefcaseIcon },
  { id: 'karyawan', name: 'Karyawan', icon: UsersIcon },
  { id: 'kendaraan', name: 'Kendaraan', icon: TruckIcon },
]

// --- STATE MANAGEMENT ---
const activeTab = ref('pekerjaan')

// Modals State
const isModalWorkOpen = ref(false)
const isModalKaryawanOpen = ref(false)
const isSavingWork = ref(false)
const isSavingKaryawan = ref(false)

// Dropdown Resources API
const apiPackages = ref([])
const apiUsers = ref([])
const apiVehicles = ref([])

// Models Form
const formWork = ref({ package_id: '', user_id: '', vehicle_id: '', harga_ongkos: '' })
const formKaryawan = ref({ name: '', email: '', password: '' }) // Menyesuaikan default user di Laravel

// --- METHODS: ADD WORK ---
const openModalWork = async () => {
  isModalWorkOpen.value = true
  try {
    const [resPkg, resUsr, resVeh] = await Promise.all([
      axios.get('http://localhost:8000/api/v1/packages'),
      axios.get('http://localhost:8000/api/v1/users'),
      axios.get('http://localhost:8000/api/v1/vehicles')
    ])
    // Filter paket yang belum selesai agar tidak ditugaskan ganda
    apiPackages.value = resPkg.data.data?.filter(p => p.status_pengiriman !== 'Terkirim') || []
    apiUsers.value = resUsr.data.data || []
    apiVehicles.value = resVeh.data.data || []
  } catch (error) {
    console.error("Gagal memuat resource dropdown:", error)
    alert("Gagal memuat data dari server.")
  }
}

const closeModalWork = () => {
  isModalWorkOpen.value = false
  formWork.value = { package_id: '', user_id: '', vehicle_id: '', harga_ongkos: '' }
}

const saveWork = async () => {
  if (!formWork.value.package_id || !formWork.value.user_id || !formWork.value.vehicle_id) {
    return alert('Harap isi semua pilihan paket, kurir, dan kendaraan.')
  }
  
  isSavingWork.value = true
  try {
    await axios.post('http://localhost:8000/api/v1/works', formWork.value)
    closeModalWork()
    
    // Auto-refresh dengan me-reset tab state
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'pekerjaan', 10)
    
  } catch (error) {
    console.error("Error Saving Work:", error)
    alert('Terjadi kesalahan saat menyimpan tugas kerja.')
  } finally {
    isSavingWork.value = false
  }
}

// --- METHODS: ADD KARYAWAN ---
const openModalKaryawan = () => {
  isModalKaryawanOpen.value = true
}

const closeModalKaryawan = () => {
  isModalKaryawanOpen.value = false
  formKaryawan.value = { name: '', email: '', password: '' }
}

const saveKaryawan = async () => {
  if (!formKaryawan.value.name || !formKaryawan.value.email || !formKaryawan.value.password) {
    return alert('Harap isi Nama, Email, dan Password.')
  }
  
  isSavingKaryawan.value = true
  try {
    // Memanggil API Users bawaan/custom Laravel untuk meregistrasi kurir baru
    await axios.post('http://localhost:8000/api/v1/users', formKaryawan.value)
    closeModalKaryawan()
    
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'karyawan', 10)
    
  } catch (error) {
    console.error("Error Saving Karyawan:", error)
    alert('Terjadi kesalahan saat menyimpan data karyawan. Pastikan email belum terpakai.')
  } finally {
    isSavingKaryawan.value = false
  }
}
</script>

<style scoped>
/* Transisi Vue standar untuk pergerakan Tab agar lebih mulus */
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

/* Kustomisasi Scrollbar untuk menu navigasi tab */
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

```