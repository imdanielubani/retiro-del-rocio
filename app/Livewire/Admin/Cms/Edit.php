<?php

namespace App\Livewire\Admin\Cms;

use App\Models\SiteContent;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public string $tab = 'homepage';

    /** @var array<string,string|null> binding-name => text value / existing image path */
    public array $values = [];

    /** @var array<string,mixed> binding-name => TemporaryUploadedFile (image fields) */
    public array $uploads = [];

    /** @var array<int,array{q:string,a:string}> the contact FAQ repeater */
    public array $faqs = [];

    public function mount(): void
    {
        foreach ($this->fieldList() as $field) {
            if ($field['type'] === 'repeater') {
                $this->faqs = array_values(cms_array($field['key']));

                continue;
            }

            $this->values[$field['name']] = SiteContent::get($field['key'], $field['default'] ?? null);
        }
    }

    /** Flattened list of every field across all sections. */
    protected function fieldList(): array
    {
        return collect(config('cms.sections'))
            ->flatMap(fn ($section) => $section['fields'])
            ->all();
    }

    public function rules(): array
    {
        return [
            'uploads.*' => ['nullable', 'image', 'max:5120'],
            'faqs' => ['array'],
            'faqs.*.q' => ['nullable', 'string', 'max:255'],
            'faqs.*.a' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function removeImage(string $name): void
    {
        $this->values[$name] = null;
        unset($this->uploads[$name]);
    }

    public function addFaq(): void
    {
        $this->faqs[] = ['q' => '', 'a' => ''];
    }

    public function removeFaq(int $i): void
    {
        unset($this->faqs[$i]);
        $this->faqs = array_values($this->faqs);
    }

    public function save(): void
    {
        $this->validate();

        foreach ($this->fieldList() as $field) {
            $key = $field['key'];
            $name = $field['name'] ?? null;

            if ($field['type'] === 'repeater') {
                // Drop fully-empty rows, then store as JSON.
                $rows = array_values(array_filter(
                    $this->faqs,
                    fn ($row) => trim((string) ($row['q'] ?? '')) !== '' || trim((string) ($row['a'] ?? '')) !== ''
                ));
                SiteContent::put($key, json_encode($rows, JSON_UNESCAPED_UNICODE));

                continue;
            }

            if ($field['type'] === 'image') {
                if (! empty($this->uploads[$name])) {
                    $path = $this->uploads[$name]->store('cms', 'public');
                    SiteContent::put($key, $path);
                    $this->values[$name] = $path;
                } else {
                    SiteContent::put($key, $this->values[$name] ?: null);
                }

                continue;
            }

            // text / textarea
            SiteContent::put($key, $this->values[$name] ?? null);
        }

        $this->uploads = [];

        $this->dispatch('toast', type: 'success', message: 'Website content updated.');
    }

    public function render()
    {
        return view('admin.cms.edit', [
            'sections' => config('cms.sections'),
        ])->layout('components.admin.app', [
            'title' => 'Website CMS',
            'subtitle' => 'Edit the homepage and contact page content & images',
        ]);
    }
}
