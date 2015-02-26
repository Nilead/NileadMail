<?php
/**
 * Created by Rubikin Team.
 * ========================
 * Date: 2/6/2015
 * Time: 9:14 AM
 * Author: vu
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nilead\Mail\Adapter;

use Aws\Ses\SesClient;
use Nilead\Mail\Message\Message;

class SesAdapter extends AbstractAdapter
{
    protected $client;

    public function __construct(SesClient $client)
    {
        $this->client = $client;
    }

    public function send(Message $message)
    {
        $this->client->send($this->parse($message));
    }

    protected function parse(Message $message)
    {
        return array(
            // Source is required
            'Source' => $this->getSingleAddress($message->getFrom()),
            // Destination is required
            'Destination' => array(
                'ToAddresses' => $this->getAddresses($message->getTo()),
                'CcAddresses' => $this->getAddresses($message->getCc()),
                'BccAddresses' => $this->getAddresses($message->getBcc())
            ),
            // Message is required
            'Message' => array(
                // Subject is required
                'Subject' => array(
                    // Data is required
                    'Data' => $message->getSubject(),
                    'Charset' => $message->getCharset(),
                ),
                // Body is required
                'Body' => array(
                    'Text' => array(
                        // Data is required
                        'Data' => $message->getTextBody(),
                        'Charset' => $message->getCharset(),
                    ),
                    'Html' => array(
                        // Data is required
                        'Data' => $message->getHtmlBody(),
                        'Charset' => $message->getCharset(),
                    ),
                ),
            ),
            'ReplyToAddresses' => $message->getReplyTo(),
            'ReturnPath' => $message->getReturnPath(),
        );
    }

    protected function getAddresses($addresses)
    {
        $list = [];
        foreach ($addresses as $address => $name) {
            if (empty($name)) {
                $list[] = $address;
            } else {
                $list[] = sprintf('%s <%s>', $name, $address);
            }
        }

        return $list;
    }
}
