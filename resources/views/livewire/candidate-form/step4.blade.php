@if($currentStep === 4)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Психометрический портрет</h2>

    <!-- Секция 1: Тест Гарднера (обязательный) -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Тест множественных интеллектов Гарднера</h3>
            <span class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Обязательно</span>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            @if($gardner_test_completed)
                <!-- Тест пройден -->
                <div class="mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-green-800">Тест пройден</h4>
                            <p class="text-sm text-gray-600">Ваши результаты сохранены и будут использованы в отчёте</p>
                        </div>
                        <a href="{{ route('gardner-test') }}"
                           target="_blank"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Посмотреть результаты
                        </a>
                    </div>
                </div>
                <!-- Информация о тесте Гарднера -->
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">О тесте Гарднера</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Тест определяет ваши доминирующие типы интеллекта по теории Говарда Гарднера.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Тест не пройден -->
                <div class="mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-amber-800">Тест не пройден</h4>
                            <p class="text-sm text-gray-600">Для продолжения необходимо пройти тест Гарднера (45 вопросов, ~10 минут)</p>
                        </div>
                        <a href="{{ route('gardner-test') }}"
                           target="_blank"
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                            Пройти тест
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <!-- Информация о тесте Гарднера -->
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">О тесте Гарднера</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>Тест определяет ваши доминирующие типы интеллекта по теории Говарда Гарднера.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @error('gardner_test_completed')
            <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                <p class="text-red-600 text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $message }}
                </p>
            </div>
        @enderror
    </div>

    <!-- Контейнер для MBTI и Gallup в одну строку -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 lg:gap-8">
        <!-- MBTI тип личности (обязательный) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Тип личности MBTI</h3>
                <span class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Обязательно</span>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Выберите ваш тип MBTI</label>
                    <select wire:model="mbti_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Выберите тип MBTI</option>
                        <optgroup label="Аналитики">
                            <option value="INTJ">INTJ - Архитектор</option>
                            <option value="INTP">INTP - Мыслитель</option>
                            <option value="ENTJ">ENTJ - Командир</option>
                            <option value="ENTP">ENTP - Полемист</option>
                        </optgroup>
                        <optgroup label="Дипломаты">
                            <option value="INFJ">INFJ - Активист</option>
                            <option value="INFP">INFP - Посредник</option>
                            <option value="ENFJ">ENFJ - Тренер</option>
                            <option value="ENFP">ENFP - Борец</option>
                        </optgroup>
                        <optgroup label="Хранители">
                            <option value="ISTJ">ISTJ - Логист</option>
                            <option value="ISFJ">ISFJ - Защитник</option>
                            <option value="ESTJ">ESTJ - Менеджер</option>
                            <option value="ESFJ">ESFJ - Консул</option>
                        </optgroup>
                        <optgroup label="Искатели">
                            <option value="ISTP">ISTP - Виртуоз</option>
                            <option value="ISFP">ISFP - Авантюрист</option>
                            <option value="ESTP">ESTP - Делец</option>
                            <option value="ESFP">ESFP - Развлекатель</option>
                        </optgroup>
                    </select>
                    @error('mbti_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Дополнительная информация о MBTI -->
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">О тесте MBTI</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p class="mb-2">Выберите ваш тип личности согласно результатам теста Myers-Briggs.</p>
                                <p>Если вы еще не знаете свой тип, <a href="https://www.16personalities.com/ru/test-lichnosti" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-800 underline hover:text-blue-900">пройдите тест здесь</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallup тест (рекомендуется) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold">Gallup CliftonStrengths 34 PDF</h3>
                <span class="px-3 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">Рекомендуется</span>
            </div>
            <div class="w-full" x-data="fileUpload()">
                <!-- Область загрузки файла -->
                <div x-show="!fileUploaded" class="bg-white rounded-lg border border-gray-200">
                    <label class="group flex justify-center w-full h-32 px-4 py-6 transition-all duration-300 ease-in-out bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer focus:outline-none transform hover:scale-[1.02] hover:shadow-lg hover:border-blue-400 hover:bg-blue-50/50"
                           @dragover.prevent="isDragOver = true"
                           @dragleave.prevent="isDragOver = false"
                           @drop.prevent="handleDrop($event)"
                           :class="isDragOver ? 'border-blue-500 bg-gradient-to-br from-blue-50 to-blue-100 shadow-2xl scale-[1.02] animate-pulse ring-2 ring-blue-400 ring-opacity-50' : 'border-gray-300'">
                        <span class="flex flex-col items-center space-y-2 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-8 h-8 text-gray-600 transition-all duration-300 group-hover:text-blue-600 group-hover:scale-110 group-hover:rotate-6"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                 :class="isDragOver ? 'text-blue-600 scale-110 rotate-12 animate-bounce' : 'text-gray-600'">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span class="font-medium text-gray-600 transition-all duration-300 group-hover:text-blue-700 text-center"
                                  :class="isDragOver ? 'text-blue-700' : 'text-gray-600'">
                                <span x-show="!isDragOver" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="block">
                                    <span class="block text-sm">Перетащите файлы или</span>
                                    <span class="text-blue-600 underline hover:text-blue-800 text-sm">выберите</span>
                                </span>
                                <span x-show="isDragOver" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="text-blue-700 font-semibold">
                                    Отпустите для загрузки PDF файла
                                </span>
                            </span>
                        </span>
                        <input type="file"
                               wire:model="gallup_pdf"
                               class="hidden"
                               accept=".pdf"
                               @change="handleFileChange($event)"
                               x-ref="fileInput">
                    </label>

                    <!-- Рекомендация под полем загрузки -->
                    <div class="p-4 bg-blue-50 rounded-b-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Рекомендация</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Рекомендуем загрузить PDF файл результата теста Gallup CliftonStrengths34 в оригинале. На основе данного документа формируются отчеты Divergents (FMD, DPT, DPs).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @error('gallup_pdf') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror

                <!-- Информация о загруженном файле -->
                <div x-show="fileUploaded" x-transition>
                    <div class="bg-white rounded-lg border border-gray-200">
                        <!-- Заголовок -->
                        <div class="flex justify-between items-center p-4 border-b bg-gray-50">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">PDF файл загружен</h4>
                                    <p class="text-sm text-gray-500">Gallup результаты готовы к отправке</p>
                                </div>
                            </div>
                            <button type="button"
                                    @click="removeFile()"
                                    class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Информация о файле -->
                        <div class="p-4">
                            <div class="grid grid-cols-1 gap-3">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-sm text-gray-500 mb-1">Имя файла</div>
                                    <div class="font-medium text-gray-900 text-sm" x-text="fileName || 'Gallup результаты.pdf'"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-sm text-gray-500 mb-1">Размер</div>
                                        <div class="font-medium text-gray-900 text-sm" x-text="fileSize || 'Загружен'"></div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="text-sm text-gray-500 mb-1">Статус</div>
                                        <div class="font-medium text-sm"
                                             x-bind:class="isExistingFile ? 'text-green-600' : 'text-blue-600'"
                                             x-text="isExistingFile ? 'Сохранен' : 'Загружен'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Кнопка для скачивания/просмотра если это существующий файл -->
                            <div x-show="isExistingFile && downloadUrl" class="mt-3">
                                <a x-bind:href="downloadUrl"
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full justify-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Скачать файл
                                </a>
                            </div>

                            <!-- Информация для нового файла -->
                            <div x-show="!isExistingFile" class="mt-3">
                                <div class="text-sm text-blue-600 bg-blue-50 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        Файл будет сохранен при переходе к следующему шагу
                                    </div>
                                </div>
                            </div>
                        </div>
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
            console.log('Step 4 file upload component initialized');

            // Проверяем, есть ли уже загруженный файл при инициализации
            setTimeout(() => {
                this.checkExistingFile();
            }, 100);

            // Слушаем обновления Livewire
            this.$nextTick(() => {
                window.addEventListener('livewire:updated', () => {
                    setTimeout(() => {
                        this.checkExistingFile();
                        this.checkForErrors();
                    }, 100);
                });

                // Слушаем событие сброса файла
                this.$wire.on('gallup-file-reset', () => {
                    console.log('Gallup file reset event received');
                    this.resetFileState();
                });
            });
        },

        checkExistingFile() {
            console.log('Checking for existing Gallup file...');

            // Проверяем есть ли файл в Livewire компоненте
            if (typeof @this !== 'undefined') {
                // Получаем информацию о файле из Livewire
                const livewireGallupPdf = @this.get('gallup_pdf');
                const candidateGallupPdf = @this.get('candidate.gallup_pdf');

                console.log('Livewire gallup_pdf:', livewireGallupPdf);
                console.log('Candidate gallup_pdf:', candidateGallupPdf);

                // Если есть любой файл, получаем информацию через PHP
                if (livewireGallupPdf || candidateGallupPdf) {
                    @this.call('getGallupFileInfo').then(fileInfo => {
                        if (fileInfo) {
                            this.fileUploaded = true;
                            this.isExistingFile = fileInfo.isExisting;
                            this.fileName = fileInfo.fileName;
                            this.fileSize = fileInfo.fileSize;

                            console.log('File info received:', fileInfo);

                            // Если это существующий файл, получаем URL для скачивания
                            if (fileInfo.isExisting) {
                                @this.call('getGallupPdfUrl').then(url => {
                                    if (url) {
                                        this.downloadUrl = url;
                                        console.log('Download URL set:', url);
                                    }
                                }).catch(error => {
                                    console.log('Error getting download URL:', error);
                                });
                            }

                            return;
                        }
                    }).catch(error => {
                        console.log('Error getting file info:', error);
                    });

                    return;
                }
            }

            console.log('No existing file found');
        },

        checkForErrors() {
            // Проверяем есть ли ошибки валидации для gallup_pdf
            if (typeof @this !== 'undefined') {
                const errors = @this.get('errors');
                if (errors && errors.gallup_pdf) {
                    console.log('Gallup PDF validation error detected, hiding file block');
                    // Скрываем блок загруженного файла при ошибке
                    this.fileUploaded = false;
                    this.isExistingFile = false;
                    this.fileName = '';
                    this.fileSize = '';
                    this.downloadUrl = '';
                    this.isDragOver = false;

                    // Очищаем input
                    const fileInput = this.$el.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                }
            }
        },

        extractFileNameFromPath(path) {
            if (!path) return 'Gallup результаты';

            // Извлекаем имя файла из пути
            const pathParts = path.split('/');
            const fileName = pathParts[pathParts.length - 1];

            // Убираем timestamp префикс если есть
            const cleanName = fileName.replace(/^\d+_/, '');

            return cleanName || 'Gallup результаты.pdf';
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

            // Устанавливаем файл в input для Livewire
            const fileInput = this.$refs.fileInput;
            if (fileInput) {
                // Создаем новый FileList для input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                // Триггерим событие change для Livewire
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Показываем информацию о файле
            this.fileUploaded = true;
            this.isExistingFile = false;
            this.fileName = file.name;
            this.fileSize = this.formatFileSize(file.size);

            console.log('File dropped:', file.name, this.formatFileSize(file.size));
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

            // Показываем информацию о новом файле
            this.fileUploaded = true;
            this.isExistingFile = false;
            this.fileName = file.name;
            this.fileSize = this.formatFileSize(file.size);

            console.log('New file selected:', file.name, this.formatFileSize(file.size));
        },

        removeFile() {
            console.log('Removing file...');

            // Очищаем Livewire
            if (typeof @this !== 'undefined' && @this.set) {
                @this.set('gallup_pdf', null);
            }

            this.resetFileState();
        },

        resetFileState() {
            console.log('Resetting file state...');

            // Очищаем input
            const fileInput = this.$el.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.value = '';
            }

            // Сбрасываем состояние
            this.fileUploaded = false;
            this.isExistingFile = false;
            this.fileName = '';
            this.fileSize = '';
            this.downloadUrl = '';
            this.isDragOver = false;

            console.log('File state reset');
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
