<div class="medium-container">
    <form wire:submit.prevent="submit" class="mb-4" autocomplete="off">
        <x-card title="Text Block {{ $textBlock->name }}">
            <x-slot name="header">
                <div class="d-flex justify-content-end align-items-center">
                    <span class="me-2">Language:</span>
                    <select
                        class="form-select w-auto"
                        wire:model.lazy="locale">
                        @foreach(config('app.supported_languages') as $lang_key => $lang_name)
                            <option
                                value="{{ $lang_key }}">
                                {{ $lang_name }} ({{ strtoupper($lang_key) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </x-slot>
            <div>
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
                @unless($textPreview)
                    <div class="input-group">
                        <div class="input-group-text">
                            {{ strtoupper($locale) }}
                        </div>
                        <textarea
                            id="contentInput"
                            wire:model.lazy="content.{{ $locale }}"
                            @if($this->defaultLocale != $locale) placeholder="{{ $content[$this->defaultLocale] ?? '' }}" @endif
                            rows="10"
                            class="form-control font-monospace @error('content.' . $locale)  is-invalid @enderror"
                            aria-describedby="contentHelp"
                        ></textarea>
                        @error('content.' . $locale) <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <small id="contentHelp" class="form-text">
                        You can use <a href="https://commonmark.org/help/" target="_blank">Markdown syntax</a> to format the text.
                    </small>
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
                <div class="d-flex justify-content-end">
                    @can('viewAny', App\Models\TextBlock::class)
                        <a
                            href="{{ route('backend.text-blocks') }}"
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
