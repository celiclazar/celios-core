@php
    $locale = app()->getLocale();
    $getStr = function($val, $default = '') use ($locale) {
        if (is_null($val)) return $default;
        if (is_array($val)) {
            return $val[$locale] ?? $val['sr'] ?? $val['Srpski'] ?? $val['en'] ?? $val['English'] ?? $val['it'] ?? reset($val) ?? $default;
        }
        return is_string($val) ? $val : (string) $val;
    };

    $title = $getStr($data['title'] ?? null, __('newsletter.block_default_title'));
    $description = $getStr($data['description'] ?? ($data['subtitle'] ?? null), __('newsletter.block_default_description'));
    $buttonText = $getStr($data['button_text'] ?? null, __('newsletter.block_default_button'));
    $disclaimer = $getStr($data['disclaimer'] ?? null, __('newsletter.block_default_disclaimer'));
@endphp

<section class="py-space-8 sm:py-space-12 max-w-max-content-width mx-auto px-gutter-md sm:px-gutter-lg w-full" x-data="{
    loading: false,
    successMessage: '',
    errorMessage: '',
    async submitForm(e) {
        this.loading = true;
        this.errorMessage = '';
        this.successMessage = '';
        const formData = new FormData(e.target);
        
        try {
            const response = await fetch(e.target.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (response.ok && data.success) {
                this.successMessage = data.message;
                e.target.reset();
            } else {
                this.errorMessage = data.message || '{{ __('newsletter.submission_failed') }}';
            }
        } catch (err) {
            this.errorMessage = '{{ __('newsletter.submission_failed') }}';
        } finally {
            this.loading = false;
        }
    }
}">
    <div class="rounded-2xl sm:rounded-3xl bg-surface-container-low border border-surface-container-high/60 p-6 sm:p-space-8 md:p-space-12 text-center relative overflow-hidden shadow-sm">
        <!-- Ambient subtle glow -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-fixed/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-secondary-container/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-3xl mx-auto relative z-10">
            @if(!empty($title))
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-display-lg font-bold text-on-surface mb-3 tracking-tight break-words">
                    {{ $title }}
                </h2>
            @endif

            @if(!empty($description))
                <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mx-auto mb-6 sm:mb-8 leading-relaxed">
                    {{ $description }}
                </p>
            @endif

            <!-- Success notification -->
            <div x-show="successMessage" x-cloak class="mb-6 p-4 rounded-xl sm:rounded-2xl bg-tertiary-container/40 border border-tertiary/30 text-on-tertiary-container max-w-xl mx-auto flex items-center justify-center space-x-2 text-sm font-medium">
                <span class="material-symbols-outlined text-tertiary text-xl shrink-0">check_circle</span>
                <span x-text="successMessage" class="break-words"></span>
            </div>

            <!-- Error notification -->
            <div x-show="errorMessage" x-cloak class="mb-6 p-4 rounded-xl sm:rounded-2xl bg-error-container/20 border border-error/30 text-error max-w-xl mx-auto flex items-center justify-center space-x-2 text-sm font-medium">
                <span class="material-symbols-outlined text-error text-xl shrink-0">error</span>
                <span x-text="errorMessage" class="break-words"></span>
            </div>

            @if(session('newsletter_status'))
                <div class="mb-6 p-4 rounded-xl sm:rounded-2xl {{ session('newsletter_status.success') ? 'bg-tertiary-container/40 text-on-tertiary-container border border-tertiary/30' : 'bg-error-container/20 text-error border border-error/30' }} max-w-xl mx-auto text-sm font-medium break-words">
                    {{ session('newsletter_status.message') }}
                </div>
            @endif

            <form 
                action="{{ route('newsletter.subscribe.localized', ['locale' => $locale]) }}" 
                method="POST" 
                @submit.prevent="submitForm($event)"
                class="max-w-xl mx-auto flex flex-col sm:flex-row gap-3">
                @csrf
                
                <!-- Spam Honeypot fields -->
                <input type="text" name="_hp_name" value="" style="display:none !important;" tabindex="-1" autocomplete="off" />
                <input type="hidden" name="_hp_time" value="{{ time() }}" />
                <input type="hidden" name="source" value="newsletter_block" />

                <div class="flex-grow">
                    <label for="newsletter-email-{{ $loop->index ?? 0 }}" class="sr-only">{{ __('newsletter.email_placeholder') }}</label>
                    <input 
                        type="email" 
                        id="newsletter-email-{{ $loop->index ?? 0 }}" 
                        name="email" 
                        required 
                        placeholder="{{ __('newsletter.email_placeholder') }}" 
                        class="w-full px-5 py-3.5 bg-surface-container-lowest border border-surface-container-high rounded-xl text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition text-sm sm:text-base" />
                </div>

                <button 
                    type="submit" 
                    :disabled="loading"
                    class="w-full sm:w-auto px-8 py-3.5 bg-primary hover:bg-primary-container text-on-primary font-label-md font-semibold rounded-xl transition duration-200 shadow-md hover:shadow-lg flex items-center justify-center space-x-2 disabled:opacity-50 active:scale-[0.98] cursor-pointer shrink-0">
                    <span x-show="!loading">{{ $buttonText }}</span>
                    <span x-show="loading" x-cloak class="flex items-center space-x-2">
                        <svg class="animate-spin h-5 w-5 text-on-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>...</span>
                    </span>
                </button>
            </form>

            @if(!empty($disclaimer))
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-4 break-words">
                    {{ $disclaimer }}
                </p>
            @endif
        </div>
    </div>
</section>
