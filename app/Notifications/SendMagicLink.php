<?php
/* namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendMagicLink extends Notification
{
    use Queueable;

    public function __construct(public $user, public $signedUrl, public $deepLink) {}

    public function via($notifiable) { return ['mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your magic sign-in link')
            ->greeting('Hi ' . ($this->user->name ?? 'there') . ',')
            ->line('Click the link below to sign in. It will expire in 15 minutes and can be used only once.')
            ->action('Sign in (Browser)', $this->signedUrl)
            ->line('Or, if you use our mobile app, open this link on your phone:')
            ->line($this->deepLink)
            ->line('If you did not request this link, you can safely ignore this email.');
    }
} */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendMagicLink extends Notification
{
    use Queueable;

    public function __construct(
        public $user,
        public $signedUrl,
        public $deepLink
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your magic sign-in link')
            ->view('emails.magiclink', [
                'user'      => $this->user,
                'signedUrl' => $this->signedUrl,
                'deepLink'  => $this->deepLink,
            ]);
    }
}


