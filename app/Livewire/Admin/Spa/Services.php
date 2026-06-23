<?php

namespace App\Livewire\Admin\Spa;

use App\Models\SpaBooking;
use App\Models\SpaService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Services extends Component
{
    use WithFileUploads;

    public string $search = '';

    public string $statusFilter = ''; // '' | active | inactive

    public string $sort = 'sort_order';

    /* ----- Add / Edit modal ----- */
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $fName = '';

    public $fPrice = '';

    public string $fDescription = '';

    public bool $fActive = true;

    public $fImage = null;

    public ?string $existingImage = null;

    protected function rules(): array
    {
        return [
            'fName' => ['required', 'string', 'max:120'],
            'fPrice' => ['required', 'integer', 'min:0'],
            'fDescription' => ['nullable', 'string', 'max:500'],
            'fImage' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function setStatus(string $status): void
    {
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $s = SpaService::find($id);
        if (! $s) {
            return;
        }
        $this->editingId = $s->id;
        $this->fName = $s->name;
        $this->fPrice = $s->price;
        $this->fDescription = (string) $s->description;
        $this->fActive = $s->is_active;
        $this->existingImage = $s->image;
        $this->showForm = true;
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'fName', 'fPrice', 'fDescription', 'fActive', 'fImage', 'existingImage']);
        $this->fActive = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $data = $this->validate();

        $imagePath = $this->existingImage;
        if ($this->fImage) {
            $imagePath = $this->fImage->store('spa', 'public');
            \App\Support\ImageOptimizer::optimize($imagePath);

            if ($this->existingImage
                && ! str_starts_with($this->existingImage, 'images/')
                && Storage::disk('public')->exists($this->existingImage)) {
                Storage::disk('public')->delete($this->existingImage);
            }
        }

        $payload = [
            'name' => $data['fName'],
            'price' => (int) $data['fPrice'],
            'description' => $data['fDescription'] ?: null,
            'is_active' => $this->fActive,
            'image' => $imagePath,
        ];

        if ($this->editingId) {
            $s = SpaService::find($this->editingId);
            if (! $s) {
                return;
            }
            $s->update($payload);
            $message = $s->name.' was updated.';
        } else {
            $payload['slug'] = $this->uniqueSlug($data['fName']);
            $payload['sort_order'] = (int) SpaService::max('sort_order') + 1;
            $s = SpaService::create($payload);
            $message = $s->name.' was added.';
        }

        $this->showForm = false;
        $this->resetForm();
        $this->dispatch('toast', type: 'success', message: $message);
    }

    public function imagePreviewUrl(): ?string
    {
        if ($this->fImage) {
            return $this->fImage->temporaryUrl();
        }
        if (! $this->existingImage) {
            return null;
        }
        if (str_starts_with($this->existingImage, 'images/')) {
            return str_replace(' ', '%20', asset($this->existingImage));
        }

        return Storage::disk('public')->url($this->existingImage);
    }

    public function delete(int $id): void
    {
        $s = SpaService::find($id);
        if (! $s) {
            return;
        }
        if ($s->image
            && ! str_starts_with($s->image, 'images/')
            && Storage::disk('public')->exists($s->image)) {
            Storage::disk('public')->delete($s->image);
        }
        $name = $s->name;
        $s->delete();
        $this->dispatch('toast', type: 'success', message: $name.' was removed.');
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'service';
        $slug = $base;
        $i = 2;
        while (SpaService::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function render()
    {
        $base = SpaService::query();
        $total = (clone $base)->count();
        $activeCount = (clone $base)->where('is_active', true)->count();
        $inactiveCount = (clone $base)->where('is_active', false)->count();
        $bookingsCount = SpaBooking::where('status', 'paid')->count();

        $stats = [
            ['label' => 'Total Services', 'value' => $total, 'sub' => 'In the spa menu', 'accent' => '#f38c00'],
            ['label' => 'Active', 'value' => $activeCount, 'sub' => 'Shown on the website', 'accent' => '#16a34a'],
            ['label' => 'Hidden', 'value' => $inactiveCount, 'sub' => 'Not bookable', 'accent' => '#d97706'],
            ['label' => 'Reservations', 'value' => $bookingsCount, 'sub' => 'Paid spa bookings', 'accent' => '#7c3aed'],
        ];

        $services = SpaService::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))
            ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when(! in_array($this->sort, ['name', 'price_desc', 'price_asc']), fn ($q) => $q->ordered())
            ->get();

        return view('admin.spa.services', [
            'services' => $services,
            'stats' => $stats,
            'counts' => ['all' => $total, 'active' => $activeCount, 'inactive' => $inactiveCount],
        ])->layout('components.admin.app', [
            'title' => 'Spa Services',
            'subtitle' => 'Manage the spa & wellness services shown on the website',
        ]);
    }
}
