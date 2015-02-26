<?php
/**
 * Created by Rubikin Team.
 * ========================
 * Date: 2/6/2015
 * Time: 10:22 AM
 * Author: vu
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nilead\Mail\Message;


class MessageArrayConverter
{
    public static function convert($data)
    {
        $message = new Message();
        $message
            ->setFrom((array)$data['from'])
            ->setTo((array)$data['to']);

        if (isset ($data['cc'])) {
            $message->setCc((array)$data['cc']);
        }

        if (isset ($data['bcc'])) {
            $message->setBcc((array)$data['bcc']);
        }

        $message->setSubject($data['subject']);

        $message->setBody($data['body'], 'plain/text');

        if (isset ($data['bodyHtml'])) {
            $message->setHtmlBody($data['html']);
        }

        if (isset ($data['replyTo'])) {
            $message->setReplyTo($data['replyTo']);
        }

        if (isset ($data['returnPath'])) {
            $message->setReturnPath($data['returnPath']);
        }

        return $message;
    }
}
