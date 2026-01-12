{{-- Subscription Expiry Popup Component --}}
{{-- Wordt getoond wanneer abonnement binnen 7 dagen verloopt, alleen bij nieuwe login --}}
@auth
@if(auth()->user()->role === 'client')
<div x-data="subscriptionExpiryPopup()" x-init="checkExpiry()" x-cloak>
    {{-- Overlay --}}
    <div x-show="showPopup" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center p-4">
        
        {{-- Popup Content --}}
        <div x-show="showPopup"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            
            {{-- Header --}}
            <div class="bg-[#c8ab7a] px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fa-solid fa-clock text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-white font-bold text-lg">Abonnement verloopt</h2>
                        <p class="text-white/80 text-sm" x-text="isExpired ? 'Je abonnement is verlopen' : 'Nog ' + daysRemaining + ' dagen over'"></p>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5">
                <p class="text-gray-700 mb-4">
                    <span x-show="!isExpired">
                        Je <span class="font-semibold" x-text="packageLabel"></span> van <span class="font-semibold" x-text="periodWeeks + ' weken'"></span> 
                        verloopt op <span class="font-semibold" x-text="endDate"></span>.
                    </span>
                    <span x-show="isExpired">
                        Je <span class="font-semibold" x-text="packageLabel"></span> van <span class="font-semibold" x-text="periodWeeks + ' weken'"></span> 
                        is verlopen op <span class="font-semibold" x-text="endDate"></span>.
                    </span>
                </p>
                
                <p class="text-gray-600 text-sm mb-6">
                    Wat wil je doen?
                </p>

                {{-- Actions --}}
                <div class="space-y-3">
                    {{-- Verlengen --}}
                    <button @click="renewSubscription()" 
                            :disabled="loading"
                            class="w-full py-3 px-4 bg-[#c8ab7a] hover:bg-[#a38b62] text-white font-semibold rounded-lg transition duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-arrow-rotate-right" :class="loading === 'renew' ? 'animate-spin' : ''"></i>
                        <span x-text="loading === 'renew' ? 'Bezig...' : 'Verlengen'"></span>
                    </button>

                    {{-- Later beslissen --}}
                    <button @click="dismissPopup()" 
                            :disabled="loading"
                            class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-clock"></i>
                        <span>Later beslissen</span>
                    </button>

                    {{-- Verwijderen --}}
                    <button @click="confirmDelete()" 
                            :disabled="loading"
                            class="w-full py-3 px-4 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-lg transition duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-trash" :class="loading === 'delete' ? 'animate-pulse' : ''"></i>
                        <span x-text="loading === 'delete' ? 'Bezig met verwijderen...' : 'Account verwijderen'"></span>
                    </button>
                </div>
            </div>

            {{-- Info footer --}}
            <div class="bg-gray-50 px-6 py-3 border-t">
                <p class="text-gray-500 text-xs text-center">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    Bij verlengen vul je een nieuwe intake in met je huidige gegevens.
                </p>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showDeleteConfirm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 z-[10000] flex items-center justify-center p-4">
        
        <div x-show="showDeleteConfirm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
            
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 mb-2">Weet je het zeker?</h3>
                <p class="text-gray-600 text-sm mb-6">
                    Je account en alle bijbehorende gegevens worden permanent verwijderd. 
                    Dit kan niet ongedaan worden gemaakt.
                </p>

                <div class="flex gap-3">
                    <button @click="showDeleteConfirm = false" 
                            :disabled="loading"
                            class="flex-1 py-2 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition duration-300">
                        Annuleren
                    </button>
                    <button @click="deleteAccount()" 
                            :disabled="loading"
                            class="flex-1 py-2 px-4 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition duration-300 disabled:opacity-50">
                        <span x-text="loading === 'delete' ? 'Bezig...' : 'Verwijderen'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function subscriptionExpiryPopup() {
    return {
        showPopup: false,
        showDeleteConfirm: false,
        loading: false,
        daysRemaining: 0,
        endDate: '',
        packageName: '',
        periodWeeks: 12,
        isExpired: false,

        get packageLabel() {
            const labels = {
                'pakket_a': 'Basis Pakket',
                'pakket_b': 'Chasing Goals Pakket',
                'pakket_c': 'Elite Hyrox Pakket'
            };
            return labels[this.packageName] || 'Pakket';
        },

        async checkExpiry() {
            try {
                const response = await fetch('{{ route("subscription.check") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.show_popup) {
                    this.daysRemaining = data.days_remaining;
                    this.endDate = data.end_date;
                    this.packageName = data.package;
                    this.periodWeeks = data.period_weeks;
                    this.isExpired = data.is_expired;
                    this.showPopup = true;
                }
            } catch (error) {
                console.error('Error checking subscription expiry:', error);
            }
        },

        async dismissPopup() {
            try {
                await fetch('{{ route("subscription.dismiss") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            } catch (error) {
                console.error('Error dismissing popup:', error);
            }
            
            this.showPopup = false;
        },

        async renewSubscription() {
            this.loading = 'renew';
            
            try {
                const response = await fetch('{{ route("subscription.renew") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Er is een fout opgetreden.');
                    this.loading = false;
                }
            } catch (error) {
                console.error('Error renewing subscription:', error);
                alert('Er is een fout opgetreden bij het verlengen.');
                this.loading = false;
            }
        },

        confirmDelete() {
            this.showDeleteConfirm = true;
        },

        async deleteAccount() {
            this.loading = 'delete';
            
            try {
                const response = await fetch('{{ route("subscription.delete") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Er is een fout opgetreden.');
                    this.loading = false;
                }
            } catch (error) {
                console.error('Error deleting account:', error);
                alert('Er is een fout opgetreden bij het verwijderen.');
                this.loading = false;
            }
        }
    }
}
</script>
@endif
@endauth
