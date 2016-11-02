<?php
/**
 * Created by Rubikin Team.
 * ========================
 * Date: 2/6/2015
 * Time: 9:13 AM
 * Author: vu
 * Question? Come to our website at http://rubikin.com
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Nilead\Mail\Adapter;

abstract class AbstractAdapter implements AdapterInterface
{
    protected function getSingleAddress($addresses)
    {
        if (!is_array($addresses)) {
            $addresses = (array) $addresses;
        }

        // we get only 1 address
        foreach ($addresses as $key => $value) {
            if (is_numeric($key)) {
                return $value;
            } else {
                return sprintf('%s <%s>', $value, $key);
            }
        }

        return false;
    }
}
