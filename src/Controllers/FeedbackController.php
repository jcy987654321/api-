<?php

declare(strict_types=1);

namespace App\Controllers;

class FeedbackController extends BaseController
{
    public function index(): void
    {
        $this->renderView('feedback', [
            'page_type' => 'feedback',
        ]);
    }

    public function submit(): void
    {
        // Handle feedback submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';
            $type = $_POST['type'] ?? 'general';
            
            // Validate input
            $errors = [];
            
            if (empty($name)) {
                $errors['name'] = 'Name is required';
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Valid email is required';
            }
            
            if (empty($message)) {
                $errors['message'] = 'Message is required';
            }
            
            if (!empty($errors)) {
                $this->renderView('feedback', [
                    'page_type' => 'feedback',
                    'errors' => $errors,
                    'old_input' => compact('name', 'email', 'message', 'type'),
                ]);
                return;
            }
            
            // Save feedback to database (implementation would go here)
            $this->saveFeedback($name, $email, $message, $type);
            
            // Redirect with success message
            $this->renderView('feedback_success', [
                'page_type' => 'feedback',
            ]);
        } else {
            $this->redirect('/feedback');
        }
    }

    private function saveFeedback(string $name, string $email, string $message, string $type): void
    {
        // Implementation would save to database
        // For now, just log it
        error_log("Feedback received: {$name} ({$email}) - {$type}: {$message}");
    }
}