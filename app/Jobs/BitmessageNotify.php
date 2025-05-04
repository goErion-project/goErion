<?php

namespace App\Jobs;

use App\Marketplace\Bitmessage\Bitmessage;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BitmessageNotify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public int $tries = 2;
    /**
     * Content to send via bitmessage
     *
     * @var string
     */
    private string $content;

    /**
     * Message title
     *
     * @var string
     */
    private string $title;

    /**
     * Bitmessage address to send content to
     *
     * @var string
     */
    private string $address;


    /**
     * Marketplace address
     *
     * @var Repository|mixed
     */
    private mixed $sender;


    private Bitmessage $bitmessage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $title,string $content,string $address)
    {
        $this->content = $content;
        $this->address = $address;
        $this->title = $title;
        $this->sender = config('bitmessage.marketplace_address');
        $this->bitmessage = new Bitmessage(
            config('bitmessage.connection.username'),
            config('bitmessage.connection.password'),
            config('bitmessage.connection.host'),
            config('bitmessage.connection.port')
        );
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle(): void
    {
        if ($this->attempts() <= $this->tries){
            $this->bitmessage->sendMessage($this->address,$this->sender,$this->title,$this->content);
        } else {
            $this->delete();
        }

    }
}
