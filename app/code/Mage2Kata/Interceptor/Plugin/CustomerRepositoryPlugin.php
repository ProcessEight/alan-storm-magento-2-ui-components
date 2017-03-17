<?php
/**
 * Created by PhpStorm.
 * User: zone8
 * Date: 17/03/17
 * Time: 19:34
 */

namespace Mage2Kata\Interceptor\Plugin;


class CustomerRepositoryPlugin
{
	public function aroundSave( $subject, $proceed, $customer, $passwordHash )
	{

	}
}