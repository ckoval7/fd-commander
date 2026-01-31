<div x-data="{
    darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggle() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', this.darkMode);
    }
}" x-init="document.documentElement.classList.toggle('dark', darkMode)" {{ $attributes }}>
    <button
        @click="toggle()"
        type="button"
        class="btn btn-circle btn-ghost btn-sm relative overflow-hidden group"
        :aria-label="darkMode ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <!-- Sun icon (shown in dark mode) -->
        <x-icon
            name="o-sun"
            class="w-5 h-5 absolute inset-0 m-auto transition-all duration-300"
            x-show="darkMode"
            x-transition:enter="rotate-0 scale-100 opacity-100"
            x-transition:leave="rotate-90 scale-0 opacity-0"
        />

        <!-- Moon icon (shown in light mode) -->
        <x-icon
            name="o-moon"
            class="w-5 h-5 absolute inset-0 m-auto transition-all duration-300"
            x-show="!darkMode"
            x-transition:enter="rotate-0 scale-100 opacity-100"
            x-transition:leave="-rotate-90 scale-0 opacity-0"
        />
    </button>
</div>
