<?php

namespace App\Message;

class ReminderEmailRec
{
    private string $to;
    private string $subject;
    private string $text;

    public function __construct(string $to, string $subject, string $text)
    {
        $this->to      = $to;
        $this->subject = $subject;
        $this->text    = $text;
    }

    public function getTo(): string    { return $this->to; }
    public function getSubject(): string { return $this->subject; }
    public function getText(): string   { return $this->text; }
}
