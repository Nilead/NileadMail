<?php
/**
 * Created by Rubikin Team.
 * ========================
 * Date: 2/6/2015
 * Time: 9:21 AM
 * Author: vu
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nilead\Mail\Adapter;

use Nilead\Notification\Message\MessageInterface;
use Mandrill;
use Psr\Log\LoggerInterface;

class MandrillAdapter extends AbstractAdapter
{
    /**
     * @var Mandrill
     */
    protected $client;

    public function __construct(Mandrill $client)
    {
        $this->client = $client;
    }

    public function send(MessageInterface $message, LoggerInterface $logger)
    {
        return $this->client->messages->send($this->parse($message));
    }

    protected function parse(MessageInterface $message)
    {
        return array_merge(
            array(
                'html' => $message->getBodyHtml(),
                'text' => $message->getBody(),
                'subject' => $message->getSubject(),
                'to' => $this->getAddresses($message),
                'headers' => array('Reply-To' => $this->getSingleAddress($message->getReplyTo())),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => null,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                //            'bcc_address' => 'message.bcc_address@example.com',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                //            'merge' => true,
                //            'merge_language' => 'mailchimp',
                //            'global_merge_vars' => array(
                //                array(
                //                    'name' => 'merge1',
                //                    'content' => 'merge1 content'
                //                )
                //            ),
                //            'merge_vars' => array(
                //                array(
                //                    'rcpt' => 'recipient.email@example.com',
                //                    'vars' => array(
                //                        array(
                //                            'name' => 'merge2',
                //                            'content' => 'merge2 content'
                //                        )
                //                    )
                //                )
                //            ),
                //            'tags' => array('password-resets'),
                //            'subaccount' => 'customer-123',
                //            'google_analytics_domains' => array('example.com'),
                //            'google_analytics_campaign' => 'message.from_email@example.com',
                //            'metadata' => array('website' => 'www.example.com'),
                //            'recipient_metadata' => array(
                //                array(
                //                    'rcpt' => 'recipient.email@example.com',
                //                    'values' => array('user_id' => 123456)
                //                )
                //            ),
                //            'attachments' => array(
                //                array(
                //                    'type' => 'text/plain',
                //                    'name' => 'myfile.txt',
                //                    'content' => 'ZXhhbXBsZSBmaWxl'
                //                )
                //            ),
                //            'images' => array(
                //                array(
                //                    'type' => 'image/png',
                //                    'name' => 'IMAGECID',
                //                    'content' => 'ZXhhbXBsZSBmaWxl'
                //                )
                //            )
            ),
            $this->getFrom($message->getFrom())
        );
    }

    protected function getFrom($addresses)
    {
        foreach ($addresses as $key => $value) {
            if (is_numeric($key)) {
                return [
                    'from_email' => $value,
                ];
            } else {
                return [
                    'from_email' => $key,
                    'from_name' => $value
                ];
            }
        };

        return [];
    }

    protected function getAddresses(MessageInterface $message)
    {
        $list = [];

        $this->_getAddresses($message->getTo(), 'to', $list);

//        $this->_getAddresses($message->getCc(), 'cc', $list);

//        $this->_getAddresses($message->getBcc(), 'bcc', $list);

        return $list;
    }

    protected function _getAddresses($addresses, $type, &$list)
    {
        if (is_array($addresses)) {
            foreach ($addresses as $key => $value) {
                if (is_numeric($key)) {
                    $list[] = [
                        'email' => $value,
                        'type' => $type
                    ];
                } else {
                    $list[] = [
                        'email' => $key,
                        'name' => $value,
                        'type' => $type
                    ];
                }
            }
        }
    }
}
