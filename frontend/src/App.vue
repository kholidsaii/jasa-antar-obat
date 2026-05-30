<template>
  <div class="flex h-screen bg-gray-50 text-gray-800">
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between">
      <div>
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
          <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
            <div class="w-4 h-4 bg-white rounded-sm"></div>
          </div>
          <span class="font-bold text-lg tracking-wide">JASTAR OBAT</span>
        </div>

        <nav class="p-4 space-y-1">
          <router-link to="/" class="block px-4 py-2.5 rounded-lg font-medium transition-colors hover:bg-gray-100" active-class="bg-gray-100 text-gray-900">Home</router-link>
          <router-link to="/pengiriman" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-gray-100 text-gray-900">Pengiriman</router-link>
          <router-link to="/teamwork" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-gray-100 text-gray-900">Teamwork</router-link>
          <router-link to="/financial" class="block px-4 py-2.5 rounded-lg font-medium text-gray-600 transition-colors hover:bg-gray-100" active-class="bg-gray-100 text-gray-900">Financial</router-link>
        </nav>
      </div>

      <div class="p-4 border-t border-gray-100 flex items-center">
        <img src="https://ui-avatars.com/api/?name=Kholid+Solutions&background=random" alt="Avatar" class="w-8 h-8 rounded-full mr-3">
        <span class="text-sm font-medium text-gray-700">Kholid Solutions</span>
      </div>
    </aside>

    <main class="flex-1 overflow-y-auto">
      <div class="p-8">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>