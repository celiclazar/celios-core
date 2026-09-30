@php
    $consentEnabled = (bool) setting('cookie_consent_enabled', '1');
@endphp

@if($consentEnabled)
    @php
        $position = setting('cookie_banner_position', 'bottom_banner');
        $expiryDays = (int) setting('cookie_consent_expiry_days', 365);
        $locale = app()->getLocale();
        $defaultPrivacyUrl = url('/' . $locale . ($locale === 'sr' ? '/politika-privatnosti' : ($locale === 'it' ? '/informativa-privacy' : '/privacy-policy')));
        $privacyUrl = setting('cookie_privacy_url') ?: $defaultPrivacyUrl;
        
        $bannerTitle = setting('cookie_banner_title') ?: __('cookies.banner_title_default');
        $bannerDescription = setting('cookie_banner_description') ?: __('cookies.banner_description_default');
        
        $analyticsEnabled = (bool) setting('cookie_cat_analytics_enabled', '1');
        $marketingEnabled = (bool) setting('cookie_cat_marketing_enabled', '1');
        $functionalEnabled = (bool) setting('cookie_cat_functional_enabled', '1');
        
        $analyticsScripts = setting('cookie_analytics_scripts', '');
        $marketingScripts = setting('cookie_marketing_scripts', '');
        $functionalScripts = setting('cookie_functional_scripts', '');
    @endphp

    <div x-data="celiosCookieConsent({
            expiryDays: {{ $expiryDays }},
            analyticsEnabled: {{ $analyticsEnabled ? 'true' : 'false' }},
            marketingEnabled: {{ $marketingEnabled ? 'true' : 'false' }},
            functionalEnabled: {{ $functionalEnabled ? 'true' : 'false' }},
            analyticsScripts: @js($analyticsScripts),
            marketingScripts: @js($marketingScripts),
            functionalScripts: @js($functionalScripts)
        })"
        x-cloak
        class="celios-cookie-manager-root">

        {{-- COOKIE CONSENT BANNER --}}
        <template x-if="showBanner">
            <div @class([
                'fixed z-50 transition-all duration-300',
                'bottom-0 inset-x-0 p-4 sm:p-6' => $position === 'bottom_banner',
                'bottom-4 right-4 sm:bottom-6 sm:right-6 max-w-lg w-full p-2' => $position === 'bottom_right',
                'bottom-4 left-4 sm:bottom-6 sm:left-6 max-w-lg w-full p-2' => $position === 'bottom_left',
                'inset-0 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm' => $position === 'center_modal',
            ])>
                <div class="w-full max-w-5xl mx-auto bg-surface-container-high/95 backdrop-blur-md border border-surface-container-highest/80 text-on-surface rounded-2xl shadow-2xl p-5 sm:p-6 ring-1 ring-black/5 dark:ring-white/10">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        
                        <!-- Left text / info -->
                        <div class="space-y-2 flex-1">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-lg">cookie</span>
                                </span>
                                <h3 class="font-display-md text-base sm:text-lg font-bold text-on-surface">
                                    {{ $bannerTitle }}
                                </h3>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                {{ $bannerDescription }}
                                @if(!empty($privacyUrl))
                                    <a href="{{ $privacyUrl }}" class="text-primary font-medium hover:underline inline-flex items-center gap-0.5 ml-1">
                                        <span>{{ __('cookies.ui_privacy_link') }}</span>
                                        <span class="material-symbols-outlined text-xs">open_in_new</span>
                                    </a>
                                @endif
                            </p>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3 shrink-0 pt-2 lg:pt-0">
                            <!-- Customize -->
                            <button type="button"
                                @click="openModal()"
                                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-surface-container hover:bg-surface-container-highest text-on-surface border border-surface-container-highest transition-colors active:scale-95 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base">tune</span>
                                <span>{{ __('cookies.ui_customize') }}</span>
                            </button>

                            <!-- Reject Non-Essential -->
                            <button type="button"
                                @click="rejectAll()"
                                class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-surface-container hover:bg-surface-container-highest text-on-surface border border-surface-container-highest transition-colors active:scale-95">
                                {{ __('cookies.ui_reject_non_essential') }}
                            </button>

                            <!-- Accept All -->
                            <button type="button"
                                @click="acceptAll()"
                                class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-primary hover:opacity-90 text-on-primary shadow-sm transition-all active:scale-95">
                                {{ __('cookies.ui_accept_all') }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </template>

        {{-- GRANULAR PREFERENCES MODAL --}}
        <div x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
            @keydown.escape.window="closeModal()">

            <div @click.away="closeModal()"
                x-show="showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-2xl bg-surface-container-high border border-surface-container-highest text-on-surface rounded-2xl shadow-2xl overflow-hidden my-8 max-h-[90vh] flex flex-col">

                <!-- Modal Header -->
                <div class="px-6 py-5 border-b border-surface-container flex items-center justify-between shrink-0 bg-surface-container-low/50">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">shield</span>
                        </div>
                        <div>
                            <h2 class="font-display-md text-lg sm:text-xl font-bold text-on-surface">
                                {{ __('cookies.ui_preferences_title') }}
                            </h2>
                            <p class="font-body-xs text-xs text-on-surface-variant">
                                {{ __('cookies.ui_preferences_subtitle') }}
                            </p>
                        </div>
                    </div>
                    <button type="button"
                        @click="closeModal()"
                        class="p-2 rounded-xl text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>

                <!-- Modal Body Categories -->
                <div class="p-6 space-y-4 overflow-y-auto flex-1 font-body-sm text-body-sm">
                    
                    <!-- Category 1: Necessary (Locked) -->
                    <div class="p-4 rounded-xl bg-surface-container border border-surface-container-highest/60 flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-semibold text-on-surface">{{ __('cookies.cat_necessary') }}</span>
                                <span class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">lock</span>
                                    <span>{{ __('cookies.cat_necessary_always_active') }}</span>
                                </span>
                            </div>
                            <p class="text-xs text-on-surface-variant leading-relaxed">
                                {{ __('cookies.cat_necessary_desc') }}
                            </p>
                        </div>
                        <div class="shrink-0 pt-0.5">
                            <div class="relative inline-flex h-7 w-12 shrink-0 rounded-full border-2 border-transparent bg-primary/60 opacity-80 cursor-not-allowed">
                                <span class="inline-flex items-center justify-center h-6 w-6 transform translate-x-5 rounded-full bg-white text-primary shadow">
                                    <span class="material-symbols-outlined text-sm font-bold">check</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Category 2: Analytics -->
                    @if($analyticsEnabled)
                        <div class="p-4 rounded-xl border transition-all duration-200 cursor-pointer select-none flex items-start justify-between gap-4"
                            :class="preferences.analytics ? 'bg-primary/5 border-primary/50 shadow-sm ring-1 ring-primary/20' : 'bg-surface-container border-surface-container-highest/60 hover:border-surface-container-highest'"
                            @click="preferences.analytics = !preferences.analytics">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-on-surface">{{ __('cookies.cat_analytics') }}</span>
                                    <span x-show="preferences.analytics" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">check_circle</span>
                                        <span>{{ __('cookies.status_enabled') }}</span>
                                    </span>
                                    <span x-show="!preferences.analytics" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant border border-surface-container-highest flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">block</span>
                                        <span>{{ __('cookies.status_disabled') }}</span>
                                    </span>
                                </div>
                                <p class="text-xs text-on-surface-variant leading-relaxed">
                                    {{ __('cookies.cat_analytics_desc') }}
                                </p>
                            </div>
                            <div class="shrink-0 pt-0.5">
                                <button type="button"
                                    role="switch"
                                    :aria-checked="preferences.analytics"
                                    @click.stop="preferences.analytics = !preferences.analytics"
                                    class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="preferences.analytics ? 'bg-primary' : 'bg-surface-container-highest'">
                                    <span class="sr-only">{{ __('cookies.cat_analytics') }}</span>
                                    <span aria-hidden="true"
                                        class="pointer-events-none inline-flex items-center justify-center h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="preferences.analytics ? 'translate-x-5 text-primary' : 'translate-x-0 text-on-surface-variant/60'">
                                        <span x-show="preferences.analytics" class="material-symbols-outlined text-sm font-bold">check</span>
                                        <span x-show="!preferences.analytics" class="material-symbols-outlined text-sm">close</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Category 3: Marketing -->
                    @if($marketingEnabled)
                        <div class="p-4 rounded-xl border transition-all duration-200 cursor-pointer select-none flex items-start justify-between gap-4"
                            :class="preferences.marketing ? 'bg-primary/5 border-primary/50 shadow-sm ring-1 ring-primary/20' : 'bg-surface-container border-surface-container-highest/60 hover:border-surface-container-highest'"
                            @click="preferences.marketing = !preferences.marketing">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-on-surface">{{ __('cookies.cat_marketing') }}</span>
                                    <span x-show="preferences.marketing" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">check_circle</span>
                                        <span>{{ __('cookies.status_enabled') }}</span>
                                    </span>
                                    <span x-show="!preferences.marketing" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant border border-surface-container-highest flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">block</span>
                                        <span>{{ __('cookies.status_disabled') }}</span>
                                    </span>
                                </div>
                                <p class="text-xs text-on-surface-variant leading-relaxed">
                                    {{ __('cookies.cat_marketing_desc') }}
                                </p>
                            </div>
                            <div class="shrink-0 pt-0.5">
                                <button type="button"
                                    role="switch"
                                    :aria-checked="preferences.marketing"
                                    @click.stop="preferences.marketing = !preferences.marketing"
                                    class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="preferences.marketing ? 'bg-primary' : 'bg-surface-container-highest'">
                                    <span class="sr-only">{{ __('cookies.cat_marketing') }}</span>
                                    <span aria-hidden="true"
                                        class="pointer-events-none inline-flex items-center justify-center h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="preferences.marketing ? 'translate-x-5 text-primary' : 'translate-x-0 text-on-surface-variant/60'">
                                        <span x-show="preferences.marketing" class="material-symbols-outlined text-sm font-bold">check</span>
                                        <span x-show="!preferences.marketing" class="material-symbols-outlined text-sm">close</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Category 4: Functional -->
                    @if($functionalEnabled)
                        <div class="p-4 rounded-xl border transition-all duration-200 cursor-pointer select-none flex items-start justify-between gap-4"
                            :class="preferences.functional ? 'bg-primary/5 border-primary/50 shadow-sm ring-1 ring-primary/20' : 'bg-surface-container border-surface-container-highest/60 hover:border-surface-container-highest'"
                            @click="preferences.functional = !preferences.functional">
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-on-surface">{{ __('cookies.cat_functional') }}</span>
                                    <span x-show="preferences.functional" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">check_circle</span>
                                        <span>{{ __('cookies.status_enabled') }}</span>
                                    </span>
                                    <span x-show="!preferences.functional" class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant border border-surface-container-highest flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">block</span>
                                        <span>{{ __('cookies.status_disabled') }}</span>
                                    </span>
                                </div>
                                <p class="text-xs text-on-surface-variant leading-relaxed">
                                    {{ __('cookies.cat_functional_desc') }}
                                </p>
                            </div>
                            <div class="shrink-0 pt-0.5">
                                <button type="button"
                                    role="switch"
                                    :aria-checked="preferences.functional"
                                    @click.stop="preferences.functional = !preferences.functional"
                                    class="relative inline-flex h-7 w-12 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="preferences.functional ? 'bg-primary' : 'bg-surface-container-highest'">
                                    <span class="sr-only">{{ __('cookies.cat_functional') }}</span>
                                    <span aria-hidden="true"
                                        class="pointer-events-none inline-flex items-center justify-center h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="preferences.functional ? 'translate-x-5 text-primary' : 'translate-x-0 text-on-surface-variant/60'">
                                        <span x-show="preferences.functional" class="material-symbols-outlined text-sm font-bold">check</span>
                                        <span x-show="!preferences.functional" class="material-symbols-outlined text-sm">close</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Modal Footer Actions -->
                <div class="px-6 py-4 border-t border-surface-container flex flex-wrap items-center justify-between gap-3 shrink-0 bg-surface-container-low/50">
                    <button type="button"
                        @click="rejectAll()"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-surface-container hover:bg-surface-container-highest text-on-surface border border-surface-container-highest transition-colors active:scale-95">
                        {{ __('cookies.ui_reject_non_essential') }}
                    </button>

                    <div class="flex items-center gap-2.5">
                        <button type="button"
                            @click="saveCustom()"
                            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold bg-surface-container-highest hover:bg-surface-container-highest/80 text-on-surface transition-colors active:scale-95">
                            {{ __('cookies.ui_save_preferences') }}
                        </button>
                        <button type="button"
                            @click="acceptAll()"
                            class="px-5 py-2 rounded-xl text-xs sm:text-sm font-bold bg-primary hover:opacity-90 text-on-primary shadow-sm transition-all active:scale-95">
                            {{ __('cookies.ui_accept_all') }}
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- FLOATING TOAST NOTIFICATION WHEN SAVED --}}
        <div x-show="showToast"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2.5 text-sm font-medium">
            <span class="material-symbols-outlined text-lg">check_circle</span>
            <span>{{ __('cookies.ui_cookies_saved_alert') }}</span>
        </div>

    </div>

    <script>
        function celiosCookieConsent(config) {
            return {
                showBanner: false,
                showModal: false,
                showToast: false,
                preferences: {
                    necessary: true,
                    analytics: false,
                    marketing: false,
                    functional: false
                },
                injectedScripts: {
                    analytics: false,
                    marketing: false,
                    functional: false
                },

                init() {
                    window.CeliosConsent = this;

                    // Listen for global open event (e.g. from footer link)
                    window.addEventListener('celios:open-cookie-preferences', () => {
                        this.openModal();
                    });

                    const consent = this.getSavedConsent();
                    if (consent) {
                        this.preferences = Object.assign({ necessary: true }, consent);
                        this.showBanner = false;
                        this.executeConsentScripts();
                    } else {
                        this.showBanner = true;
                    }
                },

                getSavedConsent() {
                    try {
                        const name = 'celios_cookie_consent=';
                        const decodedCookie = decodeURIComponent(document.cookie);
                        const ca = decodedCookie.split(';');
                        for (let i = 0; i < ca.length; i++) {
                            let c = ca[i].trim();
                            if (c.indexOf(name) === 0) {
                                return JSON.parse(c.substring(name.length, c.length));
                            }
                        }
                        const local = localStorage.getItem('celios_cookie_consent');
                        if (local) {
                            return JSON.parse(local);
                        }
                    } catch (e) {
                        console.error('Error parsing cookie consent:', e);
                    }
                    return null;
                },

                saveConsent(prefs) {
                    const consentData = {
                        necessary: true,
                        analytics: !!prefs.analytics,
                        marketing: !!prefs.marketing,
                        functional: !!prefs.functional,
                        timestamp: new Date().toISOString()
                    };

                    const days = config.expiryDays || 365;
                    const date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    const expires = "expires=" + date.toUTCString();

                    document.cookie = "celios_cookie_consent=" + encodeURIComponent(JSON.stringify(consentData)) + ";" + expires + ";path=/;SameSite=Lax";
                    try {
                        localStorage.setItem('celios_cookie_consent', JSON.stringify(consentData));
                    } catch (e) {}

                    this.preferences = consentData;
                    this.showBanner = false;
                    this.showModal = false;
                    this.showToast = true;

                    setTimeout(() => {
                        this.showToast = false;
                    }, 3500);

                    this.executeConsentScripts();

                    window.dispatchEvent(new CustomEvent('celios:consent-updated', {
                        detail: consentData
                    }));
                },

                acceptAll() {
                    this.preferences.analytics = config.analyticsEnabled;
                    this.preferences.marketing = config.marketingEnabled;
                    this.preferences.functional = config.functionalEnabled;
                    this.saveConsent(this.preferences);
                },

                rejectAll() {
                    this.preferences.analytics = false;
                    this.preferences.marketing = false;
                    this.preferences.functional = false;
                    this.saveConsent(this.preferences);
                },

                saveCustom() {
                    this.saveConsent(this.preferences);
                },

                openModal() {
                    this.showModal = true;
                },

                closeModal() {
                    this.showModal = false;
                },

                executeConsentScripts() {
                    if (this.preferences.analytics && config.analyticsScripts && !this.injectedScripts.analytics) {
                        this.injectRawScripts(config.analyticsScripts);
                        this.injectedScripts.analytics = true;
                    }

                    if (this.preferences.marketing && config.marketingScripts && !this.injectedScripts.marketing) {
                        this.injectRawScripts(config.marketingScripts);
                        this.injectedScripts.marketing = true;
                    }

                    if (this.preferences.functional && config.functionalScripts && !this.injectedScripts.functional) {
                        this.injectRawScripts(config.functionalScripts);
                        this.injectedScripts.functional = true;
                    }

                    // Activate any conditional <script type="text/plain" data-cookiecategory="..."> tags in the DOM
                    this.activateEmbeddedScriptTags();
                },

                injectRawScripts(rawHtml) {
                    if (!rawHtml || !rawHtml.trim()) return;
                    const temp = document.createElement('div');
                    temp.innerHTML = rawHtml;
                    
                    Array.from(temp.childNodes).forEach(node => {
                        if (node.nodeType === 1 && node.tagName.toLowerCase() === 'script') {
                            const newScript = document.createElement('script');
                            Array.from(node.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.textContent = node.textContent;
                            document.head.appendChild(newScript);
                        } else if (node.nodeType === 1) {
                            document.body.appendChild(node.cloneNode(true));
                        }
                    });
                },

                activateEmbeddedScriptTags() {
                    document.querySelectorAll('script[type="text/plain"][data-cookiecategory]').forEach(script => {
                        const category = script.getAttribute('data-cookiecategory');
                        if (this.preferences[category]) {
                            const newScript = document.createElement('script');
                            Array.from(script.attributes).forEach(attr => {
                                if (attr.name !== 'type' && attr.name !== 'data-cookiecategory') {
                                    newScript.setAttribute(attr.name, attr.value);
                                }
                            });
                            newScript.type = 'text/javascript';
                            newScript.textContent = script.textContent;
                            script.parentNode.replaceChild(newScript, script);
                        }
                    });
                }
            };
        }
    </script>
@endif
