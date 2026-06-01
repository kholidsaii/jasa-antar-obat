<template>
  <!-- 
    ==================================================================================================
    MEGA COMPONENT: EXECUTIVE REPORT DASHBOARD (LAPORAN.VUE)
    ==================================================================================================
    Sistem Laporan Tingkat Lanjut. Menggabungkan data dari Modul Pengiriman, Teamwork, dan Finansial.
    Dibangun dengan pendekatan "Mobile-First" dan "Responsive Flex-Grid" agar 100% anti-tiban di HP.
    Memiliki fitur Export CSV, Print PDF khusus, dan 4 Grafik Interaktif (Chart.js).
    ==================================================================================================
  -->
  <div class="max-w-[1400px] mx-auto pb-16 px-4 sm:px-6 lg:px-8 space-y-8 animate-fade-in print-bg-white relative">
    
    <!-- ======================================================================= -->
    <!-- 1. HERO HEADER & EXPORT ACTIONS (RESPONSIF SEMPURNA)                    -->
    <!-- ======================================================================= -->
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-gray-100 hide-on-print relative group">
      
      <!-- Parallax Background & Gradient Overlay -->
      <div class="h-48 sm:h-56 w-full bg-cover bg-center relative transition-transform duration-1000 group-hover:scale-105" style="background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-gradient-to-r from-[#1e3a8a]/95 via-[#1e40af]/80 to-transparent backdrop-blur-[2px]"></div>
      </div>

      <div class="px-6 sm:px-10 pb-8 flex flex-col lg:flex-row lg:items-end lg:justify-between relative z-10">
        
        <!-- Identitas Laporan (Stack di Mobile, Sejajar di PC) -->
        <div class="flex flex-col sm:flex-row sm:items-end -mt-16 sm:-mt-20 mb-6 lg:mb-0">
          <div class="bg-white p-2 rounded-3xl shadow-2xl border border-gray-100 inline-block shrink-0 transition-transform duration-500 hover:-translate-y-2 w-max">
            <img src="https://api.dicebear.com/7.x/shapes/svg?seed=Report&backgroundColor=e2e8f0" alt="Laporan" class="w-24 h-24 sm:w-32 sm:h-32 rounded-2xl object-cover bg-gray-50"/>
          </div>
          
          <div class="mt-4 sm:mt-0 sm:ml-6 pb-1 sm:pb-2 text-white lg:text-gray-900 drop-shadow-md lg:drop-shadow-none">
            <span class="bg-blue-500/20 lg:bg-blue-50 lg:text-blue-700 text-white backdrop-blur-md px-3 py-1.5 rounded-full text-[10px] sm:text-xs font-black tracking-widest uppercase border border-white/30 lg:border-blue-200 shadow-inner mb-3 inline-flex items-center">
              <svg class="w-3.5 h-3.5 mr-1.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              Sistem Terintegrasi
            </span>
            <h1 class="text-2xl sm:text-4xl xl:text-5xl font-black leading-tight tracking-tight">LAPORAN MANAJEMEN</h1>
            <p class="text-blue-100 lg:text-gray-500 text-xs sm:text-sm font-semibold mt-2 max-w-xl leading-relaxed">
              Pusat evaluasi komprehensif. Menganalisis siklus pengiriman, performa teamwork kurir, dan neraca buku kas finansial (Profit & Loss).
            </p>
          </div>
        </div>

        <!-- Tombol Aksi Laporan (Penuh di HP, Auto di PC) -->
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto shrink-0 pt-4 lg:pt-0 border-t border-white/10 lg:border-none">
          <button @click="fetchReportData" :disabled="isLoading" class="w-full sm:w-auto flex justify-center items-center bg-gray-50 hover:bg-gray-100 text-[#3b5998] border border-gray-200 px-6 py-3.5 rounded-xl font-bold text-sm transition-all shadow-sm disabled:opacity-50 group focus:ring-2 focus:ring-blue-500 focus:outline-none">
            <svg :class="{'animate-spin': isLoading}" class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Sinkronisasi Data
          </button>
          
          <button @click="exportToCSV" :disabled="isLoading" class="w-full sm:w-auto flex justify-center items-center bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3.5 rounded-xl font-bold text-sm transition-all shadow-md hover:shadow-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export Excel (CSV)
          </button>

          <button @click="printReport" :disabled="isLoading" class="w-full sm:w-auto flex justify-center items-center bg-[#3b5998] hover:bg-blue-800 text-white px-6 py-3.5 rounded-xl font-bold text-sm transition-all shadow-md hover:shadow-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak PDF
          </button>
        </div>
      </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 2. PRINT HEADER (HANYA MUNCUL DI PDF/KERTAS CETAK)                      -->
    <!-- ======================================================================= -->
    <div class="hidden print-header mb-10 text-center border-b-[3px] border-gray-900 pb-6">
      <div class="flex justify-center items-center mb-4">
        <svg class="w-12 h-12 text-gray-900 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        <h1 class="text-4xl font-black text-gray-900 uppercase tracking-widest">LAPORAN EVALUASI OPERASIONAL</h1>
      </div>
      <p class="text-gray-700 font-bold text-lg uppercase tracking-wide">Sistem Layanan Jastar Obat - RS PPN</p>
      <p class="text-sm text-gray-500 mt-2 font-mono">Dicetak secara otomatis oleh sistem pada: {{ new Date().toLocaleString('id-ID') }}</p>
    </div>

    <!-- ======================================================================= -->
    <!-- 3. SKELETON LOADING LOADER                                              -->
    <!-- ======================================================================= -->
    <div v-if="isLoading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 hide-on-print">
      <div v-for="i in 4" :key="'skel-rpt-'+i" class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-40">
        <div class="h-3 bg-gray-200 rounded-full w-1/2 mb-5"></div>
        <div class="h-10 bg-gray-200 rounded-lg w-1/3 mb-4"></div>
        <div class="h-3 bg-gray-100 rounded-full w-3/4"></div>
      </div>
      <div class="xl:col-span-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 animate-pulse h-96"></div>
    </div>

    <!-- ======================================================================= -->
    <!-- 4. KEY PERFORMANCE INDICATORS (KPI CARDS)                               -->
    <!-- ======================================================================= -->
    <div v-show="!isLoading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 print-grid">
      
      <!-- KPI 1: Volume Data -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute right-0 top-0 mt-5 mr-5 bg-blue-50 text-blue-600 p-3.5 rounded-2xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <div class="relative z-10 w-3/4">
          <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Paket Terdata</p>
          <p class="text-4xl font-black text-gray-900 tracking-tight">{{ totalPackages }}</p>
          <div class="mt-4 flex items-center">
            <span class="text-[10px] text-blue-700 font-bold bg-blue-50 px-2.5 py-1 rounded-md border border-blue-100 uppercase">
              {{ packages.filter(p => p.status_pengiriman === 'Pengemasan').length }} Antrean Farmasi
            </span>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500"></div>
      </div>

      <!-- KPI 2: Success Rate -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute right-0 top-0 mt-5 mr-5 bg-emerald-50 text-emerald-600 p-3.5 rounded-2xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="relative z-10 w-3/4">
          <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Rasio Keberhasilan</p>
          <p class="text-4xl font-black text-emerald-600 tracking-tight">{{ successRate }}%</p>
          <div class="mt-4 flex items-center">
            <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100 uppercase">
              {{ packages.filter(p => p.status_pengiriman === 'Terkirim').length }} Sukses Terkirim
            </span>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-1 bg-emerald-500"></div>
      </div>

      <!-- KPI 3: SDM Aktif -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute right-0 top-0 mt-5 mr-5 bg-purple-50 text-purple-600 p-3.5 rounded-2xl group-hover:scale-110 group-hover:rotate-12 transition-transform duration-300">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div class="relative z-10 w-3/4">
          <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">SDM Kurir Bertugas</p>
          <p class="text-4xl font-black text-purple-600 tracking-tight">{{ activeCouriersCount }}</p>
          <div class="mt-4 flex items-center">
            <span class="text-[10px] text-purple-700 font-bold bg-purple-50 px-2.5 py-1 rounded-md border border-purple-100 uppercase">
              Dari {{ users.filter(u => u.role === 'kurir').length }} Total Driver
            </span>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 w-full h-1 bg-purple-500"></div>
      </div>

      <!-- KPI 4: Net Profit Kas -->
      <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-lg transition-all duration-300">
        <div class="absolute right-0 top-0 mt-5 mr-5 bg-amber-50 text-amber-600 p-3.5 rounded-2xl group-hover:scale-110 group-hover:-rotate-12 transition-transform duration-300">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="relative z-10 w-full">
          <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-1">Laba Bersih / Margin</p>
          <p :class="['text-2xl lg:text-3xl font-black tracking-tight truncate mt-2', netProfit >= 0 ? 'text-gray-900' : 'text-red-600']" :title="formatRupiah(netProfit)">
            {{ formatRupiah(netProfit) }}
          </p>
          <div class="mt-3 flex items-center">
            <span :class="['text-[10px] font-bold px-2.5 py-1 rounded-md border uppercase', netProfit >= 0 ? 'bg-amber-50 text-amber-800 border-amber-100' : 'bg-red-50 text-red-800 border-red-100']">
              Pemasukan - Beban
            </span>
          </div>
        </div>
        <div :class="['absolute bottom-0 left-0 w-full h-1', netProfit >= 0 ? 'bg-amber-500' : 'bg-red-500']"></div>
      </div>
    </div>

    <!-- ======================================================================= -->
    <!-- 5. AREA GRAFIK INTERAKTIF (CHART.JS) - HIDE ON PRINT                    -->
    <!-- ======================================================================= -->
    <div v-show="!isLoading" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8 hide-on-print">
      
      <!-- Chart 1: Alur Pengiriman (Bar) -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 lg:col-span-2 flex flex-col">
        <div class="mb-6 flex justify-between items-start border-b border-gray-100 pb-5">
          <div>
            <h3 class="text-xl font-black text-gray-800 tracking-tight">Siklus Alur Pengiriman</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Pemantauan volume paket dari Farmasi hingga di tangan Pasien.</p>
          </div>
          <div class="bg-blue-50 p-2.5 rounded-xl border border-blue-100">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
          </div>
        </div>
        <div class="flex-1 relative w-full min-h-[300px]">
          <canvas id="pengirimanChartCanvas"></canvas>
        </div>
      </div>

      <!-- Chart 2: Komparasi Kas (Doughnut) -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col">
        <div class="mb-6 flex justify-between items-start border-b border-gray-100 pb-5">
          <div>
            <h3 class="text-xl font-black text-gray-800 tracking-tight">Komparasi Arus Kas</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Rasio Income vs Expenses.</p>
          </div>
          <div class="bg-emerald-50 p-2.5 rounded-xl border border-emerald-100">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
          </div>
        </div>
        <div class="flex-1 relative w-full min-h-[250px] flex items-center justify-center">
          <canvas id="keuanganChartCanvas"></canvas>
        </div>
      </div>

      <!-- Chart 3: Performa Kurir Top Driver (Horizontal Bar) -->
      <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 lg:col-span-2 xl:col-span-3 flex flex-col">
        <div class="mb-6 flex justify-between items-start border-b border-gray-100 pb-5">
          <div>
            <h3 class="text-xl font-black text-gray-800 tracking-tight">Analitik Kinerja Armada & Kurir</h3>
            <p class="text-xs text-gray-500 font-medium mt-1">Mengukur produktivitas dan penyelesaian rute berdasarkan masing-masing armada.</p>
          </div>
          <div class="bg-purple-50 p-2.5 rounded-xl border border-purple-100">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
          </div>
        </div>
        <div class="flex-1 relative w-full min-h-[350px]">
          <canvas id="kurirChartCanvas"></canvas>
        </div>
      </div>

    </div>

    <!-- ======================================================================= -->
    <!-- 6. TABULAR REPORTS (DATA RINCI BISA DICETAK)                            -->
    <!-- ======================================================================= -->
    <div v-show="!isLoading" class="space-y-8 mt-8">
      
      <!-- Tabel 1: Rekapitulasi SDM & Kurir -->
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden break-inside-avoid">
        <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/50 flex items-center">
          <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mr-4 shadow-sm border border-purple-200">
            <span class="font-black text-lg">1</span>
          </div>
          <div>
            <h3 class="text-lg font-black text-gray-900 tracking-tight">Tabel Rekapitulasi SDM Lapangan</h3>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Penilaian kontribusi pengantaran setiap kurir terhadap margin operasional.</p>
          </div>
        </div>
        
        <div class="overflow-x-auto p-2">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
              <tr>
                <th class="px-6 py-5 text-left text-[11px] font-black text-gray-400 uppercase tracking-widest">Identitas Kurir / Driver</th>
                <th class="px-6 py-5 text-center text-[11px] font-black text-gray-400 uppercase tracking-widest">Paket Selesai</th>
                <th class="px-6 py-5 text-center text-[11px] font-black text-gray-400 uppercase tracking-widest">Status Berjalan</th>
                <th class="px-6 py-5 text-right text-[11px] font-black text-gray-400 uppercase tracking-widest">Kontribusi Ongkos (Gross)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-if="Object.keys(courierStats).length === 0">
                <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm font-medium">Data penugasan kurir masih kosong.</td>
              </tr>
              <tr v-for="(stat, name) in courierStats" :key="name" class="hover:bg-blue-50/30 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-sm font-black text-[#3b5998] flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    {{ name }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-emerald-100 shadow-sm">{{ stat.terkirim }} Diselesaikan</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span class="bg-orange-50 text-orange-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-orange-100 shadow-sm">{{ stat.diperjalanan }} OTW</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                  <span class="text-sm font-black text-gray-900">{{ formatRupiah(stat.revenue) }}</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tabel 2: Profit & Loss Statement (Akuntansi) -->
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden break-inside-avoid">
        <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/50 flex items-center">
          <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mr-4 shadow-sm border border-emerald-200">
            <span class="font-black text-lg">2</span>
          </div>
          <div>
            <h3 class="text-lg font-black text-gray-900 tracking-tight">Laporan Keuangan (Profit & Loss)</h3>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Buku besar yang merangkum kalkulasi akhir dari seluruh divisi.</p>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100 bg-white">
          <!-- Sisi Pendapatan -->
          <div class="p-6 sm:p-8">
            <h4 class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-6 border-b border-gray-100 pb-3 flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
              Sektor Pendapatan (Income)
            </h4>
            <div class="space-y-5">
              <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="text-gray-600 text-sm font-bold">Total Volume Transaksi</span>
                <span class="font-black text-gray-900">{{ incomeTransactions.length }} Entry Buku</span>
              </div>
              <div class="flex justify-between items-center bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                <span class="text-emerald-800 text-sm font-bold">Akumulasi Omset Kotor</span>
                <span class="font-black text-emerald-600 text-xl tracking-tight">{{ formatRupiah(totalIncome) }}</span>
              </div>
            </div>
          </div>
          
          <!-- Sisi Pengeluaran -->
          <div class="p-6 sm:p-8">
            <h4 class="text-xs font-black text-red-600 uppercase tracking-widest mb-6 border-b border-gray-100 pb-3 flex items-center">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
              Sektor Beban (Expense)
            </h4>
            <div class="space-y-5">
              <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="text-gray-600 text-sm font-bold">Total Volume Pengeluaran</span>
                <span class="font-black text-gray-900">{{ expenseTransactions.length }} Entry Buku</span>
              </div>
              <div class="flex justify-between items-center bg-red-50/50 p-4 rounded-xl border border-red-100">
                <span class="text-red-800 text-sm font-bold">Akumulasi Beban Operasional</span>
                <span class="font-black text-red-600 text-xl tracking-tight">{{ formatRupiah(totalExpense) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Margin Kesimpulan -->
        <div class="bg-gray-900 p-8 flex flex-col sm:flex-row justify-between items-center relative overflow-hidden">
          <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
          <div class="mb-4 sm:mb-0 text-center sm:text-left relative z-10">
            <p class="text-sm font-black text-gray-300 uppercase tracking-widest">Laba Bersih Akhir Perusahaan</p>
            <p class="text-xs text-gray-400 font-medium mt-1.5 leading-relaxed max-w-sm">Sisa saldo murni di kas rumah sakit setelah dipotong seluruh beban kerja operasional.</p>
          </div>
          <div class="relative z-10 text-3xl sm:text-4xl font-black px-6 py-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl" :class="netProfit >= 0 ? 'text-emerald-400' : 'text-red-400'">
            {{ formatRupiah(netProfit) }}
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'

// ==============================================================================
// 1. STATE MANAGEMENT & API ENDPOINTS
// ==============================================================================
const isLoading = ref(true)
const errorMessage = ref('')

const packages = ref([])
const works = ref([])
const transactions = ref([])
const users = ref([])

// Variabel referensi instance Chart.js
let pengirimanChart = null
let keuanganChart = null
let kurirChart = null

// ==============================================================================
// 2. KOMPUTASI LOGIKA DATA (DATA AGGREGATION & KPI COMPUTATION)
// ==============================================================================

// A. Metrik Utama Modul Pengiriman
const totalPackages = computed(() => packages.value.length)
const successRate = computed(() => {
  if (totalPackages.value === 0) return 0
  const terkirim = packages.value.filter(p => p.status_pengiriman === 'Terkirim').length
  return Math.round((terkirim / totalPackages.value) * 100)
})

// B. Metrik Utama Modul Finansial
const incomeTransactions = computed(() => transactions.value.filter(t => t.tipe === 'Uang Masuk'))
const expenseTransactions = computed(() => transactions.value.filter(t => t.tipe === 'Uang Keluar'))

const totalIncome = computed(() => incomeTransactions.value.reduce((acc, curr) => acc + Number(curr.nominal || 0), 0))
const totalExpense = computed(() => expenseTransactions.value.reduce((acc, curr) => acc + Number(curr.nominal || 0), 0))
const netProfit = computed(() => totalIncome.value - totalExpense.value)

// Pemantauan Piutang
const belumLunasPackages = computed(() => packages.value.filter(p => p.status_pembayaran === 'Belum Lunas' && p.status_pengiriman !== 'Dibatalkan'))
const belumLunasCount = computed(() => belumLunasPackages.value.length)
const belumLunasValue = computed(() => belumLunasPackages.value.reduce((sum, p) => sum + Number(p.total_harga || 0), 0))

// C. Metrik Utama Modul Teamwork (SDM)
const activeCouriersCount = computed(() => Object.keys(courierStats.value).length)

/**
 * Mengkalkulasi kinerja spesifik untuk masing-masing kurir.
 * Menghitung berapa paket yang selesai diantar, berapa yang sedang OTW,
 * serta berapa sumbangan uang (revenue/harga) yang mereka selesaikan.
 */
const courierStats = computed(() => {
  const stats = {}
  works.value.forEach(w => {
    const name = w.courier?.name || 'Kurir Tdk Ditemukan'
    const status = w.package?.status_pengiriman || ''
    
    if (!stats[name]) {
      stats[name] = { terkirim: 0, diperjalanan: 0, revenue: 0 }
    }
    
    if (status === 'Terkirim' || status === 'Selesai') {
      stats[name].terkirim += 1
      stats[name].revenue += Number(w.package?.total_harga || w.harga_ongkos || 0)
    } else if (status === 'Diperjalanan') {
      stats[name].diperjalanan += 1
    }
  })
  
  // Sortir agar kurir dengan setoran tertinggi ada di posisi atas (Top Driver)
  const sortedKeys = Object.keys(stats).sort((a, b) => stats[b].revenue - stats[a].revenue)
  const result = {}
  sortedKeys.forEach(k => { result[k] = stats[k] })
  return result
})

// ==============================================================================
// 3. FUNGSI UTILITY & EXPORT
// ==============================================================================

/**
 * Format angka menjadi format Rupiah standar Indonesia (IDR)
 */
const formatRupiah = (angka) => {
  return new Intl.NumberFormat('id-ID', { 
    style: 'currency', 
    currency: 'IDR', 
    minimumFractionDigits: 0 
  }).format(angka || 0)
}

/**
 * Trigger menu Print dari Browser yang akan merender format PDF
 */
const printReport = () => {
  window.print()
}

/**
 * Mengonversi data JSON ke format CSV lalu mendownloadnya
 * Fitur ini ditambahkan untuk kebutuhan Enterprise Data Export
 */
const exportToCSV = () => {
  if (packages.value.length === 0) {
    return alert('Tidak ada data untuk di-export.')
  }
  
  // Header Kolom CSV
  let csvContent = "data:text/csv;charset=utf-8,"
  csvContent += "ID Paket,Pelanggan,Alamat,Status Pengiriman,Status Pembayaran,Total Harga\n"
  
  // Isi Baris Data
  packages.value.forEach(p => {
    const nama = p.customer?.nama || 'N/A'
    const alamat = (p.customer?.alamat || '').replace(/,/g, ' ') // Hilangkan koma agar CSV tidak rusak
    const status = p.status_pengiriman || 'N/A'
    const payment = p.status_pembayaran || 'N/A'
    const harga = p.total_harga || 0
    
    csvContent += `PKT-${p.id},${nama},${alamat},${status},${payment},${harga}\n`
  })
  
  // Buat link download palsu
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement("a")
  link.setAttribute("href", encodedUri)
  link.setAttribute("download", `Laporan_Operasional_${new Date().toLocaleDateString('id-ID')}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

// ==============================================================================
// 4. API FETCHING (SINKRONISASI 4 MODUL SEKALIGUS)
// ==============================================================================
const fetchReportData = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    // Relative path memastikan tidak ada masalah CORS/Localhost
    const [resPkg, resWork, resTrx, resUsr] = await Promise.all([
      axios.get('/packages'),
      axios.get('/works'),
      axios.get('/transactions'),
      axios.get('/users')
    ])
    
    packages.value = resPkg.data.data || []
    works.value = resWork.data.data || []
    transactions.value = resTrx.data.data || []
    users.value = resUsr.data.data || []

    // Setelah state terisi, pastikan DOM terender sebelum melukis grafik
    await nextTick()
    renderCharts()
    
  } catch (error) {
    console.error("Sinkronisasi gagal:", error)
    errorMessage.value = "Sistem gagal terhubung dengan pangkalan data utama. Silakan muat ulang halaman."
  } finally {
    isLoading.value = false
  }
}

// ==============================================================================
// 5. RENDERISASI GRAFIK INTERAKTIF (CHART.JS)
// ==============================================================================
const renderCharts = () => {
  // Reset instance lama mencegah bug Hover/Glitch jika direfresh
  if (pengirimanChart) pengirimanChart.destroy()
  if (keuanganChart) keuanganChart.destroy()
  if (kurirChart) kurirChart.destroy()

  const ctxPengiriman = document.getElementById('pengirimanChartCanvas')
  const ctxKeuangan = document.getElementById('keuanganChartCanvas')
  const ctxKurir = document.getElementById('kurirChartCanvas')

  if (!ctxPengiriman || !ctxKeuangan || !ctxKurir) return

  // ---------------------------------------------------------
  // CHART 1: SIKLUS PENGIRIMAN (BAR CHART VERTICAL)
  // ---------------------------------------------------------
  const statusKeys = ['Pesanan diverifikasi', 'Pengemasan', 'Menunggu Driver', 'Diperjalanan', 'Terkirim', 'Dibatalkan']
  const statusCounts = statusKeys.map(key => packages.value.filter(p => p.status_pengiriman === key).length)
  
  pengirimanChart = new Chart(ctxPengiriman, {
    type: 'bar',
    data: {
      labels: ['1. Verif', '2. Kemas', '3. Tngu Kurir', '4. OTW', '5. Sukses', 'Batal'],
      datasets: [{
        label: 'Volume Berkas Paket',
        data: statusCounts,
        backgroundColor: [
          'rgba(226, 232, 240, 0.8)', // slate-200
          'rgba(254, 240, 138, 0.8)', // yellow-200
          'rgba(255, 237, 213, 0.8)', // orange-100
          'rgba(191, 219, 254, 0.8)', // blue-200
          'rgba(167, 243, 208, 0.8)', // emerald-200
          'rgba(254, 202, 202, 0.8)'  // red-200
        ],
        borderColor: ['#94a3b8', '#ca8a04', '#ea580c', '#3b82f6', '#10b981', '#ef4444'],
        borderWidth: 2,
        borderRadius: 8,
        barPercentage: 0.6
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { 
        legend: { display: false },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.95)', padding: 12, cornerRadius: 8 }
      },
      scales: { 
        x: { grid: { display: false }, ticks: { font: { size: 10, family: "'Inter', sans-serif", weight: '600' } } },
        y: { beginAtZero: true, grid: { borderDash: [4, 4] }, ticks: { stepSize: 1 } } 
      }
    }
  })

  // ---------------------------------------------------------
  // CHART 2: ARUS KAS (DOUGHNUT CHART)
  // ---------------------------------------------------------
  keuanganChart = new Chart(ctxKeuangan, {
    type: 'doughnut',
    data: {
      labels: ['Sektor Pemasukan', 'Sektor Pengeluaran'],
      datasets: [{
        data: [totalIncome.value, totalExpense.value],
        backgroundColor: ['#10b981', '#ef4444'], // Emerald vs Red
        borderWidth: 4,
        borderColor: '#ffffff',
        hoverOffset: 10
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      cutout: '75%',
      plugins: {
        legend: { position: 'bottom', labels: { padding: 20, boxWidth: 12, font: { family: "'Inter', sans-serif", weight: 'bold' } } },
        tooltip: { 
          backgroundColor: 'rgba(15, 23, 42, 0.95)', padding: 12, cornerRadius: 8,
          callbacks: {
            label: function(context) { return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw); }
          }
        }
      }
    }
  })

  // ---------------------------------------------------------
  // CHART 3: KINERJA KURIR (HORIZONTAL BAR CHART STACKED)
  // ---------------------------------------------------------
  const kurirLabels = Object.keys(courierStats.value)
  const kurirTerkirim = kurirLabels.map(name => courierStats.value[name].terkirim)
  const kurirJalan = kurirLabels.map(name => courierStats.value[name].diperjalanan)

  kurirChart = new Chart(ctxKurir, {
    type: 'bar',
    data: {
      labels: kurirLabels.length > 0 ? kurirLabels : ['Belum Ada Kurir Bertugas'],
      datasets: [
        {
          label: 'Paket Berhasil (Sukses)',
          data: kurirLabels.length > 0 ? kurirTerkirim : [0],
          backgroundColor: '#10b981', // Emerald
          borderRadius: 4,
          barThickness: 'flex'
        },
        {
          label: 'Paket Diperjalanan (OTW)',
          data: kurirLabels.length > 0 ? kurirJalan : [0],
          backgroundColor: '#f59e0b', // Amber
          borderRadius: 4,
          barThickness: 'flex'
        }
      ]
    },
    options: {
      indexAxis: 'y', // Memutar grafik menjadi menyamping
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position: 'top', align: 'end', labels: { boxWidth: 12, font: { family: "'Inter', sans-serif", weight: 'bold' } } },
        tooltip: { backgroundColor: 'rgba(15, 23, 42, 0.95)', padding: 12, cornerRadius: 8 }
      },
      scales: {
        x: { stacked: true, beginAtZero: true, grid: { borderDash: [4, 4] }, ticks: { stepSize: 1 } },
        y: { stacked: true, grid: { display: false }, ticks: { font: { size: 11, family: "'Inter', sans-serif", weight: '600' } } }
      }
    }
  })
}

// ==============================================================================
// 6. MOUNTING LIFECYCLE
// ==============================================================================
onMounted(() => {
  fetchReportData()
})
</script>

<style scoped>
/* Transisi Kemunculan Komponen yang Sangat Halus */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-down {
  animation: slideDown 0.4s ease-out forwards;
}

/* 
  ===========================================================================
  CSS KHUSUS MODE CETAK (PRINT FORMATTING)
  ===========================================================================
  Kode CSS ini memastikan ketika User menekan tombol "Cetak PDF" atau Ctrl+P,
  halaman web akan merubah bentuknya menjadi format dokumen kertas A4 formal.
  Semua tombol interaktif, animasi, dan shadow akan dimatikan.
*/
@media print {
  @page { size: A4 portrait; margin: 15mm; }
  
  /* Reset Background ke Putih Murni */
  body, html, .print-bg-white { 
    background-color: #ffffff !important; 
    color: #000000 !important;
  }
  
  /* Paksa lebar komponen menjadi 100% untuk PDF */
  .max-w-\[1400px\] { 
    max-width: 100% !important; 
    margin: 0 !important; 
    padding: 0 !important; 
  }
  
  /* Sembunyikan Header Web, Tombol, SVG Animasi, dan Grafik Interaktif */
  .hide-on-print { display: none !important; }
  
  /* Munculkan Header Khusus Print (Judul Surat Laporan) */
  .print-header { display: block !important; }
  
  /* Modifikasi Grid agar KPI Cards sejajar rapi di kertas */
  .print-grid { 
    display: grid !important; 
    grid-template-columns: repeat(4, 1fr) !important; 
    gap: 15px !important; 
    margin-bottom: 30px !important; 
  }
  
  .print-grid > div { 
    border: 1px solid #d1d5db !important; 
    box-shadow: none !important; 
    padding: 15px !important; 
    background: #ffffff !important;
    break-inside: avoid;
  }
  
  /* Hilangkan efek melengkung dan shadow pada tabel */
  .rounded-3xl, .rounded-2xl, .rounded-xl { border-radius: 4px !important; }
  .shadow-sm, .shadow-md, .shadow-xl, .shadow-2xl { box-shadow: none !important; }
  .bg-gray-900 { background-color: #f3f4f6 !important; color: #000 !important; }
  
  /* Penyesuaian Tabel Data */
  table { 
    width: 100% !important; 
    border-collapse: collapse !important; 
    page-break-inside: auto;
  }
  tr { page-break-inside: avoid; page-break-after: auto; }
  th { background-color: #e5e7eb !important; color: #000 !important; }
  th, td { 
    border: 1px solid #9ca3af !important; 
    padding: 10px !important; 
    font-size: 11px !important; 
  }
  
  /* Jangan potong box/tabel di tengah halaman */
  .break-inside-avoid { break-inside: avoid !important; margin-bottom: 20px !important; border: 1px solid #d1d5db !important; }
}
</style>