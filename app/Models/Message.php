<?php

namespace App\Models;

use App\Exceptions\RequestException;
use App\Marketplace\Encryption\Keypair;
use App\Traits\Uuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Random\RandomException;
use SodiumException;

/**
 * @property mixed $conversation_id
 * @property int|mixed $sender_id
 * @property int|mixed $receiver_id
 * @property mixed $created_at
 * @property mixed $content
 * @property string $content_sender
 * @property string $nonce_sender
 * @property string $nonce_receiver
 * @property string $content_receiver
 */
class Message extends Model
{
    use Uuids;
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = ['read'];

    /**
     * Determines if the parameter $message is encrypted
     *
     * @param $message
     * @return bool
     */
    public static function messageEncrypted($message) : bool
    {
        $message = trim($message); // fix for blank chars at the start and end
        $startsWith = "-----BEGIN PGP MESSAGE-----";
        $endsWith = "-----END PGP MESSAGE-----";

        // if the content starts with string and ends with pgp string
        return
            str_starts_with($message, $startsWith)
            &&
            str_ends_with($message, $endsWith);
    }

    /**
     * Generate a Market keypair if it is not generated
     * @throws SodiumException
     */
    public static function generateMarketKeypair(): void
    {
        // create users public and private RSA Keys
        $keyPair = new Keypair();
        $privateKey = $keyPair->getPrivateKey();
        $publicKey =   $keyPair->getPublicKey();
        // encrypt private key with user's password
        $encryptedPrivate = encrypt($privateKey);
        $encryptedPublic = encrypt($publicKey);

        // save to files
        if(!Storage::exists('marketkey.private')
            && !Storage::exists('marketkey.public')) {

            Storage::put('marketkey.private', $encryptedPrivate);
            Storage::put('marketkey.public', $encryptedPublic);
        }
    }

    /**
     * Return an encrypted Market public key
     *
     * @return string|null
     * @throws SodiumException
     */
    public static function getMarketPublicKey(): ?string
    {
        if(!Storage::exists('marketkey.public'))
            self::generateMarketKeypair();
        return Storage::get('marketkey.public');
    }

    /**
     * Return encrypted private key of the market
     *
     * @return string|null
     * @throws SodiumException
     */
    public static function getMarketPrivateKey(): ?string
    {
        if(!Storage::exists('marketkey.private'))
            self::generateMarketKeypair();
        return Storage::get('marketkey.private');
    }

    /**
     * Generate a one-time private key for mass messages
     *
     * @return string
     * @throws SodiumException
     */
    public static function encryptedPrivateKey() : string
    {
        // create users public and private RSA Keys
        $keyPair = new Keypair();
        return $keyPair->getPrivateKey();
    }


    /**
     * Relationship with the User who sends the messages
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this -> hasOne(User::class, 'id', 'sender_id');
    }

    /**
     * Relationship with the Conversation
     *
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this -> belongsTo(Conversation::class, 'conversation_id');
    }

    /**
     * Set the conversation of the message
     *
     * @param Conversation $conversation
     */
    public function setConversation(Conversation $conversation): void
    {
        $this ->conversation_id = $conversation -> id;
    }

    /**
     * Set the user sender of the message
     *
     * @param User $user
     */
    public function setSender(User $user): void
    {
        $this -> sender_id = $user -> id;
    }
    /**
     * Set the user receiver of the message
     *
     * @param User $user
     */
    public function setReceiver(User $user): void
    {
        $this -> receiver_id = $user -> id;
    }

    /**
     * Returns if this message is sent by market
     *
     * @return bool
     */
    public function isMassMessage() : bool
    {
        return $this -> sender_id == null;
    }

    public function getReceiver(): User
    {
        if ($this->receiver_id)
            return User::query()->findOrFail($this->receiver_id);

        // Return a stub user if it is not selected
        return User::stub(); // ← this may be returning null or something unexpected
    }

    public function getSender(): User
    {
        if ($this->sender_id)
            return User::query()->findOrFail($this->sender_id);

        // Return a stub user if it is not selected
        return User::stub(); // ← this may be returning null or something unexpected
    }


    public function getContentSenderAttribute($value){
        return decrypt($value);
    }
    public function getContentReceiverAttribute($value){
        return decrypt($value);
    }
    public function getNonceSenderAttribute($value){
        return decrypt($value);
    }
    public function getNonceReceiverAttribute($value){
        return decrypt($value);
    }

    public function setContentSenderAttribute($value): void
    {
        $this->attributes['content_sender'] = encrypt($value);
    }
    public function setContentReceiverAttribute($value): void
    {
        $this->attributes['content_receiver'] = encrypt($value);
    }
    public function setNonceSenderAttribute($value): void
    {
        $this->attributes['nonce_sender'] = encrypt($value);
    }
    public function setNonceReceiverAttribute($value): void
    {
        $this->attributes['nonce_receiver'] = encrypt($value);
    }

    /**
     * Returned string for time ago
     *
     * @return string
     */
    public function timeAgo(): string
    {
        return Carbon::parse($this -> created_at) -> diffForHumans();
    }

    /**
     * Determines if the message is encrypted
     *
     * @return bool
     *
     */
    public function isEncrypted() : bool
    {
        return self::messageEncrypted($this -> content);
    }


    /**
     * Setting mass message content
     *
     * @param $content
     * @param User $receiver
     * @throws RequestException
     * @throws RandomException
     */
    public function setMassMessageContent($content, User $receiver): void
    {
        try {
            /**
             * Set empty content sender
             */
            $this->content_sender = '';
            $this->nonce_sender = '';
            /**
             * Set content for the receiver
             */
            $private_market_key = decrypt(Message::getMarketPrivateKey());
            $public_key_receiver = decrypt($receiver->msg_public_key);
            $receiver_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
                $private_market_key,
                $public_key_receiver
            );
            $nonce_receiver = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);

            $content_receiver = sodium_crypto_box(
                $content,
                $nonce_receiver,
                $receiver_keypair
            );
            $this->nonce_receiver = $nonce_receiver;
            $this->content_receiver = $content_receiver;
        }
        catch (SodiumException $e){
            \Illuminate\Support\Facades\Log::error($e);
            throw new RequestException('Error with encryption, please try again!');
        }
    }

    /**
     * Setting the content of the message, with encryption
     *
     * @param $content
     * @param User $sender
     * @param User $receiver
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RequestException|RandomException
     */
    public function setContent($content,User $sender ,User $receiver): void
    {
        try{
            $private_key_sender = decrypt(session()->get('private_rsa_key_decrypted'));
            $public_key_sender = decrypt($sender->msg_public_key);
            /**
             * Set content for sender
             */

            $sender_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
                $private_key_sender,
                $public_key_sender
            );
            $nonce_sender = random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
            $content_sender = sodium_crypto_box(
                $content,
                $nonce_sender,
                $sender_keypair
            );
            $this->content_sender = $content_sender;
            $this->nonce_sender = $nonce_sender;
            /**
             * Set content for the receiver
             */
            $public_key_receiver = decrypt($receiver->msg_public_key);
            $receiver_keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
                $private_key_sender,
                $public_key_receiver
            );
            $nonce_receiver= random_bytes(SODIUM_CRYPTO_BOX_NONCEBYTES);
            $content_receiver = sodium_crypto_box(
                $content,
                $nonce_receiver,
                $receiver_keypair
            );
            $this->nonce_receiver = $nonce_receiver;
            $this->content_receiver = $content_receiver;
        }
        catch (SodiumException $e){
            throw new RequestException('Error with encryption, please try again!');
        }
    }

    /**
     * Decrypts content with a key
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws SodiumException
     */
    public function getContent(): string
    {

        $user = auth()->user();
        $user_private_key = decrypt(session()->get('private_rsa_key_decrypted'));

        // get sender check for existing cause sender can be stubbed
        if($this -> getSender() -> id  && $user->id == $this->getSender()->id){
            /**
             * Decrypt sender content and return it
             */
            $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
                $user_private_key,
                decrypt($user->msg_public_key)
            );
            $plaintext = sodium_crypto_box_open(
                $this->content_sender,
                $this->nonce_sender,
                $keypair
            );

            return urldecode($plaintext);

        } else if ($user->id == $this->getReceiver()->id){
            $plaintext = '';
            // normal message
            if($this -> getSender() -> exists) {
                /**
                 * Decrypt receiver content and return it
                 */
                $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
                    $user_private_key,
                    decrypt($this->getSender()->msg_public_key)
                );
                $plaintext = sodium_crypto_box_open(
                    $this->content_receiver,
                    $this->nonce_receiver,
                    $keypair
                );
            }
            else {
                /**
                 * Decrypt receiver content and return it
                 */
                $keypair = sodium_crypto_box_keypair_from_secretkey_and_publickey(
                    $user_private_key,
                    decrypt(self::getMarketPublicKey())
                );
                $plaintext = sodium_crypto_box_open(
                    $this->content_receiver,
                    $this->nonce_receiver,
                    $keypair
                );
            }
            return urldecode($plaintext);
        }
        return '';
    }
}
