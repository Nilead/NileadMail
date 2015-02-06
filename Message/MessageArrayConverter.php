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
            ->setFrom($data['from'])
            ->setTo($data['to']);

        if (isset ($data['cc'])) {
            $message->setCc($data['cc']);
        }

        if (isset ($data['bcc'])) {
            $message->setBcc($data['bcc']);
        }

        $message->setSubject($data['subject']);

        $message->setBody($data['text'], 'plain/text');

        if (isset ($data['html'])) {
            $message->setHtmlBody($data['html']);
        }

        $message->setReplyTo($data['replyTo']);

        $message->setReturnPath($data['returnPath']);

        return $message;
    }
}
