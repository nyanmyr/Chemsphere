<x-app-layout title="Add Batch — {{ $chemical->chemical_name }}">
    <div class="mb-4">
        <a href="{{ route('inventory.show', $chemical->chemical_id) }}" class="link text-sm">← Back to {{ $chemical->chemical_name }}</a>
        <h2 class="mt-2 text-lg font-semibold">Add a batch</h2>
        <p class="mt-1 text-sm text-muted">Batch information is tracked independently within this chemical category.</p>
    </div>

    <form action="{{ route('inventory.batch.store', $chemical->chemical_id) }}" method="POST" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @include('partials.chemical-batch-fields')
        <x-form-actions submit="Add Batch" :cancel="route('inventory.show', $chemical->chemical_id)" />
    </form>
</x-app-layout>
