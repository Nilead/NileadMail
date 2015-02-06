<?php

/**
 * Created by Rubikin Team.
 * ========================
 * Date: 2/5/2015
 * Time: 3:23 PM
 * Author: vu
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// A simple mailer wrapper
namespace Nilead\Mail;

use Nilead\Mail\Adapter\AdapterInterface;
use Nilead\Mail\Message\Message;

class Mailer
{
    /**
     * @var AdapterInterface[]
     */
    protected $mailers = [];

    public function registerMailer($key, AdapterInterface $client)
    {
        $this->mailers[$key] = $client;
    }

    public function send($key, Message $message)
    {
        return $this->mailers[$key]->send($message);
    }
}
