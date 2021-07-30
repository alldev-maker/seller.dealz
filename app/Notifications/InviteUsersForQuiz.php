<?php

namespace App\Notifications;

use App\Models\Admin\User;
use App\Models\Quizzes\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class InviteUsersForQuiz extends Notification
{
    use Queueable;

    /**
     * @var Quiz
     */
    protected $quiz;

    /**
     * @var User|bool
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param $quiz
     * @param $user
     *
     * @return void
     */
    public function __construct($quiz, $user = null)
    {
        $this->quiz = $quiz;
        $this->user = $user ?? new User();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $message = new MailMessage();
        $message->subject('[QuizMaster] Invitation to Take Quiz ' . $this->quiz->name);

        if (!empty($this->user->id) || $this->quiz->allow_guests) {
            $message->line(new HtmlString('You are invited to take the quiz named <strong>' . $this->quiz->name . '</strong>.'));
            $message->action('Take this Quiz', url(route('qz.form', ['id' => $this->quiz->id])));
        } else {
            $message->line(new HtmlString('Please sign up to take the quiz named <strong>' . $this->quiz->name . '</strong>.'));
            $message->action('Sign Up', url(route('register.index')));
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
