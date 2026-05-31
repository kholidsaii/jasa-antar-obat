<template>
  <div class="max-w-7xl mx-auto pb-10">
    <div class="bg-white rounded-t-xl overflow-hidden shadow-sm border border-gray-100">
      <div class="h-48 w-full bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2084&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-black/30 backdrop-blur-[2px]"></div>
      </div>
      <div class="px-6 sm:px-8 pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-12 sm:-mt-16 mb-4 sm:mb-0">
          <div class="bg-white p-1.5 rounded-2xl shadow-lg border border-gray-100 inline-block">
            <img src="https://api.dicebear.com/7.x/bottts/svg?seed=Teamwork&backgroundColor=e2e8f0" alt="Maskot" class="w-24 h-24 sm:w-28 sm:h-28 rounded-xl object-cover bg-gray-50"/>
          </div>
          <div class="ml-5 pb-2">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">TEAMWORK</h1>
            <p class="text-gray-500 text-sm sm:text-base font-medium mt-1">Komunikasi, Koordinasi & Eksekusi Kurir</p>
          </div>
        </div>
        <div class="flex space-x-3 pb-2">
          <button @click="openModalWork" class="bg-[#3b5998] hover:bg-blue-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-md flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Add Work
          </button>
          <button @click="openModalKaryawan" class="bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 px-5 py-2.5 rounded-lg font-semibold text-sm transition-all shadow-sm flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> Add Karyawan
          </button>
        </div>
      </div>
      <div class="px-6 sm:px-8 border-t border-gray-100 bg-gray-50/50">
        <nav class="flex space-x-8 overflow-x-auto custom-scrollbar">
          <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[activeTab === tab.id ? 'border-[#3b5998] text-[#3b5998]' : 'border-transparent text-gray-500 hover:text-gray-700', 'group inline-flex items-center py-4 border-b-2 font-medium text-sm transition-colors outline-none']">
            <component :is="tab.icon" :class="[activeTab === tab.id ? 'text-[#3b5998]' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 h-5 w-5 transition-colors']" />
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

    <div v-if="isModalWorkOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeModalWork"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden max-h-[90vh]">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Buat Pengantaran </h3>
          <button @click="closeModalWork" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        </div>
        <div class="p-6 overflow-y-auto space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Paket (Hanya yg belum diambil)</label>
            <div v-if="apiPackages.length === 0" class="text-xs text-red-500 bg-red-50 p-3 rounded-lg border border-red-100">Belum ada paket yang siap dikirim.</div>
            <div v-else class="max-h-40 overflow-y-auto space-y-2 border border-gray-200 rounded-lg p-3 bg-gray-50 custom-scrollbar">
              <label v-for="pkt in apiPackages" :key="pkt.id" class="flex items-start space-x-3 cursor-pointer p-2 hover:bg-white rounded border border-transparent shadow-sm">
                <input type="checkbox" v-model="formWork.package_ids" :value="pkt.id" class="mt-0.5 w-4 h-4 text-[#3b5998] rounded">
                <div class="flex-1">
                  <p class="text-sm font-bold text-gray-800">#PKT-{{ String(pkt.id).padStart(4, '0') }} - {{ pkt.customer?.nama || 'Unknown' }}</p>
                  <p class="text-xs text-gray-500 line-clamp-1">{{ pkt.deskripsi_pesanan }}</p>
                </div>
              </label>
            </div>
            <p class="text-xs text-gray-500 mt-1 font-medium">Paket terpilih: {{ formWork.package_ids.length }}</p>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Karyawan (Kurir)</label>
            <select v-model="formWork.user_id" @change="autoSelectVehicle" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none">
              <option disabled value="">-- Pilih Kurir Pengantar --</option>
              <option v-for="usr in apiUsers.filter(u => u.role === 'kurir')" :key="usr.id" :value="usr.id">{{ usr.name }}</option>
            </select>
          </div>
          <div v-if="formWork.user_id">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kendaraan Operasional</label>
            <select v-model="formWork.vehicle_id" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none">
              <option disabled value="">-- Pilih Kendaraan Secara Manual --</option>
              <option v-for="veh in apiVehicles" :key="veh.id" :value="veh.id">{{ veh.nama_kendaraan }} - {{ veh.plat_nomor }} ({{ veh.jenis_kepemilikan }})</option>
            </select>
            <p class="text-xs text-gray-400 mt-1">Pilih kendaraan yang akan dipakai kurir ini.</p>
          </div>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeModalWork" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-gray-700">Batal</button>
          <button @click="saveWork" :disabled="isSavingWork || !formWork.vehicle_id" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
            {{ isSavingWork ? 'Memproses...' : 'Tugaskan Kurir' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="isModalKaryawanOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeModalKaryawan"></div>
      <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-auto flex flex-col overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h3 class="text-lg font-extrabold text-gray-900">Registrasi Karyawan Baru</h3>
          <button @click="closeModalKaryawan" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        <div class="p-6 overflow-y-auto max-h-[70vh]">
          <form @submit.prevent="saveKaryawan" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <input v-model="formKaryawan.name" type="text" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon</label>
                <input v-model="formKaryawan.no_telepon" type="text" placeholder="0812xxxxxx" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
              <input v-model="formKaryawan.email" type="email" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-[#3b5998]">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Role / Jabatan</label>
              <select v-model="formKaryawan.role" required class="w-full border border-gray-300 rounded-lg p-2.5 bg-white outline-none focus:ring-[#3b5998]">
                <option value="kurir">Kurir / Driver</option>
                <option value="farmasi">Farmasi / Apoteker</option>
                <option value="admin">Admin Operasional</option>
                <option value="superadmin">Super Administrator</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto Profil (Opsional)</label>
              <div 
                @dragover.prevent="isDraggingAdd = true" 
                @dragleave.prevent="isDraggingAdd = false" 
                @drop.prevent="handleDropAdd" 
                @click="$refs.fileInputAdd.click()"
                :class="[
                  'border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition-all flex flex-col items-center justify-center min-h-[120px]', 
                  isDraggingAdd ? 'border-[#3b5998] bg-blue-50' : 'border-gray-300 bg-white hover:bg-gray-50'
                ]"
              >
                <input type="file" accept="image/*" class="hidden" ref="fileInputAdd" @change="handleFileSelectAdd">
                
                <div v-if="previewAddFoto" class="relative">
                  <img :src="previewAddFoto" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md mx-auto">
                  <span class="text-xs text-[#3b5998] hover:underline mt-2 block font-medium">Klik atau drop untuk ganti</span>
                </div>
                
                <div v-else>
                  <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                  <p class="text-sm text-gray-500"><span class="font-semibold text-[#3b5998]">Klik untuk upload</span> atau drag and drop</p>
                  <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG</p>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t flex justify-end space-x-3">
          <button @click="closeModalKaryawan" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg font-medium text-gray-700">Batal</button>
          <button @click="saveKaryawan" :disabled="isSavingKaryawan" class="px-5 py-2.5 bg-[#3b5998] text-white rounded-lg font-medium hover:bg-blue-800 disabled:opacity-50">
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

import Overview from '../components/teamwork/Overview.vue'
import Pekerjaan from '../components/teamwork/Pekerjaan.vue'
import Karyawan from '../components/teamwork/Karyawan.vue'
import Kendaraan from '../components/teamwork/Kendaraan.vue'

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

const activeTab = ref('pekerjaan')
const isModalWorkOpen = ref(false)
const isModalKaryawanOpen = ref(false)
const isSavingWork = ref(false)
const isSavingKaryawan = ref(false)

const apiPackages = ref([])
const apiUsers = ref([])
const apiVehicles = ref([])

const formWork = ref({ package_ids: [], user_id: '', vehicle_id: '' })
const formKaryawan = ref({ name: '', email: '', role: 'kurir', no_telepon: '', foto: null })
// Tambahkan State untuk Drag and Drop Add
const isDraggingAdd = ref(false)
const previewAddFoto = ref(null)
const fileInputAdd = ref(null)

const handleDropAdd = (e) => {
  isDraggingAdd.value = false
  const file = e.dataTransfer.files[0]
  if (file && file.type.startsWith('image/')) {
    formKaryawan.value.foto = file
    previewAddFoto.value = URL.createObjectURL(file)
  }
}

const handleFileSelectAdd = (e) => {
  const file = e.target.files[0]
  if (file) {
    formKaryawan.value.foto = file
    previewAddFoto.value = URL.createObjectURL(file)
  }
}

// Pastikan saat closeModalKaryawan() dipanggil, preview foto di-reset kembali
const closeModalKaryawan = () => {
  isModalKaryawanOpen.value = false
  formKaryawan.value = { name: '', email: '', role: 'kurir', no_telepon: '', foto: null }
  previewAddFoto.value = null // Tambahkan baris ini
}

// --- METHODS: ADD WORK ---
const openModalWork = async () => {
  isModalWorkOpen.value = true
  try {
    const [resPkg, resUsr, resVeh] = await Promise.all([
      axios.get('/packages'),
      axios.get('/users'),
      axios.get('/vehicles')
    ])
    // Filter paket yang BENAR-BENAR belum diambil
    apiPackages.value = resPkg.data.data?.filter(p => p.status_pengiriman === 'Pesanan diverifikasi') || []
    apiUsers.value = resUsr.data.data || []
    apiVehicles.value = resVeh.data.data || []
  } catch (error) {
    alert("Gagal memuat data dropdown.")
  }
}

// Logika otomatis mencari kendaraan default ketika kurir dipilih
const autoSelectVehicle = () => {
  const selectedKurir = apiUsers.value.find(u => u.id === formWork.value.user_id)
  if (selectedKurir && selectedKurir.vehicle) {
    formWork.value.vehicle_id = selectedKurir.vehicle.id
  } else {
    formWork.value.vehicle_id = '' // Kosongkan agar bisa milih manual
  }
}

const closeModalWork = () => {
  isModalWorkOpen.value = false
  formWork.value = { package_ids: [], user_id: '', vehicle_id: '' }
}

const saveWork = async () => {
  if (formWork.value.package_ids.length === 0 || !formWork.value.user_id || !formWork.value.vehicle_id) {
    return alert('Harap pilih paket, kurir, dan pastikan kendaraan telah dipilih.')
  }
  isSavingWork.value = true
  try {
    await axios.post('/works', formWork.value)
    closeModalWork()
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'pekerjaan', 10)
  } catch (error) {
    alert(error.response?.data?.message || 'Gagal menyimpan tugas.')
  } finally {
    isSavingWork.value = false
  }
}

// --- METHODS: ADD KARYAWAN ---
const openModalKaryawan = () => isModalKaryawanOpen.value = true
// const closeModalKaryawan = () => {
//   isModalKaryawanOpen.value = false
//   formKaryawan.value = { name: '', email: '', role: 'kurir', no_telepon: '', foto: null }
// }

const handleFileUpload = (event) => {
  formKaryawan.value.foto = event.target.files[0]
}

const saveKaryawan = async () => {
  if (!formKaryawan.value.name || !formKaryawan.value.email) return alert('Nama dan Email Wajib diisi!')
  
  isSavingKaryawan.value = true
  try {
    const formData = new FormData()
    formData.append('name', formKaryawan.value.name)
    formData.append('email', formKaryawan.value.email)
    formData.append('role', formKaryawan.value.role)
    if (formKaryawan.value.no_telepon) formData.append('no_telepon', formKaryawan.value.no_telepon)
    if (formKaryawan.value.foto) formData.append('foto', formKaryawan.value.foto)

    await axios.post('/users', formData, { headers: { 'Content-Type': 'multipart/form-data' } })
    closeModalKaryawan()
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'karyawan', 10)
  } catch (error) {
    alert('Gagal menambahkan karyawan. Pastikan email belum terpakai.')
  } finally {
    isSavingKaryawan.value = false
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>