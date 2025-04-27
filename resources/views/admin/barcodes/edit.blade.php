<x-app-layout>
  @once
    @push('styles')
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush
  @endonce

  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
      {{ __('Edit Barcode') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <form action="{{ route('admin.barcodes.update', $barcode->id) }}" method="post">
            @csrf
            @method('PATCH')

            <div class="flex flex-col gap-4 md:flex-row md:items-start md:gap-3">
              <div class="w-full">
                <x-label for="name">Nama Barcode</x-label>
                <x-input name="name" id="name" class="mt-1 block w-full" type="text"
                  placeholder="Barcode Baru" :value="old('name', $barcode->name)" />
                @error('name')
                  <x-input-error for="name" class="mt-2" message="{{ $message }}" />
                @enderror
              </div>
              <div class="w-full">
                <x-label for="value">Value Barcode</x-label>
                @livewire('admin.barcode-value-input-component', ['value' => $barcode->value])
              </div>
            </div>

            <div class="mt-4 flex gap-3">
              <div class="w-full">
                <x-label for="radius">Radius Valid Absen</x-label>
                <x-input name="radius" id="radius" class="mt-1 block w-full" type="number"
                  placeholder="50 (meter)" :value="old('radius', $barcode->radius)" />
                @error('radius')
                  <x-input-error for="radius" class="mt-2" message="{{ $message }}" />
                @enderror
              </div>
              <div class="w-full"></div>
            </div>

            <div class="mt-5">
              <h3 class="text-lg font-semibold dark:text-gray-400">{{ __('Coordinate') }}</h3>

              <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div class="w-full">
                  <x-label for="lat">Latitude</x-label>
                  <x-input name="lat" id="lat" class="mt-1 block w-full" type="text"
                    placeholder="cth: -6.12345"
                    :value="old('lat', $barcode->latLng['lat'] ?? '')" />
                  @error('lat')
                    <x-input-error for="lat" class="mt-2" message="{{ $message }}" />
                  @enderror
                </div>
                <div class="w-full">
                  <x-label for="lng">Longitude</x-label>
                  <x-input name="lng" id="lng" class="mt-1 block w-full" type="text"
                    placeholder="cth: 106.12345"
                    :value="old('lng', $barcode->latLng['lng'] ?? '')" />
                  @error('lng')
                    <x-input-error for="lng" class="mt-2" message="{{ $message }}" />
                  @enderror
                </div>
              </div>

              <div class="flex flex-col items-start gap-3 md:flex-row">
                <x-button type="button" onclick="toggleMap()" class="text-nowrap mt-4">
                  <x-heroicon-s-map-pin class="mr-2 h-5 w-5" /> Tampilkan/Sembunyikan Peta
                </x-button>
                <div id="map" class="my-6 h-72 w-full md:h-96" style="display: none;"></div>
              </div>

              <div class="mb-3 mt-4 flex items-center justify-end">
                <x-button class="ms-4">
                  {{ __('Save') }}
                </x-button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @once
    @push('scripts')
      <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
      <script>
        let map, marker;

        function initializeMap(lat, lng) {
          if (!map) {
            map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
              attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([lat, lng], { draggable: true }).addTo(map);

            marker.on('dragend', function (event) {
              let position = event.target.getLatLng();
              document.getElementById('lat').value = position.lat.toFixed(6);
              document.getElementById('lng').value = position.lng.toFixed(6);
            });
          }
        }

        function getUserLocation() {
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              function (position) {
                let userLat = position.coords.latitude;
                let userLng = position.coords.longitude;
                document.getElementById('lat').value = userLat.toFixed(6);
                document.getElementById('lng').value = userLng.toFixed(6);
                initializeMap(userLat, userLng);
              },
              function () {
                alert("Gagal mendapatkan lokasi. Periksa izin lokasi di browser.");
                initializeMap(-6.200000, 106.816666); // Default Jakarta
              }
            );
          } else {
            alert("Geolocation tidak didukung di browser ini.");
            initializeMap(-6.200000, 106.816666); // Default Jakarta
          }
        }

        function toggleMap() {
          let mapContainer = document.getElementById('map');
          if (mapContainer.style.display === "none" || mapContainer.style.display === "") {
            mapContainer.style.display = "block";
            let lat = parseFloat(document.getElementById('lat').value) || -6.200000;
            let lng = parseFloat(document.getElementById('lng').value) || 106.816666;
            initializeMap(lat, lng);
          } else {
            mapContainer.style.display = "none";
          }
        }

        window.addEventListener("load", getUserLocation);
      </script>
    @endpush
  @endonce
</x-app-layout>
