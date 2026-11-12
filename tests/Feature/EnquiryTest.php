<?php

namespace Tests\Feature;

use App\Mail\PartnershipEnquiry;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    private function payload(array $changes = []): array
    {
        return array_replace(['name' => 'Test Visitor', 'email' => 'visitor@example.org', 'organisation' => 'Example Organisation', 'enquiry_type' => 'Partnership', 'message' => 'We would like to discuss a practical skills collaboration.', 'website' => ''], $changes);
    }

    public function test_required_fields_are_reported_and_nothing_is_sent(): void
    {
        Mail::fake();

        $this->from('/contact')->post('/contact', [])->assertRedirect('/contact')->assertSessionHasErrors(['name', 'email', 'enquiry_type', 'message']);

        Mail::assertNothingSent();
    }

    public static function invalidInputs(): array
    {
        return [
            ['name', str_repeat('x', 121), 'The name field must not be greater than 120 characters.'],
            ['email', 'invalid', 'The email field must be a valid email address.'],
            ['email', str_repeat('a', 245).'@example.org', 'The email field must not be greater than 254 characters.'],
            ['organisation', str_repeat('x', 181), 'The organisation field must not be greater than 180 characters.'],
            ['enquiry_type', 'Donation', 'The selected enquiry type is invalid.'],
            ['message', 'short', 'The message field must be at least 20 characters.'],
            ['message', str_repeat('x', 5001), 'The message field must not be greater than 5000 characters.'],
            ['website', 'spam.example', 'Your enquiry could not be submitted. Please email or call us directly.'],
        ];
    }

    #[DataProvider('invalidInputs')]
    public function test_invalid_enquiries_show_errors_without_sending(string $field, string $value, string $error): void
    {
        Mail::fake();

        $this->from('/contact')->post('/contact', $this->payload([$field => $value]))->assertRedirect('/contact')->assertSessionHasErrors([$field => $error]);

        Mail::assertNothingSent();
    }

    public function test_unconfigured_mail_preserves_input_without_claiming_success(): void
    {
        Mail::fake();

        $this->from('/contact')->post('/contact', $this->payload())->assertRedirect('/contact')->assertSessionHas('delivery_error')->assertSessionHasInput('name', 'Test Visitor')->assertSessionMissing('success');

        Mail::assertNothingSent();
    }

    public function test_non_smtp_mail_cannot_claim_success_even_if_enabled(): void
    {
        config(['idealwoman.enquiries_enabled' => true, 'mail.default' => 'log']);
        Mail::fake();

        $this->from('/contact')->post('/contact', $this->payload())->assertRedirect('/contact')->assertSessionHas('delivery_error')->assertSessionMissing('success');

        Mail::assertNothingSent();
    }

    public function test_transport_failure_preserves_input_and_shows_direct_alternatives(): void
    {
        config(['idealwoman.enquiries_enabled' => true, 'mail.default' => 'smtp', 'mail.mailers.smtp' => ['transport' => 'smtp', 'host' => '127.0.0.1', 'port' => 1, 'timeout' => 1]]);

        $this->from('/contact')->post('/contact', $this->payload())->assertRedirect('/contact')->assertSessionHas('delivery_error')->assertSessionMissing('success');
        $this->get('/contact')->assertSee('Your message has not been sent.')->assertSee('info@idealwomanuganda.com')->assertSee('value="Test Visitor"', false);
    }

    public function test_accepted_transport_sends_correct_enquiry_and_clears_input(): void
    {
        config(['idealwoman.enquiries_enabled' => true, 'mail.default' => 'smtp', 'mail.mailers.smtp' => ['transport' => 'array']]);

        $this->from('/contact')->post('/contact', $this->payload(['organisation' => null]))->assertRedirect('/contact')->assertSessionHas('success')->assertSessionMissing('delivery_error');

        $messages = Mail::mailer('smtp')->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $email = $messages->first()->getOriginalMessage();
        $this->assertSame('info@idealwomanuganda.com', $email->getTo()[0]->getAddress());
        $this->assertSame('visitor@example.org', $email->getReplyTo()[0]->getAddress());
        $this->assertStringContainsString('practical skills collaboration', $email->getHtmlBody());
    }

    public function test_mail_content_escapes_html(): void
    {
        $mail = new PartnershipEnquiry($this->payload(['message' => '<script>alert("test")</script>']));

        $mail->assertSeeInHtml('&lt;script&gt;', false);
        $mail->assertDontSeeInHtml('<script>', false);
    }

    public function test_sixth_attempt_is_rate_limited(): void
    {
        Mail::fake();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from('/contact')->post('/contact', $this->payload())->assertRedirect();
        }

        $this->post('/contact', $this->payload())->assertTooManyRequests();

        Mail::assertNothingSent();
    }
}
