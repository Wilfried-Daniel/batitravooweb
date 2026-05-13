@if (! empty($helpFaqsList) && count($helpFaqsList))
    <div class="app-faq app-mt">
        @foreach ($helpFaqsList as $item)
            <details class="app-faq__item app-card">
                <summary class="app-faq__q">{{ $item['question'] ?? '—' }}</summary>
                <p class="app-faq__a app-muted app-mb-0">{{ $item['answer'] ?? '' }}</p>
            </details>
        @endforeach
    </div>
@else
    <div class="app-card app-mt app-muted">FAQ indisponible pour le moment.</div>
@endif
