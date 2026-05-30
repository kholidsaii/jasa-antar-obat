<template>
  <div class="flex h-screen bg-gray-50 text-gray-800 relative overflow-hidden">
    
    <aside class="hidden md:flex w-64 bg-white border-r border-gray-200 flex-col justify-between z-20">
      <div>
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
          <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
            <div class="w-4 h-4 bg-white rounded-sm"></div>
          </div>
          <span class="font-bold text-lg tracking-wide text-gray-900">JASTAR OBAT</span>
        </div>

        <nav class="p-4 space-y-1">
          <router-link to="/" class="block px-4 py-2.5 rounded-lg font-medium transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Home</router-link>
          <router-link to="/pengiriman" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Pengiriman</router-link>
          <router-link to="/teamwork" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Teamwork</router-link>
          <router-link to="/financial" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Financial</router-link>
          <router-link to="/laporan" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Laporan</router-link>
        </nav>
      </div>

      <div class="p-4 border-t border-gray-100 flex items-center">
        <img src="https://ui-avatars.com/api/?name=Kholid+Solutions&background=random" alt="Avatar" class="w-8 h-8 rounded-full mr-3">
        <span class="text-sm font-medium text-gray-700">Kholid Solutions</span>
      </div>
    </aside>

    <main class="flex-1 overflow-y-auto pb-20 md:pb-0 relative w-full">
      <div class="p-4 sm:p-8">
        <router-view></router-view>
      </div>
    </main>

    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-5px_15px_rgba(0,0,0,0.05)] z-50">
      <div class="flex justify-around items-center h-16 px-1">
        <router-link to="/" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
          <span class="text-[10px] font-semibold">Home</span>
        </router-link>
        
        <router-link to="/pengiriman" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          <span class="text-[10px] font-semibold">Kirim</span>
        </router-link>
        
        <router-link to="/teamwork" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          <span class="text-[10px] font-semibold">Tim</span>
        </router-link>
        
        <router-link to="/financial" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span class="text-[10px] font-semibold">Finance</span>
        </router-link>

        <router-link to="/laporan" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          <span class="text-[10px] font-semibold">Laporan</span>
        </router-link>
      </div>
    </nav>
    
  </div>
</template>