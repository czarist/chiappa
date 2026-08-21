<section class="section section-dark stats-section"><div class="shell grid grid-4">@foreach($content['items'] ?? [] as $item)
    @php
        $rawValue = (string) ($item['value'] ?? '0');
        $prefix = str_starts_with($rawValue, '+') ? '+' : '';
        $numericValue = ltrim($rawValue, '+');
        $decimals = str_contains($numericValue, '.') ? strlen(strstr($numericValue, '.')) - 1 : 0;
    @endphp
    <article class="stat-card" data-aos="zoom-in" data-aos-delay="{{ $loop->index * 100 }}"><strong class="counter-number" data-target="{{ $numericValue }}" data-prefix="{{ $prefix }}" data-decimals="{{ $decimals }}"><span class="counter-value">0</span>@if($numericValue === '4.9') <i class="fas fa-star text-xl align-middle ml-1" aria-hidden="true"></i>@endif</strong><span class="counter-label">{{ $item['label'] ?? '' }}</span></article>
@endforeach</div></section>
