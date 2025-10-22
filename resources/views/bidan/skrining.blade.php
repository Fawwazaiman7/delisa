<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bidan — List Skrining</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    
    <div class="flex min-h-screen" 
         x-data="{
             modalOpen: false,
             selectedSkriningId: null,
             selectedSkriningUrl: '',
             isLoading: false,

             // Fungsi untuk membuka modal
             openModal(skriningId, skriningUrl) {
                 this.selectedSkriningId = skriningId;
                 this.selectedSkriningUrl = skriningUrl;
                 this.modalOpen = true;
             },

             // Fungsi saat klik 'Ya'
             async confirmView() {
                 if (this.isLoading) return;
                 this.isLoading = true;

                 try {
                     const response = await fetch(`/bidan/skrining/${this.selectedSkriningId}/mark-as-viewed`, {
                         method: 'POST',
                         headers: {
                             'Content-Type': 'application/json',
                             'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                         },
                     });
                     
                     if (!response.ok) {
                         throw new Error('Gagal update status');
                     }

                     const data = await response.json();
                     
                     // Pindahkan halaman ke URL detail
                     window.location.href = data.redirect_url;

                 } catch (error) {
                     console.error(error);
                     alert('Terjadi kesalahan. Silakan coba lagi.');
                     this.isLoading = false;
                     this.modalOpen = false;
                 }
             }
         }">
        
        <x-bidan.sidebar />

        <div class="flex-1 flex flex-col lg:pl-64">
            
            <header class="sticky top-0 z-10 flex h-20 items-center justify-between border-b bg-white px-4 sm:px-6 lg:px-8">
                <div class="flex-1">
                    <div class="relative w-full max-w-md">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="search" placeholder="Search..." class="w-full rounded-full border-gray-300 pl-10 pr-4 py-2 text-sm focus:border-pink-500 focus:ring-pink-500">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341A6.002 6.002 0 006 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2">
                            <x-avatar-initials :name="Auth::user()->name" />
                            <div class="text-left hidden md:block">
                                <div class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500">{{ Auth::user()->role->nama_role }}</div>
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" x-transition>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                
                <h1 class="text-2xl font-semibold text-gray-800 mb-6">List Skrining Ibu Hamil</h1>

                <div class="bg-white p-6 rounded-lg shadow-lg overflow-x-auto">
                    <h3 class="font-semibold text-lg mb-1 text-gray-700">Data Pasien Ibu Hamil</h3>
                    <p class="text-sm text-gray-500 mb-4">Data pasien yang melakukan pengecekan pada puskesmas ini</p>
                    
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pasien</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Telp</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kesimpulan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">View Detail</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($skrinings as $skrining)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $skrining->pasien->nik ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $skrining->pasien->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $skrining->created_at->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $skrining->pasien->PKecamatan ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $skrining->pasien->user->phone ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $badgeColor = 'bg-gray-100 text-gray-800'; // Default
                                            if ($skrining->kesimpulan == 'Beresiko') $badgeColor = 'bg-red-100 text-red-800';
                                            if ($skrining->kesimpulan == 'Aman') $badgeColor = 'bg-green-100 text-green-800';
                                            if ($skrining->kesimpulan == 'Waspada') $badgeColor = 'bg-yellow-100 text-yellow-800';
                                            if ($skrining->kesimpulan == 'Normal') $badgeColor = 'bg-blue-100 text-blue-800';
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                            {{ $skrining->kesimpulan ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        
                                        @php
                                            // Tentukan class pudar jika checked_status == true
                                            $buttonClass = $skrining->checked_status
                                                ? 'border-gray-200 text-gray-400 bg-gray-50 cursor-not-allowed'
                                                : 'border-gray-300 text-gray-700 hover:bg-gray-50';
                                        @endphp
                                        
                                        <button type="button" 
                                                class="px-3 py-1 border rounded-md text-sm transition-colors {{ $buttonClass }}"
                                                @click="openModal({{ $skrining->id }}, '{{ route('bidan.skrining.show', $skrining->id) }}')"
                                                {{ $skrining->checked_status ? 'disabled' : '' }} >
                                            View
                                        </button>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Belum ada data pasien skrining.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $skrinings->links() }}
                    </div>

                </div>
            </main>
        </div>

        <div x-show="modalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="modalOpen = false"
                x-show="modalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl text-center">
                
                <h3 class="text-2xl font-bold text-gray-800">Ingin Melihat Detail Data Pasien?</h3>
                <p class="mt-2 text-gray-600">Pilih "Ya" untuk melihat dan "Batal" untuk membatalkan</p>

                <div class="mt-8 flex justify-center gap-4">
                    <button @click="modalOpen = false"
                            class="rounded-lg bg-red-500 px-8 py-3 text-base font-medium text-white shadow-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                        Batal
                    </button>
                    
                    <button @click="confirmView()"
                            :disabled="isLoading"
                            class="rounded-lg bg-green-500 px-8 py-3 text-base font-medium text-white shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2 disabled:bg-gray-400">
                        <span x-show="!isLoading">Ya</span>
                        <span x-show="isLoading">Loading...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
