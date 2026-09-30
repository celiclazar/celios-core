@php
    $formId = $data['form_id'] ?? null;
    $showTitle = $data['show_title'] ?? true;
    $showDescription = $data['show_description'] ?? true;
    $locale = app()->getLocale();

    $form = $formId ? \App\Models\Form::where('is_active', true)->find($formId) : null;
@endphp

@if($form)
    @php
        $formTitle = $form->getTranslation('title', $locale, true);
        $formDesc = $form->getTranslation('description', $locale, true);
        $btnText = $form->getTranslation('submit_button_text', $locale, true) ?: __('forms.submit');
        $fields = $form->fields ?? [];
        $submitUrl = url('/' . $locale . '/forms/' . $form->slug . '/submit');
    @endphp

    <section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" id="form-{{ $form->slug }}">
        <div class="max-w-3xl mx-auto">
            @if($showTitle && !empty($formTitle))
                <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-display-lg font-bold text-on-surface tracking-tight break-words">
                        {{ $formTitle }}
                    </h2>
                    @if($showDescription && !empty($formDesc))
                        <p class="mt-3 font-body-md text-body-md text-on-surface-variant leading-relaxed">
                            {{ $formDesc }}
                        </p>
                    @endif
                </div>
            @endif

            <div 
                x-data="{
                    isSubmitting: false,
                    successMessage: '',
                    errorMessage: '',
                    errors: {},
                    async submitForm(e) {
                        this.isSubmitting = true;
                        this.errorMessage = '';
                        this.errors = {};

                        const formData = new FormData(e.target);

                        try {
                            const response = await fetch('{{ $submitUrl }}', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                                body: formData
                            });

                            const result = await response.json();

                            if (response.ok && result.success) {
                                this.successMessage = result.message;
                                e.target.reset();
                                if (result.redirect) {
                                    setTimeout(() => {
                                        window.location.href = result.redirect;
                                    }, 1500);
                                }
                            } else if (response.status === 422) {
                                this.errors = result.errors || {};
                                this.errorMessage = result.message || '{{ __('forms.submission_failed') }}';
                            } else {
                                this.errorMessage = result.message || '{{ __('forms.submission_failed') }}';
                            }
                        } catch (err) {
                            this.errorMessage = '{{ __('forms.submission_failed') }}';
                        } finally {
                            this.isSubmitting = false;
                        }
                    }
                }"
                class="bg-surface-container-lowest rounded-2xl sm:rounded-3xl border border-surface-container-high/60 p-5 sm:p-8 md:p-12 shadow-sm"
            >
                {{-- Success Banner --}}
                <div 
                    x-show="successMessage" 
                    x-cloak 
                    class="mb-6 p-4 rounded-xl bg-tertiary-container/40 border border-tertiary/30 text-on-tertiary-container text-sm font-medium"
                >
                    <div class="flex items-center space-x-2">
                        <span class="material-symbols-outlined text-tertiary text-xl shrink-0">check_circle</span>
                        <span x-text="successMessage" class="break-words"></span>
                    </div>
                </div>

                {{-- Error Banner --}}
                <div 
                    x-show="errorMessage" 
                    x-cloak 
                    class="mb-6 p-4 rounded-xl bg-error-container/20 border border-error/30 text-error text-sm font-medium"
                >
                    <div class="flex items-center space-x-2">
                        <span class="material-symbols-outlined text-error text-xl shrink-0">error</span>
                        <span x-text="errorMessage" class="break-words"></span>
                    </div>
                </div>

                {{-- The Form --}}
                <form @submit.prevent="submitForm" enctype="multipart/form-data" method="POST" action="{{ $submitUrl }}">
                    @csrf

                    {{-- Honeypot bot protection --}}
                    <input type="text" name="_hp_name" style="display:none !important;" tabindex="-1" autocomplete="off" />
                    <input type="hidden" name="_hp_time" value="{{ time() }}" />

                    <div class="grid grid-cols-12 gap-4 sm:gap-6">
                        @foreach($fields as $field)
                            @php
                                $fieldKey = $field['key'] ?? '';
                                $fieldType = $field['type'] ?? 'text';
                                $fieldWidth = $field['width'] ?? '12';
                                $isRequired = !empty($field['required']);
                                $fieldLabel = is_array($field['label'] ?? null)
                                    ? ($field['label'][$locale] ?? $field['label']['sr'] ?? $field['label']['en'] ?? $fieldKey)
                                    : ($field['label'] ?? $fieldKey);
                                $fieldPlaceholder = is_array($field['placeholder'] ?? null)
                                    ? ($field['placeholder'][$locale] ?? $field['placeholder']['sr'] ?? $field['placeholder']['en'] ?? '')
                                    : ($field['placeholder'] ?? '');

                                $colSpanClass = match((string)$fieldWidth) {
                                    '6' => 'col-span-12 md:col-span-6',
                                    '4' => 'col-span-12 sm:col-span-6 md:col-span-4',
                                    '8' => 'col-span-12 md:col-span-8',
                                    default => 'col-span-12',
                                };
                            @endphp

                            <div class="{{ $colSpanClass }}">
                                <label for="field_{{ $fieldKey }}" class="block font-label-sm text-label-sm font-semibold text-on-surface mb-1.5">
                                    {{ $fieldLabel }}
                                    @if($isRequired)
                                        <span class="text-error">*</span>
                                    @endif
                                </label>

                                @if($fieldType === 'textarea')
                                    <textarea
                                        id="field_{{ $fieldKey }}"
                                        name="{{ $fieldKey }}"
                                        rows="4"
                                        placeholder="{{ $fieldPlaceholder }}"
                                        @if($isRequired) required @endif
                                        class="block w-full rounded-xl border border-surface-container-high bg-surface text-on-surface px-4 py-3 placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm transition"
                                        :class="errors['{{ $fieldKey }}'] ? 'border-error focus:border-error focus:ring-error/20' : ''"
                                    ></textarea>

                                @elseif($fieldType === 'select')
                                    @php
                                        $options = array_filter(array_map('trim', explode("\n", $field['options'] ?? '')));
                                    @endphp
                                    <select
                                        id="field_{{ $fieldKey }}"
                                        name="{{ $fieldKey }}"
                                        @if($isRequired) required @endif
                                        class="block w-full rounded-xl border border-surface-container-high bg-surface text-on-surface px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm transition"
                                        :class="errors['{{ $fieldKey }}'] ? 'border-error focus:border-error focus:ring-error/20' : ''"
                                    >
                                        <option value="">{{ $fieldPlaceholder ?: '-- ' . __('forms.select_form') . ' --' }}</option>
                                        @foreach($options as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>

                                @elseif($fieldType === 'radio')
                                    @php
                                        $options = array_filter(array_map('trim', explode("\n", $field['options'] ?? '')));
                                    @endphp
                                    <div class="mt-2 flex flex-wrap gap-4">
                                        @foreach($options as $optIdx => $opt)
                                            <label class="inline-flex items-center text-sm text-on-surface cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    name="{{ $fieldKey }}" 
                                                    value="{{ $opt }}" 
                                                    class="h-4 w-4 text-primary border-surface-container-high focus:ring-primary" 
                                                    @if($isRequired && $optIdx === 0) required @endif
                                                />
                                                <span class="ml-2 font-body-sm">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                @elseif($fieldType === 'checkbox')
                                    @php
                                        $options = array_filter(array_map('trim', explode("\n", $field['options'] ?? '')));
                                    @endphp
                                    @if(count($options) > 0)
                                        <div class="mt-2 flex flex-wrap gap-4">
                                            @foreach($options as $opt)
                                                <label class="inline-flex items-center text-sm text-on-surface cursor-pointer">
                                                    <input 
                                                        type="checkbox" 
                                                        name="{{ $fieldKey }}[]" 
                                                        value="{{ $opt }}" 
                                                        class="h-4 w-4 rounded text-primary border-surface-container-high focus:ring-primary" 
                                                    />
                                                    <span class="ml-2 font-body-sm">{{ $opt }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <label class="inline-flex items-center text-sm text-on-surface cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    name="{{ $fieldKey }}" 
                                                    value="1" 
                                                    class="h-4 w-4 rounded text-primary border-surface-container-high focus:ring-primary" 
                                                    @if($isRequired) required @endif
                                                />
                                                <span class="ml-2 font-body-sm">{{ $fieldPlaceholder ?: $fieldLabel }}</span>
                                            </label>
                                        </div>
                                    @endif

                                @elseif($fieldType === 'file')
                                    <div class="mt-1">
                                        <input
                                            id="field_{{ $fieldKey }}"
                                            type="file"
                                            name="{{ $fieldKey }}"
                                            @if($isRequired) required @endif
                                            @if(!empty($field['allowed_types']))
                                                accept="{{ '.' . str_replace([' ', ','], ['', ',.'], $field['allowed_types']) }}"
                                            @endif
                                            class="block w-full text-xs sm:text-sm text-on-surface-variant border border-surface-container-high rounded-xl p-2 bg-surface file:mr-3 sm:file:mr-4 file:py-1.5 sm:file:py-2 file:px-3 sm:file:px-4 file:rounded-lg file:border-0 file:text-xs sm:file:text-sm file:font-semibold file:bg-surface-container file:text-primary hover:file:bg-surface-container-high cursor-pointer"
                                        />
                                        @if(!empty($field['allowed_types']))
                                            <p class="mt-1 text-xs text-on-surface-variant break-words">{{ $field['allowed_types'] }}</p>
                                        @endif
                                    </div>

                                @else
                                    <input
                                        id="field_{{ $fieldKey }}"
                                        type="{{ in_array($fieldType, ['email', 'tel', 'number', 'date']) ? $fieldType : 'text' }}"
                                        name="{{ $fieldKey }}"
                                        placeholder="{{ $fieldPlaceholder }}"
                                        @if($isRequired) required @endif
                                        class="block w-full rounded-xl border border-surface-container-high bg-surface text-on-surface px-4 py-3 placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-2 focus:ring-primary/20 text-sm transition"
                                        :class="errors['{{ $fieldKey }}'] ? 'border-error focus:border-error focus:ring-error/20' : ''"
                                    />
                                @endif

                                {{-- Field Error --}}
                                <template x-if="errors['{{ $fieldKey }}']">
                                    <p class="mt-1 text-xs text-error font-medium" x-text="errors['{{ $fieldKey }}'][0]"></p>
                                </template>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-stretch sm:justify-end">
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-primary text-on-primary font-label-md font-semibold hover:bg-primary-container shadow-md hover:shadow-lg transition-all active:scale-[0.98] disabled:opacity-50 cursor-pointer"
                        >
                            <svg 
                                x-show="isSubmitting" 
                                x-cloak 
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-on-primary" 
                                xmlns="http://www.w3.org/2000/svg" 
                                fill="none" 
                                viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? '{{ __('forms.sending') }}' : '{{ $btnText }}'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endif
