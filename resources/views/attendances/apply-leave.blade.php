<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
      Pengajuan Izin Baru
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-xl dark:bg-gray-800 sm:rounded-lg">
        <div class="p-6 lg:p-8">
          <!-- Tombol Kembali -->
          <div class="mb-4">
            <x-secondary-button href="{{ url()->previous() }}">
              <x-heroicon-o-chevron-left class="mr-2 h-5 w-5" />
              Kembali
            </x-secondary-button>
          </div>

          <!-- Form Pengajuan Izin -->
          <form action="{{ route('store-leave-request') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <!-- Status -->
                <div>
                  <x-label for="status" value="Status" />
                  <x-select id="status" class="mt-1 block w-full" name="status" required>
                    <option value="excused" {{ old('status', $attendance->status ?? '') === 'excused' ? 'selected' : '' }}>
                      Izin
                    </option>
                    <option value="sick" {{ old('status', $attendance->status ?? '') === 'sick' ? 'selected' : '' }}>
                      Sakit
                    </option>
                  </x-select>
                  <x-input-error for="status" class="mt-2" />
                </div>

                <!-- Tanggal Mulai & Berakhir -->
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-3">
                  <div>
                    <x-label for="from" value="Tanggal Mulai" />
                    <x-input type="date" min="{{ date('Y-m-d') }}" value="{{ old('from', date('Y-m-d')) }}" id="from"
                      class="mt-1 block w-full" name="from" required />
                    <x-input-error for="from" class="mt-2" />
                  </div>
                  <div>
                    <x-label for="to" value="Tanggal Berakhir (Opsional)" />
                    <x-input type="date" id="to" min="{{ date('Y-m-d') }}" class="mt-1 block w-full"
                      name="to" value="{{ old('to') }}" />
                    <x-input-error for="to" class="mt-2" />
                  </div>
                </div>

                <!-- Keterangan -->
                <div class="mt-4">
                  <x-label for="note" value="Keterangan" />
                  <x-textarea id="note" class="mt-1 block w-full" name="note" required>{{ old('note', $attendance->note ?? '') }}</x-textarea>
                  <x-input-error for="note" class="mt-2" />
                </div>
              </div>

              <!-- Upload Lampiran -->
              <div x-data="{ filename: null, preview: null }">
                <input type="file" class="hidden" id="attachment" name="attachment" x-ref="attachment"
                  x-on:change="
                    filename = $refs.attachment.files[0].name;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                      preview = e.target.result;
                    };
                    reader.readAsDataURL($refs.attachment.files[0]);
                  " />

                <x-label for="attachment" value="Lampiran" />

                <div class="mb-2 mt-2" x-show="preview" style="display: none;">
                  <img class="block h-48 max-h-72 w-full object-contain object-left" x-bind:src="preview" />
                </div>

                @if (!empty($attendance->attachment))
                  <div class="mb-2 mt-2" x-show="!preview">
                    <img class="block h-48 max-h-72 w-full object-contain object-left"
                      src="{{ $attendance->attachment_url }}" />
                  </div>
                @endif

                <x-secondary-button class="me-2 mt-2" type="button" x-on:click.prevent="$refs.attachment.click()">
                  Pilih Lampiran
                </x-secondary-button>

                <x-secondary-button type="button" class="mt-2" x-show="preview"
                  x-on:click="filename = null; preview = null">
                  Hapus Lampiran
                </x-secondary-button>

                <x-input-error for="attachment" class="mt-2" />
              </div>
            </div>

            <input type="hidden" id="lat" name="lat" value="{{ old('lat', $attendance->latitude ?? '') }}">
            <input type="hidden" id="lng" name="lng" value="{{ old('lng', $attendance->longitude ?? '') }}">

            <!-- Tombol Submit -->
            <div class="mb-3 mt-4 flex items-center justify-end">
              <x-button class="ms-4">
                Simpan
              </x-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition((position) => {
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lng').value = position.coords.longitude;
          }, (error) => {
            console.error(`ERROR(${error.code}): ${error.message}`);
            alert('Mohon aktifkan lokasi Anda.');
          });
        }
      });
    </script>
  @endpush
</x-app-layout>