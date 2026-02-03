<?php

namespace App\Livewire\Equipment;

use App\Models\Band;
use App\Models\Equipment;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class EquipmentForm extends Component
{
    use AuthorizesRequests, WithFileUploads;

    // Modal state
    public bool $showModal = false;

    // Equipment being edited (null for create mode)
    public ?int $equipmentId = null;

    // Form fields
    public string $make = '';

    public string $model = '';

    public string $type = 'radio';

    public ?string $description = null;

    public ?string $serial_number = null;

    public ?float $value_usd = null;

    public ?string $emergency_contact_phone = null;

    public ?int $power_output_watts = null;

    public ?string $notes = null;

    public array $tags = [];

    public string $tagsInput = '';

    public array $selectedBands = [];

    // Photo handling
    public ?TemporaryUploadedFile $photo = null;

    public ?string $existingPhotoPath = null;

    public function mount(?Equipment $equipment = null): void
    {
        if ($equipment) {
            $this->equipmentId = $equipment->id;
            $this->loadEquipment();
            $this->authorize('update', $equipment);
        } else {
            $this->authorize('create', Equipment::class);
        }
    }

    private function loadEquipment(): void
    {
        $equipment = Equipment::with('bands')->findOrFail($this->equipmentId);

        $this->make = $equipment->make ?? '';
        $this->model = $equipment->model ?? '';
        $this->type = $equipment->type;
        $this->description = $equipment->description;
        $this->serial_number = $equipment->serial_number;
        $this->value_usd = $equipment->value_usd ? (float) $equipment->value_usd : null;
        $this->emergency_contact_phone = $equipment->emergency_contact_phone;
        $this->power_output_watts = $equipment->power_output_watts;
        $this->notes = $equipment->notes;
        $this->tags = $equipment->tags ?? [];
        $this->tagsInput = implode(',', $equipment->tags ?? []);
        $this->selectedBands = $equipment->bands->pluck('id')->toArray();
        $this->existingPhotoPath = $equipment->photo_path;
    }

    #[Computed]
    public function bands()
    {
        return Band::orderBy('sort_order')->get();
    }

    #[Computed]
    public function equipmentTypes(): array
    {
        return [
            ['value' => 'radio', 'label' => 'Radio'],
            ['value' => 'antenna', 'label' => 'Antenna'],
            ['value' => 'amplifier', 'label' => 'Amplifier'],
            ['value' => 'computer', 'label' => 'Computer'],
            ['value' => 'power_supply', 'label' => 'Power Supply'],
            ['value' => 'accessory', 'label' => 'Accessory'],
            ['value' => 'tool', 'label' => 'Tool'],
            ['value' => 'furniture', 'label' => 'Furniture'],
            ['value' => 'other', 'label' => 'Other'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        // Convert comma-separated tags input to array
        $tags = [];
        if (! empty($this->tagsInput)) {
            $tags = array_filter(
                array_map('trim', explode(',', $this->tagsInput))
            );
        }

        // Handle photo upload
        $photoPath = $this->existingPhotoPath;
        if ($this->photo) {
            // Delete old photo if exists
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            // Store new photo
            $photoPath = $this->photo->store('equipment-photos', 'public');
        }

        // Prepare equipment data
        $equipmentData = [
            'owner_user_id' => auth()->id(),
            'make' => $validated['make'],
            'model' => $validated['model'],
            'type' => $validated['type'],
            'description' => $validated['description'],
            'serial_number' => $validated['serial_number'],
            'value_usd' => $validated['value_usd'],
            'emergency_contact_phone' => $validated['emergency_contact_phone'],
            'power_output_watts' => $validated['power_output_watts'],
            'notes' => $validated['notes'],
            'tags' => $tags,
            'photo_path' => $photoPath,
        ];

        if ($this->equipmentId) {
            // Update existing equipment
            $equipment = Equipment::findOrFail($this->equipmentId);

            // Don't override owner for existing equipment
            unset($equipmentData['owner_user_id']);

            $equipment->update($equipmentData);
        } else {
            // Create new equipment
            $equipment = Equipment::create($equipmentData);
        }

        // Sync bands
        $equipment->bands()->sync($validated['selectedBands'] ?? []);

        // Success notification
        $this->dispatch('toast', [
            'title' => 'Success',
            'description' => $this->equipmentId ? 'Equipment updated successfully' : 'Equipment created successfully',
            'icon' => 'o-check-circle',
            'css' => 'alert-success',
        ]);

        // Redirect to list after saving
        return $this->redirect(route('equipment.index'), navigate: true);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'equipmentId',
            'make',
            'model',
            'type',
            'description',
            'serial_number',
            'value_usd',
            'emergency_contact_phone',
            'power_output_watts',
            'notes',
            'tags',
            'tagsInput',
            'selectedBands',
            'photo',
            'existingPhotoPath',
        ]);

        $this->type = 'radio'; // Reset to default
    }

    protected function rules(): array
    {
        return [
            'make' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:radio,antenna,amplifier,computer,power_supply,accessory,tool,furniture,other'],
            'description' => ['nullable', 'string'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'value_usd' => ['nullable', 'numeric', 'min:0'],
            'emergency_contact_phone' => ['nullable', 'string'],
            'power_output_watts' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'notes' => ['nullable', 'string'],
            'tagsInput' => ['nullable', 'string'],
            'selectedBands' => ['nullable', 'array'],
            'selectedBands.*' => ['exists:bands,id'],
            'photo' => ['nullable', 'image', 'max:5120'], // 5MB max
        ];
    }

    protected function messages(): array
    {
        return [
            'make.required' => 'Please provide the equipment manufacturer.',
            'model.required' => 'Please provide the equipment model.',
            'type.required' => 'Please select an equipment type.',
            'type.in' => 'The selected equipment type is invalid.',
            'value_usd.min' => 'The value must be at least $0.',
            'power_output_watts.min' => 'Power output must be at least 1 watt.',
            'power_output_watts.max' => 'Power output cannot exceed 10,000 watts.',
            'photo.image' => 'The file must be an image.',
            'photo.max' => 'The photo size cannot exceed 5MB.',
        ];
    }

    public function render(): View
    {
        return view('livewire.equipment.equipment-form');
    }
}
