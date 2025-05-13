<?php

namespace App\Marketplace\Utility\MoneroRPC;

use App\Marketplace\Utility\MoneroRPC\Exceptions\RuntimeException;
use Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Example;
use PhpOption\None;

class walletRPC
{
    private jsonRPCClient $client;

    private $protocol;
    private $host;
    private $port;
    private string $url;
    private mixed $user;
    private mixed $password;

    /**
     * Start a connection with the Monero wallet RPC interface (monero-wallet-rpc)
     *
     * @param array|string $host monero-wallet-rpc hostname or configuration array
     * @param int $port monero-wallet-rpc port (optional)
     * @param string $protocol monero-wallet-rpc protocol (eg. 'http')  (optional)
     * @param string|null $user monero-wallet-rpc username (optional)
     * @param string|null $password monero-wallet-rpc passphrase (optional)
     * @throws RuntimeException
     */


    function __construct ( array|string $host = '127.0.0.1', int $port = 18083, string $protocol = 'http', string $user = null, string $password = null)
    {
        if (is_array($host)) { // Parameters passed in as object/dictionary
            $params = $host;

            if (array_key_exists('host', $params)) {
                $host = $params['host'];
            } else {
                $host = '127.0.0.1';
            }
            if (array_key_exists('port', $params)) {
                $port = $params['port'];
            }
            if (array_key_exists('protocol', $params)) {
                $protocol = $params['protocol'];
            }
            if (array_key_exists('user', $params)) {
                $user = $params['user'];
            }
            if (array_key_exists('password', $params)) {
                $password = $params['password'];
            }
        }

        $this->host = $host;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->user = $user;
        $this->password = $password;

        $this->url = $protocol.'://'.$host.':'.$port.'/json_rpc';
        $this->client = new jsonRPCClient($this->url, $this->user, $this->password);
    }

    /**
     *
     * Execute command via jsonRPCClient
     *
     * @param string $method RPC method to call
     * @param null $params Parameters to pass (optional)
     *
     * @return string  Call result
     * @throws RuntimeException
     */
    private function _run(string $method, $params = null): string
    {
        return $this->client->_run($method, $params);
    }

    /**
     *
     * Print JSON object (for API)
     *
     * @param object $json  JSON object to print
     *
     * @return void
     *
     */
    public function _print(object $json): void
    {
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     *
     * Convert from moneroj to tacoshi (piconero)
     *
     * @param int $amount
     * @return float|int
     */
    public function _transform(int $amount = 0): float|int
    {
        //
        return $amount * 1000000000000;
    }

    /**
     *
     * Look up an account's balance
     *
     * @param int $account_index Index of an account to look up (optional)
     *
     * @return object|string Example: {
     *   "balance": 140,000,000,000,
     *   "unlocked_balance": 50000000000
     *
     * @throws RuntimeException
     */
    public function get_balance(int $account_index = 0): object|string
    {
        $params = array('account_index' => $account_index);
        return $this->_run('get_balance', $params);
    }

    /**
     *
     * Alias of get_balance()
     *
     * @throws RuntimeException
     */
    public function getbalance($account_index = 0): object|string
    {
        return $this->get_balance($account_index);
    }

    /**
     *
     * Look up wallet address(es)
     *
     * @param int $account_index Index of an account to look up (optional)
     * @param int $address_index Index of subaddress to look up (optional)
     *
     * @return object|string Example: {
     *   "address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATSiHHrDzcSFqHpARF",
     *   "addresses": [
     *     {
     *       "address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATSiHHrDzcSFqHpARF",
     *       "address_index": 0,
     *       "label": "Primary account",
     *       "used": true
     *     }, {
     *       "address": "Bh3ttLbjGFnVGCeGJF1HgVh4DfCaBNpDt7PQAgsC2GFug7WKskgfbTmB6e7UupyiijiHDQPmDC7wSCo9eLoGgbAFJQaAaDS",
     *       "address_index": 1,
     *       "label": "",
     *       "used": true
     *     }
     *   ]
     * }
     * @throws RuntimeException
     */
    public function get_address(int $account_index = 0, int $address_index = 0): object|string
    {
        $params = array('account_index' => $account_index, 'address_index' => $address_index);
        return $this->_run('get_address', $params);
    }

    /**
     *
     * Alias of get_address()
     *
     * @param int $account_index Index of an account to look up (optional)
     * @param int $address_index Index of subaddress to look up (optional)
     *
     * @return object|string Example: {
     *   "address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
     * @throws RuntimeException
     */
    public function getaddress(int $account_index = 0, int $address_index = 0): object|string
    {
        return $this->get_address($account_index = 0, $address_index = 0);
    }

    /**
     *
     * Create a new subaddress
     *
     * @param int $account_index The subaddress account index
     * @param string $label A label to apply to the new subaddress
     *
     * @return object|string Example: {
     *   "address": "Bh3ttLbjGFnVGCeGJF1HgVh4DfCaBNpDt7PQAgsC2GFug7WKskgfbTmB6e7UupyiijiHDQPmDC7wSCo9eLoGgbAFJQaAaDS"
     *   "address_index": 1
     * }
     * @throws RuntimeException
     */
    public function create_address(int $account_index = 0, string $label = ''): object|string
    {
        $params = array('account_index' => $account_index, 'label' => $label);
        $create_address_method = $this->_run('create_address', $params);

        $save = $this->store(); // Save wallet state after subaddress creation

        return $create_address_method;
    }

    /**
     *
     * Label a subaddress
     *
     * @param number $index The index of the subaddress to label
     * @param $label
     * @return null|string
     * @throws RuntimeException
     */
    public function label_address($index, $label): null|string
    {
        $params = array('index' => $index ,'label' => $label);
        return $this->_run('label_address', $params);
    }

    /**
     *
     * Look up wallet accounts
     *
     * @return object|string Example: {
     *   "subaddress_accounts": {
     *     "0": {
     *       "account_index": 0,
     *       "balance": 2808597352948771,
     *       "base_address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATSiHHrDzcSFqHpARF",
     *       "label": "Primary account",
     *       "tag": "",
     *       "unlocked_balance": 2717153096298162
     *     },
     *     "1": {
     *       "account_index": 1,
     *       "balance": 0,
     *       "base_address": "BcXKsfrvffKYVoNGN4HUFfaruAMRdk5DrLZDmJBnYgXrTFrXyudn81xMj7rsmU5P9dX56kRZGqSaigUxUYoaFETo9gfDKx5",
     *       "label": "Secondary account",
     *       "tag": "",
     *       "unlocked_balance": 0
     *    },
     *    "total_balance": 2808597352948771,
     *    "total_unlocked_balance": 2717153096298162
     * }
     * @throws RuntimeException
     */
    public function get_accounts(): object|string
    {
        return $this->_run('get_accounts');
    }

    /**
     *
     * Create a new account
     *
     * @param string $label Label to apply to newaccount
     *
     * @return string|null
     * @throws RuntimeException
     */
    public function create_account(string $label = ''): null|string
    {
        $params = array('label' => $label);
        $create_account_method = $this->_run('create_account', $params);

        $save = $this->store(); // Save wallet state after account creation

        return $create_account_method;
    }

    /**
     *
     * Label an account
     *
     * @param number $account_index Index of an account to label
     * @param string $label Label to apply
     *
     * @return null|string
     * @throws RuntimeException
     */
    public function label_account($account_index, string $label): null|string
    {
        $params = array('account_index' => $account_index, 'label' => $label);
        $label_account_method = $this->_run('label_account', $params);

        $save = $this->store(); // Save wallet state after the account label

        return $label_account_method;
    }

    /**
     *
     * Look up account tags
     *
     * @return object|string Example: {
     *   "account_tags": {
     *     "0": {
     *       "accounts": {
     *         "0": 0,
     *         "1": 1
     *       },
     *       "label": "",
     *       "tag": "Example tag"
     *     }
     *   }
     *
     * @throws RuntimeException
     */
    public function get_account_tags(): object|string
    {
        return $this->_run('get_account_tags');
    }

    /**
     *
     * Tag accounts
     *
     * @param array $accounts The indices of the accounts to tag
     * @param string $tag Tag to apply
     *
     * @return string|null
     * @throws RuntimeException
     */
    public function tag_accounts(array $accounts, string $tag): null|string
    {
        $params = array('accounts' => $accounts, 'tag' => $tag);
        $tag_accounts_method = $this->_run('tag_accounts', $params);

        $save = $this->store(); // Save wallet state after account tagging

        return $tag_accounts_method;
    }

    /**
     *
     * Untag accounts
     *
     * @param array $accounts The indices of the accounts to untag
     *
     * @return string|null
     * @throws RuntimeException
     */
    public function untag_accounts(array $accounts): null|string
    {
        $params = array('accounts' => $accounts);
        $untag_accounts_method = $this->_run('untag_accounts', $params);

        $save = $this->store(); // Save wallet state after untagging accounts

        return $untag_accounts_method;
    }

    /**
     *
     * Describe a tag
     *
     * @param string $tag Tag to describe
     * @param string $description Description to apply to tag
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function set_account_tag_description(string $tag, string $description): object|string
    {
        $params = array('tag' => $tag, 'description' => $description);
        $set_account_tag_description_method = $this->_run('set_account_tag_description', $params);

        $save = $this->store(); // Save wallet state after describing a tag

        return $set_account_tag_description_method;
    }

    /**
     *
     * Look up how many blocks are in the longest chain known to the wallet
     *
     * @return object|string Example: {
     *   "height": 994,310
     *
     * @throws RuntimeException
     */
    public function get_height(): object|string
    {
        return $this->_run('get_height');
    }

    /**
     *
     * Alias of get_height()
     *
     * @throws RuntimeException
     */
    public function getheight(): object|string
    {
        return $this->get_height();
    }

    /**
     *
     * Send it monero
     * Parameters can be passed in individually (as listed below) or as an object/dictionary (as listed at bottom)
     * To send to multiple recipients, use the object/dictionary (bottom) format and pass an array of recipient addresses and amount arrays in the destination field (as in "destinations = [['amount' => 1, 'address' => ...], ['amount' => 2, 'address' => ...]]")
     *
     * @param string $amount Amount of monero to send
     * @param string $address Address to receive funds
     * @param string $payment_id Payment ID (optional)
     * @param int $mixin Mixin number (ringsize - 1)(optional)
     * @param int $account_index Account to send from (optional)
     * @param string $subaddr_indices Comma-separated list of subaddress indices to spend from (optional)
     * @param int $priority Transaction priority (optional) * @paramint$unlock_time UNIX time or block height to unlock output (optional)
     * @param int $unlock_time
     * @param boolean $do_not_relay Do not relay transaction (optional)
     *
     *   OR
     *
     * @return object|string Example: {
     *   "amount": "1000000000000",
     *   "fee": "1000020000",
     *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
     *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
     *
     * @throws RuntimeException
     * @throws Exception
     */
    public function transfer(mixed $amount, string $address = '', string $payment_id = '', int $mixin = 6, int $account_index = 0, string $subaddr_indices = '', int $priority = 2, int $unlock_time = 0, bool $do_not_relay = false): object|string
    {
        if (is_array($amount)) { // Parameters passed in as object/dictionary
            $params = $amount;

            if (array_key_exists('destinations', $params)) {
                $destinations = $params['destinations'];

                if (!is_array($destinations)) {
                    throw new Exception('Error: destinations must be an array');
                }

                foreach ($destinations as $destination) {
                    if (array_key_exists('amount', $destinations[$destination])) {
                        $destinations[$destination]['amount'] = $this->_transform($destinations[$destination]['amount']);
                    } else {
                        throw new Exception('Error: Amount required');
                    }
                    if (!array_key_exists('address', $destinations[$destination])) {
                        throw new Exception('Error: Address required');
                    }
                }
            } else {
                if (array_key_exists('amount', $params)) {
                    $amount = $params['amount'];
                } else {
                    throw new Exception('Error: Amount required');
                }
                if (array_key_exists('address', $params)) {
                    $address = $params['address'];
                } else {
                    throw new Exception('Error: Address required');
                }
                $destinations = array(array('amount' => $this->_transform($amount), 'address' => $address));
            }
            if (array_key_exists('payment_id', $params)) {
                $payment_id = $params['payment_id'];
            }
            if (array_key_exists('mixin', $params)) {
                $mixin = $params['mixin'];
            }
            if (array_key_exists('account_index', $params)) {
                $account_index = $params['account_index'];
            }
            if (array_key_exists('subaddr_indices', $params)) {
                $subaddr_indices = $params['subaddr_indices'];
            }
            if (array_key_exists('priority', $params)) {
                $priority = $params['priority'];
            }
            if (array_key_exists('unlock_time', $params)) {
                $unlock_time = $params['unlock_time'];
            }
            if (array_key_exists('do_not_relay', $params)) {
                $do_not_relay = $params['do_not_relay'];
            }
        } else { // Legacy parameters used
            $destinations = array(array('amount' => $this->_transform($amount), 'address' => $address));
        }

        $params = array('destinations' => $destinations, 'mixin' => $mixin, 'get_tx_key' => true, 'payment_id' => $payment_id, 'account_index' => $account_index, 'subaddr_indices' => $subaddr_indices, 'priority' => $priority, 'do_not_relay' => $do_not_relay);
        $transfer_method = $this->_run('transfer', $params);

        $save = $this->store(); // Save wallet state after transfer

        return $transfer_method;
    }

    /**
     *
     * Same as transfer, but splits transfer into more than one transaction if necessary
     *
     * @throws Exception
     */
    public function transfer_split($amount, $address = '', $payment_id = '', $mixin = 6, $account_index = 0, $subaddr_indices = '', $priority = 2, $unlock_time = 0, $do_not_relay = false): string
    {
        if (is_array($amount)) { // Parameters passed in as object/dictionary
            $params = $amount;

            if (array_key_exists('destinations', $params)) {
                $destinations = $params['destinations'];

                if (!is_array($destinations)) {
                    throw new Exception('Error: destinations must be an array');
                }

                foreach ($destinations as $destination) {
                    if (array_key_exists('amount', $destinations[$destination])) {
                        $destinations[$destination]['amount'] = $this->_transform($destinations[$destination]['amount']);
                    } else {
                        throw new Exception('Error: Amount required');
                    }
                    if (!array_key_exists('address', $destinations[$destination])) {
                        throw new Exception('Error: Address required');
                    }
                }
            } else {
                if (array_key_exists('amount', $params)) {
                    $amount = $params['amount'];
                } else {
                    throw new Exception('Error: Amount required');
                }
                if (array_key_exists('address', $params)) {
                    $address = $params['address'];
                } else {
                    throw new Exception('Error: Address required');
                }
                $destinations = array(array('amount' => $this->_transform($amount), 'address' => $address));
            }
            if (array_key_exists('mixin', $params)) {
                $mixin = $params['mixin'];
            }
            if (array_key_exists('payment_id', $params)) {
                $payment_id = $params['payment_id'];
            }
            if (array_key_exists('account_index', $params)) {
                $account_index = $params['account_index'];
            }
            if (array_key_exists('subaddr_indices', $params)) {
                $subaddr_indices = $params['subaddr_indices'];
            }
            if (array_key_exists('priority', $params)) {
                $priority = $params['priority'];
            }
            if (array_key_exists('unlock_time', $params)) {
                $unlock_time = $params['unlock_time'];
            }
            if (array_key_exists('do_not_relay', $params)) {
                $do_not_relay = $params['do_not_relay'];
            }
        } else { // Legacy parameters used
            $destinations = array(array('amount' => $this->_transform($amount), 'address' => $address));
        }

        $params = array('destinations' => $destinations, 'mixin' => $mixin, 'get_tx_key' => true, 'account_index' => $account_index, 'subaddr_indices' => $subaddr_indices, 'payment_id' => $payment_id, 'priority' => $priority, 'unlock_time' => $unlock_time, 'do_not_relay' => $do_not_relay);
        $transfer_method = $this->_run('transfer_split', $params);

        $save = $this->store(); // Save wallet state after transfer

        return $transfer_method;
    }

    /**
     *
     * Send all dust outputs back to the wallet
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function sweep_dust(): object|string
    {
        return $this->_run('sweep_dust');
    }

    /**
     *
     * Send all unmixable outputs back to the wallet
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function sweep_unmixable(): object|string
    {
        return $this->_run('sweep_unmixable');
    }

    /**
     *
     * Send all unlocked outputs from an account to an address
     *
     * @param string $address Address to receive funds
     * @param string $subaddr_indices Comma-separated list of subaddress indices to sweep(optional)
     * @param int $account_index Index of the account to sweep(optional)
     * @param string $payment_id Payment ID(optional)
     * @param int $mixin Mixin number (ringsize - 1)(optional)
     * @param int $priority Payment ID(optional)
     * @param int $below_amount Only send outputs below this amount(optional)
     * @param int $unlock_time UNIX time or block height to unlock output(optional)
     * @param boolean $do_not_relay Do not relay transaction(optional)
     *
     *   OR
     *
     * @return object|string Example: {
     *   "amount": "1000000000000",
     *   "fee": "1000020000",
     *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
     *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
     * }
     * @throws RuntimeException
     * @throws Exception
     */
    public function sweep_all(mixed $address, string $subaddr_indices = '', int $account_index = 0, string $payment_id = '', int $mixin = 6, int $priority = 2, int $below_amount = 0, int $unlock_time = 0, bool $do_not_relay = false): object|string
    {
        if (is_array($address)) { // Parameters passed in as object/dictionary
            $params = $address;

            if (array_key_exists('address', $params)) {
                $address = $params['address'];
            } else {
                throw new Exception('Error: Address required');
            }
            if (array_key_exists('subaddr_indices', $params)) {
                $subaddr_indices = $params['subaddr_indices'];
            }
            if (array_key_exists('account_index', $params)) {
                $account_index = $params['account_index'];
            }
            if (array_key_exists('payment_id', $params)) {
                $payment_id = $params['payment_id'];
            }
            if (array_key_exists('mixin', $params)) {
                $mixin = $params['mixin'];
            }
            if (array_key_exists('priority', $params)) {
                $priority = $params['priority'];
            }
            if (array_key_exists('below_amount', $params)) {
                $below_amount = $params['below_amount'];
            }
            if (array_key_exists('unlock_time', $params)) {
                $unlock_time = $params['unlock_time'];
            }
            if (array_key_exists('do_not_relay', $params)) {
                $do_not_relay = $params['do_not_relay'];
            }
        }

        $params = array('address' => $address, 'mixin' => $mixin, 'get_tx_key' => true, 'subaddr_indices' => $subaddr_indices, 'account_index' => $account_index, 'payment_id' => $payment_id, 'priority' => $priority, 'below_amount' => $this->_transform($below_amount), 'unlock_time' => $unlock_time, 'do_not_relay' => $do_not_relay);
        $sweep_all_method = $this->_run('sweep_all', $params);

        $save = $this->store(); // Save wallet state after transfer

        return $sweep_all_method;
    }

    /**
     *
     * Sweep a single key image to an address
     *
     * @param array|string $key_image Key image to sweep
     * @param string $address Address to receive funds
     * @param string $payment_id Payment ID(optional)
     * @param int $mixin Mixin number (ringsize - 1)(optional)
     * @param int $priority Payment ID(optional)
     * @param int $below_amount Only send outputs below this amount(optional)
     * @param int $unlock_time UNIX time or block height to unlock output(optional)
     * @param int $do_not_relay Do not relay transaction(optional)
     *
     *   OR
     *
     * @return object|string Example: {
     *   "amount": "1000000000000",
     *   "fee": "1000020000",
     *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
     *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
     *
     * @throws RuntimeException
     * @throws Exception
     */
    public function sweep_single(array|string $key_image, string $address, string $payment_id = '', int $mixin = 6, int $priority = 2, int $below_amount = 0, int $unlock_time = 0, int $do_not_relay = 0): object|string
    {
        if (is_array($key_image)) { // Parameters passed in as object/dictionary
            $params = $key_image;

            if (array_key_exists('key_image', $params)) {
                $key_image = $params['key_image'];
            } else {
                throw new Exception('Error: Key image required');
            }
            if (array_key_exists('address', $params)) {
                $address = $params['address'];
            } else {
                throw new Exception('Error: Address required');
            }

            if (array_key_exists('payment_id', $params)) {
                $payment_id = $params['payment_id'];
            }
            if (array_key_exists('mixin', $params)) {
                $mixin = $params['mixin'];
            }
            if (array_key_exists('account_index', $params)) {
                $account_index = $params['account_index'];
            }
            if (array_key_exists('priority', $params)) {
                $priority = $params['priority'];
            }
            if (array_key_exists('unlock_time', $params)) {
                $unlock_time = $params['unlock_time'];
            }
            if (array_key_exists('unlock_time', $params)) {
                $unlock_time = $params['unlock_time'];
            }
            if (array_key_exists('below_amount', $params)) {
                $below_amount = $params['below_amount'];
            }
            if (array_key_exists('do_not_relay', $params)) {
                $do_not_relay = $params['do_not_relay'];
            }
        }

        $params = array('address' => $address, 'mixin' => $mixin, 'get_tx_key' => true, 'account_index' => $account_index, 'payment_id' => $payment_id, 'priority' => $priority, 'below_amount' => $this->_transform($below_amount), 'unlock_time' => $unlock_time, 'do_not_relay' => $do_not_relay);
        $sweep_single_method = $this->_run('sweep_single', $params);

        $save = $this->store(); // Save wallet state after transfer

        return $sweep_single_method;
    }

    /**
     *
     * Relay a transaction
     *
     * @param string $hex Blob of transaction to relay
     *
     * @return object|string // TODO example
     *
     * @throws RuntimeException
     */
    public function relay_tx(string $hex): object|string
    {
        $params = array('hex' => $hex);
        $relay_tx_method = $this->_run('relay_tx_method', $params);

        $save = $this->store(); // Save wallet state after transaction relay

        return $this->_run('relay_tx');
    }

    /**
     *
     * Save wallet
     *
     * @return object|string Example:
     * @throws RuntimeException
     */
    public function store(): object|string
    {
        return $this->_run('store');
    }

    /**
     *
     * Look up incoming payments by payment ID
     *
     * @param string $payment_id Payment ID to look up
     *
     * @return object|string Example: {
     *   "payments": [{
     *     "amount": 10,350,000,000,000,
     *     "block_height": 994327,
     *     "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "unlock_time": 0
     *   }]
     *
     * @throws RuntimeException
     */
    public function get_payments(string $payment_id): object|string
    {
        // $params = array('payment_id' => $payment_id); // does not work
        $params = [];
        $params['payment_id'] = $payment_id;
        return $this->_run('get_payments', $params);
    }

    /**
     *
     * Look up incoming payments by payment ID (or a list of payment IDs) from a given height
     *
     * @param array|string $payment_ids Array of payment IDs to look up
     * @param string $min_block_height Height to begin search
     *
     * @return object|string Example: {
     *   "payments": [{
     *     "amount": 10,350,000,000,000,
     *     "block_height": 994327,
     *     "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "unlock_time": 0
     *   }]
     *
     * @throws RuntimeException
     * @throws Exception
     */
    public function get_bulk_payments(array|string $payment_ids, string $min_block_height): object|string
    {
        // $params = array('payment_ids' => $payment_ids, 'min_block_height' => $min_block_height); // does not work
        $params = array('min_block_height' => $min_block_height); // does not work
        $params = [];
        if (!is_array($payment_ids)) {
            throw new Exception('Error: Payment IDs must be array.');
        }
        if ($payment_ids) {
            $params['payment_ids'] = [];
            foreach ($payment_ids as $payment_id) {
                $params['payment_ids'][] = $payment_id;
            }
        }
        return $this->_run('get_bulk_payments', $params);
    }

    /**
     *
     * Look up incoming transfers
     *
     * @param string $type Type of transfer to look up; must be 'all', 'available', or 'unavailable' (incoming transfers which have already been spent)
     * @param int $account_index Index of an account to look up(optional)
     * @param string $subaddr_indices Comma-separated list of subaddress indices to look up(optional)
     *
     * @return object|string Example: {
     *   "transfers": [{
     *     "amount": 10000000000000,
     *     "global_index": 711506,
     *     "spent": false,
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "tx_size": 5870
     *   },{
     *     "amount": 300000000000,
     *     "global_index": 794232,
     *     "spent": false,
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "tx_size": 5870
     *   },{
     *     "amount": 50000000000,
     *     "global_index": 213659,
     *     "spent": false,
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "tx_size": 5870
     *   }]
     *
     * @throws RuntimeException
     */
    public function incoming_transfers(string $type = 'all', int $account_index = 0, string $subaddr_indices = ''): object|string
    {
        $params = array('transfer_type' => $type, 'account_index' => $account_index, 'subaddr_indices' => $subaddr_indices);
        return $this->_run('incoming_transfers', $params);
    }

    /**
     *
     * Look up a wallet key
     *
     * @param string $key_type Type of key to look up; must be 'view_key', 'spend_key', or 'mnemonic'
     *
     * @return object|string Example: {
     *   "key": "7e341d..."
     *
     *
     * @throws RuntimeException
     */
    public function query_key(string $key_type): object|string
    {
        $params = array('key_type' => $key_type);
        return $this->_run('query_key', $params);
    }

    /**
     *
     * Look up the wallet view key
     *
     * @return object|string Example: {
     *   "key": "7e341d..."
     *
     *
     * @throws RuntimeException
     */
    public function view_key(): object|string
    {
        $params = array('key_type' => 'view_key');
        return $this->_run('query_key', $params);
    }

    /**
     *
     * Look up wallet spend key
     *
     *
     *
     * @return object|string Example: {
     *   "key": "2ab810..."
     *
     *
     * @throws RuntimeException
     */
    public function spend_key(): object|string
    {
        $params = array('key_type' => 'spend_key');
        return $this->_run('query_key', $params);
    }

    /**
     *
     * Look up wallet mnemonic seed
     *
     *
     *
     * @return object|string Example: {
     *   "key": "2ab810..."
     *
     *
     * @throws RuntimeException
     */
    public function mnemonic(): object|string
    {
        $params = array('key_type' => 'mnemonic');
        return $this->_run('query_key', $params);
    }

    /**
     *
     * Create an integrated address from a given payment ID
     *
     * @param null $payment_id Payment ID(optional)
     *
     * @return object|string Example: {
     *   "integrated_address": "4BpEv3WrufwXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQQ8H2RRJveAtUeiFs6J"
     *
     *
     * @throws RuntimeException
     */
    public function make_integrated_address($payment_id = null): object|string
    {
        $params = array('payment_id' => $payment_id);
        return $this->_run('make_integrated_address', $params);
    }

    /**
     *
     * Look up the wallet address and payment ID corresponding to an integrated address
     *
     * @param string $integrated_address Integrated address to split
     *
     * @return object|string Example: {
     *   "payment_id": "420fa29b2d9a49f5",
     *   "standard_address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
     *
     *
     * @throws RuntimeException
     */
    public function split_integrated_address(string $integrated_address): object|string
    {
        $params = array('integrated_address' => $integrated_address);
        return $this->_run('split_integrated_address', $params);
    }

    /**
     *
     * Stop the wallet, saving the state
     *
     *
     *
     *
     *
     * @throws RuntimeException
     */
    public function stop_wallet(): null|string
    {
        return $this->_run('stop_wallet');
    }

    /**
     *
     * Rescan the blockchain from scratch
     *
     *
     *
     *
     *
     * @throws RuntimeException
     */

    public function rescan_blockchain(): null|string
    {
        return $this->_run('rescan_blockchain');
    }

    /**
     *
     * Add notes to transactions
     *
     * @param array $txids Array of transaction IDs to note
     * @param array $notes Array of notes (strings) to add
     *
     *
     *
     * @throws RuntimeException
     */
    public function set_tx_notes(array $txids, array $notes):null|string
    {
        $params = array('txids' => $txids, 'notes' => $notes);
        return $this->_run('set_tx_notes', $params);
    }

    /**
     *
     * Look up the transaction note
     *
     * @param array $txids Array of transaction IDs (strings) to look up
     *
     *
     *
     *
     * @throws RuntimeException
     */
    public function get_tx_notes(array $txids): string
    {
        $params = array('txids' => $txids);
        return $this->_run('get_tx_notes', $params);
    }

    /**
     *
     * Set a wallet option
     *
     * @param string $key Option to set
     * @param string $value Value to set
     *
     * @return string|null
     *
     * @throws RuntimeException
     */
    public function set_attribute(string $key, string $value): null|string
    {
        $params = array('key' => $key, 'value' => $value);
        return $this->_run('set_attribute', $params);
    }

    /**
     *
     * Look up a wallet option
     *
     * @param string $key Wallet option to query
     *
     * @return object|string Example: {
     *   // TODO example
     *
     *
     * @throws RuntimeException
     */
    public function get_attribute(string $key): object|string
    {
        $params = array('key' => $key);
        return $this->_run('get_attribute', $params);
    }

    /**
     *
     * Look up a transaction key
     *
     * @param string $txid Transaction ID to look up
     *
     * @return object|string Example: {
     *   "tx_key": "e8e97866b1606bd87178eada8f995bf96d2af3fec5db0bc570a451ab1d589b0f"
     *
     *
     * @throws RuntimeException
     */
    public function get_tx_key(string $txid): object|string
    {
        $params = array('txid' => $txid);
        return $this->_run('get_tx_key', $params);
    }

    /**
     *
     * Check a transaction key
     *
     * @param string $address Address that sent transaction
     * @param string $txid Transaction ID
     * @param string $tx_key Transaction key
     *
     * @return object|string Example: {
     *   "confirmations": 1,
     *   "in_pool":
     *   "received": 0
     *
     *
     * @throws RuntimeException
     */
    public function check_tx_key(string $address, string $txid, string $tx_key): object|string
    {
        $params = array('address' => $address, 'txid' => $txid, 'tx_key' => $tx_key);
        return $this->_run('check_tx_key', $params);
    }

    /**
     *
     * Create proof (signature) of transaction
     *
     * @param string $address Address that spent funds
     * @param string $txid Transaction ID
     *
     * @return object|string Example: {
     *   "signature": "InProofV1Lq4nejMXxMnAdnLeZhHe3FGCmFdnSvzVM1AiGcXjngTRi4hfHPcDL9D4th7KUuvF9ZHnzCDXysNBhfy7gFvUfSbQWiqWtzbs35yUSmtW8orRZzJpYKNjxtzfqGthy1U3puiF"
     *
     *
     * @throws RuntimeException
     */
    public function get_tx_proof(string $address, string $txid): object|string
    {
        $params = array('address' => $address, 'txid' => $txid);
        return $this->_run('get_tx_proof', $params);
    }

    /**
     *
     * Verify transaction proof
     *
     * @param string $address Address that spent funds
     * @param string $txid Transaction ID
     * @param string $signature Signature (tx_proof)
     *
     * @return string|Example : {
     *   "confirmations": 2,
     *   "good": 1,
     *   "in_pool":
     *   "received": 15752471409492,
     *
     *
     * @throws RuntimeException
     */
    public function check_tx_proof(string $address, string $txid, string $signature): string|Example
    {
        $params = array('address' => $address, 'txid' => $txid, 'signature' => $signature);
        return $this->_run('check_tx_proof', $params);
    }

    /**
     *
     * Create proof of a spend
     *
     * @param string $txid Transaction ID
     * @param null $message
     * @return object|string Example: {
     *   "signature": "SpendProofV1RnP6ywcDQHuQTBzXEMiHKbe5ErzRAjpUB1h4RUMfGPNv4bbR6V7EFyiYkCrURwbbrYWWxa6Kb38ZWWYTQhr2Y1cRHVoDBkK9GzBbikj6c8GWyKbu3RKi9hoYp2fA9zze7UEdeNrYrJ3tkoE6mkR3Lk5HP6X2ixnjhUTG65EzJgfCS4qZ85oGkd17UWgQo6fKRC2GRgisER8HiNwsqZdUTM313RmdUX7AYaTUNyhdhTinVLuaEw83L6hNHANb3aQds5CwdKCUQu4pkt5zn9K66z16QGDAXqL6ttHK6K9TmDHF17SGNQVPHzffENLGUf7MXqS3Pb6eijeYirFDxmisZc1n2mh6d5EW8ugyHGfNvbLEd2vjVPDk8zZYYr7NyJ8JjaHhDmDWeLYy27afXC5HyWgJH5nDyCBptoCxxDnyRuAnNddBnLsZZES399zJBYHkGb197ZJm85TV8SRC6cuYB4MdphsFdvSzygnjFtbAcZWHy62Py3QCTVhrwdUomAkeNByM8Ygc1cg245Se1V2XjaUyXuAFjj8nmDNoZG7VDxaD2GT9dXDaPd5dimCpbeDJEVoJXkeEFsZF85WwNcd67D4s5dWySFyS8RbsEnNA5UmoF3wUstZ2TtsUhiaeXmPwjNvnyLif3ASBmFTDDu2ZEsShLdddiydJcsYFJUrN8L37dyxENJN41RnmEf1FaszBHYW1HW13bUfiSrQ9sLLtqcawHAbZWnq4ZQLkCuomHaXTRNfg63hWzMjdNrQ2wrETxyXEwSRaodLmSVBn5wTFVzJe5LfSFHMx1FY1xf8kgXVGafGcijY2hg1yw8ru9wvyba9kdr16Lxfip5RJGFkiBDANqZCBkgYcKUcTaRc1aSwHEJ5m8umpFwEY2JtakvNMnShjURRA3yr7GDHKkCRTSzguYEgiFXdEiq55d6BXDfMaKNTNZzTdJXYZ9A2j6G9gRXksYKAVSDgfWVpM5FaZNRANvaJRguQyqWRRZ1gQdHgN4DqmQ589GPmStrdfoGEhk1LnfDZVwkhvDoYfiLwk9Z2JvZ4ZF4TojUupFQyvsUb5VPz2KNSzFi5wYp1pqGHKv7psYCCodWdte1waaWgKxDken44AB4k6wg2V8y1vG7Nd4hrfkvV4Y6YBhn6i45jdiQddEo5Hj2866MWNsdpmbuith7gmTmfat77Dh68GrRukSWKetPBLw7Soh2PygGU5zWEtgaX5g79FdGZg"
     *
     *
     * @throws RuntimeException
     */
    public function get_spend_proof(string $txid, $message=null): object|string
    {
        $params = array('txid' => $txid);
        if( $message !== null ) {
            $params['message'] = $message;
        }
        return $this->_run('get_spend_proof', $params);
    }

    /**
     *
     * Verify spend proof
     *
     * @param string $txid Transaction ID
     * @param string $signature Spend proof to verify
     * @param null $message
     * @return object|string Example: {
     *   "good": 1
     *
     *
     * @throws RuntimeException
     */
    public function check_spend_proof(string $txid, string $signature, $message=null): object|string
    {
        $params = array('txid' => $txid, 'signature' => $signature);
        if( $message !== null ) {
            $params['message'] = $message;
        }
        return $this->_run('check_spend_proof', $params);
    }

    /**
     *
     * Create proof of reserves
     *
     * @param string $account_index Comma-separated list of account indices of which to prove reserves (proves reserve of all accounts if empty)(optional)
     *
     * @return Example|string : {
     *   "signature": "ReserveProofV11BZ23sBt9sZJeGccf84mzyAmNCP3KzYbE111111111111AjsVgKzau88VxXVGACbYgPVrDGC84vBU61Gmm2eiYxdZULAE4yzBxT1D9epWgCT7qiHFvFMbdChf3CpR2YsZj8CEhp8qDbitsfdy7iBdK6d5pPUiMEwCNsCGDp8AiAc6sLRiuTsLEJcfPYEKe"
     *
     *
     * @throws RuntimeException
     */
    public function get_reserve_proof(string $account_index = 'all'): Example|string
    {
        if ($account_index == 'all') {
            $params = array('all' => true);
        } else {
            $params = array('account_index' => $account_index);
        }

        return $this->_run('get_reserve_proof');
    }

    /**
     *
     * Verify a reserve proof
     *
     * @param string $address Wallet address
     * @param string $signature Reserve proof
     *
     * @return object|string Example: {
     *   "good": 1,
     *   "spent": 0,
     *   "total": 0
     *
     *
     * @throws RuntimeException
     */
    public function check_reserve_proof(string $address, string $signature): object|string
    {
        $params = array('address' => $address, 'signature' => $signature);
        return $this->_run('check_reserve_proof', $params);
    }

    /**
     *
     * Look up transfers
     *
     * @param array $input_types Array of transfer type strings; possible values include 'all', 'in', 'out', 'pending', 'failed', and 'pool' (optional)
     * @param int $account_index Index of an account to look up (optional)
     * @param string $subaddr_indices Comma-separated list of subaddress indices to look up (optional)
     * @param int $min_height Minimum block height to use when looking up transfers (optional)
     * @param int $max_height Maximum block height to use when looking up transfers (optional)
     *
     *   OR
     *
     * @return object|string Example: {
     *   "pool": [{
     *     "amount": 500000000000,
     *     "fee": 0,
     *     "height": 0,
     *     "note": "",
     *     "payment_id": "758d9b225fda7b7f",
     *     "timestamp": 1488312467,
     *     "txid": "da7301d5423efa09fabacb720002e978d114ff2db6a1546f8b820644a1b96208",
     *     "type": "pool"
     *   }]
     *
     *
     * @throws RuntimeException
     */
    public function get_transfers(array $input_types = ['all'], int $account_index = 0, string $subaddr_indices = '', int $min_height = 0, int $max_height = 4206931337): object|string
    {
        if (is_string($input_types)) { // If a user is using old method
            $params = array('subaddr_indices' => $subaddr_indices, 'min_height' => $min_height, 'max_height' => $max_height);
            if (is_bool($account_index)) { // If the user passed e.g., get_transfers('in', true)
                $params['account_index'] = 0;
                $params[$input_types] = $account_index; // $params = array($input_type => $input_value);
            } else { // If user passed eg. get_transfers('in')
                $params['account_index'] = $account_index;
                $params[$input_types] = true;
            }
        } else {
            if (is_object($input_types)) { // Parameters passed in as object/dictionary
                $params = $input_types;

                if (array_key_exists('input_types', $params)) {
                    $input_types = $params['input_types'];
                } else {
                    $input_types = ['all'];
                }
                if (array_key_exists('account_index', $params)) {
                    $account_index = $params['account_index'];
                }
                if (array_key_exists('subaddr_indices', $params)) {
                    $subaddr_indices = $params['subaddr_indices'];
                }
                if (array_key_exists('min_height', $params)) {
                    $min_height = $params['min_height'];
                }
                if (array_key_exists('max_height', $params)) {
                    $max_height = $params['max_height'];
                }
            }

            $params = array('account_index' => $account_index, 'subaddr_indices' => $subaddr_indices, 'min_height' => $min_height, 'max_height' => $max_height);
            for ($i = 0; $i < count($input_types); $i++) {
                $params[$input_types[$i]] = true;
            }
        }

        if (array_key_exists('all', $params)) {
            unset($params['all']);
            $params['in'] = true;
            $params['out'] = true;
            $params['pending'] = true;
            $params['failed'] = true;
            $params['pool'] = true;
        }

        if (($min_height || $max_height) && $max_height != 4206931337) {
            $params['filter_by_height'] = true;
        }

        return $this->_run('get_transfers', $params);
    }

    /**
     *
     * Look up transaction by transaction ID
     *
     * @param string $txid Transaction ID to look up
     * @param int|string $account_index Index of an account to query (optional)
     *
     * @return object|string Example: {
     *   "transfer": {
     *     "amount": 10000000000000,
     *     "fee": 0,
     *     "height": 1316388,
     *     "note": "",
     *     "payment_id": "0000000000000000",
     *     "timestamp": 1495539310,
     *     "txid": "f2d33ba969a09941c6671e6dfe7e9456e5f686eca72c1a94a3e63ac6d7f27baf",
     *     "type": "in"
     *   }
     *
     *
     * @throws RuntimeException
     */
    public function get_transfer_by_txid(string $txid, int|string $account_index = 0): object|string
    {
        $params = array('txid' => $txid, 'account_index' => $account_index);
        return $this->_run('get_transfer_by_txid', $params);
    }

    /**
     *
     * Sign a string
     *
     * @param string $data Data to sign
     *
     * @return object|string Example: {
     *   "signature": "SigV1Xp61ZkGguxSCHpkYEVw9eaWfRfSoAf36PCsSCApx4DUrKWHEqM9CdNwjeuhJii6LHDVDFxvTPijFsj3L8NDQp1TV"
     * }
     *
     * @throws RuntimeException
     */
    public function sign(string $data): object|string
    {
        $params = array('string' => $data);
        return $this->_run('sign', $params);
    }

    /**
     *
     * Verify a signature
     *
     * @param string $data Signed data
     * @param string $address Address that signed data
     * @param string $signature Signature to verify
     *
     * @return object|string Example: {
     *   "good": true
     *
     *
     * @throws RuntimeException
     */
    public function verify(string $data, string $address, string $signature): object|string
    {
        $params = array('data' => $data, 'address' => $address, 'signature' => $signature);
        return $this->_run('verify', $params);
    }

    /**
     *
     * Export an array of signed key images
     *
     * @return array|string Example: {
     *   // TODO example
     * }
     *
     * @throws RuntimeException
     */
    public function export_key_images(): array|string
    {
        return $this->_run('export_key_images');
    }

    /**
     *
     * Import a signed set of key images
     *
     * @param array $signed_key_images Array of signed key images
     *
     * @return object|string Example: {
     *   // TODO example
     *   height: ,
     *   spent: ,
     *   unspent:
     * }
     * @throws RuntimeException
     */
    public function import_key_images(array $signed_key_images): object|string
    {
        $params = array('signed_key_images' => $signed_key_images);
        return $this->_run('import_key_images', $params);
    }

    /**
     *
     * Create a payment URI using the official URI specification
     *
     * @param string $address Address to receive funds
     * @param string $amount Amount of monero to request
     * @param null $payment_id Payment ID (optional)
     * @param null $recipient_name Name of recipient (optional)
     * @param null $tx_description Payment description (optional)
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function make_uri(string $address, string $amount, $payment_id = null, $recipient_name = null, $tx_description = null): object|string
    {
        $params = array('address' => $address, 'amount' => $this->_transform($amount), 'payment_id' => $payment_id, 'recipient_name' => $recipient_name, 'tx_description' => $tx_description);
        return $this->_run('make_uri', $params);
    }

    /**
     *
     * Parse a payment URI
     *
     * @param string $uri Payment URI
     *
     * @return object|string Example: {
     *   "uri": {
     *     "address": "44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A",
     *     "amount": 10,
     *     "payment_id": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
     *     "recipient_name": "Monero Project donation address",
     *     "tx_description": "Testing out the make_uri function"
     *   }
     *
     *
     * @throws RuntimeException
     */
    public function parse_uri(string $uri): object|string
    {
        $params = array('uri' => $uri);
        return $this->_run('parse_uri', $params);
    }

    /**
     *
     * Look up address book entries
     *
     * @param array $entries Array of address book entry indices to look up
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function get_address_book(array $entries): object|string
    {
        $params = array('entries' => $entries);
        return $this->_run('get_address_book', $params);
    }

    /**
     *
     * Add entry to the address book
     *
     * @param string $address Address to add to address book
     * @param string $payment_id Payment ID to use with address in address book (optional)
     * @param string $description Description of address (optional)
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function add_address_book(string $address, string $payment_id, string $description): object|string
    {
        $params = array('address' => $address, 'payment_id' => $payment_id, 'description' => $description);
        return $this->_run('add_address_book', $params);
    }

    /**
     *
     * Delete an entry from the address book
     *
     * @param array $index Index of the address book entry to remove
     *
     * @return null|string
     * @throws RuntimeException
     */
    public function delete_address_book(array $index):null|string
    {
        $params = array('index' => $index);
        return $this->_run('delete_address_book', $params);
    }

    /**
     *
     * Rescan the blockchain for spent outputs
     *
     * @throws RuntimeException
     */
    public function rescan_spent(): string
    {
        return $this->_run('rescan_spent');
    }

    /**
     *
     * Start mining
     *
     * @param number $threads_count Number of threads with which to mine
     * @param boolean $do_background_mining Mine in the background?
     * @param boolean $ignore_battery Ignore battery?
     *
     * @return string|null
     * @throws RuntimeException
     */
    public function start_mining($threads_count, bool $do_background_mining, bool $ignore_battery): null|string
    {
        $params = array('threads_count' => $threads_count, 'do_background_mining' => $do_background_mining, 'ignore_battery' => $ignore_battery);
        return $this->_run('start_mining', $params);
    }

    /**
     *
     * Stop mining
     *
     * @return string|null
     * @throws RuntimeException
     */
    public function stop_mining():null|string
    {
        return $this->_run('stop_mining');
    }

    /**
     *
     * Look up a list of available languages for your wallet's seed
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function get_languages(): object|string
    {
        return $this->_run('get_languages');
    }

    /**
     *
     * Create a new wallet
     *
     * @param string $filename Filename of a new wallet to create
     * @param null $password Password of new wallet to create
     * @param string $language Language of a new wallet to create
     *
     * @return string|null
     * @throws RuntimeException
     */
    public function create_wallet(string $filename = 'monero_wallet', $password = null, string $language = 'English'): ?string
    {
        $params = array('filename' => $filename, 'password' => $password, 'language' => $language);
        return $this->_run('create_wallet', $params);
    }

    /**
     *
     * Open a wallet
     *
     * @param string $filename Filename of wallet to open
     * @param string|null $password Password of wallet to open
     *
     * @return string|null
     * @throws RuntimeException
     */
    public function open_wallet(string $filename = 'monero_wallet', string $password = null): ?string
    {
        $params = array('filename' => $filename, 'password' => $password);
        return $this->_run('open_wallet', $params);
    }

    /**
     *
     * Check if wallet is multisig
     *
     * @return object|string Example: (non-multisignature wallet) {
     *   "multisig": ,
     *   "ready": ,
     *   "threshold": 0,
     *   "total": 0
     * } // TODO multisig wallet example
     * @throws RuntimeException
     */
    public function is_multisig(): object|string
    {
        return $this->_run('is_multisig');
    }

    /**
     *
     * Create information needed to create a multisignature wallet
     *
     * @return object|string Example: {
     *   "multisig_info": "MultisigV1WBnkPKszceUBriuPZ6zoDsU6RYJuzQTiwUqE5gYSAD1yGTz85vqZGetawVvioaZB5cL86kYkVJmKbXvNrvEz7o5kibr7tHtenngGUSK4FgKbKhKSZxVXRYjMRKEdkcbwFBaSbsBZxJFFVYwLUrtGccSihta3F4GJfYzbPMveCFyT53oK"
     *
     * @throws RuntimeException
     */
    public function prepare_multisig(): object|string
    {
        return $this->_run('prepare_multisig');
    }

    /**
     *
     * Create a multisignature wallet
     *
     * @param string $multisig_info Multisignature information (from eg. prepare_multisig)
     * @param string $threshold Threshold required spending from a multisignature wallet
     * @param string $password Passphrase to apply to multisignature wallet
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function make_multisig(string $multisig_info, string $threshold, string $password = ''): object|string
    {
        $params = array('multisig_info' => $multisig_info, 'threshold' => $threshold, 'password' => $password);
        return $this->_run('make_multisig', $params);
    }

    /**
     *
     * Export multisignature information
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function export_multisig_info(): object|string
    {
        return $this->_run('export_multisig_info');
    }

    /**
     *
     * Import mutlisignature information
     *
     * @param string $info Multisignature info (from eg. prepare_multisig)
     *
     * @return Example|string : {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function import_multisig_info(string $info): Example|string
    {
        $params = array('info' => $info);
        return $this->_run('import_multisig_info', $params);
    }

    /**
     *
     * Finalize a multisignature wallet
     *
     * @param string $multisig_info Multisignature info (from eg. prepare_multisig)
     * @param string $password Multisignature info (from eg. prepare_multisig)
     *
     * @return Example|string : {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function finalize_multisig(string $multisig_info, string $password = ''): Example|string
    {
        $params = array('multisig_info' => $multisig_info, 'password' => $password);
        return $this->_run('finalize_multisig', $params);
    }

    /**
     *
     * Sign a multisignature transaction
     *
     * @param string $tx_data_hex Blob of transaction to sign
     *
     * @return object|string Example: {
     *   // TODO example
     * }
     *
     * @throws RuntimeException
     */
    public function sign_multisig(string $tx_data_hex): object|string
    {
        $params = array('tx_data_hex' => $tx_data_hex);
        return $this->_run('sign_multisig', $params);
    }

    /**
     *
     * Submit (relay) a multisignature transaction
     *
     * @param string $tx_data_hex Blob of transaction to submit
     *
     * @return Example|string : {
     *   // TODO example
     * }
     * @throws RuntimeException
     */
    public function submit_multisig(string $tx_data_hex): Example|string
    {
        $params = array('tx_data_hex' => $tx_data_hex);
        return $this->_run('submit_multisig', $params);
    }

    /**
     * @return jsonRPCClient
     */
    public function get_client(): jsonRPCClient
    {
        return $this->client;
    }

    /**
     * @return jsonRPCClient
     */
    public function getClient(): jsonRPCClient
    {
        return $this->client;
    }
}
