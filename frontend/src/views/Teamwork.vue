<template>
  <div class="max-w-7xl mx-auto pb-10 sm:pb-12 bg-gray-50/50 min-h-screen">
    
    <!-- HEADER SECTION -->
    <div class="bg-white rounded-b-2xl sm:rounded-t-3xl sm:rounded-b-none overflow-hidden shadow-sm border-b sm:border border-gray-100 relative group">
      
      <!-- Banner Background -->
      <div class="h-32 sm:h-48 w-full bg-cover bg-center relative transition-transform duration-1000 group-hover:scale-105" style="background-image: url('https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2084&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-gradient-to-t sm:bg-gradient-to-r from-indigo-900/90 via-purple-900/70 to-transparent backdrop-blur-[1px]"></div>
      </div>
      
      <div class="px-4 sm:px-8 pb-5 sm:pb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        
        <!-- Profile & Title -->
        <div class="flex items-end -mt-10 sm:-mt-16 mb-4 sm:mb-0 relative z-10 w-full sm:w-auto">
          <div class="bg-white p-1 sm:p-1.5 rounded-xl sm:rounded-2xl shadow-xl border border-gray-100 inline-block shrink-0 transition-transform duration-500 hover:-translate-y-2">
            <img src="https://api.dicebear.com/7.x/bottts/svg?seed=Teamwork&backgroundColor=e2e8f0" alt="Maskot Teamwork" class="w-20 h-20 sm:w-28 sm:h-28 rounded-lg sm:rounded-xl object-cover bg-gray-50"/>
          </div>
          
          <div class="ml-4 sm:ml-5 pb-1 sm:pb-2">
            <span class="bg-indigo-50 text-indigo-600 px-2.5 sm:px-3 py-1 rounded-full text-[10px] sm:text-xs font-black tracking-widest uppercase border border-indigo-100 mb-1.5 sm:mb-2 inline-block">
              Divisi Operasional
            </span>
            <h1 class="text-xl sm:text-3xl font-black text-gray-900 leading-tight tracking-tight">TEAMWORK</h1>
            <p class="text-gray-500 text-xs sm:text-sm font-medium mt-0.5 sm:mt-1 hidden sm:block max-w-lg">
              Pusat koordinasi SDM, alokasi armada, dan eksekusi penugasan kurir.
            </p>
          </div>
        </div>
        
        <!-- Action Buttons (Full width on Mobile) -->
        <div class="flex flex-col sm:flex-row space-y-2.5 sm:space-y-0 sm:space-x-3 w-full sm:w-auto relative z-10 shrink-0" v-if="['superadmin', 'admin'].includes(userRole)">
          <button @click="openModalWork" class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-800 text-white px-5 py-3.5 sm:py-2.5 rounded-xl font-bold text-sm transition-all duration-300 shadow-md hover:shadow-xl flex items-center active:scale-95">
            <svg class="w-5 h-5 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Buat Penugasan Baru
          </button>
          <button @click="openModalKaryawan" class="w-full sm:w-auto justify-center bg-white hover:bg-gray-50 text-gray-800 border border-gray-300 px-5 py-3.5 sm:py-2.5 rounded-xl font-bold text-sm transition-all duration-300 shadow-sm hover:shadow-md flex items-center active:scale-95">
            <svg class="w-5 h-5 sm:w-4 sm:h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Registrasi Karyawan
          </button>
        </div>
      </div>
      
      <!-- Navigation Tabs (Swipeable on Mobile) -->
      <div class="border-t border-gray-100 bg-white">
        <nav class="flex overflow-x-auto hide-scrollbar scroll-smooth px-2 sm:px-10" aria-label="Tabs">
          <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300', 
              'group inline-flex items-center px-4 py-3.5 sm:py-4 border-b-[3px] font-bold text-[13px] sm:text-sm transition-all whitespace-nowrap outline-none flex-shrink-0'
            ]">
            <component :is="tab.icon" :class="[activeTab === tab.id ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 sm:mr-2.5 h-5 w-5 transition-colors']" />
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <div class="mt-4 sm:mt-6 px-0 sm:px-0 relative w-full">
      <Transition name="fade-slide" mode="out-in">
        <div :key="activeTab" class="w-full">
          <Pekerjaan v-if="activeTab === 'pekerjaan'" />
          <Karyawan v-else-if="activeTab === 'karyawan'" />
          <Kendaraan v-else-if="activeTab === 'kendaraan'" />
        </div>
      </Transition>
    </div>

    <!-- ========================================== -->
    <!-- MODAL PENUGASAN KURIR (Mobile Bottom Sheet)-->
    <!-- ========================================== -->
    <div v-if="isModalWorkOpen" class="fixed inset-0 z-[100] flex flex-col justify-end sm:items-center sm:justify-center p-0 sm:p-4">
      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModalWork"></div>
      
      <div class="relative bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl w-full sm:max-w-2xl mx-auto flex flex-col overflow-hidden max-h-[92vh] sm:max-h-[90vh] animate-slide-up sm:animate-modal-in">
        
        <!-- Mobile Handlebar -->
        <div class="w-full flex justify-center pt-3 pb-2 sm:hidden cursor-grab" @click="closeModalWork">
          <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 bg-white sm:bg-gray-50 flex justify-between items-center sticky top-0 z-20">
          <div>
            <h3 class="text-base sm:text-xl font-black text-gray-900">Formasi Penugasan</h3>
            <p class="text-[10px] sm:text-xs font-semibold text-gray-500 mt-0.5 sm:mt-1 uppercase tracking-wider">Tahap 3: Alokasi Armada</p>
          </div>
          <button @click="closeModalWork" class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-full transition-colors focus:outline-none shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar space-y-5 sm:space-y-6 bg-white sm:bg-slate-50/30 flex-1">
          
          <!-- TAMBAHAN: STEP 1 (Informasi Penugasan & Deskripsi) -->
          <div>
            <label class="block text-xs sm:text-sm font-black text-gray-800 uppercase tracking-wider mb-2">1. Informasi Penugasan</label>
            <div class="space-y-3">
              <input v-model="formWork.nama_pekerjaan" type="text" required placeholder="Nama / Judul Tugas (Misal: Pengantaran Shift Pagi)" class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium shadow-sm transition-all bg-white">
              <textarea v-model="formWork.deskripsi" rows="2" placeholder="Deskripsi atau Catatan Khusus untuk Kurir (Opsional)" class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium shadow-sm transition-all bg-white custom-scrollbar"></textarea>
            </div>
          </div>

          <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl flex items-start shadow-sm">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-[11px] sm:text-xs text-indigo-900 font-medium leading-relaxed">
              Daftar paket di bawah adalah paket yang <strong class="bg-indigo-200 px-1 rounded">telah selesai dikemas</strong> oleh Farmasi. Pilih satu atau beberapa paket untuk diserahkan ke Kurir.
            </p>
          </div>

          <!-- STEP 2 (Pilih Paket Obat) -->
          <div>
            <div class="flex justify-between items-end mb-2.5">
              <label class="block text-xs sm:text-sm font-black text-gray-800 uppercase tracking-wider">2. Pilih Paket Obat</label>
              <span class="text-[10px] sm:text-xs font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full shadow-inner">{{ formWork.package_ids.length }} Terpilih</span>
            </div>
            
            <div v-if="apiPackages.length === 0" class="flex flex-col items-center justify-center p-8 bg-gray-50 border border-dashed border-gray-300 rounded-xl">
              <div class="bg-white p-3 rounded-full mb-3 shadow-sm">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
              </div>
              <p class="text-sm font-bold text-gray-600">Semua paket sudah ter-dispatch!</p>
              <p class="text-[11px] sm:text-xs text-gray-400 mt-1 text-center">Belum ada paket dari Farmasi saat ini.</p>
            </div>
            
            <div v-else class="max-h-60 overflow-y-auto space-y-2 border border-gray-200 rounded-xl p-3 bg-gray-50 shadow-inner custom-scrollbar relative">
              <label 
                v-for="pkt in apiPackages" 
                :key="pkt.id" 
                :class="[
                  formWork.package_ids.includes(pkt.id) ? 'border-indigo-500 bg-indigo-50 shadow-md ring-1 ring-indigo-500' : 'border-gray-200 bg-white hover:border-indigo-300',
                  'flex items-start space-x-3 sm:space-x-4 cursor-pointer p-3 sm:p-4 rounded-xl border transition-all duration-200'
                ]"
              >
                <div class="flex items-center h-full pt-1">
                  <input type="checkbox" v-model="formWork.package_ids" :value="pkt.id" class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-colors cursor-pointer">
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-start mb-1">
                    <p class="text-[13px] sm:text-sm font-black text-gray-900 truncate pr-2">
                      {{ pkt.customer?.nama || 'Unknown' }}
                    </p>
                    <span class="text-[9px] sm:text-[10px] font-bold bg-white text-gray-600 px-2 py-0.5 rounded border border-gray-200 shrink-0">
                      #PKT-{{ String(pkt.id).padStart(4, '0') }}
                    </span>
                  </div>
                  <p class="text-[11px] sm:text-xs text-gray-600 leading-snug line-clamp-2"><i class="fas fa-prescription-bottle-alt text-gray-400 mr-1"></i> {{ pkt.deskripsi_pesanan }}</p>
                  <p class="text-[10px] text-gray-400 mt-1.5 font-medium truncate"><i class="fas fa-map-marker-alt text-red-400 mr-1"></i> {{ pkt.customer?.alamat || '-' }}</p>
                </div>
              </label>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
            <!-- STEP 3 (Pilih Kurir) -->
            <div>
              <label class="block text-xs sm:text-sm font-black text-gray-800 uppercase tracking-wider mb-1.5 sm:mb-2">3. Pilih Kurir</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                  <i class="fas fa-user-circle text-gray-400"></i>
                </div>
                <select v-model="formWork.user_id" @change="autoSelectVehicle" required class="w-full border border-gray-300 rounded-xl py-3.5 sm:py-3 pl-10 pr-4 bg-white text-[13px] sm:text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm appearance-none">
                  <option disabled value="" class="text-gray-400">-- Pilih Kurir Aktif --</option>
                  <option v-for="usr in apiUsers.filter(u => u.role === 'kurir')" :key="usr.id" :value="usr.id" class="text-gray-900 font-medium">
                    {{ usr.name }}
                  </option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                  <i class="fas fa-chevron-down text-xs"></i>
                </div>
              </div>
            </div>
            
            <!-- STEP 4 (Armada) -->
            <div v-if="formWork.user_id" class="animate-fade-in">
              <label class="block text-xs sm:text-sm font-black text-gray-800 uppercase tracking-wider mb-1.5 sm:mb-2">4. Armada</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                  <i class="fas fa-motorcycle text-gray-400"></i>
                </div>
                <select v-model="formWork.vehicle_id" required class="w-full border border-gray-300 rounded-xl py-3.5 sm:py-3 pl-10 pr-4 bg-white text-[13px] sm:text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm appearance-none">
                  <option disabled value="">-- Set Manual Kendaraan --</option>
                  <option v-for="veh in apiVehicles" :key="veh.id" :value="veh.id" :disabled="veh.status !== 'Tersedia' && veh.user_id !== formWork.user_id">
                    {{ veh.nama_kendaraan }} - {{ veh.plat_nomor }}
                  </option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                  <i class="fas fa-chevron-down text-xs"></i>
                </div>
              </div>
              <p class="text-[10px] text-indigo-600 mt-1.5 font-bold"><i class="fas fa-info-circle"></i> Otomatis diset ke motor default kurir.</p>
            </div>
          </div>
        </div>
        
        <!-- Sticky Footer -->
        <div class="bg-white sm:bg-gray-50 px-5 sm:px-6 py-4 border-t border-gray-100 flex gap-3 sticky bottom-0 z-20 pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] sm:shadow-none sm:rounded-b-2xl">
          <button @click="closeModalWork" class="w-1/3 sm:w-auto px-4 py-3.5 sm:py-2.5 bg-gray-100 hover:bg-gray-200 border border-transparent rounded-xl text-gray-700 font-bold transition-all active:scale-95 text-sm">
            Batal
          </button>
          <button @click="saveWork" :disabled="isSavingWork || !formWork.nama_pekerjaan || formWork.package_ids.length === 0 || !formWork.vehicle_id" class="flex-1 px-4 py-3.5 sm:py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-800 disabled:opacity-50 transition-all shadow-md active:scale-95 flex justify-center items-center text-sm">
            <svg v-if="isSavingWork" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSavingWork ? 'Memproses...' : 'Rilis Penugasan' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL REGISTRASI KARYAWAN (Mobile Bottom Sheet) -->
    <!-- ========================================== -->
    <div v-if="isModalKaryawanOpen" class="fixed inset-0 z-[100] flex flex-col justify-end sm:items-center sm:justify-center p-0 sm:p-4">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeModalKaryawan"></div>
      
      <div class="relative bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl w-full sm:max-w-xl mx-auto flex flex-col overflow-hidden max-h-[92vh] sm:max-h-[95vh] animate-slide-up sm:animate-modal-in">
        
        <!-- Mobile Handlebar -->
        <div class="w-full flex justify-center pt-3 pb-2 sm:hidden cursor-grab" @click="closeModalKaryawan">
          <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 flex justify-between items-center bg-white sm:bg-gray-50 sticky top-0 z-20">
          <div>
            <h3 class="text-base sm:text-xl font-black text-gray-900">Registrasi Karyawan</h3>
            <p class="text-[10px] sm:text-xs font-semibold text-gray-500 mt-0.5 sm:mt-1 uppercase tracking-wider">Buku Induk Karyawan</p>
          </div>
          <button @click="closeModalKaryawan" class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 p-2 rounded-full transition-colors focus:outline-none shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <div class="p-5 sm:p-6 overflow-y-auto bg-white custom-scrollbar space-y-5 sm:space-y-6 flex-1">
          <form @submit.prevent="saveKaryawan" id="formKaryawanBaru" class="space-y-4 sm:space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
              <div>
                <label class="block text-[11px] sm:text-sm font-black text-gray-700 mb-1.5 uppercase tracking-wide">Nama Lengkap</label>
                <input v-model="formKaryawan.name" type="text" required placeholder="Sesuai KTP" class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium text-gray-900 shadow-sm transition-all">
              </div>
              <div>
                <label class="block text-[11px] sm:text-sm font-black text-gray-700 mb-1.5 uppercase tracking-wide">Nomor Telepon</label>
                <input v-model="formKaryawan.no_telepon" type="number" placeholder="0812..." class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium text-gray-900 shadow-sm transition-all">
              </div>
            </div>
            
            <div>
              <label class="block text-[11px] sm:text-sm font-black text-gray-700 mb-1.5 uppercase tracking-wide">Alamat Email Akses</label>
              <input v-model="formKaryawan.email" type="email" required placeholder="nama@jastar.com" class="w-full border border-gray-300 rounded-xl p-3.5 sm:p-3 outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-medium text-gray-900 lowercase shadow-sm transition-all">
              <p class="text-[10px] text-gray-400 mt-1 font-bold">Email untuk login aplikasi.</p>
            </div>
            
            <div class="bg-slate-50 p-4 rounded-xl border border-gray-100">
              <label class="block text-[11px] sm:text-sm font-black text-gray-700 mb-2.5 uppercase tracking-wide">Role / Jabatan Sistem</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-all" :class="formKaryawan.role === 'kurir' ? 'bg-indigo-50 border-indigo-500 ring-1 ring-indigo-500' : 'bg-white border-gray-200'">
                  <input type="radio" v-model="formKaryawan.role" value="kurir" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                  <span class="ml-2.5 text-sm font-bold text-gray-800">Kurir / Driver</span>
                </label>
                <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-all" :class="formKaryawan.role === 'farmasi' ? 'bg-green-50 border-green-500 ring-1 ring-green-500' : 'bg-white border-gray-200'">
                  <input type="radio" v-model="formKaryawan.role" value="farmasi" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                  <span class="ml-2.5 text-sm font-bold text-gray-800">Farmasi / Apotek</span>
                </label>
                <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-all" :class="formKaryawan.role === 'admin' ? 'bg-purple-50 border-purple-500 ring-1 ring-purple-500' : 'bg-white border-gray-200'">
                  <input type="radio" v-model="formKaryawan.role" value="admin" class="w-4 h-4 text-purple-600 border-gray-300 focus:ring-purple-500">
                  <span class="ml-2.5 text-sm font-bold text-gray-800">Admin Dispatch</span>
                </label>
                <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-all" :class="formKaryawan.role === 'superadmin' ? 'bg-red-50 border-red-500 ring-1 ring-red-500' : 'bg-white border-gray-200'">
                  <input type="radio" v-model="formKaryawan.role" value="superadmin" class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                  <span class="ml-2.5 text-sm font-bold text-gray-800">Super Admin</span>
                </label>
              </div>
            </div>
            
            <div>
              <label class="block text-[11px] sm:text-sm font-black text-gray-700 mb-1.5 uppercase tracking-wide flex items-center justify-between">
                <span>Foto Profil Id Card</span>
                <span class="text-[9px] sm:text-[10px] text-gray-400 font-medium normal-case bg-gray-100 px-2 py-0.5 rounded">Opsional</span>
              </label>
              <div 
                @dragover.prevent="isDraggingAdd = true" 
                @dragleave.prevent="isDraggingAdd = false" 
                @drop.prevent="handleDropAdd" 
                @click="$refs.fileInputAdd.click()"
                :class="[
                  'border-2 border-dashed rounded-2xl p-5 sm:p-6 text-center cursor-pointer transition-all flex flex-col items-center justify-center min-h-[120px]', 
                  isDraggingAdd ? 'border-indigo-500 bg-indigo-50 scale-[1.02]' : 'border-gray-300 bg-gray-50 hover:bg-indigo-50/50'
                ]"
              >
                <input type="file" accept="image/*" class="hidden" ref="fileInputAdd" @change="handleFileSelectAdd">
                
                <div v-if="previewAddFoto" class="relative">
                  <img :src="previewAddFoto" class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-4 border-white shadow-md mx-auto">
                  <span class="text-[10px] sm:text-xs text-indigo-600 font-bold mt-2.5 block">Ketuk untuk ganti foto</span>
                </div>
                
                <div v-else class="space-y-1.5">
                  <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-2 shadow-sm">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                  </div>
                  <p class="text-xs sm:text-sm text-gray-600 font-bold text-indigo-600">Ketuk untuk upload</p>
                  <p class="text-[10px] font-semibold text-gray-400 hidden sm:block">Format PNG/JPG max 2MB</p>
                </div>
              </div>
            </div>
          </form>
        </div>
        
        <!-- Sticky Footer -->
        <div class="bg-white sm:bg-gray-50 px-5 sm:px-6 py-4 border-t border-gray-100 flex gap-3 sticky bottom-0 z-20 pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] sm:shadow-none sm:rounded-b-2xl">
          <button @click="closeModalKaryawan" type="button" class="w-1/3 sm:w-auto px-4 py-3.5 sm:py-2.5 bg-gray-100 hover:bg-gray-200 border border-transparent rounded-xl text-gray-700 font-bold transition-all active:scale-95 text-sm">
            Batal
          </button>
          <button @click="saveKaryawan" form="formKaryawanBaru" type="submit" :disabled="isSavingKaryawan" class="flex-1 px-4 py-3.5 sm:py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-800 disabled:opacity-50 transition-all shadow-md active:scale-95 flex justify-center items-center text-sm">
            <svg v-if="isSavingKaryawan" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            {{ isSavingKaryawan ? 'Mendaftarkan...' : 'Simpan Karyawan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, markRaw, computed } from 'vue'
import axios from 'axios'

import Pekerjaan from '../components/teamwork/Pekerjaan.vue'
import Karyawan from '../components/teamwork/Karyawan.vue'
import Kendaraan from '../components/teamwork/Kendaraan.vue'

const currentUser = ref(JSON.parse(localStorage.getItem('user') || '{}'))
const userRole = computed(() => currentUser.value.role || 'guest')

const BriefcaseIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.896 1.95-2 1.95H5.75c-1.104 0-2-.856-2-1.95v-4.25M12 18.75v-7.5M12 11.25a3 3 0 100-6 3 3 0 000 6z" /></svg>` })
const UsersIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>` })
const TruckIcon = markRaw({ template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>` })

const tabs = [
  { id: 'pekerjaan', name: 'Pekerjaan', icon: BriefcaseIcon },
  { id: 'karyawan', name: 'Karyawan', icon: UsersIcon },
  { id: 'kendaraan', name: 'Armada', icon: TruckIcon },
]

const activeTab = ref('pekerjaan')

const isModalWorkOpen = ref(false)
const isModalKaryawanOpen = ref(false)
const isSavingWork = ref(false)
const isSavingKaryawan = ref(false)

const apiPackages = ref([])
const apiUsers = ref([])
const apiVehicles = ref([])

// TAMBAHAN: State nama_pekerjaan dan deskripsi
const formWork = ref({ nama_pekerjaan: '', deskripsi: '', package_ids: [], user_id: '', vehicle_id: '' })
const formKaryawan = ref({ name: '', email: '', role: 'kurir', no_telepon: '', foto: null })

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

const openModalKaryawan = () => isModalKaryawanOpen.value = true

const closeModalKaryawan = () => {
  isModalKaryawanOpen.value = false
  formKaryawan.value = { name: '', email: '', role: 'kurir', no_telepon: '', foto: null }
  previewAddFoto.value = null 
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
    alert('Registrasi karyawan sukses!')
    
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'karyawan', 10)
  } catch (error) {
    alert('Gagal menambahkan karyawan. Pastikan alamat email belum terpakai oleh user lain.')
  } finally {
    isSavingKaryawan.value = false
  }
}

const openModalWork = async () => {
  isModalWorkOpen.value = true
  try {
    const [resPkg, resUsr, resVeh] = await Promise.all([
      axios.get('/packages'),
      axios.get('/users'),
      axios.get('/vehicles')
    ])
    
    apiPackages.value = resPkg.data.data?.filter(p => [
      '3. Ambil paket obat farmasi', 
      '4. Diserah paket obat jastar', 
      '5. Sedang menunggu kurir'
    ].includes(p.status_pengiriman)) || []
    
    apiUsers.value = resUsr.data.data || []
    apiVehicles.value = resVeh.data.data || []
  } catch (error) {
    alert("Koneksi server gagal saat memuat data penugasan.")
  }
}

const autoSelectVehicle = () => {
  const selectedKurir = apiUsers.value.find(u => u.id === formWork.value.user_id)
  if (selectedKurir && selectedKurir.vehicle) {
    formWork.value.vehicle_id = selectedKurir.vehicle.id
  } else {
    formWork.value.vehicle_id = '' 
  }
}

const closeModalWork = () => {
  isModalWorkOpen.value = false
  // Reset semua field
  formWork.value = { nama_pekerjaan: '', deskripsi: '', package_ids: [], user_id: '', vehicle_id: '' }
}

const saveWork = async () => {
  if (!formWork.value.nama_pekerjaan || formWork.value.package_ids.length === 0 || !formWork.value.user_id || !formWork.value.vehicle_id) {
    return alert('Validasi Gagal: Harap isi Judul Penugasan, centang minimal 1 paket, pilih kurir, dan pastikan armada terisi.')
  }
  isSavingWork.value = true
  try {
    await axios.post('/works', formWork.value)
    closeModalWork()
    alert('Work Order berhasil diserahkan kepada Kurir.')
    
    activeTab.value = ''
    setTimeout(() => activeTab.value = 'pekerjaan', 10)
  } catch (error) {
    alert(error.response?.data?.message || 'Terjadi kesalahan sistem saat membuat penugasan.')
  } finally {
    isSavingWork.value = false
  }
}
</script>

<style scoped>
/* Transisi Vue standar untuk pergerakan Tab/Component */
.fade-slide-enter-active,
.fade-slide-leave-active { transition: opacity 0.3s ease, transform 0.3s ease; }
.fade-slide-enter-from { opacity: 0; transform: translateY(10px); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-10px); }

/* Animasi HP Bottom Sheet */
@keyframes slideUp {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up { animation: slideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

/* Animasi Desktop Modal Center */
@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal-in { animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

/* Sembunyikan scrollbar untuk area tab agar mulus di swipe di HP */
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

/* Pengamanan padding di HP (Khusus iPhone Swipe Bar) */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
  .pb-safe { padding-bottom: calc(1rem + env(safe-area-inset-bottom)); }
}
</style>