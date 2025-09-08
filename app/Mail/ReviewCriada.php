<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReviewCriada extends Mailable
{
    use Queueable, SerializesModels;

    public $review;
    public $moderationUrl;

    public function __construct(Review $review)
    {
        $this->review = $review;

        // Usar a rota "ponte" que guarda o intended e redireciona para login
        $this->moderationUrl = route('moderacao.reviews.link');
    }

    public function build()
    {
        return $this->subject('Nova Review Submetida')
            ->view('emails.review-criada')
            ->with([
                'review' => $this->review,
                'moderationUrl' => $this->moderationUrl,
            ]);
    }
}
