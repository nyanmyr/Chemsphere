<x-app-layout title="Edit Batch — {{ $chemical->chemical_name }}">
    <x-meta :record="$batch" />
    <div class="mb-4">
        <a href="{{ route('inventory.show', $chemical->chemical_id) }}" class="link text-sm">← Back to {{ $chemical->chemical_name }}</a>
        <h2 class="mt-2 text-lg font-semibold">Edit batch / lot {{ $batch->batch_number }}</h2>
    </div>

    <form action="{{ route('inventory.batch.update', $batch->chemical_batch_id) }}" method="POST" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('partials.chemical-batch-fields')
        <x-form-actions submit="Save changes" :cancel="route('inventory.show', $chemical->chemical_id)" />
    </form>
</x-app-layout>
