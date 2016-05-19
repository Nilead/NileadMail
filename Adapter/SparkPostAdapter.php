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

use SparkPost\SparkPost;
use Nilead\Notification\Message\MessageInterface;

class SparkPostAdapter extends AbstractAdapter
{
    /**
     * @var SparkPost
     */
    protected $client;

    public function __construct(SparkPost $client)
    {
        $this->client = $client;
    }

    public function send(MessageInterface $message)
    {
        return $this->client->transmission->send($this->parse($message));
    }

    protected function parse(MessageInterface $message)
    {
        return array_merge(
            array(
                'html'          => $message->getBodyHtml(),
                'text'          => $message->getBody(),
                'subject'       => $message->getSubject(),
                'recipients'    => $this->getAddresses($message),
                'replyTo'       => $this->getSingleAddress($message->getReplyTo()),
                'trackClicks'   => true,
                'trackOpens'    => true,
                'inlineCss'     => true,
                'transactional' => true,
            ),
            $this->getFrom($message->getFrom())
        );
    }

    protected function getFrom($addresses)
    {
        foreach ($addresses as $key => $value) {
            if (is_numeric($key)) {
                return [
                    'from' => [
                        'email' => $value
                    ]
                ];
            } else {
                return [
                    'from' => [
                        'name' => $key,
                        'email' => $value
                    ]
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
                            'name' => $value,
                            'email' => $key,
                        ],
                    ];
                }
            }
        }
    }
}