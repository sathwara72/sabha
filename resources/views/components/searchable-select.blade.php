@props([
    'wireModel',
    'options' => [],
    'placeholder' => 'Search...',
    'value' => '',
    'allowCustom' => true,
    'wireKey' => null,
    'leadingIcon' => null,
    // Optional label => id map. When given, selecting an option commits the
    // mapped id to $wireModel instead of the label text itself — used for
    // pickers (e.g. "member") where the display label isn't the value the
    // backend actually needs.
    'valueMap' => null,
])

{{--
    When `options` depends on another field (e.g. Area options depend on the
    selected City), pass a `wireKey` that changes whenever `options` should
    change (e.g. the current city name). Livewire/Alpine preserve DOM state
    across a morph by default, so without a changing key this component
    would keep its stale initial option list forever instead of picking up
    the new ones.
--}}
<div
    @if ($wireKey) wire:key="{{ $wireKey }}" @endif
    x-data="{
        open: false,
        query: {{ Illuminate\Support\Js::from((string) $value) }},
        options: {{ Illuminate\Support\Js::from(array_values($options)) }},
        valueMap: {{ Illuminate\Support\Js::from($valueMap ?? []) }},
        get filtered() {
            const q = (this.query || '').toLowerCase().trim();
            if (!q) return this.options;
            return this.options.filter((o) => o.toLowerCase().includes(q));
        },
        select(opt) {
            this.query = opt;
            this.open = false;
            $wire.set('{{ $wireModel }}', opt in this.valueMap ? this.valueMap[opt] : opt);
        },
        commit() {
            @if (! $allowCustom)
                if (! this.options.includes(this.query)) {
                    this.query = '';
                }
            @endif
            $wire.set('{{ $wireModel }}', this.query in this.valueMap ? this.valueMap[this.query] : this.query);
        },
    }"
    class="relative"
>
    @php
        // $attributes->merge() concatenates rather than overrides, so a
        // caller-supplied class (e.g. py-1.5) can silently lose a CSS
        // cascade fight against this component's own default (e.g. py-2).
        // Same fix as icon.blade.php: the caller's class fully replaces
        // ours instead of merging with it.
        $inputClass = $attributes->get('class', 'w-full rounded-lg border border-border bg-white px-3 py-2 text-xs text-foreground outline-none transition-colors focus:border-primary');
        $inputClass .= ' pr-8' . ($leadingIcon ? ' pl-8' : '');
    @endphp
    <div class="relative">
        @if ($leadingIcon)
            <x-icon :name="$leadingIcon" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground pointer-events-none" />
        @endif
        <input
            type="text"
            x-model="query"
            x-on:focus="open = true"
            x-on:input="open = true"
            x-on:blur="setTimeout(() => { open = false; commit(); }, 150)"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            {{ $attributes->except('class') }}
            class="{{ $inputClass }}"
        />
        <x-icon name="search" class="absolute right-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-muted-foreground pointer-events-none" />
    </div>

    <div
        x-show="open && filtered.length > 0"
        x-cloak
        x-transition
        class="absolute z-30 mt-1 w-full max-h-56 overflow-y-auto rounded-xl border border-border bg-white shadow-lg py-1"
    >
        <template x-for="opt in filtered" :key="opt">
            <button
                type="button"
                x-on:mousedown.prevent="select(opt)"
                class="w-full text-left px-3 py-2 text-xs text-foreground hover:bg-primary-soft hover:text-primary transition-colors cursor-pointer"
                x-text="opt"
            ></button>
        </template>
    </div>

    <div
        x-show="open && filtered.length === 0"
        x-cloak
        class="absolute z-30 mt-1 w-full rounded-xl border border-border bg-white shadow-lg py-2 px-3 text-[12px] text-muted-foreground"
    >
        @if ($allowCustom)
            No matches — your typed value will be used as-is.
        @else
            No matches found.
        @endif
    </div>
</div>
