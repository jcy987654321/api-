<?php

namespace Tests\Feature;

use App\Models\FeedbackThread;
use App\Services\FeedbackEncryptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_encryption_service_works(): void
    {
        $service = app(FeedbackEncryptionService::class);
        $originalData = 'Test sensitive data';

        $encrypted = $service->encrypt($originalData);
        $decrypted = $service->decrypt($encrypted);

        $this->assertEquals($originalData, $decrypted);
        $this->assertNotEquals($originalData, $encrypted);
    }

    public function test_feedback_thread_creation(): void
    {
        $thread = FeedbackThread::create([
            'visitor_name' => 'Test User',
            'visitor_email' => 'test@qq.com',
            'subject' => 'Test Subject',
            'visitor_ip' => '127.0.0.1',
            'visitor_user_agent' => 'Test Agent',
        ]);

        $this->assertNotNull($thread->reference_id);
        $this->assertEquals('Test User', $thread->visitor_name);
        $this->assertEquals('test@qq.com', $thread->visitor_email);
        $this->assertEquals('Test Subject', $thread->subject);
        $this->assertEquals('open', $thread->status);
    }

    public function test_feedback_thread_visitor_token(): void
    {
        $thread = FeedbackThread::create([
            'visitor_name' => 'Test User',
            'visitor_email' => 'test@qq.com',
            'subject' => 'Test Subject',
            'visitor_ip' => '127.0.0.1',
            'visitor_user_agent' => 'Test Agent',
        ]);

        $token = $thread->generateVisitorToken();
        $this->assertNotEmpty($token);

        $foundThread = FeedbackThread::findByVisitorToken($token);
        $this->assertNotNull($foundThread);
        $this->assertEquals($thread->id, $foundThread->id);
        $this->assertEquals($thread->reference_id, $foundThread->reference_id);
    }

    public function test_feedback_form_page_loads(): void
    {
        $response = $this->get(route('feedback.form'));

        $response->assertStatus(200);
        $response->assertSee('Submit Feedback');
        $response->assertSee('QQ Email');
    }

    public function test_feedback_form_validation(): void
    {
        // Test missing required fields
        $response = $this->post(route('feedback.store'), []);

        $response->assertSessionHasErrors(['visitor_email', 'subject', 'message', 'consent']);
    }

    public function test_feedback_form_email_validation(): void
    {
        $response = $this->post(route('feedback.store'), [
            'visitor_email' => 'invalid-email',
            'subject' => 'Test Subject',
            'message' => 'Test message with enough characters',
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors(['visitor_email']);
    }

    public function test_feedback_form_qq_email_validation(): void
    {
        $response = $this->post(route('feedback.store'), [
            'visitor_email' => 'test@gmail.com', // Not QQ email
            'subject' => 'Test Subject',
            'message' => 'Test message with enough characters',
            'consent' => '1',
        ]);

        $response->assertSessionHasErrors(['visitor_email']);
    }

    public function test_admin_feedback_index_page(): void
    {
        $response = $this->get(route('admin.feedback.index'));

        $response->assertStatus(200);
        $response->assertSee('Feedback Management');
    }

    public function test_feedback_thread_status_methods(): void
    {
        $thread = FeedbackThread::create([
            'visitor_name' => 'Test User',
            'visitor_email' => 'test@qq.com',
            'subject' => 'Test Subject',
            'visitor_ip' => '127.0.0.1',
            'visitor_user_agent' => 'Test Agent',
        ]);

        $this->assertEquals('open', $thread->status);

        $thread->markAsClosed();
        $this->assertEquals('closed', $thread->status);

        $thread->markAsOpen();
        $this->assertEquals('open', $thread->status);

        $thread->archive();
        $this->assertEquals('archived', $thread->status);
    }
}