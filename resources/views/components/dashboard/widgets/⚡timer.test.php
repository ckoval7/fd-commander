<?php

use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test('dashboard.widgets.timer')
        ->assertStatus(200);
});
