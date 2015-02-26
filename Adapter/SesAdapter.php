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
use Nilead\Notification\Message\MessageInterface;

class SesAdapter extends AbstractAdapter
{
    protected $client;

    public function __construct(SesClient $client)
    {
        $this->client = $client;
    }

    public function send(MessageInterface $message)
    {
        $this->client->sendEmail($this->parse($message));
    }

    protected function parse(MessageInterface $message)
    {
        $result = array(
            // Source is required
            'Source' => $this->getSingleAddress($message->getFrom()),
            // Destination is required
            'Destination' => array(
                'ToAddresses' => $this->getAddresses($message->getTo()),
                //                'CcAddresses' => $this->getAddresses($message->getCc()),
                //                'BccAddresses' => $this->getAddresses($message->getBcc())
            ),
            // Message is required
            'Message' => array(
                // Subject is required
                'Subject' => array(
                    // Data is required
                    'Data' => $message->getSubject(),
                    'Charset' => 'utf-8',
                ),
                // Body is required
                'Body' => array(
                    'Text' => array(
                        // Data is required
                        'Data' => $message->getBody(),
                        'Charset' => 'utf-8',
                    ),
                ),
            ),
            'ReplyToAddresses' => $message->getReplyTo(),
            'ReturnPath' => $message->getReturnPath(),
        );

        if (!empty($message->getBodyHtml())) {
            $result['Message']['Body']['Html'] = [
                'Data' => $message->getBodyHtml(),
                'Charset' => 'utf-8',
            ];
        }

        return $result;
    }

    protected function getAddresses($addresses)
    {
        $list = [];
        foreach ($addresses as $key => $value) {
            if (is_numeric($key)) {
                $list[] = $value;
            } else {
                $list[] = sprintf('%s <%s>', $value, $key);
            }
        }

        return $list;
    }
}
