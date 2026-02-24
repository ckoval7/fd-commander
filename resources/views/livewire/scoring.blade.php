<div class="min-h-screen" style="background-color: var(--score-bg); color: var(--score-text);">

    {{-- EMPTY STATE --}}
    @if (! $this->event)
        <div class="flex flex-col items-center justify-center min-h-[60vh] gap-4">
            <x-mary-icon name="o-trophy" class="w-16 h-16 opacity-30" />
            <div class="text-2xl font-semibold opacity-50">No active event</div>
            <p class="text-sm opacity-40">Scores will appear here during an active Field Day event.</p>
        </div>
    @else

    {{-- ZONE 1: MASTHEAD --}}
    <div class="px-6 pt-8 pb-4" style="border-bottom: 2px solid var(--score-divider);">
        <div class="text-center font-black tracking-[0.25em] uppercase"
             style="font-size: clamp(2rem, 6vw, 4rem); color: var(--score-text);">
            {{ $this->event->eventConfiguration->callsign }}
        </div>
        <div class="text-center text-sm tracking-widest uppercase mt-2 font-medium"
             style="color: var(--score-text-muted); border-top: 1px solid var(--score-divider); border-bottom: 1px solid var(--score-divider); padding: 0.375rem 0; margin-top: 0.5rem;">
            {{ $this->event->eventConfiguration->operatingClass?->name ?? 'Unknown Class' }}
            &nbsp;·&nbsp;
            {{ $this->event->eventConfiguration->section?->name ?? 'Unknown Section' }}
            &nbsp;·&nbsp;
            {{ $this->event->eventConfiguration->transmitter_count }}
            {{ Str::plural('Transmitter', $this->event->eventConfiguration->transmitter_count) }}
            &nbsp;·&nbsp;
            {{ $this->event->start_time->format('M j, Y') }}
        </div>
        @if ($this->event->eventConfiguration->club_name)
            <div class="text-center text-xs tracking-wider mt-1" style="color: var(--score-text-muted);">
                {{ $this->event->eventConfiguration->club_name }}
            </div>
        @endif
    </div>

    {{-- ZONE 2: HEADLINE EQUATION --}}
    <div class="px-6 py-10" style="border-bottom: 2px solid var(--score-divider);">
        <div class="flex flex-wrap items-center justify-center gap-2 md:gap-4">

            <span class="text-4xl md:text-5xl font-light select-none" style="color: var(--score-text-muted);">(</span>

            <a href="#col-qso" class="text-center group no-underline">
                <div class="font-black tabular-nums transition-opacity group-hover:opacity-75"
                     style="font-size: clamp(2.5rem, 5vw, 4rem); color: var(--score-headline);">
                    {{ number_format($this->qsoBasePoints) }}
                </div>
                <div class="text-xs uppercase tracking-widest mt-1" style="color: var(--score-text-muted);">
                    QSO Base Pts
                </div>
            </a>

            <span class="text-3xl md:text-4xl font-light select-none" style="color: var(--score-text-muted);">×</span>

            <a href="#col-power" class="text-center group no-underline">
                <div class="font-black tabular-nums transition-opacity group-hover:opacity-75"
                     style="font-size: clamp(2.5rem, 5vw, 4rem); color: var(--score-headline);">
                    {{ $this->powerMultiplier }}×
                </div>
                <div class="text-xs uppercase tracking-widest mt-1" style="color: var(--score-text-muted);">
                    Power Multi.
                </div>
            </a>

            <span class="text-4xl md:text-5xl font-light select-none" style="color: var(--score-text-muted);">)</span>
            <span class="text-3xl md:text-4xl font-light select-none" style="color: var(--score-text-muted);">+</span>

            <a href="#col-bonus" class="text-center group no-underline">
                <div class="font-black tabular-nums transition-opacity group-hover:opacity-75"
                     style="font-size: clamp(2.5rem, 5vw, 4rem); color: var(--score-headline);">
                    {{ number_format($this->bonusScore) }}
                </div>
                <div class="text-xs uppercase tracking-widest mt-1" style="color: var(--score-text-muted);">
                    Bonus Pts
                </div>
            </a>

            <span class="text-3xl md:text-4xl font-light select-none" style="color: var(--score-text-muted);">=</span>

            <div class="text-center">
                <div class="font-black tabular-nums"
                     style="font-size: clamp(3.5rem, 8vw, 6rem); color: var(--score-headline-lg); line-height: 1;">
                    {{ number_format($this->finalScore) }}
                </div>
                <div class="text-xs uppercase tracking-widest mt-1 font-bold" style="color: var(--score-text-muted);">
                    Final Score
                </div>
            </div>

        </div>
    </div>

    {{-- ZONE 3: THREE COLUMNS --}}
    <div class="grid grid-cols-1 md:grid-cols-3" style="border-bottom: 1px solid var(--score-divider);">

        {{-- Column 1: QSO Points --}}
        <div id="col-qso" class="p-6 md:border-r" style="border-color: var(--score-border);">
            <div class="text-xs font-bold uppercase tracking-widest mb-4" style="color: var(--score-text-muted);">
                QSO Points
            </div>
            <div class="text-sm opacity-50" style="color: var(--score-text-muted);">Coming soon…</div>
        </div>

        {{-- Column 2: Power Multiplier --}}
        <div id="col-power" class="p-6 md:border-r" style="border-color: var(--score-border);">
            <div class="text-xs font-bold uppercase tracking-widest mb-4" style="color: var(--score-text-muted);">
                Power Multiplier
            </div>
            <div class="text-sm opacity-50" style="color: var(--score-text-muted);">Coming soon…</div>
        </div>

        {{-- Column 3: Bonus Points --}}
        <div id="col-bonus" class="p-6">
            <div class="text-xs font-bold uppercase tracking-widest mb-4" style="color: var(--score-text-muted);">
                Bonus Points
            </div>
            <div class="text-sm opacity-50" style="color: var(--score-text-muted);">Coming soon…</div>
        </div>

    </div>

    {{-- ZONE 4: CORRECTIONS (placeholder — added in Task 8) --}}

    @endif
</div>
