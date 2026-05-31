<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Login Jastar Obat
      </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" @submit.prevent="handleLogin">
          <div>
            <label class="block text-sm font-medium text-gray-700">Email address</label>
            <div class="mt-1">
              <input v-model="form.email" type="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1">
              <input v-model="form.password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#3b5998] focus:border-[#3b5998] sm:text-sm">
            </div>
          </div>

          <div v-if="errorMessage" class="text-red-500 text-sm text-center">
            {{ errorMessage }}
          </div>

          <div>
            <button type="submit" :disabled="isLoading" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#3b5998] hover:bg-blue-800 focus:outline-none disabled:opacity-50">
              {{ isLoading ? 'Loading...' : 'Sign in' }}
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