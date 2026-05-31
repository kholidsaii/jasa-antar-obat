<template>
  <div class="min-h-screen bg-[#f4f9f8] flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Ornamen Background Halus -->
    <div class="absolute inset-0 bg-[#21a68d]/5"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <div class="flex justify-center">
        <!-- Wadah Logo -->
        <div class="bg-black rounded-full p-1.5 shadow-xl ring-4 ring-white">
          <img src="/logo.png" alt="Logo Jastar Obat" class="h-24 w-24 rounded-full object-cover" />
        </div>
      </div>
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 tracking-tight">
        Jastar Obat
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600 font-medium">
        Sistem Manajemen Pengiriman Obat Terpadu
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
      <div class="bg-white py-8 px-6 shadow-2xl sm:rounded-2xl sm:px-10 border border-gray-100">
        <form class="space-y-6" @submit.prevent="handleLogin">
          
          <!-- Input Email -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Email Address</label>
            <div class="mt-2 relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
              </div>
              <input v-model="form.email" type="email" required placeholder="admin@jastar.com" class="pl-10 appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#21a68d] focus:border-[#21a68d] sm:text-sm transition-colors">
            </div>
          </div>

          <!-- Input Password -->
          <div>
            <label class="block text-sm font-semibold text-gray-700">Password</label>
            <div class="mt-2 relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              </div>
              <input v-model="form.password" type="password" required placeholder="••••••••" class="pl-10 appearance-none block w-full px-3 py-2.5 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#21a68d] focus:border-[#21a68d] sm:text-sm transition-colors">
            </div>
          </div>

          <!-- Pesan Error -->
          <div v-if="errorMessage" class="bg-red-50 p-3 rounded-lg flex items-center">
            <svg class="h-5 w-5 text-red-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
            <span class="text-sm text-red-700 font-medium">{{ errorMessage }}</span>
          </div>

          <!-- Tombol Login -->
          <div>
            <button type="submit" :disabled="isLoading" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-[#21a68d] hover:bg-[#18806c] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#21a68d] disabled:opacity-70 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5">
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              {{ isLoading ? 'Memproses...' : 'Masuk ke Sistem' }}
            </button>
          </div>
          
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()
const form = ref({ email: '', password: '' })
const isLoading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  try {
    const res = await axios.post('/login', form.value)
    
    // Simpan token & data user ke Local Storage
    localStorage.setItem('token', res.data.data.token)
    localStorage.setItem('user', JSON.stringify(res.data.data.user))
    
    // Redirect ke Home
    router.push('/')
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Email atau password salah.'
  } finally {
    isLoading.value = false
  }
}
</script>