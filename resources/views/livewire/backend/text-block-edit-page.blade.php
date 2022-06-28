@inject('localization', 'App\Services\LocalizationService')
<div class="medium-container">
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <x-card title="Edit Text Block '{{ $textBlock->name }}'">
            <x-slot name="header">
                <div class="d-flex justify-content-end align-items-center">
                    <span class="me-2">Language:</span>
                    <select
                        class="form-select w-auto"
                        wire:model.lazy="locale">
                        @foreach($localization->getLanguageNames() as $key => $value)
                            <option value="{{ $key }}">
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </x-slot>
            <label for="contentInput" class="form-label">
                {{ config('text-blocks.' . $textBlock->name . '.purpose') }}
            </label>
            <div>
                @if($this->supportsMarkdown)
                    <ul class="nav nav-tabs mb-2">
                        <li class="nav-item">
                            <a
                                class="nav-link @unless($textPreview) active @endunless"
                                @unless($textPreview) aria-current="page" @endunless
                                href="#editor"
                                wire:click="$set('textPreview', false)">
                                Editor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link @if($textPreview) active @endif"
                                @if($textPreview) aria-current="page" @endif
                                href="#preview"
                                wire:click="$set('textPreview', true)">
                                Preview
                            </a>
                        </li>
                    </ul>
                @endif
                @unless($textPreview)
                    <div class="input-group">
                        @if($this->defaultLocale != $locale)
                            <span class="input-group-text d-none d-md-flex">{{ $localization->getLanguageName($this->defaultLocale) }}:</span>
                            <textarea
                                class="form-control font-monospace d-none d-md-flex"
                                readonly
                                rows="5"
                            >{{ $content[$this->defaultLocale] ?? '' }}</textarea>
                            <span class="input-group-text">{{ $localization->getLanguageName($locale) }}:</span>
                        @endif
                        <textarea
                            id="contentInput"
                            wire:model.lazy="content.{{ $locale }}"
                            @if($localization->isRtl($locale)) dir="rtl" @endif
                            @if($this->defaultLocale != $locale) placeholder="{{ $content[$this->defaultLocale] ?? '' }}" @endif
                            @if($this->defaultLocale == $locale && config('text-blocks.' . $textBlock->name . '.required')) required @endif
                            rows="13"
                            class="form-control font-monospace @error('content.' . $locale)  is-invalid @enderror"
                            @if($localization->isRtl($locale)) dir="rtl" @endif
                            @if(filled(config('text-blocks.' . $textBlock->name . '.help'))) aria-describedby="contentHelp" @endif
                        ></textarea>
                        @error('content.' . $locale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    @if($this->defaultLocale != $locale && $errors->has('content.' . $this->defaultLocale)) <div><small class="text-danger">{{ implode(' ', $errors->get('content.' . $this->defaultLocale)) }}</small></div> @endif
                    @if(filled(config('text-blocks.' . $textBlock->name . '.help')))
                        <small id="contentHelp" class="form-text">
                            {!! config('text-blocks.' . $textBlock->name . '.help') !!}
                        </small>
                    @endif
                    @if($this->supportsMarkdown)
                        <small class="form-text">
                            You can use <a href="https://commonmark.org/help/" target="_blank">Markdown syntax</a> to format the text.
                        </small>
                    @endif
                @else
                    @if(isset($content[$locale]) && filled($content[$locale]))
                        {!! Str::of($content[$locale])->markdown() !!}
                    @else
                        <x-alert type="info mb-0">
                            No preview available.
                        </x-alert>
                    @endif
                @endunless
            </div>

            <x-slot name="footer">
                <div class="d-flex justify-content-between">
                    @can('viewAny', App\Models\TextBlock::class)
                        <a
                            href="{{ route('backend.configuration.text-blocks') }}"
                            class="btn btn-link">Cancel</a>
                    @endcan
                    <button
                        type="submit"
                        class="btn btn-primary"
                        wire:target="submit"
                        wire:loading.attr="disabled">
                        <x-spinner wire:loading wire:target="submit"/>
                        Save
                    </button>
                </div>
            </x-slot>
        </x-card>
    </form>
</div>
