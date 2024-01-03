<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $template;
    private string $mailTo;
    private string|null $cc;
    private array $fields;
    private string|null $pathToFile;
    /**
     * Create a new job instance.
     */
    public function __construct(string $template, string $mailTo, string|null $cc, array $fields, string|null $pathToFile=null)
    {
        $this->template = $template;
        $this->mailTo = $mailTo;
        $this->cc = $cc;
        $this->fields = $fields;
        $this->pathToFile = $pathToFile;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::send('emails.'.$this->template, $this->fields, function($message) {
            $message->subject(trans('mail.message_from_the_site').' '.env('APP_NAME'));
//            $message->from(env('MAIL_FROM_ADDRESS'));
            $message->to($this->mailTo);
            if ($this->cc) $message->cc($this->cc);
            if ($this->pathToFile) $message->attach($this->pathToFile);
        });
    }
}
