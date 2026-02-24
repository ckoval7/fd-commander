<div>
    @if (!$this->event)
        <p>No active event.</p>
    @else
        <p>Score: {{ number_format($this->finalScore) }}</p>
    @endif
</div>
