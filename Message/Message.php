<?php
/**
 * Created by Rubikin Team.
 * ========================
 * Date: 2/5/2015
 * Time: 9:30 PM
 * Author: vu
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Nilead\Mail;

use Swift_Message;

/**
 * Class Message
 *
 * A simple wrapper to easily retrieve the html body
 */
class Message extends Swift_Message
{
    protected $htmlBody;

    public function setHtmlBody($body)
    {
        $this->htmlBody = $body;
        $this->addPart($body, 'text/html');
    }

    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    public function setTextBody($body)
    {
        $this->setBody($body, 'plain/text');
    }

    public function getTextBody()
    {
        return $this->getBody();
    }
}
