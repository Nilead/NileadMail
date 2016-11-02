<?php
/**
 * Created by Kevin.
 * User: Kevin
 * Date: 5/10/16
 * Time: 18:20
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nilead\Mail\Adapter;

use Nilead\Notification\Message\MessageInterface;
use Psr\Log\LoggerInterface;
use SparkPost\SparkPost;

class SparkPostAdapter extends AbstractAdapter
{
    /**
     * @var SparkPost
     */
    protected $client;

    public function __construct(SparkPost $client)
    {
        $this->client = $client;
        $this->client->setOptions(['async' => false]);

    }

    public function send(MessageInterface $message, LoggerInterface $logger)
    {
        try {
            $promise = $this->client->transmissions->post($this->parse($message));
            $logger->info(
                sprintf(
                    "Status code: %s\r\n Body message: %s\r\n",
                    $promise->getStatusCode(),
                    json_encode($promise->getBody())
                )
            );
        } catch (\APIResponseException $e) {
            $logger->critical(
                sprintf(
                    "Error code: %s\r\n Error message: %s\r\n Error description: %s\r\n",
                    $e->getAPICode(),
                    $e->getAPIMessage(),
                    $e->getAPIDescription()
                )
            );
        } catch (\Exception $e) {
            $logger->critical(sprintf("Error code: %s\r\n Error message: %s\r\n", $e->getCode(), $e->getMessage()));
        }
    }

    protected function parse(MessageInterface $message)
    {
        $content = [
            'content' => [
                'html' => $message->getBodyHtml(),
                'text' => $message->getBody(),
                'subject' => $message->getSubject(),
                'from' => $this->getSingleAddress($message->getFrom()),
                'replyTo' => $this->getSingleAddress($message->getReplyTo())
            ],
            'recipients' => $this->getAddresses($message->getTo())
        ];

        return $content;
    }

    protected function getReplyTo($replyTo)
    {
        if (false !== ($address = $this->getSingleAddress($replyTo))) {
            return ['reply_to' => $address];
        } else {
            return [];
        }
    }

    protected function getAddresses($addresses)
    {
        $list = [];

        if (!is_array($addresses)) {
            $addresses = (array) $addresses;
        }

        foreach ($addresses as $key => $value) {
            if (is_numeric($key)) {
                $list[] = [
                    'address' => [
                        'email' => $value,
                    ],
                ];
            } else {
                $list[] = [
                    'address' => [
                        'name' => $value,
                        'email' => $key,
                    ],
                ];
            }
        }

        return $list;
    }
}
