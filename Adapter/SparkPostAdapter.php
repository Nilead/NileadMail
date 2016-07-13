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
use SparkPost\SparkPost;
use SparkPost\APIResponseException;
use Psr\Log\LoggerInterface;

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
            $logger->info(sprintf("Status code: %s\r\n Body message: %s\r\n", $promise->getStatusCode(), print_r($promise->getBody(), true)));
        } catch (\APIResponseException $e) {
            $logger->critical(sprintf("Error code: %s\r\n Error message: %s\r\n Error description: %s\r\n", $e->getAPICode(), $e->getAPIMessage(), $e->getAPIDescription()));
        } catch (\Exception $e) {
            $logger->critical(sprintf("Error code: %s\r\n Error message: %s\r\n", $e->getCode(), $e->getMessage()));
        }
    }

    protected function parse(MessageInterface $message)
    {
        $content = array_merge(
            [
                'html'        => $message->getBodyHtml(),
                'text'        => $message->getBody(),
                'subject'     => $message->getSubject(),
            ],
            $this->getFrom($message->getFrom())
        );
                
        return [ 
            'content'    => $content,
            'recipients' => $this->getAddresses($message)
        ];
    }

    protected function getReplyTo($replyTo)
    {
        if (false !== $this->getSingleAddress($replyTo)) {
            return ['replyTo' => $this->getSingleAddress($replyTo)];
        } else {
            return [];
        }
    }

    protected function getFrom($addresses)
    {
        foreach ($addresses as $key => $value) {
            if (is_numeric($key)) {
                return [
                    'from' => [
                        'email' => $value,
                    ],
                ];
            } else {
                return [
                    'from' => [
                        'name'  => $key,
                        'email' => $value,
                    ],
                ];
            }
        };

        return [];
    }

    protected function getAddresses(MessageInterface $message)
    {
        $list = [];

        $this->_getAddresses($message->getTo(), $list);

        return $list;
    }

    protected function _getAddresses($addresses, &$list)
    {
        if (is_array($addresses)) {
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
                            'name'  => $value,
                            'email' => $key,
                        ],
                    ];
                }
            }
        }
    }
}