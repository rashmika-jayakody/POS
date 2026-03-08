@extends('layouts.admin')

@section('title', __('Customize Translations'))

@push('styles')
<style>
    .trans-tabs { display: flex; flex-wrap: wrap; gap: 4px; border-bottom: 2px solid var(--gray-200); margin-bottom: 0; padding: 0 4px 0 0; }
    .trans-tab { padding: 12px 18px; font-size: 0.9rem; font-weight: 600; color: var(--gray-500); background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; cursor: pointer; transition: color 0.2s, border-color 0.2s; border-radius: var(--radius-sm) var(--radius-sm) 0 0; }
    .trans-tab:hover { color: var(--navy-dark); }
    .trans-tab.active { color: var(--light-blue); border-bottom-color: var(--light-blue); }
    .trans-panels { padding-top: 20px; }
    .trans-panel { display: none; }
    .trans-panel.active { display: block; }
</style>
@endpush

@section('content')
    <div class="page-header animate-in">
        <div class="page-title">
            <i class="fas fa-language"></i>
            {{ __('Customize Translations') }}
        </div>
        <div class="page-subtitle">
            {{ __('Override translation strings per locale. Custom values are stored in the database and take precedence over language files.') }}
        </div>
    </div>

    @if(session('success'))
        <div
            style="background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2);">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="section animate-in">
        <form action="{{ route('translations.store') }}" method="POST" id="translations-form">
            @csrf
            <input type="hidden" name="locale" value="{{ $currentLocale }}">

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 16px; margin-bottom: 24px;">
                <label for="locale-select" style="font-weight: 600; color: var(--gray-700);">{{ __('Language') }}:</label>
                <select id="locale-select" name="locale_select" style="padding: 8px 14px; border-radius: 8px; border: 1px solid var(--gray-300); font-size: 0.95rem;">
                    @foreach($locales as $loc)
                        <option value="{{ $loc }}" {{ $currentLocale === $loc ? 'selected' : '' }}>
                            {{ $loc === 'en' ? 'English' : 'සිංහල' }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('Save') }}
                </button>
            </div>

            @if(count($keysBySection) > 0)
                <div class="trans-tabs" role="tablist">
                    @foreach($keysBySection as $sectionKey => $section)
                        <button type="button"
                                class="trans-tab {{ $loop->first ? 'active' : '' }}"
                                role="tab"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                aria-controls="panel-{{ $sectionKey }}"
                                id="tab-{{ $sectionKey }}"
                                data-tab="{{ $sectionKey }}">
                            {{ $section['label'] }}
                            <span style="font-size: 0.75rem; font-weight: 500; color: var(--gray-400); margin-left: 6px;">({{ count($section['keys']) }})</span>
                        </button>
                    @endforeach
                </div>

                <div class="trans-panels">
                    @foreach($keysBySection as $sectionKey => $section)
                        <div class="trans-panel {{ $loop->first ? 'active' : '' }}"
                             id="panel-{{ $sectionKey }}"
                             role="tabpanel"
                             aria-labelledby="tab-{{ $sectionKey }}"
                             data-panel="{{ $sectionKey }}">
                            <div class="table-wrapper">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 28%;">{{ __('Key') }}</th>
                                            <th>{{ __('Translation') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($section['keys'] as $key)
                                            <tr>
                                                <td style="vertical-align: top; font-size: 0.85rem; color: var(--gray-600); word-break: break-all;">
                                                    <code>{{ $key }}</code>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                           name="translations[{{ $key }}]"
                                                           value="{{ old('translations.'.$key, $translations[$key] ?? '') }}"
                                                           placeholder="{{ __('Enter translation') }}"
                                                           style="width: 100%; padding: 10px 14px; border: 1px solid var(--gray-300); border-radius: 8px; font-size: 0.95rem;">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div style="margin-top: 16px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ __('Save') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: var(--gray-500);">{{ __('No translation keys found in language files.') }}</p>
            @endif
        </form>
    </div>

    <script>
        document.getElementById('locale-select')?.addEventListener('change', function() {
            window.location.href = '{{ route("translations.index") }}?locale=' + encodeURIComponent(this.value);
        });

        (function() {
            var tabs = document.querySelectorAll('.trans-tab');
            var panels = document.querySelectorAll('.trans-panel');
            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    var id = this.getAttribute('data-tab');
                    tabs.forEach(function(t) {
                        t.classList.remove('active');
                        t.setAttribute('aria-selected', 'false');
                    });
                    panels.forEach(function(p) {
                        p.classList.toggle('active', p.getAttribute('data-panel') === id);
                    });
                    this.classList.add('active');
                    this.setAttribute('aria-selected', 'true');
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, '', '#tab-' + id);
                    }
                });
            });
            var hash = window.location.hash;
            if (hash && hash.startsWith('#tab-')) {
                var tabId = hash.slice(5);
                var tabEl = document.getElementById('tab-' + tabId);
                if (tabEl) {
                    tabEl.click();
                }
            }
        })();
    </script>
@endsection
