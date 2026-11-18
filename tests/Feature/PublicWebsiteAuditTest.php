<?php

namespace Tests\Feature;

use DOMDocument;
use Tests\TestCase;

class PublicWebsiteAuditTest extends TestCase
{
    public function test_missing_pages_offer_a_branded_way_back_home(): void
    {
        $this->get('/page-that-does-not-exist')->assertNotFound()
            ->assertSee('Page not found | Ideal Woman')
            ->assertSee('Back to home')->assertSee('Contact our team');
    }

    public function test_public_pages_have_working_internal_links_images_and_form_labels(): void
    {
        $paths = ['/', '/about', '/our-work', '/stories', '/get-involved', '/volunteer-with-us', '/contact', '/stories/students-championing-peace', '/stories/learning-through-making'];
        $documents = [];
        foreach ($paths as $path) {
            $response = $this->get($path)->assertOk();
            $document = new DOMDocument;
            $previous = libxml_use_internal_errors(true);
            $document->loadHTML($response->getContent());
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            $documents[$path] = $document;
        }

        foreach ($documents as $path => $document) {
            $ids = [];
            foreach ($document->getElementsByTagName('*') as $element) {
                if ($element->hasAttribute('id')) {
                    $id = $element->getAttribute('id');
                    $this->assertNotContains($id, $ids, "Duplicate id {$id} on {$path}");
                    $ids[] = $id;
                }
            }
            foreach ($document->getElementsByTagName('img') as $image) {
                $this->assertTrue($image->hasAttribute('alt'), "Image has no alternative text on {$path}");
                foreach (['src', 'data-src'] as $attribute) {
                    if ($image->hasAttribute($attribute)) {
                        if (str_starts_with($image->getAttribute($attribute), 'data:')) {
                            continue;
                        }
                        $imagePath = parse_url($image->getAttribute($attribute), PHP_URL_PATH);
                        $this->assertFileExists(public_path(ltrim($imagePath, '/')), "Missing image on {$path}");
                    }
                }
            }
            foreach ($document->getElementsByTagName('a') as $link) {
                $url = parse_url($link->getAttribute('href'));
                if (isset($url['scheme']) && ! in_array($url['scheme'], ['http', 'https'])) {
                    continue;
                }
                if (isset($url['host']) && $url['host'] !== parse_url(config('app.url'), PHP_URL_HOST)) {
                    continue;
                }
                $destination = $url['path'] ?? (isset($url['host']) ? '/' : $path);
                if (! isset($documents[$destination])) {
                    $this->assertFileExists(public_path(ltrim($destination, '/')), "Broken link on {$path}");

                    continue;
                }
                if (! empty($url['fragment'])) {
                    $this->assertNotNull($documents[$destination]->getElementById(rawurldecode($url['fragment'])), "Missing anchor on {$destination}");
                }
            }
            foreach (['input', 'select', 'textarea'] as $tag) {
                foreach ($document->getElementsByTagName($tag) as $input) {
                    if ($input->getAttribute('type') === 'hidden') {
                        continue;
                    }
                    $labelled = $input->hasAttribute('aria-label');
                    foreach ($document->getElementsByTagName('label') as $label) {
                        if ($input->hasAttribute('id') && $label->getAttribute('for') === $input->getAttribute('id')) {
                            $labelled = true;
                        }
                    }
                    for ($parent = $input->parentNode; $parent !== null; $parent = $parent->parentNode) {
                        if ($parent->nodeName === 'label') {
                            $labelled = true;
                        }
                    }
                    $this->assertTrue($labelled, "Unlabelled {$tag} on {$path}");
                }
            }
        }
    }
}
