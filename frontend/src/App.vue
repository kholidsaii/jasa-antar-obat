<template>
  <div class="flex h-screen bg-gray-50 text-gray-800 relative overflow-hidden">
    
    <aside v-if="$route.name !== 'Login'" class="hidden md:flex w-64 bg-white border-r border-gray-200 flex-col justify-between z-20">
      <div>
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
          <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
            <div class="w-4 h-4 bg-white rounded-sm"></div>
          </div>
          <span class="font-bold text-lg tracking-wide text-gray-900">JASTAR OBAT</span>
        </div>

        <nav class="p-4 space-y-1">
          <router-link to="/" class="block px-4 py-2.5 rounded-lg font-medium transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Home</router-link>
          
          <router-link v-if="['superadmin', 'admin', 'farmasi', 'kurir'].includes(userRole)" to="/pengiriman" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Pengiriman</router-link>
          
          <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/teamwork" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Teamwork</router-link>
          
          <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/financial" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Financial</router-link>
          
          <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/laporan" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Laporan</router-link>
          
          <!-- TAMBAHKAN MENU HELP DESK DI SINI -->
          <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/helpdesk" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-blue-50 text-[#3b5998]">Help Desk</router-link>
          <div class="pt-4 mt-2 border-t border-gray-100">
            <button @click="handleLogout" class="w-full flex items-center px-4 py-2.5 rounded-lg font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700 text-left">
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
              Logout
            </button>
          </div>
        </nav>
      </div>

      <div class="p-4 border-t border-gray-100 flex items-center bg-gray-50/50">
        <div class="w-9 h-9 rounded-full bg-[#3b5998] text-white flex items-center justify-center font-bold mr-3 uppercase shadow-sm">
          {{ userName.charAt(0) }}
        </div>
        <div class="flex flex-col overflow-hidden">
          <span class="text-sm font-bold text-gray-800 truncate" :title="userName">{{ userName }}</span>
          <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">{{ userRole }}</span>
        </div>
      </div>
    </aside>

    <main :class="['flex-1 overflow-y-auto relative w-full', $route.name !== 'Login' ? 'pb-20 md:pb-0' : '']">
      <div :class="[$route.name !== 'Login' ? 'p-4 sm:p-8' : 'h-full']">
        <router-view></router-view>
      </div>
    </main>

    <nav v-if="$route.name !== 'Login'" class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-5px_15px_rgba(0,0,0,0.05)] z-50">
      <div class="flex justify-around items-center h-16 px-1">
        
        <router-link to="/" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
          <span class="text-[10px] font-semibold">Home</span>
        </router-link>
        
        <router-link v-if="['superadmin', 'admin', 'farmasi', 'kurir'].includes(userRole)" to="/pengiriman" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
          <span class="text-[10px] font-semibold">Kirim</span>
        </router-link>
        
        <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/teamwork" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
          <span class="text-[10px] font-semibold">Tim</span>
        </router-link>
        
        <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/financial" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          <span class="text-[10px] font-semibold">Finance</span>
        </router-link>

        <router-link v-if="['superadmin', 'admin'].includes(userRole)" to="/laporan" class="flex flex-col items-center justify-center w-full h-full text-gray-500 transition-colors" active-class="text-[#3b5998]">
          <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          <span class="text-[10px] font-semibold">Laporan</span>
        </router-link>

      </div>
    </nav>
    
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

// Memaksa Vue membaca ulang localStorage setiap kali rute berpindah 
// (dengan mendaftarkan `route.name` sebagai pemicu reaktivitas)
const currentUser = computed(() => {
  const triggerReactivity = route.name; 
  try {
    const user = JSON.parse(localStorage.getItem('user'))
    return user || {}
  } catch (error) {
    return {}
  }
})

// Memisahkan data Role dan Nama agar UI Sidebar lebih rapi
const userRole = computed(() => currentUser.value.role || 'guest')
const userName = computed(() => currentUser.value.name || 'Belum Login')

// Fungsi eksekusi Logout
const handleLogout = async () => {
  try {
    await axios.post('/logout')
  } catch (error) {
    console.error("Gagal memanggil API Logout:", error)
  } finally {
    // Apapun yang terjadi di backend, kita bersihkan token & sesi di frontend
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}
</script>
