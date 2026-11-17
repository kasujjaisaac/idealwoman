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

    /** @var array<string, array{width: int, height: int, version: string, variants: array<int, string>}>|null */
    private ?array $imageManifest = null;

    /** @return array<string, array<string, mixed>> */
    public function definitions(): array
    {
        $definitions = config('content');
        $definitions['home']['fields']['featured_story']['options'] = ['' => 'Show all stories'] + array_map(fn (array $story): string => $story['title'], config('idealwoman.stories'));
        foreach (config('idealwoman.work') as $slug => $work) {
            $work['alt'] ??= '';
            $fields = [];
            foreach ($work as $key => $value) {
                if ($key === 'icon') {
                    continue;
                }
                $fields[$key] = ['label' => match ($key) {
                    'description' => 'Home page card description',
                    'detail' => 'Full programme description',
                    'alt' => 'Photograph description',
                    default => ucfirst($key),
                }, 'default' => $value ?? '', 'type' => $key === 'image' ? 'asset' : (in_array($key, ['description', 'detail']) ? 'textarea' : 'text'), 'group' => in_array($key, ['image', 'alt']) ? 'Programme photograph' : 'Programme content'];
            }
            $fields['objectives'] = ['label' => 'Objectives (one per line)', 'default' => implode("\n", config('idealwoman.objectives.'.$slug, [])), 'type' => 'textarea', 'group' => 'Programme content'];
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

    public function pageRoute(string $slug): string
    {
        return match (true) {
            str_starts_with($slug, 'programme-'), $slug === 'galleries' => 'work',
            $slug === 'leadership' => 'about',
            in_array($slug, ['collaboration', 'profile-download']) => 'involved',
            $slug === 'story' => 'stories',
            in_array($slug, ['home', 'about', 'work', 'stories', 'involved', 'contact']) => $slug,
            default => 'home',
        };
    }

    public function editorUrl(string $slug): string
    {
        if (str_starts_with($slug, 'story-')) {
            $record = ContentSection::query()->where('slug', $slug)->where('kind', 'story')->first();

            return $record ? route('admin.stories.edit', $record) : route('admin.stories.index');
        }

        return route('admin.content.edit', $slug);
    }

    /** @return array<int, array{name: string, alt: string}> */
    public function gallery(string $name): array
    {
        $photos = [];
        foreach (config('content.galleries.fields', []) as $key => $field) {
            if (str_starts_with($key, $name.'_') && str_ends_with($key, '_image')) {
                $image = $this->text('galleries.'.$key);
                if ($image !== '') {
                    $photos[] = ['name' => $image, 'alt' => $this->text('galleries.'.substr($key, 0, -6).'_alt')];
                }
            }
        }

        return $photos;
    }

    /** @return array<int, array{value: string, label: string, url: string}> */
    public function imageOptions(): array
    {
        return array_map(fn (string $name): array => ['value' => $name, 'label' => ucwords(str_replace('-', ' ', $name)), 'url' => $this->photo($name)['src']], $this->imageNames());
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

    /** @return array{src: string, srcset: string, width: int, height: int} */
    public function photo(string $name): array
    {
        $custom = $this->media($name, '');
        if ($custom !== '') {
            $filename = basename(parse_url($custom, PHP_URL_PATH));
            $metadataPath = 'cms/variants/'.pathinfo($filename, PATHINFO_FILENAME).'.json';
            $disk = Storage::disk('local');
            $metadata = $disk->exists($metadataPath) ? json_decode($disk->get($metadataPath), true, flags: JSON_THROW_ON_ERROR) : null;
            $url = fn (string $file): string => route('media.show', $file).'?v='.($metadata['version'] ?? '1');
            $source = $disk->path('cms/'.$filename);
        } else {
            $this->imageManifest ??= is_file(public_path('images/variants.json'))
                ? json_decode(file_get_contents(public_path('images/variants.json')), true, flags: JSON_THROW_ON_ERROR)
                : [];
            $metadata = $this->imageManifest[$name] ?? null;
            $url = fn (string $file): string => asset('images/'.$file).'?v='.($metadata['version'] ?? '1');
            $source = public_path('images/'.$name.'.jpg');
        }
        $dimensions = $metadata ?? (is_file($source) ? @getimagesize($source) : null);
        $variants = $metadata['variants'] ?? [];
        $srcset = [];
        foreach ($variants as $width => $filename) {
            $srcset[] = $url($filename).' '.$width.'w';
        }
        $fallback = $variants[960] ?? $variants[640] ?? (count($variants) ? reset($variants) : null);

        return [
            'src' => $fallback ? $url($fallback) : ($custom ?: asset('images/'.$name.'.jpg')),
            'srcset' => implode(', ', $srcset),
            'width' => $dimensions['width'] ?? $dimensions[0] ?? 1600,
            'height' => $dimensions['height'] ?? $dimensions[1] ?? 1067,
        ];
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
