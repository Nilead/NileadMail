<?php
/**
 * Created by Rubikin Team.
 * ========================
 * Date: 2/6/2015
 * Time: 10:32 AM
 * Author: vu
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nilead\Mail\Adapter;

use Nilead\Notification\Message\MessageInterface;

interface AdapterInterface
{
    public function send(MessageInterface $message);
}
