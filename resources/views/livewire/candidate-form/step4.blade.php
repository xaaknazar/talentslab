@if($currentStep === 4)
<div class="step">
    <h2 class="text-2xl font-bold mb-8 text-gray-900">Психометрический портрет</h2>

    <!-- Тест Гарднера -->
    <div class="mb-8">
        <div class="flex items-center mb-3">
            <h3 class="text-lg font-semibold text-gray-900">Тест множественных интеллектов Гарднера</h3>
            <span class="ml-3 px-2 py-0.5 text-xs font-medium text-red-600 bg-red-50 rounded">Обязательно</span>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-5">
            @if($gardner_test_completed)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Тест пройден</p>
                            <p class="text-sm text-gray-500">Результаты сохранены</p>
                        </div>
                    </div>
                    <a href="{{ route('gardner-test') }}"
                       target="_blank"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Посмотреть результаты
                    </a>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-amber-50 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Тест не пройден</p>
                            <p class="text-sm text-gray-500">44 вопроса, около 10 минут</p>
                        </div>
                    </div>
                    <a href="{{ route('gardner-test') }}"
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                        Пройти тест
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
        @error('gardner_test_completed')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- MBTI и Gallup -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- MBTI -->
        <div>
            <div class="flex items-center mb-3">
                <h3 class="text-lg font-semibold text-gray-900">Тип личности MBTI</h3>
                <span class="ml-3 px-2 py-0.5 text-xs font-medium text-red-600 bg-red-50 rounded">Обязательно</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <select wire:model="mbti_type" class="w-full rounded-lg border-gray-300 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Выберите тип MBTI</option>
                    <optgroup label="Аналитики">
                        <option value="INTJ">INTJ — Стратег</option>
                        <option value="INTP">INTP — Учёный</option>
                        <option value="ENTJ">ENTJ — Командир</option>
                        <option value="ENTP">ENTP — Полемист</option>
                    </optgroup>
                    <optgroup label="Дипломаты">
                        <option value="INFJ">INFJ — Активист</option>
                        <option value="INFP">INFP — Посредник</option>
                        <option value="ENFJ">ENFJ — Тренер</option>
                        <option value="ENFP">ENFP — Борец</option>
                    </optgroup>
                    <optgroup label="Хранители">
                        <option value="ISTJ">ISTJ — Логист</option>
                        <option value="ISFJ">ISFJ — Защитник</option>
                        <option value="ESTJ">ESTJ — Менеджер</option>
                        <option value="ESFJ">ESFJ — Консул</option>
                    </optgroup>
                    <optgroup label="Искатели">
                        <option value="ISTP">ISTP — Виртуоз</option>
                        <option value="ISFP">ISFP — Артист</option>
                        <option value="ESTP">ESTP — Делец</option>
                        <option value="ESFP">ESFP — Развлекатель</option>
                    </optgroup>
                </select>
                @error('mbti_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">О тесте MBTI</p>
                            <p class="text-blue-700">Выберите тип личности по результатам теста Myers-Briggs. Если не знаете свой тип — <a href="https://www.16personalities.com/ru/test-lichnosti" target="_blank" class="underline hover:no-underline">пройдите тест</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallup -->
        <div>
            <div class="flex items-center mb-3">
                <h3 class="text-lg font-semibold text-gray-900">Gallup PDF</h3>
                <span class="ml-3 px-2 py-0.5 text-xs font-medium text-gray-500 bg-gray-100 rounded">Рекомендуется</span>
            </div>

            <div class="bg-white rounded-lg border border-gray-200" x-data="fileUpload()">
                <div x-show="!fileUploaded" class="p-5">
                    <label class="flex justify-center w-full h-24 px-4 border-2 border-gray-200 border-dashed rounded-lg cursor-pointer hover:border-gray-300 hover:bg-gray-50 transition-colors"
                           @dragover.prevent="isDragOver = true"
                           @dragleave.prevent="isDragOver = false"
                           @drop.prevent="handleDrop($event)"
                           :class="isDragOver ? 'border-blue-400 bg-blue-50' : ''">
                        <span class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="mt-2 text-sm text-gray-500">
                                <span class="text-blue-600 hover:underline">Выберите файл</span> или перетащите
                            </span>
                        </span>
                        <input type="file"
                               wire:model="gallup_pdf"
                               class="hidden"
                               accept=".pdf"
                               @change="handleFileChange($event)"
                               x-ref="fileInput">
                    </label>
                    @error('gallup_pdf') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Рекомендация</p>
                                <p class="text-blue-700">Загрузите оригинальный Gallup PDF — через него формируются отчёты команды Дивергента.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Загруженный файл -->
                <div x-show="fileUploaded" x-transition class="p-5">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900" x-text="fileName || 'Gallup.pdf'"></p>
                                <p class="text-xs text-gray-500" x-text="fileSize || ''"></p>
                            </div>
                        </div>
                        <button type="button" @click="removeFile()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div x-show="isExistingFile && downloadUrl" class="mt-3">
                        <a x-bind:href="downloadUrl" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Скачать файл
                        </a>
                    </div>

                    <div x-show="!isExistingFile" class="mt-3">
                        <p class="text-sm text-gray-500">Файл будет сохранён при переходе к следующему шагу</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
function fileUpload() {
    return {
        fileUploaded: false,
        fileName: '',
        fileSize: '',
        isExistingFile: false,
        downloadUrl: '',
        isDragOver: false,

        init() {
            setTimeout(() => this.checkExistingFile(), 100);

            this.$nextTick(() => {
                window.addEventListener('livewire:updated', () => {
                    setTimeout(() => {
                        this.checkExistingFile();
                        this.checkForErrors();
                    }, 100);
                });

                this.$wire.on('gallup-file-reset', () => this.resetFileState());
            });
        },

        checkExistingFile() {
            if (typeof @this !== 'undefined') {
                const livewireGallupPdf = @this.get('gallup_pdf');
                const candidateGallupPdf = @this.get('candidate.gallup_pdf');

                if (livewireGallupPdf || candidateGallupPdf) {
                    @this.call('getGallupFileInfo').then(fileInfo => {
                        if (fileInfo) {
                            this.fileUploaded = true;
                            this.isExistingFile = fileInfo.isExisting;
                            this.fileName = fileInfo.fileName;
                            this.fileSize = fileInfo.fileSize;

                            if (fileInfo.isExisting) {
                                @this.call('getGallupPdfUrl').then(url => {
                                    if (url) this.downloadUrl = url;
                                }).catch(() => {});
                            }
                        }
                    }).catch(() => {});
                }
            }
        },

        checkForErrors() {
            if (typeof @this !== 'undefined') {
                const errors = @this.get('errors');
                if (errors && errors.gallup_pdf) {
                    this.resetFileState();
                }
            }
        },

        handleDrop(event) {
            this.isDragOver = false;
            const files = event.dataTransfer.files;
            if (!files.length) return;

            const file = files[0];
            if (file.type !== 'application/pdf') {
                alert('Пожалуйста, выберите PDF файл');
                return;
            }

            const fileInput = this.$refs.fileInput;
            if (fileInput) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            this.fileUploaded = true;
            this.isExistingFile = false;
            this.fileName = file.name;
            this.fileSize = this.formatFileSize(file.size);
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (!file) {
                this.fileUploaded = false;
                return;
            }

            if (file.type !== 'application/pdf') {
                alert('Пожалуйста, выберите PDF файл');
                event.target.value = '';
                return;
            }

            this.fileUploaded = true;
            this.isExistingFile = false;
            this.fileName = file.name;
            this.fileSize = this.formatFileSize(file.size);
        },

        removeFile() {
            if (typeof @this !== 'undefined' && @this.set) {
                @this.set('gallup_pdf', null);
            }
            this.resetFileState();
        },

        resetFileState() {
            const fileInput = this.$el.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';

            this.fileUploaded = false;
            this.isExistingFile = false;
            this.fileName = '';
            this.fileSize = '';
            this.downloadUrl = '';
            this.isDragOver = false;
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }
}
</script>
@endpush
