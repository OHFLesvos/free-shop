<div wire:ignore class="mb-3">
    <label class="form-label" for="tagify">
        Tags
    </label>
    <input type="text" id="tagify" class="form-control p-0" value='@json(collect($tags)->map(fn($tag) => ['value'=>$tag]))'>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var input = document.getElementById('tagify')
            var tagify = new Tagify(input, {
                whitelist: [
                    @foreach ($suggestions as $tag)
                        '{{ $tag }}'@if (!$loop->last), @endif
                    @endforeach
                ]
            })
            input.addEventListener('change', onChange)

            function onChange(e) {
                @this.call('changeTags', e.target.value)
            }
        })
    </script>
@endpush
