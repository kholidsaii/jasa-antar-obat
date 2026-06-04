<template>
  <div class="max-w-7xl mx-auto pb-10 sm:pb-16 bg-gray-50/50 min-h-screen">
    
    <div class="bg-white rounded-b-2xl sm:rounded-t-3xl sm:rounded-b-none overflow-hidden shadow-sm border-b sm:border border-gray-100 relative group">
      <div class="h-36 sm:h-52 w-full bg-cover bg-center relative transition-transform duration-1000 group-hover:scale-105" style="background-image: url('https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=2070&auto=format&fit=crop');">
        <div class="absolute inset-0 bg-gradient-to-t sm:bg-gradient-to-r from-blue-900/95 via-blue-800/80 to-transparent backdrop-blur-[2px]"></div>
      </div>
      
      <div class="px-5 sm:px-10 pb-6 sm:pb-8 flex flex-col sm:flex-row sm:items-end sm:justify-between relative">
        <div class="flex items-end -mt-12 sm:-mt-16 mb-5 sm:mb-0 relative z-10 w-full sm:w-auto">
          <div class="bg-white p-1.5 sm:p-2 rounded-2xl sm:rounded-3xl shadow-xl border border-gray-100 inline-block shrink-0 transition-transform duration-500 hover:-translate-y-2">
            <div class="w-20 h-20 sm:w-28 sm:h-28 bg-blue-50 rounded-xl sm:rounded-2xl flex items-center justify-center text-blue-600">
              <i class="fas fa-headset text-4xl sm:text-6xl"></i>
            </div>
          </div>
          
          <div class="ml-4 sm:ml-6 pb-1 sm:pb-3">
            <span class="bg-blue-100 text-blue-700 px-3 sm:px-4 py-1.5 rounded-full text-[10px] sm:text-xs font-black tracking-widest uppercase border border-blue-200 mb-2 sm:mb-2.5 inline-block shadow-sm">
              Layanan Pelanggan
            </span>
            <h1 class="text-2xl sm:text-4xl font-black text-gray-900 leading-tight tracking-tight">HELP DESK</h1>
            <p class="text-gray-500 text-xs sm:text-sm font-medium mt-1.5 sm:mt-2 max-w-xl leading-relaxed hidden sm:block">
              Pusat resolusi masalah, pelacakan paket, dan pengaduan layanan operasional kurir Jastar RSPPN Soedirman.
            </p>
          </div>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 w-full sm:w-auto relative z-10 shrink-0">
          <button @click="openTicketModal" class="w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-800 text-white px-6 py-4 sm:py-3 rounded-xl font-bold text-sm transition-all duration-300 shadow-lg shadow-blue-200 hover:shadow-xl flex items-center active:scale-95">
            <i class="fas fa-ticket-alt mr-2.5 text-lg"></i>
            Buat Tiket Pengaduan
          </button>
        </div>
      </div>
      
      <div class="border-t border-gray-100 bg-white">
        <nav class="flex overflow-x-auto hide-scrollbar scroll-smooth px-3 sm:px-10" aria-label="Tabs">
          <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id ? 'border-blue-600 text-blue-700 bg-blue-50/30' : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300 hover:bg-gray-50', 
              'group inline-flex items-center px-5 py-4 sm:py-4 border-b-[3px] font-bold text-[13px] sm:text-sm transition-all whitespace-nowrap outline-none flex-shrink-0'
            ]">
            <i :class="['fas mr-2.5 sm:mr-3 text-lg transition-colors', tab.icon, activeTab === tab.id ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500']"></i>
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <div class="mt-4 sm:mt-8 px-4 sm:px-0 relative w-full">
      <Transition name="fade-slide" mode="out-in">
        
        <div v-if="activeTab === 'tracking'" key="tracking" class="max-w-4xl mx-auto space-y-6">
          <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 text-center">
            <h2 class="text-xl sm:text-2xl font-black text-gray-800 mb-2">Lacak Paket Obat</h2>
            <p class="text-sm text-gray-500 mb-6">Masukkan Nomor Resi (Contoh: PKT-0010) atau Nomor Struk.</p>
            
            <div class="relative max-w-2xl mx-auto">
              <input 
                v-model="trackingInput" 
                @keyup.enter="searchTracking"
                type="text" 
                placeholder="Ketik Nomor Resi / Struk..." 
                class="w-full border-2 border-gray-200 rounded-xl py-4 pl-6 pr-32 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 text-base sm:text-lg font-bold text-gray-800 shadow-sm transition-all uppercase"
              >
              <button @click="searchTracking" :disabled="isTracking" class="absolute inset-y-2 right-2 bg-blue-600 hover:bg-blue-800 text-white font-bold px-6 rounded-lg active:scale-95 transition-all flex items-center disabled:opacity-70">
                <i v-if="isTracking" class="fas fa-circle-notch fa-spin"></i>
                <span v-else>LACAK</span>
              </button>
            </div>
          </div>

          <div v-if="trackingResult" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-slide-up">
            <div class="bg-slate-800 p-6 sm:p-8 text-white relative overflow-hidden">
              <div class="absolute -right-10 -bottom-10 opacity-10"><i class="fas fa-box-open text-9xl"></i></div>
              <p class="text-xs font-bold text-blue-300 uppercase tracking-widest mb-1 relative z-10">Hasil Pelacakan</p>
              <h3 class="text-3xl sm:text-4xl font-black tracking-wider relative z-10 mb-4">
                #PKT-[{{ String(trackingResult.id).padStart(4, '0') }}]
              </h3>
              <div class="flex flex-wrap items-center gap-3 relative z-10">
                <span :class="getStatusBadgeClass(trackingResult.status_pengiriman)" class="px-3 py-1.5 text-xs font-black uppercase tracking-wider rounded-md shadow-sm border">
                  {{ trackingResult.status_pengiriman }}
                </span>
                <span v-if="trackingResult.waktu_pengantaran" :class="getWaktuResiClass(trackingResult.waktu_pengantaran)" class="px-3 py-1.5 text-xs font-black uppercase tracking-wider rounded-md shadow-sm border flex items-center">
                  <i class="fas mr-1.5" :class="getWaktuIconClass(trackingResult.waktu_pengantaran)"></i> Waktu: {{ trackingResult.waktu_pengantaran }}
                </span>
              </div>
            </div>

            <div class="p-6 sm:p-8 flex flex-col md:flex-row gap-8">
              <div class="w-full md:w-1/3 space-y-6 shrink-0">
                <div>
                  <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Penerima (Pasien)</p>
                  <p class="text-base font-black text-gray-800">{{ trackingResult.customer?.nama || 'Unknown' }}</p>
                  <p class="text-xs text-gray-500 mt-1 leading-relaxed"><i class="fas fa-map-marker-alt text-red-500 mr-1"></i> {{ trackingResult.customer?.alamat }}</p>
                </div>
                <div>
                  <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kurir Pengantar</p>
                  <div v-if="trackingResult.work?.user" class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <img :src="trackingResult.work?.user?.foto ? '/storage/' + trackingResult.work.user.foto : `https://ui-avatars.com/api/?name=${trackingResult.work.user.name}&background=3b5998&color=fff`" class="w-10 h-10 rounded-full shadow-sm">
                    <div>
                      <p class="text-sm font-black text-gray-800">{{ trackingResult.work.user.name }}</p>
                      <p class="text-[10px] text-gray-500 font-bold"><i class="fas fa-motorcycle mr-1"></i> {{ trackingResult.work.vehicle?.plat_nomor || '-' }}</p>
                    </div>
                  </div>
                  <div v-else class="text-sm text-gray-500 italic bg-gray-50 p-3 rounded-xl border border-gray-100">Belum ada kurir yang ditugaskan.</div>
                </div>
              </div>

              <div class="w-full md:w-2/3 border-t md:border-t-0 md:border-l border-gray-100 pt-6 md:pt-0 md:pl-8">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Riwayat Perjalanan (Log)</p>
                
                <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
                  <div v-for="(history, index) in trackingResult.histories" :key="history.id" class="relative pl-6">
                    <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-white shadow-sm" :class="index === 0 ? 'bg-blue-500' : 'bg-gray-300'"></span>
                    <h4 class="text-sm font-black" :class="index === 0 ? 'text-blue-700' : 'text-gray-700'">{{ history.status_pengiriman }}</h4>
                    <p class="text-xs text-gray-500 font-medium mt-1 italic">{{ history.keterangan || 'Diupdate oleh sistem.' }}</p>
                    <p class="text-[10px] text-gray-400 font-bold mt-1.5"><i class="far fa-clock mr-1"></i> {{ formatDate(history.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div v-else-if="searchAttempted" class="bg-red-50 p-8 rounded-2xl border border-red-200 text-center animate-fade-in shadow-sm">
            <i class="fas fa-search-minus text-4xl text-red-400 mb-3"></i>
            <h3 class="text-lg font-black text-red-800">Resi Tidak Ditemukan</h3>
            <p class="text-sm text-red-600 mt-1">Pastikan kombinasi huruf dan angka sudah benar.</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'tickets'" key="tickets" class="space-y-6">
          <div class="grid grid-cols-3 gap-3 sm:gap-6">
            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
              <div>
                <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Tiket</p>
                <p class="text-xl sm:text-3xl font-black text-gray-800">
                  <i v-if="isLoadingTickets" class="fas fa-spinner fa-spin text-gray-300 text-lg"></i>
                  <span v-else>{{ tickets.length }}</span>
                </p>
              </div>
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center text-lg sm:text-xl"><i class="fas fa-ticket-alt"></i></div>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
              <div>
                <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Perlu Respons</p>
                <p class="text-xl sm:text-3xl font-black text-orange-600">
                  <i v-if="isLoadingTickets" class="fas fa-spinner fa-spin text-orange-200 text-lg"></i>
                  <span v-else>{{ tickets.filter(t => t.status === 'Open').length }}</span>
                </p>
              </div>
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-50 text-orange-500 rounded-full flex items-center justify-center text-lg sm:text-xl"><i class="fas fa-exclamation-circle"></i></div>
            </div>
            <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
              <div>
                <p class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Selesai</p>
                <p class="text-xl sm:text-3xl font-black text-green-600">
                  <i v-if="isLoadingTickets" class="fas fa-spinner fa-spin text-green-200 text-lg"></i>
                  <span v-else>{{ tickets.filter(t => t.status === 'Resolved').length }}</span>
                </p>
              </div>
              <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-50 text-green-500 rounded-full flex items-center justify-center text-lg sm:text-xl"><i class="fas fa-check-double"></i></div>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
              <h3 class="font-black text-gray-800 text-lg">Daftar Komplain & Laporan</h3>
              <div class="flex gap-2">
                <button @click="fetchTickets" class="bg-white border border-gray-300 text-gray-600 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-50 transition-colors shadow-sm">
                  <i class="fas fa-sync-alt" :class="{'fa-spin': isLoadingTickets}"></i>
                </button>
                <select v-model="filterStatus" class="border border-gray-300 rounded-lg text-xs font-bold px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500 bg-white shadow-sm">
                  <option value="All">Semua Status</option>
                  <option value="Open">Open (Baru)</option>
                  <option value="Progress">In Progress</option>
                  <option value="Resolved">Resolved (Selesai)</option>
                </select>
              </div>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                  <tr class="bg-white border-b border-gray-100 text-[10px] uppercase tracking-widest text-gray-400">
                    <th class="p-4 font-bold">ID Tiket</th>
                    <th class="p-4 font-bold">Subjek & Kategori</th>
                    <th class="p-4 font-bold">Resi Terkait</th>
                    <th class="p-4 font-bold">Pelapor</th>
                    <th class="p-4 font-bold">Status</th>
                    <th class="p-4 font-bold text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                  <tr v-if="isLoadingTickets" class="bg-gray-50/50">
                    <td colspan="6" class="p-8 text-center"><i class="fas fa-circle-notch fa-spin text-blue-500 text-2xl"></i></td>
                  </tr>
                  <tr v-else-if="filteredTickets.length === 0">
                    <td colspan="6" class="p-8 text-center text-sm font-bold text-gray-400 italic">Tidak ada tiket pengaduan.</td>
                  </tr>
                  <tr v-else v-for="ticket in filteredTickets" :key="ticket.id" class="hover:bg-blue-50/30 transition-colors group">
                    <td class="p-4 text-xs font-black text-gray-800">#TCK-{{ String(ticket.id).padStart(4,'0') }}</td>
                    <td class="p-4">
                      <p class="text-sm font-bold text-gray-900 line-clamp-1">{{ ticket.subject }}</p>
                      <p class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 inline-block mt-1">{{ ticket.category }}</p>
                    </td>
                    <td class="p-4 text-xs font-bold text-gray-500">{{ ticket.resi || '-' }}</td>
                    <td class="p-4">
                      <p class="text-xs font-bold text-gray-800">{{ ticket.reporter }}</p>
                      <p class="text-[10px] text-gray-400">{{ formatDateOnly(ticket.created_at) }}</p>
                    </td>
                    <td class="p-4">
                      <span :class="getTicketStatusClass(ticket.status)" class="px-2.5 py-1 text-[10px] font-black uppercase rounded-md border shadow-sm inline-block">
                        {{ ticket.status }}
                      </span>
                    </td>
                    <td class="p-4 text-center">
                      <button @click="openChat(ticket)" class="bg-gray-100 hover:bg-blue-600 text-gray-600 hover:text-white w-8 h-8 rounded-lg transition-colors shadow-sm">
                        <i class="fas fa-comment-dots"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab === 'faq'" key="faq" class="max-w-4xl mx-auto space-y-4">
          <div class="relative mb-6">
            <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
            <input v-model="faqSearch" type="text" placeholder="Cari pertanyaan... (Misal: resi, rusak, kurir)" class="w-full border border-gray-200 rounded-xl py-3.5 pl-12 pr-4 shadow-sm text-sm font-bold focus:ring-2 focus:ring-blue-500 outline-none">
          </div>

          <div v-for="(faq, index) in filteredFaqs" :key="index" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300">
            <button @click="toggleFaq(index)" class="w-full text-left p-5 flex justify-between items-center focus:outline-none hover:bg-gray-50">
              <span class="font-black text-gray-800 text-sm sm:text-base pr-4">{{ faq.question }}</span>
              <i class="fas text-gray-400 transition-transform duration-300" :class="faq.isOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
            </button>
            <div v-show="faq.isOpen" class="px-5 pb-5 pt-1 text-sm text-gray-600 leading-relaxed border-t border-gray-50 bg-gray-50/50">
              {{ faq.answer }}
            </div>
          </div>
          
          <div v-if="filteredFaqs.length === 0" class="text-center py-10 text-gray-400 font-bold">
            <i class="fas fa-box-open text-4xl mb-3 opacity-50"></i>
            <p>Tidak ada panduan yang cocok.</p>
          </div>
        </div>

        <div v-else-if="activeTab === 'contact'" key="contact" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(contact, index) in contacts" :key="index" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center text-center hover:shadow-md transition-shadow group">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 text-2xl mb-4 group-hover:scale-110 transition-transform">
              <i :class="contact.icon"></i>
            </div>
            <h3 class="font-black text-gray-900 text-lg">{{ contact.name }}</h3>
            <p class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded mt-1">{{ contact.role }}</p>
            <p class="text-sm text-gray-500 mt-3 mb-5 leading-relaxed line-clamp-2">{{ contact.desc }}</p>
            
            <a :href="`https://wa.me/${contact.phone}`" target="_blank" class="mt-auto w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2.5 rounded-xl transition-colors shadow-sm shadow-green-200 flex justify-center items-center text-sm">
              <i class="fab fa-whatsapp text-lg mr-2"></i> Hubungi WA
            </a>
          </div>
        </div>

      </Transition>
    </div>

    <div v-if="isTicketModalOpen" class="fixed inset-0 z-[100] flex flex-col justify-end sm:items-center sm:justify-center p-0 sm:p-4">
      <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeTicketModal"></div>
      
      <div class="relative bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl w-full sm:max-w-lg mx-auto flex flex-col overflow-hidden max-h-[92vh] sm:max-h-[90vh] animate-slide-up sm:animate-modal-in">
        <div class="w-full flex justify-center pt-3 pb-2 sm:hidden cursor-grab" @click="closeTicketModal">
          <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
        </div>

        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
          <h3 class="text-base sm:text-lg font-black text-gray-900">Buat Tiket Baru</h3>
          <button @click="closeTicketModal" class="text-gray-400 hover:text-red-500 bg-white p-2 rounded-full transition-colors shadow-sm">
            <i class="fas fa-times"></i>
          </button>
        </div>
        
        <div class="p-5 sm:p-6 overflow-y-auto custom-scrollbar">
          <form @submit.prevent="submitTicket" class="space-y-5">
            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Subjek / Judul Masalah</label>
              <input v-model="formTicket.subject" type="text" required placeholder="Singkat dan jelas. Misal: Obat Tumpah" class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium shadow-sm">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                <select v-model="formTicket.category" required class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-bold shadow-sm bg-white">
                  <option value="Keterlambatan">Keterlambatan</option>
                  <option value="Barang Rusak">Barang Rusak</option>
                  <option value="Sikap Kurir">Sikap Kurir</option>
                  <option value="Sistem/Aplikasi">Sistem/Aplikasi</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5 flex justify-between">
                  ID Resi <span class="text-[9px] bg-gray-100 px-1 rounded text-gray-400 normal-case">Opsional</span>
                </label>
                <input v-model="formTicket.resi" type="text" placeholder="Contoh: PKT-0010" class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium shadow-sm uppercase">
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jelaskan Detail Kejadian</label>
              <textarea v-model="formTicket.description" required rows="4" placeholder="Ceritakan kronologinya di sini..." class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:ring-2 focus:ring-blue-500 text-sm shadow-sm custom-scrollbar"></textarea>
            </div>
          </form>
        </div>
        
        <div class="bg-white px-5 sm:px-6 py-4 border-t border-gray-100 flex gap-3 pb-safe shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] sm:shadow-none sm:rounded-b-2xl">
          <button @click="closeTicketModal" type="button" class="w-1/3 px-4 py-3 bg-gray-100 rounded-xl text-gray-700 font-bold hover:bg-gray-200 transition-colors active:scale-95 text-sm">Batal</button>
          <button @click="submitTicket" :disabled="!formTicket.subject || !formTicket.description || isSubmittingTicket" type="button" class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-800 disabled:opacity-50 transition-all shadow-md active:scale-95 text-sm flex justify-center items-center">
            <i v-if="isSubmittingTicket" class="fas fa-spinner fa-spin mr-2"></i> Kirim Laporan
          </button>
        </div>
      </div>
    </div>

    <div v-if="isChatModalOpen && activeTicket" class="fixed inset-0 z-[110] flex flex-col justify-end sm:items-center sm:justify-center p-0 sm:p-4">
      <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity" @click="closeChatModal"></div>
      
      <div class="relative bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl w-full sm:max-w-2xl mx-auto flex flex-col overflow-hidden h-[90vh] sm:h-[80vh] animate-slide-up sm:animate-modal-in">
        
        <div class="px-5 py-4 border-b border-gray-100 bg-white flex justify-between items-center z-10 shadow-sm relative">
          <div class="flex items-center gap-3">
            <button @click="closeChatModal" class="sm:hidden w-8 h-8 flex items-center justify-center bg-gray-100 rounded-full text-gray-600"><i class="fas fa-arrow-left"></i></button>
            <div>
              <div class="flex items-center gap-2 mb-0.5">
                <h3 class="text-base font-black text-gray-900">#TCK-{{ String(activeTicket.id).padStart(4,'0') }}</h3>
                <span :class="getTicketStatusClass(activeTicket.status)" class="px-2 py-0.5 text-[9px] font-black uppercase rounded border">{{ activeTicket.status }}</span>
              </div>
              <p class="text-xs font-bold text-gray-500 line-clamp-1">{{ activeTicket.subject }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button v-if="activeTicket.status !== 'Resolved'" @click="resolveTicket" class="text-[10px] sm:text-xs font-bold bg-green-50 text-green-600 border border-green-200 px-3 py-1.5 rounded-lg hover:bg-green-100 transition-colors shadow-sm">
              <i class="fas fa-check mr-1"></i> Tutup
            </button>
            <button @click="closeChatModal" class="hidden sm:block text-gray-400 hover:text-red-500 bg-gray-50 p-2 rounded-full transition-colors"><i class="fas fa-times"></i></button>
          </div>
        </div>
        
        <div class="flex-1 p-4 sm:p-6 overflow-y-auto bg-slate-50/50 custom-scrollbar space-y-4" id="chat-container">
          <div class="text-center my-2">
            <span class="bg-blue-100 text-blue-800 text-[9px] font-bold px-3 py-1 rounded-full uppercase tracking-widest border border-blue-200">
              Tiket Dibuat: {{ formatDateOnly(activeTicket.created_at) }}
            </span>
          </div>

          <div v-for="(chat, index) in activeTicket.chats" :key="index" :class="['flex w-full', chat.role === 'admin' ? 'justify-end' : 'justify-start']">
            <div :class="['max-w-[85%] sm:max-w-[75%] rounded-2xl p-3 shadow-sm relative', chat.role === 'admin' ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white border border-gray-100 text-gray-800 rounded-tl-none']">
              <p class="text-[10px] font-black mb-1 opacity-70" :class="chat.role === 'admin' ? 'text-blue-100' : 'text-gray-500'">
                {{ chat.sender_name }} <span v-if="chat.role === 'admin'"><i class="fas fa-check-circle ml-0.5"></i></span>
              </p>
              <p class="text-sm font-medium leading-relaxed whitespace-pre-wrap">{{ chat.message }}</p>
              <p class="text-[9px] text-right mt-1.5 opacity-60 font-bold">
                {{ formatTime(chat.created_at) }} <i v-if="chat.role === 'admin'" class="fas fa-check-double ml-1"></i>
              </p>
            </div>
          </div>
          <div v-if="activeTicket.status === 'Resolved'" class="text-center mt-6 p-3 bg-green-50 border border-green-200 rounded-xl">
             <i class="fas fa-lock text-green-500 mb-1"></i>
             <p class="text-xs font-bold text-green-700">Tiket ini telah ditutup karena masalah sudah terselesaikan.</p>
          </div>
        </div>
        
        <div class="p-3 sm:p-4 bg-white border-t border-gray-100 pb-safe">
          <form @submit.prevent="sendChatMessage" class="flex items-end gap-2 relative">
            <textarea v-model="chatInput" @keydown.enter.prevent="sendChatMessage" :disabled="activeTicket.status === 'Resolved' || isSendingChat" rows="1" placeholder="Ketik balasan..." class="w-full border border-gray-300 rounded-xl py-3 px-4 outline-none focus:ring-2 focus:ring-blue-500 text-sm shadow-sm disabled:bg-gray-100 resize-none max-h-32 custom-scrollbar"></textarea>
            <button type="submit" :disabled="!chatInput.trim() || activeTicket.status === 'Resolved' || isSendingChat" class="w-10 h-10 sm:w-12 sm:h-12 shrink-0 bg-blue-600 hover:bg-blue-800 text-white rounded-xl flex items-center justify-center transition-all disabled:opacity-50 active:scale-95 shadow-md shadow-blue-200">
              <i v-if="isSendingChat" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-paper-plane"></i>
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, watch } from 'vue'
import axios from 'axios'

// Identitas User (Untuk pengirim chat)
const currentUser = ref(JSON.parse(localStorage.getItem('user') || '{}'))

// TABS CONFIG
const tabs = [
  { id: 'tracking', name: 'Lacak Resi', icon: 'fa-search-location' },
  { id: 'tickets',  name: 'Tiket Bantuan', icon: 'fa-ticket-alt' },
  { id: 'faq',      name: 'FAQ & Panduan', icon: 'fa-book-open' },
  { id: 'contact',  name: 'Kontak RS', icon: 'fa-phone-alt' }
]
const activeTab = ref('tracking')

// ==========================================
// LOGIKA TAB 1: TRACKING (TERHUBUNG API)
// ==========================================
const trackingInput = ref('')
const isTracking = ref(false)
const searchAttempted = ref(false)
const trackingResult = ref(null)

const searchTracking = async () => {
  if (!trackingInput.value) return
  isTracking.value = true
  searchAttempted.value = false
  trackingResult.value = null
  
  let rawInput = trackingInput.value.trim().toUpperCase()
  let packageId = rawInput
  if (rawInput.startsWith('PKT-')) {
    const parts = rawInput.split('-')
    if (parts.length > 1) packageId = parseInt(parts[1], 10)
  } else if (!isNaN(rawInput)) {
    packageId = parseInt(rawInput, 10)
  }

  try {
    const response = await axios.get(`/packages/${packageId}`)
    if (response.data.status === 'success') trackingResult.value = response.data.data
  } catch (error) {
    trackingResult.value = null
  } finally {
    isTracking.value = false
    searchAttempted.value = true
  }
}

const getStatusBadgeClass = (status) => {
  if (!status) return 'bg-gray-100 text-gray-800'
  if (status.includes('Selesai')) return 'bg-green-100 text-green-800 border-green-300'
  if (status.includes('Cancel')) return 'bg-red-50 text-red-700 border-red-200 line-through'
  if (status.includes('Dalam perjalanan')) return 'bg-yellow-100 text-yellow-800 border-yellow-300'
  return 'bg-blue-100 text-blue-800 border-blue-200'
}

const getWaktuResiClass = (waktu) => {
  if (waktu === 'Segera') return 'bg-red-50 text-red-700 border-red-200'
  if (waktu === 'Malam') return 'bg-emerald-50 text-emerald-700 border-emerald-200'
  return 'bg-blue-50 text-blue-700 border-blue-200'
}

const getWaktuIconClass = (waktu) => {
  if (waktu === 'Segera') return 'fa-shipping-fast'
  if (waktu === 'Malam') return 'fa-moon'
  return 'fa-calendar-day'
}

// ==========================================
// LOGIKA TAB 2: TIKET BANTUAN (TERHUBUNG API)
// ==========================================
const tickets = ref([])
const filterStatus = ref('All')
const isLoadingTickets = ref(false)
const isSubmittingTicket = ref(false)
const isSendingChat = ref(false)

const isTicketModalOpen = ref(false)
const isChatModalOpen = ref(false)
const activeTicket = ref(null)
const chatInput = ref('')

const formTicket = ref({ subject: '', category: 'Keterlambatan', resi: '', description: '' })

// Filter Tiket Berdasarkan Status
const filteredTickets = computed(() => {
  if (filterStatus.value === 'All') return tickets.value
  return tickets.value.filter(t => t.status === filterStatus.value)
})

const getTicketStatusClass = (status) => {
  if (status === 'Open') return 'bg-orange-50 text-orange-600 border-orange-200'
  if (status === 'Progress') return 'bg-blue-50 text-blue-600 border-blue-200'
  if (status === 'Resolved') return 'bg-green-50 text-green-600 border-green-200'
  return 'bg-gray-100 text-gray-600 border-gray-200'
}

// Tarik Data Tiket dari Backend API
const fetchTickets = async () => {
  isLoadingTickets.value = true
  try {
    const res = await axios.get('/tickets')
    tickets.value = res.data.data
  } catch (error) {
    console.error("Gagal memuat daftar tiket pengaduan")
  } finally {
    isLoadingTickets.value = false
  }
}

const openTicketModal = () => isTicketModalOpen.value = true
const closeTicketModal = () => {
  isTicketModalOpen.value = false
  formTicket.value = { subject: '', category: 'Keterlambatan', resi: '', description: '' }
}

// Submit Tiket ke Backend API
const submitTicket = async () => {
  isSubmittingTicket.value = true
  try {
    const payload = {
      subject: formTicket.value.subject,
      category: formTicket.value.category,
      resi: formTicket.value.resi,
      reporter: currentUser.value.name || 'Admin',
      description: formTicket.value.description
    }
    await axios.post('/tickets', payload)
    
    closeTicketModal()
    activeTab.value = 'tickets'
    fetchTickets() // Segarkan daftar tiket
    alert("Tiket Pengaduan Berhasil Dibuat!")
  } catch (error) {
    alert("Gagal membuat tiket, periksa koneksi Anda.")
  } finally {
    isSubmittingTicket.value = false
  }
}

const openChat = (ticket) => {
  activeTicket.value = ticket
  isChatModalOpen.value = true
  scrollToBottom()
}
const closeChatModal = () => {
  isChatModalOpen.value = false
  activeTicket.value = null
  chatInput.value = ''
}

// Kirim Balasan Chat ke Backend API
const sendChatMessage = async () => {
  if (!chatInput.value.trim() || activeTicket.value.status === 'Resolved') return
  
  isSendingChat.value = true
  try {
    const payload = {
      role: 'admin', 
      sender_name: currentUser.value.name || 'Admin CS',
      message: chatInput.value
    }
    const res = await axios.post(`/tickets/${activeTicket.value.id}/reply`, payload)
    
    // Perbarui data tiket yang sedang aktif dari respon API (sudah diload beserta chats terbarunya)
    activeTicket.value = res.data.data
    
    // Perbarui data di dalam list array
    const index = tickets.value.findIndex(t => t.id === activeTicket.value.id)
    if (index !== -1) tickets.value[index] = activeTicket.value
    
    chatInput.value = ''
    scrollToBottom()
  } catch (error) {
    alert("Gagal mengirim balasan, coba lagi.")
  } finally {
    isSendingChat.value = false
  }
}

// Ubah Status menjadi Resolved (Tutup Tiket)
const resolveTicket = async () => {
  if(!confirm("Yakin ingin menutup tiket ini? Pasien/Admin tidak akan bisa membalas lagi.")) return
  try {
    await axios.put(`/tickets/${activeTicket.value.id}/status`, { status: 'Resolved' })
    activeTicket.value.status = 'Resolved'
    alert("Tiket berhasil ditutup.")
  } catch (error) {
    alert("Gagal menutup tiket.")
  }
}

const scrollToBottom = () => {
  nextTick(() => {
    const container = document.getElementById('chat-container')
    if (container) container.scrollTop = container.scrollHeight
  })
}

// Watcher agar setiap kali pindah ke tab 'tickets', data disegarkan otomatis
watch(activeTab, (newTab) => {
  if (newTab === 'tickets') {
    fetchTickets()
  }
})

onMounted(() => {
  if (activeTab.value === 'tickets') fetchTickets()
})

// ==========================================
// LOGIKA TAB 3 & 4: FAQ & KONTAK
// ==========================================
const faqSearch = ref('')
const faqs = ref([
  { question: 'Berapa lama estimasi pengantaran?', answer: 'Estimasi standar adalah 1-3 jam setelah status berubah menjadi "Diserahkan ke Kurir". Waktu dapat bervariasi bergantung pada jarak dan kondisi cuaca.', isOpen: false },
  { question: 'Bagaimana jika kurir tidak dapat menemukan alamat?', answer: 'Kurir akan mencoba menelepon nomor WhatsApp yang terdaftar. Jika tidak ada respons dalam 15 menit, kurir berhak mengembalikan obat ke RS (Batal / Pending).', isOpen: false },
  { question: 'Apa yang harus dilakukan jika obat rusak di jalan?', answer: 'Silakan tolak penerimaan paket dan buat Tiket Pengaduan dengan kategori "Barang Rusak". Kami akan mengonfirmasi ulang dengan pihak Farmasi RSPPN.', isOpen: false }
])

const filteredFaqs = computed(() => {
  if (!faqSearch.value) return faqs.value
  const query = faqSearch.value.toLowerCase()
  return faqs.value.filter(f => f.question.toLowerCase().includes(query) || f.answer.toLowerCase().includes(query))
})

const toggleFaq = (index) => {
  filteredFaqs.value[index].isOpen = !filteredFaqs.value[index].isOpen
}

const contacts = [
  { name: 'Dr. Shafira Amalia', role: 'Ka. Instalasi Farmasi', phone: '6281234567890', icon: 'fas fa-pills', desc: 'Hubungi jika terjadi masalah ketersediaan stok obat atau resep salah.' },
  { name: 'Kholid Saifullah', role: 'IT Support & Sistem', phone: '6285824856566', icon: 'fas fa-server', desc: 'Bantuan error aplikasi, server down, atau resi gagal terbentuk.' }
]

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const d = new Date(dateString)
  return d.toLocaleDateString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) + ' WIB'
}
const formatDateOnly = (dateString) => {
  if (!dateString) return '-'
  const d = new Date(dateString)
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
}
const formatTime = (dateString) => {
  if (!dateString) return '-'
  const d = new Date(dateString)
  return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }

.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

.fade-slide-enter-active,
.fade-slide-leave-active { transition: opacity 0.3s ease, transform 0.3s ease; }
.fade-slide-enter-from { opacity: 0; transform: translateY(10px); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-10px); }

@keyframes slideUp {
  from { transform: translateY(100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.animate-slide-up { animation: slideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

@keyframes modalIn {
  from { opacity: 0; transform: scale(0.95) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
@media (min-width: 640px) {
  .animate-modal-in { animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
}

.animate-fade-in { animation: fadeIn 0.4s ease-in-out forwards; }
@keyframes fadeIn { 
  from { opacity: 0; transform: translateY(5px); } 
  to { opacity: 1; transform: translateY(0); } 
}

@supports (padding-bottom: env(safe-area-inset-bottom)) {
  .pb-safe { padding-bottom: calc(1rem + env(safe-area-inset-bottom)); }
}
</style>