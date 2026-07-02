{{-- resources/views/dashboard/partials/menu-item.blade.php --}}
@php
    $hasChildren = !empty($item['children']);
    $id = 'itm-'.md5(($item['title'] ?? '') . ($item['route'] ?? '') . ($item['url'] ?? ''));
@endphp

<div class="mb-1" x-data="{ open: false }" x-init="open = {{ $hasChildren ? 'false' : 'true' }}">
    @if ($hasChildren)
        <button type="button"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50 focus:bg-gray-50 ring-focus"
                @click="open = !open" :aria-expanded="open.toString()"
                data-title="{{ $item['data_title'] }}" data-desc="{{ $item['data_desc'] }}" data-route="{{ $item['data_route'] }}">
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-white"
                  style="background: linear-gradient(135deg, {{ $groupColor }} 0%, {{ $groupDark }} 100%);">
                <i class="fa {{ $item['icon'] ?? 'fa-layer-group' }}"></i>
            </span>
            <span class="flex-1">
                <span class="font-medium text-gray-900" x-text="'{{ $item['title'] }}'"></span>
                @if(!empty($item['desc']))
                    <span class="ml-2 text-xs text-gray-500">{{ $item['desc'] }}</span>
                @endif
            </span>
            <span class="text-gray-400 chev"><i class="fa fa-chevron-down"></i></span>
        </button>

        <div x-show="open" x-collapse class="ml-6 border-l border-gray-100 pl-3 mt-1">
            @foreach ($item['children'] as $child)
                @include('dashboard.partials.menu-item', ['item' => $child, 'groupColor' => $groupColor, 'groupDark' => $groupDark])
            @endforeach
        </div>
    @else
        <a href="{{ $item['url'] }}"
           class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50 focus:bg-gray-50 ring-focus"
           title="{{ $item['title'] }} - {{ $item['desc'] }}"
           data-title="{{ $item['data_title'] }}" data-desc="{{ $item['data_desc'] }}" data-route="{{ $item['data_route'] }}">
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-white"
                  style="background: linear-gradient(135deg, {{ $groupColor }} 0%, {{ $groupDark }} 100%);">
                <i class="fa {{ $item['icon'] ?? 'fa-link' }}"></i>
            </span>
            <span class="flex-1">
                <span class="font-medium text-gray-900">{{ $item['title'] }}</span>
                @if(!empty($item['route_display']))
                    <span class="ml-2 text-xs text-gray-500">{{ $item['route_display'] }}</span>
                @endif
            </span>
            @if(!empty($item['external']))
                <span class="text-xs text-indigo-600 font-medium">EXT</span>
            @endif
        </a>
    @endif
</div>
