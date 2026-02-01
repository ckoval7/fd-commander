<x-modal wire:model="showDeleteModal" title="Delete User">
    <div class="space-y-4">
        <div class="alert alert-error">
            <x-icon name="o-exclamation-triangle" class="w-6 h-6" />
            <div>
                <h3 class="font-bold">Are you sure?</h3>
                <p>This action will delete the user account. This can be undone by restoring from the deleted users list.</p>
            </div>
        </div>
    </div>

    <x-slot:actions>
        <x-button label="Cancel" wire:click="$set('showDeleteModal', false)" class="btn-ghost" />
        <x-button label="Delete User" wire:click="deleteUser" class="btn-error" icon="o-trash" spinner="deleteUser" />
    </x-slot:actions>
</x-modal>
