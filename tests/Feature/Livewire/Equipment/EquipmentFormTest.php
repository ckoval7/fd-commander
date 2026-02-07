<?php

use App\Livewire\Equipment\EquipmentForm;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Storage::fake('public');

    $this->user = User::factory()->create();

    // Create permissions
    Permission::create(['name' => 'manage-own-equipment']);
    Permission::create(['name' => 'edit-any-equipment']);

    $role = Role::create(['name' => 'Operator', 'guard_name' => 'web']);
    $role->givePermissionTo('manage-own-equipment');
    $this->user->assignRole($role);
});

test('photo upload shows validation feedback immediately for valid image', function () {
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('equipment.jpg', 800, 600)->size(1000);

    // Create mode - explicitly pass null for equipment
    Livewire::test(EquipmentForm::class, ['equipment' => null])
        ->set('photo', $file)
        ->assertHasNoErrors('photo');
});

test('photo upload validates file type and shows error for non-image', function () {
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    Livewire::test(EquipmentForm::class, ['equipment' => null])
        ->set('photo', $file)
        ->assertHasErrors('photo');
});

test('photo upload validates file size and shows error for large files', function () {
    $this->actingAs($this->user);

    // Create a file larger than 5MB (5120KB)
    $file = UploadedFile::fake()->image('equipment.jpg')->size(6000);

    Livewire::test(EquipmentForm::class, ['equipment' => null])
        ->set('photo', $file)
        ->assertHasErrors('photo');
});

test('photo upload stores file and creates equipment', function () {
    $this->actingAs($this->user);

    $file = UploadedFile::fake()->image('equipment.jpg');

    Livewire::test(EquipmentForm::class, ['equipment' => null])
        ->set('make', 'Yaesu')
        ->set('model', 'FT-891')
        ->set('type', 'radio')
        ->set('photo', $file)
        ->call('save');

    $equipment = Equipment::where('make', 'Yaesu')
        ->where('model', 'FT-891')
        ->first();

    expect($equipment)->not->toBeNull()
        ->and($equipment->photo_path)->not->toBeNull();

    Storage::disk('public')->assertExists($equipment->photo_path);
});

test('photo upload replaces existing photo when updating equipment', function () {
    $this->actingAs($this->user);

    // Create equipment with existing photo
    $oldFile = UploadedFile::fake()->image('old-photo.jpg');
    $oldPath = $oldFile->store('equipment-photos', 'public');

    $equipment = Equipment::factory()->create([
        'owner_user_id' => $this->user->id,
        'photo_path' => $oldPath,
    ]);

    Storage::disk('public')->put($oldPath, 'old content');

    // Upload new photo
    $newFile = UploadedFile::fake()->image('new-photo.jpg');

    Livewire::test(EquipmentForm::class, ['equipment' => $equipment])
        ->set('photo', $newFile)
        ->call('save');

    $equipment->refresh();

    // Old photo should be deleted
    Storage::disk('public')->assertMissing($oldPath);

    // New photo should exist
    Storage::disk('public')->assertExists($equipment->photo_path);
    expect($equipment->photo_path)->not->toBe($oldPath);
});

test('equipment form can be created without photo', function () {
    $this->actingAs($this->user);

    Livewire::test(EquipmentForm::class, ['equipment' => null])
        ->set('make', 'Icom')
        ->set('model', 'IC-7300')
        ->set('type', 'radio')
        ->call('save');

    $equipment = Equipment::where('make', 'Icom')
        ->where('model', 'IC-7300')
        ->first();

    expect($equipment)->not->toBeNull()
        ->and($equipment->photo_path)->toBeNull();
});
