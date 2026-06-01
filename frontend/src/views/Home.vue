<template>
  <div class="max-w-7xl mx-auto pb-12 space-y-8 animate-fade-in dashboard-wrapper">
    
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative group">
      <div class="absolute inset-0 bg-gradient-to-br from-[#1e3a8a] via-[#3b5998] to-[#60a5fa] opacity-95 z-10 transition-opacity duration-500 group-hover:opacity-100"></div>
      
      <div class="absolute inset-0 z-10 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>
      
      <div 
        class="absolute inset-0 bg-cover bg-center mix-blend-overlay transition-transform duration-1000 group-hover:scale-105" 
        style="background-image: url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=2053&auto=format&fit=crop');"
      ></div>
      
      <div class="relative p-8 sm:p-12 z-20 text-white flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
        
        <div class="max-w-3xl space-y-4">
          <div class="flex items-center space-x-3 mb-2">
            <span class="bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-black tracking-widest uppercase border border-white/30 shadow-inner">
              <span class="inline-block w-2 h-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
              Sistem Operasional Aktif
            </span>
            <!-- <span class="bg-black/20 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase border border-white/10 hidden sm:inline-block">
              RS PPN
            </span> -->
          </div>
          
          <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight drop-shadow-lg">
            Dashboard Jastar Obat
          </h1>
          
          <p class="text-sm sm:text-lg text-blue-100 font-medium max-w-2xl leading-relaxed drop-shadow-md">
            Pusat kendali terintegrasi untuk pemantauan distribusi farmasi, pelacakan rute armada kurir, serta rekapitulasi data finansial secara real-time.
          </p>
          
          <div v-if="currentUser.name" class="mt-6 flex items-center bg-white/10 p-2 pr-6 rounded-full backdrop-blur-md border border-white/20 w-max shadow-xl">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mr-3 border border-white/40 overflow-hidden">
              <img :src="`https://api.dicebear.com/7.x/initials/svg?seed=${currentUser.name}&backgroundColor=3b5998`" alt="Avatar" class="w-full h-full object-cover">
            </div>
            <div>
              <p class="text-xs text-blue-200 font-semibold uppercase tracking-wider">Selamat Bertugas,</p>
              <p class="text-sm font-bold text-white">{{ currentUser.name }} <span class="opacity-70 font-normal">({{ userRole.toUpperCase() }})</span></p>
            </div>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto mt-6 lg:mt-0">
          <button @click="fetchGlobalDashboard" :disabled="isLoading" class="flex items-center justify-center bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all shadow-lg disabled:opacity-50 group">
            <svg :class="{'animate-spin': isLoading}" class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            {{ isLoading ? 'Sinkronisasi...' : 'Sinkron Data' }}
          </button>
          
          <button @click="handleLogout" class="flex items-center justify-center bg-red-500/80 hover:bg-red-600 backdrop-blur-md border border-red-400 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all shadow-lg group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Keluar Sistem
          </button>
        </div>

      </div>
    </div>

    <div v-if="isLoading" class="space-y-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="i in 4" :key="'skel-met-'+i" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-36">
          <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
          <div class="h-10 bg-gray-200 rounded w-1/2 mb-2"></div>
          <div class="h-3 bg-gray-100 rounded w-2/3"></div>
        </div>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-96"></div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-96"></div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div v-if="['superadmin', 'admin', 'farmasi', 'kurir'].includes(userRole)" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-blue-50 w-24 h-24 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Total Keseluruhan</p>
            <p class="text-4xl font-black text-gray-900 tracking-tight">{{ totalPaketData }}</p>
            <p class="text-xs text-blue-600 font-bold bg-blue-50 inline-block px-2 py-1 rounded-md mt-2">Semua Data Paket</p>
          </div>
          <div class="p-4 bg-blue-50 text-[#3b5998] rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          </div>
        </div>
      </div>

      <div v-if="['superadmin', 'admin', 'farmasi', 'kurir'].includes(userRole)" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-purple-50 w-24 h-24 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Sedang Diantar</p>
            <p class="text-4xl font-black text-purple-600 tracking-tight">{{ totalDiantar }}</p>
            <p class="text-xs text-purple-600 font-bold bg-purple-50 inline-block px-2 py-1 rounded-md mt-2">Oleh Kurir / Armada</p>
          </div>
          <div class="p-4 bg-purple-50 text-purple-600 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
          </div>
        </div>
      </div>

      <div v-if="['superadmin', 'admin', 'farmasi', 'kurir'].includes(userRole)" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-green-50 w-24 h-24 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Paket Selesai</p>
            <p class="text-4xl font-black text-green-500 tracking-tight">{{ totalSelesai }}</p>
            <p class="text-xs text-green-700 font-bold bg-green-50 inline-block px-2 py-1 rounded-md mt-2">Diterima oleh Pasien</p>
          </div>
          <div class="p-4 bg-green-50 text-green-500 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>

      <div v-if="['superadmin', 'admin'].includes(userRole)" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
        <div class="absolute -right-6 -top-6 bg-orange-50 w-24 h-24 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10">
          <div class="space-y-1">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Total Karyawan</p>
            <p class="text-4xl font-black text-gray-900 tracking-tight">{{ totalKurir }}</p>
            <p class="text-xs text-orange-600 font-bold bg-orange-50 inline-block px-2 py-1 rounded-md mt-2">Sistem Internal Operasional</p>
          </div>
          <div class="p-4 bg-orange-50 text-orange-600 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          </div>
        </div>
      </div>

      <div v-if="userRole === 'superadmin'" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center relative overflow-hidden group hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 sm:col-span-2 lg:col-span-4 xl:col-span-1">
        <div class="absolute -right-6 -top-6 bg-emerald-50 w-24 h-24 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
        <div class="flex justify-between items-start relative z-10 w-full">
          <div class="space-y-1 w-2/3">
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Saldo Kas Bersih</p>
            <p class="text-2xl lg:text-3xl font-black text-emerald-600 tracking-tight truncate" :title="formatRupiah(saldoBersih)">
              {{ formatRupiah(saldoBersih) }}
            </p>
            <p class="text-xs text-emerald-700 font-bold bg-emerald-50 inline-block px-2 py-1 rounded-md mt-2">Buku Akuntansi Real-time</p>
          </div>
          <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl shadow-inner group-hover:rotate-12 transition-transform">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          </div>
        </div>
      </div>
    </div>

    <div v-show="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 col-span-1 flex flex-col hover:shadow-md transition-shadow">
        <div class="mb-6 flex justify-between items-start border-b border-gray-100 pb-4">
          <div>
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Status Paket</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Distribusi posisi paket saat ini</p>
          </div>
          <div class="p-2 bg-blue-50 rounded-lg">
            <svg class="w-5 h-5 text-[#3b5998]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
          </div>
        </div>
        <div class="flex-1 relative min-h-[250px] flex items-center justify-center">
          <canvas id="homeStatusChart"></canvas>
          <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-4">
             <span class="text-3xl font-black text-gray-800">{{ totalPaketData }}</span>
             <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total</span>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 col-span-1 lg:col-span-2 flex flex-col hover:shadow-md transition-shadow">
        <div class="mb-6 flex justify-between items-start border-b border-gray-100 pb-4">
          <div>
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Tren Operasional Pengiriman</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Pergerakan siklus paket berdasarkan kategori</p>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-[10px] font-black bg-gray-100 text-gray-600 px-3 py-1 rounded-md uppercase tracking-wider">Real-time</span>
          </div>
        </div>
        <div class="flex-1 relative min-h-[250px] w-full">
          <canvas id="homeTrendChart"></canvas>
        </div>
      </div>

    </div>

    <div v-show="!isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <div v-if="['superadmin', 'admin'].includes(userRole)" class="bg-white rounded-3xl shadow-sm border border-gray-100 col-span-1 lg:col-span-2 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
        <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Rekapitulasi Kinerja Kurir</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Distribusi tugas pada armada aktif hari ini</p>
          </div>
          <div class="p-2 bg-purple-50 rounded-lg">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
          </div>
        </div>
        
        <div class="overflow-x-auto flex-1 p-2">
          <table class="min-w-full divide-y divide-gray-100">
            <thead>
              <tr>
                <th scope="col" class="px-6 py-4 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">Nama Kurir</th>
                <th scope="col" class="px-6 py-4 text-center text-[11px] font-black text-gray-400 uppercase tracking-widest">Paket Selesai</th>
                <th scope="col" class="px-6 py-4 text-center text-[11px] font-black text-gray-400 uppercase tracking-widest">Proses Jalan</th>
                <th scope="col" class="px-6 py-4 text-right text-[11px] font-black text-gray-400 uppercase tracking-widest">Revenue (Kas)</th>
              </tr>
            </thead>
            <tbody v-if="Object.keys(kurirStats).length > 0" class="divide-y divide-gray-50">
              <tr v-for="(stat, name) in kurirStats" :key="name" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#3b5998]">
                  <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3 text-xs uppercase">{{ name.charAt(0) }}</div>
                    {{ name }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="bg-green-50 text-green-600 px-3 py-1 rounded-md text-xs font-bold border border-green-100">{{ stat.terkirim }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="bg-orange-50 text-orange-600 px-3 py-1 rounded-md text-xs font-bold border border-orange-100">{{ stat.diperjalanan }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-black text-gray-700">
                  {{ formatRupiah(stat.revenue) }}
                </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td colspan="4" class="px-6 py-16 text-center">
                  <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                  <p class="text-sm font-bold text-gray-500">Belum ada tugas kurir yang dieksekusi.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div :class="['bg-white rounded-3xl shadow-sm border border-gray-100 flex flex-col overflow-hidden hover:shadow-md transition-shadow', ['superadmin', 'admin'].includes(userRole) ? 'col-span-1' : 'col-span-1 lg:col-span-3']">
        <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center relative">
          <div>
            <h3 class="text-lg font-black text-gray-800 tracking-tight">Live Feed Operasional</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Aktivitas penugasan armada terkini</p>
          </div>
          <span class="absolute top-8 right-8 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
          </span>
        </div>
        
        <div class="p-6 overflow-y-auto custom-scrollbar flex-1 max-h-[350px] relative">
          
          <div v-if="recentWorks.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
            <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="text-sm font-bold text-gray-400">Pusat kendali kosong.</p>
            <p class="text-xs text-gray-400 mt-1">Belum ada pergerakan kurir atau paket hari ini.</p>
          </div>
          
          <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
            
            <div v-for="(work, index) in recentWorks" :key="work.id" class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
              <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-blue-100 text-blue-600 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 relative z-10 transition-transform group-hover:scale-110">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
              </div>
              
              <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-2xl border border-gray-100 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-1">
                  <span class="text-[10px] font-black px-2 py-1 rounded bg-blue-50 text-blue-600 uppercase tracking-widest border border-blue-100">
                    #WRK-{{ String(work.id).padStart(4, '0') }}
                  </span>
                </div>
                <h4 class="font-bold text-gray-800 text-sm mt-2 leading-snug">
                  Ditugaskan kepada <span class="text-[#3b5998]">{{ work.courier?.name }}</span>
                </h4>
                <p class="text-xs text-gray-500 mt-1">
                  Tujuan: <span class="font-semibold text-gray-700">{{ work.package?.customer?.nama || 'Umum' }}</span>
                </p>
                <div class="mt-3 flex justify-between items-center border-t border-gray-50 pt-2">
                   <span class="text-[10px] text-gray-400 font-bold"><i class="fas fa-clock mr-1"></i> Baru saja</span>
                   <span class="text-xs font-black text-green-600">{{ formatRupiah(work.harga_ongkos) }}</span>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-center">
          <span class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Sistem Pemantauan Otomatis
          </span>
        </div>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import Chart from 'chart.js/auto'

const router = useRouter()

// ==========================================
// 1. PENGELOLAAN SESI & HAK AKSES
// ==========================================
const currentUser = ref(JSON.parse(localStorage.getItem('user') || '{}'))
const userRole = computed(() => currentUser.value.role || 'guest')

// ==========================================
// 2. STATE MANAJEMEN DATA GLOBAL
// ==========================================
const isLoading = ref(true)

// Data Mentah dari API
const rawPackages = ref([])
const rawUsers = ref([])
const rawWorks = ref([])
const rawTransactions = ref([])

// Metrik Perhitungan
const totalPaketData = ref(0)
const totalDiantar = ref(0)
const totalSelesai = ref(0)
const totalKurir = ref(0)
const saldoBersih = ref(0)
const recentWorks = ref([])

// Referensi Instance Chart.js
let chartStatusInstance = null
let chartTrendInstance = null

// ==========================================
// 3. LOGIKA KOMPUTASI & AGREGASI
// ==========================================

// Komputasi Performa Kurir (Hanya Admin & Superadmin)
const kurirStats = computed(() => {
  const stats = {}
  rawWorks.value.forEach(w => {
    const name = w.courier?.name || 'Kurir Tdk Diketahui'
    const status = w.package?.status_pengiriman || ''
    
    if (!stats[name]) {
      stats[name] = { terkirim: 0, diperjalanan: 0, revenue: 0 }
    }
    
    if (['Terkirim', 'Selesai'].includes(status)) {
      stats[name].terkirim += 1
      stats[name].revenue += Number(w.harga_ongkos || w.package?.total_harga || 0)
    } else if (status === 'Diperjalanan') {
      stats[name].diperjalanan += 1
    }
  })
  
  // Sort by revenue tertinggi (Top Driver)
  const sortedKeys = Object.keys(stats).sort((a, b) => stats[b].revenue - stats[a].revenue)
  const sortedStats = {}
  sortedKeys.forEach(k => { sortedStats[k] = stats[k] })
  
  return sortedStats
})

// ==========================================
// 4. API FETCHING & SINKRONISASI
// ==========================================
const fetchGlobalDashboard = async () => {
  isLoading.value = true
  const role = userRole.value;

  try {
    // A. DATA PAKET (Diakses Semua Role)
    if (['superadmin', 'admin', 'farmasi', 'kurir'].includes(role)) {
      const resPackages = await axios.get('/packages')
      rawPackages.value = resPackages.data.data || []
      
      totalPaketData.value = rawPackages.value.length
      totalDiantar.value = rawPackages.value.filter(p => p.status_pengiriman === 'Diperjalanan').length
      totalSelesai.value = rawPackages.value.filter(p => ['Terkirim', 'Selesai'].includes(p.status_pengiriman)).length
    }

    // B. DATA TEAMWORK & KARYAWAN (Hanya Admin & Superadmin)
    if (['superadmin', 'admin'].includes(role)) {
      const [resUsers, resWorks] = await Promise.all([
        axios.get('/users'),
        axios.get('/works')
      ])
      
      rawUsers.value = resUsers.data.data || []
      rawWorks.value = resWorks.data.data || []
      
      totalKurir.value = rawUsers.value.filter(u => u.role === 'kurir').length
      recentWorks.value = rawWorks.value.slice(0, 6) // Ambil 6 terbaru untuk feed
    } else {
      // Jika Farmasi/Kurir, kita buat mock recent works dari paket terakhir 
      // yang ada hubungannya dengan distribusi agar Live Feed tidak kosong.
      const mockWorks = rawPackages.value.slice(0, 5).map(p => ({
        id: p.id,
        courier: { name: 'Sistem Pusat' },
        package: p,
        harga_ongkos: p.total_harga
      }))
      recentWorks.value = mockWorks
    }

    // C. DATA TRANSAKSI KAS (Hanya Superadmin)
    if (role === 'superadmin') {
      const resTransactions = await axios.get('/transactions')
      rawTransactions.value = resTransactions.data.data || []
      
      const pemasukan = rawTransactions.value.filter(t => t.tipe === 'Uang Masuk').reduce((acc, c) => acc + Number(c.nominal), 0)
      const pengeluaran = rawTransactions.value.filter(t => t.tipe === 'Uang Keluar').reduce((acc, c) => acc + Number(c.nominal), 0)
      saldoBersih.value = pemasukan - pengeluaran
    }

    // Menunggu DOM ter-render baru jalankan Chart.js
    await nextTick()
    renderDashboardCharts()

  } catch (error) {
    console.error("Gagal melakukan sinkronisasi mega-dashboard:", error)
  } finally {
    isLoading.value = false
  }
}

// ==========================================
// 5. VISUALISASI GRAFIK (CHART.JS)
// ==========================================
const renderDashboardCharts = () => {
  // Hancurkan instance lama jika tombol refresh ditekan
  if (chartStatusInstance) chartStatusInstance.destroy()
  if (chartTrendInstance) chartTrendInstance.destroy()

  const ctxStatus = document.getElementById('homeStatusChart')
  const ctxTrend = document.getElementById('homeTrendChart')

  if (!ctxStatus || !ctxTrend) return

  // ---- PERSIAPAN DATA GRAFIK STATUS (DOUGHNUT) ----
  const countDiverifikasi = rawPackages.value.filter(p => p.status_pengiriman === 'Pesanan diverifikasi').length
  const countPengemasan = rawPackages.value.filter(p => p.status_pengiriman === 'Pengemasan').length
  const countTungguDriver = rawPackages.value.filter(p => p.status_pengiriman === 'Menunggu Driver').length
  const countDiperjalanan = rawPackages.value.filter(p => p.status_pengiriman === 'Diperjalanan').length
  const countTerkirim = rawPackages.value.filter(p => p.status_pengiriman === 'Terkirim').length
  const countBatal = rawPackages.value.filter(p => p.status_pengiriman === 'Dibatalkan').length

  chartStatusInstance = new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
      labels: ['Verifikasi', 'Pengemasan', 'Tunggu Driver', 'Diperjalanan', 'Terkirim', 'Batal'],
      datasets: [{
        data: [countDiverifikasi, countPengemasan, countTungguDriver, countDiperjalanan, countTerkirim, countBatal],
        backgroundColor: ['#e2e8f0', '#fef08a', '#ffedd5', '#dbeafe', '#22c55e', '#ef4444'],
        borderWidth: 2,
        borderColor: '#ffffff',
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '75%', // Cincin lebih tipis agar estetik
      plugins: {
        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 15, font: { size: 10, family: "'Inter', sans-serif", weight: 'bold' } } },
        tooltip: {
          backgroundColor: 'rgba(15, 23, 42, 0.9)',
          padding: 12,
          cornerRadius: 8,
          titleFont: { size: 13, family: "'Inter', sans-serif" },
          bodyFont: { size: 12, family: "'Inter', sans-serif" }
        }
      }
    }
  })

  // ---- PERSIAPAN DATA GRAFIK TREND (BAR / LINE COMBINATION) ----
  // Karena data riil harian mungkin belum banyak, kita representasikan
  // kategori besar sebagai komparasi beban kerja operasional saat ini.
  chartTrendInstance = new Chart(ctxTrend, {
    type: 'bar',
    data: {
      labels: ['Diverifikasi (Farmasi)', 'Pengemasan (Farmasi)', 'Tunggu Armada (Admin)', 'Pengantaran (Kurir)', 'Selesai (Sistem)'],
      datasets: [
        {
          label: 'Volume Beban Kerja',
          data: [countDiverifikasi, countPengemasan, countTungguDriver, countDiperjalanan, countTerkirim],
          backgroundColor: '#3b5998',
          borderRadius: 6,
          barPercentage: 0.5
        },
        {
          label: 'Garis Tren Efisiensi',
          data: [countDiverifikasi, countPengemasan, countTungguDriver, countDiperjalanan, countTerkirim],
          type: 'line',
          borderColor: '#10b981',
          borderWidth: 3,
          tension: 0.4,
          fill: false,
          pointBackgroundColor: '#10b981',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { position: 'top', align: 'end', labels: { boxWidth: 12, font: { size: 11, family: "'Inter', sans-serif", weight: 'bold' } } },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.9)', padding: 12, cornerRadius: 8 }
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10, family: "'Inter', sans-serif", weight: '600' }, color: '#64748b' } },
        y: { 
          beginAtZero: true, 
          grid: { color: '#f1f5f9', borderDash: [5, 5] },
          ticks: { stepSize: 1, font: { size: 10, family: "'Inter', sans-serif", weight: 'bold' }, color: '#94a3b8' } 
        }
      }
    }
  })
}

// ==========================================
// 6. UTILITAS & FORMATTER
// ==========================================
const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(angka || 0)
}

const handleLogout = async () => {
  try {
    await axios.post('/logout')
  } catch (e) {
    console.log("Error during API logout:", e)
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

// ==========================================
// 7. SIKLUS HIDUP VUE (LIFECYCLE)
// ==========================================
onMounted(() => {
  fetchGlobalDashboard()
})
</script>

<style scoped>
/* Transisi Utama */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

/* Modifikasi Scrollbar agar Estetik dan Profesional */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f8fafc; 
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}

/* Efek Glassmorphism Tambahan */
.backdrop-blur-md {
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
}

/* Timeline Custom Styles untuk Live Feed */
.is-active::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 20px; /* Adjust according to icon width */
  width: 2px;
  height: 100%;
  background: #e2e8f0;
  z-index: 0;
}
.is-active:last-child::before {
  display: none;
}
@media (min-width: 768px) {
  .is-active::before {
    left: 50%;
    transform: translateX(-50%);
  }
}
</style>