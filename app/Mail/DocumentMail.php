<?php

namespace App\Mail;

use App\Models\Customer;
use App\Models\IncomingPo;
use App\Models\Invoice;
use App\Models\Proposal;
use App\Models\Quotation;
use App\Services\Documents\DocumentPdfRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Model $document,
        public string $bodyMessage,
        ?string $mailLocale = null,
    ) {
        $this->locale($mailLocale ?? app()->getLocale());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.document',
            with: [
                'document' => $this->document,
                'number' => $this->document->getAttribute($this->document->documentType()->numberAttribute()),
                'message' => $this->bodyMessage,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $renderer = app(DocumentPdfRenderer::class);

        return [
            Attachment::fromData(
                fn (): string => $renderer->render($this->document)->output(),
                $renderer->filename($this->document),
            )->withMime('application/pdf'),
        ];
    }

    private function subjectLine(): string
    {
        $type = $this->document->documentType();
        $number = $this->document->getAttribute($type->numberAttribute());

        return __('documents.mail.subject', [
            'type' => $type->getLabel(),
            'number' => $number,
        ]);
    }

    /**
     * Alamat email customer yang terkait dengan dokumen.
     */
    public static function recipientFor(Model $document): ?string
    {
        $customer = match (true) {
            $document instanceof Customer => $document,
            $document instanceof Proposal,
            $document instanceof Quotation,
            $document instanceof IncomingPo,
            $document instanceof Invoice => $document->customer,
            default => null,
        };

        return $customer?->email;
    }
}
