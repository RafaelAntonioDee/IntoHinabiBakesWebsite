<?php
/**
 * Interactive Delivery Address Pinning Modal (Leaflet.js + OSM)
 * Self-contained component reusable across Banner, Profile, and Checkout.
 */

$modalLoggedInUserId = \App\Core\Session::id();
$modalDefaultRecipient = \App\Core\Session::name() ?? '';
?>

<!-- Leaflet.js Assets (No API Key Required) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    .delivery-map-container .leaflet-pane {
        z-index: 20 !important;
    }
    .delivery-map-container .leaflet-top,
    .delivery-map-container .leaflet-bottom {
        z-index: 25 !important;
    }
    @keyframes modal-pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(217, 119, 6, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
    }
    .locating-pulse {
        animation: modal-pulse 1.5s infinite;
    }
</style>

<script>
window.modalAddressConfig = {
    isLoggedIn: <?= $modalLoggedInUserId ? 'true' : 'false' ?>,
    defaultRecipientName: <?= json_encode($modalDefaultRecipient, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
    saveUrl: <?= json_encode(url('/api/address/save'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
    loginUrl: <?= json_encode(url('/login'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
};

window.openDeliveryAddressModal = function(addressData = null) {
    if (!window.modalAddressConfig || !window.modalAddressConfig.isLoggedIn) {
        window.location.href = (window.modalAddressConfig && window.modalAddressConfig.loginUrl) ? window.modalAddressConfig.loginUrl : '/login';
        return;
    }
    window.dispatchEvent(new CustomEvent('open-delivery-modal', { detail: addressData }));
};

function deliveryAddressModalManager() {
    const config = window.modalAddressConfig || {};
    return {
        isOpen: false,
        isSearching: false,
        isLocating: false,
        isSaving: false,
        isReverseGeocoding: false,
        errorMessage: '',
        searchQuery: '',

        lat: 14.5995,
        lng: 120.9842,

        map: null,
        marker: null,

        form: {
            id: null,
            recipient_name: config.defaultRecipientName || '',
            street: '',
            barangay: '',
            city: '',
            postal_code: '',
            notes: '',
            is_default: false
        },

        get isEditing() {
            return Boolean(this.form.id);
        },

        get isFormValid() {
            return (this.form.street.trim().length > 0 || this.form.barangay.trim().length > 0) && this.form.city.trim().length > 0;
        },

        resetForm(addressData = null) {
            this.errorMessage = '';
            if (addressData && typeof addressData === 'object') {
                const parsedLat = parseFloat(addressData.latitude ?? addressData.lat);
                const parsedLng = parseFloat(addressData.longitude ?? addressData.lng);
                this.lat = Number.isFinite(parsedLat) ? parsedLat : 14.5995;
                this.lng = Number.isFinite(parsedLng) ? parsedLng : 120.9842;
                this.form = {
                    id: addressData.id || null,
                    recipient_name: addressData.recipient_name || config.defaultRecipientName || '',
                    street: addressData.street_address || addressData.street || '',
                    barangay: addressData.barangay || '',
                    city: addressData.city || '',
                    postal_code: addressData.postal_code || '',
                    notes: addressData.delivery_notes || addressData.notes || '',
                    is_default: Boolean(addressData.is_default)
                };
            } else {
                this.lat = 14.5995;
                this.lng = 120.9842;
                this.form = {
                    id: null,
                    recipient_name: config.defaultRecipientName || '',
                    street: '',
                    barangay: '',
                    city: '',
                    postal_code: '',
                    notes: '',
                    is_default: false
                };
            }
        },

        openModal(addressData = null) {
            if (!config.isLoggedIn) {
                window.location.href = config.loginUrl || '/login';
                return;
            }
            this.resetForm(addressData);
            this.isOpen = true;
            this.$nextTick(() => {
                setTimeout(() => {
                    this.initMap();
                }, 200);
            });
        },

        closeModal() {
            this.isOpen = false;
        },

        initMap() {
            const mapContainer = document.getElementById('hinabi-delivery-map-modal');
            if (!mapContainer || typeof L === 'undefined') return;

            const targetLat = this.lat || 14.5995;
            const targetLng = this.lng || 120.9842;

            if (!this.map) {
                this.map = L.map('hinabi-delivery-map-modal', {
                    zoomControl: true,
                    attributionControl: false
                }).setView([targetLat, targetLng], 14);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(this.map);

                const pinIcon = L.divIcon({
                    className: 'custom-hinabi-pin',
                    html: `
                        <div style="position: relative; width: 34px; height: 42px; transform: translate(-50%, -100%);">
                            <svg viewBox="0 0 24 24" style="width: 34px; height: 42px; filter: drop-shadow(0 3px 4px rgba(0,0,0,0.35)); fill: #78350f;">
                                <path d="M12 0C7.58 0 4 3.58 4 8c0 5.25 7 13 8 13s8-7.75 8-13c0-4.42-3.58-8-8-8zm0 11c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3z"/>
                            </svg>
                        </div>
                    `,
                    iconSize: [34, 42],
                    iconAnchor: [17, 42]
                });

                this.marker = L.marker([targetLat, targetLng], {
                    draggable: true,
                    icon: pinIcon
                }).addTo(this.map);

                this.marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.updateCoordinates(pos.lat, pos.lng);
                });

                this.map.on('click', (e) => {
                    this.marker.setLatLng(e.latlng);
                    this.updateCoordinates(e.latlng.lat, e.latlng.lng);
                });

            } else {
                this.map.invalidateSize();
                this.map.setView([targetLat, targetLng], 14);
                if (this.marker) {
                    this.marker.setLatLng([targetLat, targetLng]);
                }
            }
        },

        updateCoordinates(newLat, newLng) {
            this.lat = newLat;
            this.lng = newLng;
            this.reverseGeocode(newLat, newLng);
        },

        reverseGeocode(latitude, longitude) {
            this.isReverseGeocoding = true;
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`;

            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    if (data && data.address) {
                        const addr = data.address;
                        const road = addr.road || addr.pedestrian || addr.suburb || '';
                        const houseNumber = addr.house_number || '';
                        const suburbOrVillage = addr.quarter || addr.suburb || addr.neighbourhood || addr.village || '';
                        const cityOrMun = addr.city || addr.town || addr.municipality || addr.county || '';

                        if (road || houseNumber) {
                            this.form.street = [houseNumber, road].filter(Boolean).join(' ');
                        }
                        if (suburbOrVillage) {
                            this.form.barangay = suburbOrVillage;
                        }
                        if (cityOrMun) {
                            this.form.city = cityOrMun;
                        }
                        if (addr.postcode) {
                            this.form.postal_code = addr.postcode;
                        }
                    }
                })
                .catch(err => {
                    console.warn('Reverse geocode note:', err);
                })
                .finally(() => {
                    this.isReverseGeocoding = false;
                });
        },

        searchLocation() {
            const query = this.searchQuery.trim();
            if (!query) return;

            this.isSearching = true;
            const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1`;

            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(results => {
                    if (results && results.length > 0) {
                        const match = results[0];
                        const newLat = parseFloat(match.lat);
                        const newLng = parseFloat(match.lon);

                        this.lat = newLat;
                        this.lng = newLng;

                        if (this.map && this.marker) {
                            this.map.flyTo([newLat, newLng], 15);
                            this.marker.setLatLng([newLat, newLng]);
                        }
                        this.reverseGeocode(newLat, newLng);
                    } else {
                        alert('Location not found. Please try entering a different landmark or city.');
                    }
                })
                .catch(err => {
                    console.error('Search error:', err);
                })
                .finally(() => {
                    this.isSearching = false;
                });
        },

        useCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            this.isLocating = true;
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const newLat = position.coords.latitude;
                    const newLng = position.coords.longitude;
                    this.lat = newLat;
                    this.lng = newLng;

                    if (this.map && this.marker) {
                        this.map.flyTo([newLat, newLng], 16);
                        this.marker.setLatLng([newLat, newLng]);
                    }
                    this.reverseGeocode(newLat, newLng);
                    this.isLocating = false;
                },
                (error) => {
                    console.warn('Geolocation error:', error);
                    alert('Unable to retrieve your location. Please check browser permissions.');
                    this.isLocating = false;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        },

        saveAddress() {
            if (!this.isFormValid) return;

            this.isSaving = true;
            this.errorMessage = '';

            const addressData = {
                id: this.form.id,
                recipient_name: this.form.recipient_name.trim(),
                street: this.form.street.trim(),
                barangay: this.form.barangay.trim(),
                city: this.form.city.trim(),
                postal_code: this.form.postal_code.trim(),
                notes: this.form.notes.trim(),
                lat: this.lat,
                lng: this.lng,
                is_default: this.form.is_default
            };

            fetch(config.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(addressData)
            })
            .then(async res => {
                const data = await res.json();
                if (res.status === 401 || data.require_login) {
                    window.location.href = config.loginUrl || '/login';
                    return null;
                }
                return data;
            })
            .then(data => {
                if (!data) return;
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('delivery-location-saved', { detail: data.address || addressData }));
                    this.closeModal();
                    if (window.location.pathname.includes('profile')) {
                        window.location.reload();
                    }
                } else {
                    this.errorMessage = data.error || 'Failed to save address.';
                }
            })
            .catch(err => {
                console.error('Save error:', err);
                this.errorMessage = 'Could not connect to server. Please check your connection.';
            })
            .finally(() => {
                this.isSaving = false;
            });
        }
    };
}
</script>

<div 
    x-data="deliveryAddressModalManager()"
    @open-delivery-modal.window="openModal($event.detail)"
    class="relative z-[70]"
>
    <!-- Modal Dialog -->
    <div 
        x-show="isOpen"
        @keydown.escape.window="if (isOpen) closeModal()"
        class="fixed inset-0 z-[70] overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
        x-cloak
    >
        <!-- Backdrop -->
        <div 
            x-show="isOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
            @click="closeModal()"
        ></div>

        <!-- Dialog Box -->
        <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
            <div 
                x-show="isOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-amber-200/80 my-6"
                @click.stop
            >
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-5 sm:px-6 py-4 border-b border-amber-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center shadow-inner flex-shrink-0">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-amber-950" id="modal-title" x-text="isEditing ? 'Edit Delivery Address' : 'Pin Your Delivery Address'"></h3>
                            <p class="text-xs text-amber-800/80">Drag the pin or click on the map to pinpoint your exact doorstep.</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="closeModal()" 
                        class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-amber-100/50 transition cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 sm:p-6 space-y-4 max-h-[calc(85vh-120px)] overflow-y-auto">
                    
                    <div x-show="errorMessage" x-cloak class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-medium">
                        <span x-text="errorMessage"></span>
                    </div>

                    <!-- Search & GPS -->
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <input 
                                type="text" 
                                x-model="searchQuery" 
                                @keydown.enter.prevent="searchLocation()"
                                placeholder="Search street, village, landmark or city..."
                                class="w-full pl-9 pr-20 py-2.5 text-sm bg-gray-50 border border-amber-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-amber-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                </svg>
                            </div>
                            <button 
                                type="button" 
                                @click="searchLocation()"
                                :disabled="isSearching"
                                class="absolute right-1.5 top-1.5 bottom-1.5 px-3 bg-amber-800 hover:bg-amber-900 text-white text-xs font-medium rounded-lg transition flex items-center gap-1 cursor-pointer disabled:opacity-50"
                            >
                                <span x-show="!isSearching">Search</span>
                                <span x-show="isSearching" class="inline-block animate-spin">⌛</span>
                            </button>
                        </div>

                        <!-- GPS Button -->
                        <button 
                            type="button" 
                            @click="useCurrentLocation()"
                            :class="isLocating ? 'locating-pulse bg-amber-700 text-white' : 'bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300'"
                            class="px-4 py-2.5 text-xs sm:text-sm font-semibold rounded-xl flex items-center justify-center gap-2 transition cursor-pointer shadow-sm whitespace-nowrap"
                            title="Locate my current position"
                        >
                            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span x-text="isLocating ? 'Locating...' : 'Use My Current Location'"></span>
                        </button>
                    </div>

                    <!-- Map Container -->
                    <div class="relative rounded-xl overflow-hidden border-2 border-amber-200/90 shadow-inner delivery-map-container">
                        <div id="hinabi-delivery-map-modal" class="w-full h-56 sm:h-64 bg-amber-50"></div>
                        
                        <div class="absolute top-2 left-2 z-30 bg-white/95 backdrop-blur-xs text-amber-950 text-[11px] font-medium px-2.5 py-1 rounded-md shadow border border-amber-100 flex items-center gap-1.5 pointer-events-none">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Click map or drag pin to position</span>
                        </div>
                    </div>

                    <!-- Form Inputs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-amber-950 mb-1">
                                Recipient Name
                            </label>
                            <input 
                                type="text" 
                                x-model="form.recipient_name" 
                                placeholder="e.g. Juan Dela Cruz"
                                class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-amber-950 mb-1">
                                Street Address / Unit / House No. <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                x-model="form.street" 
                                placeholder="e.g. Unit 4B, 123 Baker Street"
                                class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-amber-950 mb-1">
                                Barangay / District <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                x-model="form.barangay" 
                                placeholder="e.g. Barangay San Antonio"
                                class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-amber-950 mb-1">
                                City / Municipality <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                x-model="form.city" 
                                placeholder="e.g. Quezon City / Manila"
                                class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-amber-950 mb-1">
                                Postal Code <span class="text-xs font-normal text-gray-500">(Optional)</span>
                            </label>
                            <input 
                                type="text" 
                                x-model="form.postal_code" 
                                placeholder="e.g. 1105"
                                class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-amber-950 mb-1">
                                Landmarks / Delivery Notes <span class="text-xs font-normal text-gray-500">(Optional)</span>
                            </label>
                            <input 
                                type="text" 
                                x-model="form.notes" 
                                placeholder="e.g. Beside 7-Eleven, call upon arrival"
                                class="w-full px-3.5 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-700 focus:border-amber-700 transition"
                            />
                        </div>

                        <div class="sm:col-span-2 flex items-center gap-2 pt-1">
                            <input 
                                id="modal-is-default"
                                type="checkbox" 
                                x-model="form.is_default"
                                class="w-4 h-4 text-amber-800 border-gray-300 rounded focus:ring-amber-700 cursor-pointer"
                            />
                            <label for="modal-is-default" class="text-xs font-medium text-amber-950 cursor-pointer">
                                Set as default delivery address
                            </label>
                        </div>
                    </div>

                    <div x-show="isReverseGeocoding" class="text-xs text-amber-800 flex items-center gap-2">
                        <span class="inline-block animate-spin">🔄</span>
                        <span>Detecting address from pinned location...</span>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="bg-amber-50/60 px-5 sm:px-6 py-3.5 border-t border-amber-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-2 sm:gap-3">
                    <button 
                        type="button" 
                        @click="closeModal()" 
                        class="w-full sm:w-auto px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition cursor-pointer text-center"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        @click="saveAddress()" 
                        :disabled="!isFormValid || isSaving"
                        class="w-full sm:w-auto px-6 py-2.5 bg-amber-900 hover:bg-amber-950 active:bg-stone-900 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg x-show="!isSaving" class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                        <span x-show="isSaving" class="inline-block animate-spin text-sm">⌛</span>
                        <span x-text="isSaving ? 'Saving Address...' : (isEditing ? 'Update Address' : 'Confirm & Save Location')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
