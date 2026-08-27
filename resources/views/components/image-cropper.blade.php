<div x-data="imageCropper()" x-show="isOpen" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center p-3 sm:p-5 font-outfit select-none">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" x-on:click="close"></div>

    <div class="relative z-50 w-full max-w-lg bg-white rounded-2xl p-4 sm:p-5 shadow-2xl border border-slate-200/80 flex flex-col gap-3 max-h-[95vh] overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <x-icon name="crop" class="text-primary h-4 w-4" />
                    <span x-text="title"></span>
                </h3>
                <p class="text-[12px] font-medium text-slate-500 mt-0.5">Drag &amp; resize the selection box over the image portion to crop</p>
            </div>
            <button x-on:click="close" class="rounded-full p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-colors cursor-pointer">
                <x-icon name="x" class="h-4 w-4" />
            </button>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-1.5 bg-slate-50 p-2 rounded-xl border border-slate-100">
            <div class="flex items-center gap-1.5 flex-wrap">
                <button type="button" x-on:click="selectFullImage" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-slate-200/80 text-[12px] font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                    <x-icon name="maximize-2" class="h-3 w-3 text-primary" /> Select Full Image
                </button>
                <button type="button" x-on:click="lockDefaultRatio" class="px-2.5 py-1 rounded-lg text-[12px] font-bold transition-all cursor-pointer" :class="aspectMode === 'default' ? 'bg-primary text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-100'">
                    Lock Ratio
                </button>
            </div>

            <button type="button" x-on:click="rotate" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white border border-slate-200/80 text-[12px] font-bold text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer ml-auto">
                <x-icon name="rotate-cw" class="h-3 w-3 text-primary" /> Rotate 90°
            </button>
        </div>

        <div x-ref="cropperWorkspace" class="relative w-full bg-slate-950 rounded-xl overflow-hidden shadow-inner flex items-center justify-center min-h-[190px] sm:min-h-[220px] h-[240px] select-none">
            <div class="relative w-full h-full flex items-center justify-center overflow-hidden p-3">
                <img
                    x-ref="cropperImage"
                    :src="imageSrc"
                    alt="Crop workspace image"
                    x-on:load="onImageLoad"
                    draggable="false"
                    x-bind:style="`transform: rotate(${rotation}deg) scale(${zoom}); transition: transform 0.15s ease-out; max-height: 100%; max-width: 100%; object-fit: contain;`"
                    class="pointer-events-none select-none max-h-full max-w-full"
                />
            </div>

            <div x-show="imgBox.width > 0 && imgBox.height > 0" class="absolute pointer-events-none" x-bind:style="`left: ${imgBox.left}px; top: ${imgBox.top}px; width: ${imgBox.width}px; height: ${imgBox.height}px;`">
                <div
                    class="absolute inset-0 pointer-events-none"
                    x-bind:style="`clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%, 0% 0%, ${cropBox.x}% ${cropBox.y}%, ${cropBox.x}% ${cropBox.y + cropBox.h}%, ${cropBox.x + cropBox.w}% ${cropBox.y + cropBox.h}%, ${cropBox.x + cropBox.w}% ${cropBox.y}%, ${cropBox.x}% ${cropBox.y}%); background-color: rgba(15, 23, 42, 0.75);`"
                ></div>

                <div
                    x-on:mousedown="startDrag('move', $event)"
                    x-on:touchstart="startDrag('move', $event)"
                    class="absolute border-2 border-white rounded-lg shadow-2xl cursor-grab active:cursor-grabbing group pointer-events-auto"
                    x-bind:style="`left: ${cropBox.x}%; top: ${cropBox.y}%; width: ${cropBox.w}%; height: ${cropBox.h}%;`"
                >
                    <div class="w-full h-full grid grid-cols-3 grid-rows-3 pointer-events-none opacity-40">
                        <div class="border-r border-b border-white/60"></div>
                        <div class="border-r border-b border-white/60"></div>
                        <div class="border-b border-white/60"></div>
                        <div class="border-r border-b border-white/60"></div>
                        <div class="border-r border-b border-white/60"></div>
                        <div class="border-b border-white/60"></div>
                        <div class="border-r border-white/60"></div>
                        <div class="border-r border-white/60"></div>
                        <div></div>
                    </div>

                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="bg-black/60 backdrop-blur-sm px-3 py-1 rounded-full text-[12px] font-bold text-white flex items-center gap-1.5 border border-white/20 shadow-md">
                            <x-icon name="move" class="h-3 w-3 text-primary" /> Drag box to position
                        </div>
                    </div>

                    @foreach ([
                        ['id' => 'nw', 'pos' => '-top-2 -left-2 cursor-nwse-resize'],
                        ['id' => 'n', 'pos' => '-top-2 left-1/2 -translate-x-1/2 cursor-ns-resize'],
                        ['id' => 'ne', 'pos' => '-top-2 -right-2 cursor-nesw-resize'],
                        ['id' => 'e', 'pos' => 'top-1/2 -right-2 -translate-y-1/2 cursor-ew-resize'],
                        ['id' => 'se', 'pos' => '-bottom-2 -right-2 cursor-nwse-resize'],
                        ['id' => 's', 'pos' => '-bottom-2 left-1/2 -translate-x-1/2 cursor-ns-resize'],
                        ['id' => 'sw', 'pos' => '-bottom-2 -left-2 cursor-nesw-resize'],
                        ['id' => 'w', 'pos' => 'top-1/2 -left-2 -translate-y-1/2 cursor-ew-resize'],
                    ] as $handle)
                        <div
                            x-on:mousedown="startDrag('{{ $handle['id'] }}', $event)"
                            x-on:touchstart="startDrag('{{ $handle['id'] }}', $event)"
                            class="absolute w-3.5 h-3.5 bg-white border-2 border-primary rounded-sm shadow-md hover:scale-125 transition-transform {{ $handle['pos'] }}"
                        ></div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 bg-slate-50 p-2 px-3 rounded-xl border border-slate-100">
            <span class="text-[12px] font-bold text-slate-700 flex items-center gap-1 shrink-0">
                <x-icon name="zoom-in" class="h-[13px] w-[13px] text-primary" /> Zoom:
            </span>
            <input type="range" min="1" max="3" step="0.05" x-model.number="zoom" x-on:input="onZoomChange" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary" />
            <span class="text-[12px] font-extrabold text-slate-900 w-9 text-right shrink-0" x-text="Math.round(zoom * 100) + '%'"></span>
            <button type="button" x-on:click="resetAll" class="text-[12px] font-bold text-slate-500 hover:text-primary transition-colors shrink-0">Reset All</button>
        </div>

        <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
            <button type="button" x-on:click="close" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">Cancel</button>
            <button type="button" x-on:click="applyCrop" :disabled="uploading" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-5 py-1.5 text-xs font-bold text-white shadow-md hover:opacity-90 active:scale-95 transition-all cursor-pointer disabled:opacity-60">
                <x-icon name="check" class="h-3.5 w-3.5" />
                <span x-text="uploading ? 'Saving...' : 'Crop & Save Portion'"></span>
            </button>
        </div>
    </div>
</div>
