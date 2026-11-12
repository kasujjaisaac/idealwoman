<?php

namespace App\Services;

use App\Models\ContentSection;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SiteContent
{
    /** @var array<string, array<string, mixed>> */
    private array $published = [];

    /** @var array<int,string>|null */
    private ?array $uploads = null;

    /** @return array<string, array<string, mixed>> */
    public function definitions(): array
    {
        $definitions = config('content');
        foreach (config('idealwoman.work') as $slug => $work) {
            $work['alt'] ??= '';
            $fields = [];
            foreach ($work as $key => $value) {
                if ($key === 'icon') {
                    continue;
                }
                $fields[$key] = ['label' => ucfirst($key), 'default' => $value ?? '', 'type' => $key === 'image' ? 'asset' : (in_array($key, ['description', 'detail']) ? 'textarea' : 'text')];
            }
            $fields['objectives'] = ['label' => 'Objectives (one per line)', 'default' => implode("\n", config('idealwoman.objectives.'.$slug, [])), 'type' => 'textarea'];
            $definitions['programme-'.$slug] = ['label' => $work['title'], 'fields' => $fields];
        }
        $fields = [];
        foreach (config('idealwoman.social_links') as $key => $value) {
            $fields[$key] = ['label' => ucfirst($key).' URL', 'default' => $value ?? '', 'type' => 'url'];
        }
        $definitions['social'] = ['label' => 'Social media', 'fields' => $fields];
        $images = [];
        foreach (glob(public_path('images/*.jpg')) ?: [] as $path) {
            $key = pathinfo($path, PATHINFO_FILENAME);
            $images[$key] = ['label' => ucwords(str_replace('-', ' ', $key)), 'default' => '', 'type' => 'image'];
        }
        foreach (glob(public_path('images/partners/*')) ?: [] as $path) {
            $key = 'partner-'.pathinfo($path, PATHINFO_FILENAME);
            $images[$key] = ['label' => 'Partner: '.ucwords(str_replace('-', ' ', pathinfo($path, PATHINFO_FILENAME))), 'default' => '', 'type' => 'image'];
        }
        $images['logo'] = ['label' => 'Website logo & favicon', 'default' => '', 'type' => 'image'];
        $images['profile'] = ['label' => 'Organisational profile (PDF)', 'default' => '', 'type' => 'pdf'];
        $definitions['media'] = ['label' => 'Images & downloads', 'fields' => $images];

        return $definitions;
    }

    /** @return array<string, string> */
    public function defaults(string $slug): array
    {
        return array_map(fn (array $field): string => (string) $field['default'], $this->definitions()[$slug]['fields'] ?? []);
    }

    public function load(): void
    {
        $this->published = ContentSection::query()->whereNotNull('published')->pluck('published', 'slug')->all();
    }

    public function preview(string $slug): void
    {
        $section = ContentSection::query()->where('slug', $slug)->firstOrFail();
        $this->published[$slug] = $section->draft;
    }

    public function text(string $key, string $fallback = ''): string
    {
        [$section,$field] = explode('.', $key, 2);

        return (string) ($this->published[$section][$field] ?? config('content.'.$section.'.fields.'.$field.'.default', $fallback));
    }

    public function media(string $name, string $fallback): string
    {
        $path = $this->published['media'][$name] ?? '';
        if ($path !== '') {
            return route('media.show', ['path' => basename($path)]);
        }
        $this->uploads ??= Storage::disk('local')->files('cms');
        foreach ($this->uploads as $file) {
            if (pathinfo($file, PATHINFO_FILENAME) === $name && in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return route('media.show', basename($file));
            }
        }

        return $fallback;
    }

    /** @return array<int,string> */
    public function imageNames(): array
    {
        $names = array_map(fn (string $file): string => pathinfo($file, PATHINFO_FILENAME), glob(public_path('images/*.jpg')) ?: []);
        foreach (Storage::disk('local')->files('cms') as $file) {
            if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $names[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }

        return $names;
    }

    public function hasMedia(string $name): bool
    {
        return ! empty($this->published['media'][$name]);
    }

    /** @return array<string,mixed> */
    public function publicConfiguration(): array
    {
        $configuration = config('idealwoman');
        foreach ($configuration['work'] as $slug => &$work) {
            $work['alt'] ??= '';
            $values = $this->published['programme-'.$slug] ?? [];
            foreach (array_keys($work) as $key) {
                if ($key !== 'icon' && array_key_exists($key, $values)) {
                    $work[$key] = $values[$key];
                }
            }
            if (isset($values['objectives'])) {
                $configuration['objectives'][$slug] = array_values(array_filter(preg_split('/\R/', $values['objectives'])));
            }
        }
        unset($work);
        $configuration['social_links'] = array_replace($configuration['social_links'], $this->published['social'] ?? []);
        foreach (ContentSection::query()->where('kind', 'story')->get() as $section) {
            $slug = substr($section->slug, 6);
            unset($configuration['stories'][$slug]);
            if (isset($this->published[$section->slug])) {
                $story = $this->published[$section->slug];
                $story['paragraphs'] = preg_split('/\R\s*\R/', $story['body']);
                $configuration['stories'][$slug] = $story;
            }
        }

        return $configuration;
    }

    /** @param array<string,mixed> $values */
    public function save(string $slug, string $label, string $kind, array $values, string $action, User $user, int $version): ContentSection
    {
        return DB::transaction(function () use ($slug, $label, $kind, $values, $action, $user, $version): ContentSection {
            $section = ContentSection::query()->where('slug', $slug)->lockForUpdate()->first();
            if (($section?->version ?? 0) !== $version) {
                throw ValidationException::withMessages(['version' => 'Someone else saved this content. Reload the editor before saving again.']);
            }
            if ($section === null) {
                $section = new ContentSection(['slug' => $slug, 'label' => $label, 'kind' => $kind]);
            }
            $section->label = $label;
            $section->draft = $values;
            $section->version = $version + 1;
            if ($action === 'publish') {
                $section->published = $values;
                $section->published_at = now();
            } elseif ($action === 'unpublish') {
                $section->published = null;
                $section->published_at = null;
            }
            $section->save();
            $section->revisions()->create(['user_id' => $user->id, 'action' => $action, 'snapshot' => $values]);

            return $section;
        });
    }
}
