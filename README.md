# Mage2Katas

Work related to the [Mage2Katas](https://www.youtube.com/channel/UCRFDWo7jTlrpEsJxzc7WyPw) series.

## The Module Skeleton Kata

* This assumes that the Magento 2 test framework (including integration tests) and your IDE are already setup and configured to run tests.
    * Refer to the DevDocs for a quick guide on setting up integration tests [[3]][3] and on setting up PhpStorm with PHPUnit [[4]][4]
* Start with Integration tests first.
* Manually create the following folder structure module in the `app/code` directory:

```bash
app
    code
        [Vendor Name]
            [Module Name]
                Test
                    Integration
```

* Create your first test class and a 'test nothing' method. We'll use this empty test to check our framework and IDE are setup correctly:
```php
<?php

namespace Mage2Kata\ModuleSkeleton\Test\Integration;

class SkeletonModuleConfigTest extends \PHPUnit_Framework_TestCase
{
	public function testNothing()
	{
		$this->markTestSkipped('Testing that PhpStorm and test framework is setup correctly');
	}
}
```
The next step is to write the next most basic test: To check that the module exists according to Magento. 

In other words, we test for the existence of the `registration.php` file:

```php
private $moduleName = 'Mage2Kata_SkeletonModule';

public function testTheModuleIsRegistered()
{
    $registrar = new ComponentRegistrar();
    $this->assertArrayHasKey(
        $this->moduleName,
        $registrar->getPaths( ComponentRegistrar::MODULE)
    );
}
```
We've extracted the module name into a member variable so we can re-use it in other tests.

At this point you may get an error if you try to run the test. This is because Magento expects every module to have, at the bare minimum, a `registration.php` file and a `module.xml` file with a `setup_version` attribute. 

Let's now move onto the next step in creating a module - the `module.xml`.

Here's the test:
```php
public function testTheModuleIsConfiguredAndEnabled()
{
    /** @var ObjectManager $objectManager */
    $objectManager = ObjectManager::getInstance();

    /** @var ModuleList $moduleList */
    $moduleList = $objectManager->create( ModuleList::class);

    $this->assertTrue( $moduleList->has( $this->moduleName), 'The module is not enabled');
}
```

If we run this test now, it will fail. If we create the `module.xml` file, then it should pass.

```xml
// File: Mage2Kata/SkeletonModule/etc/module.xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
    <module name="Mage2Kata_SkeletonModule" setup_version="0.1.0">
    </module>
</config>
```

So that's our extremely basic module created using TDD.

## Sources
* [Running Unit Tests in the CLI](http://devdocs.magento.com/guides/v2.1/test/unit/unit_test_execution_cli.html)
* [Running Unit Tests in PHPStorm](http://devdocs.magento.com/guides/v2.1/test/unit/unit_test_execution_phpstorm.html)

[1]: http://magento.stackexchange.com/questions/140314/magento-2-unit-test-with-mock-data-dont-work-why/140337#140337
[2]: http://devdocs.magento.com/guides/v2.1/test/unit/writing_testable_code.html
[3]: http://devdocs.magento.com/guides/v2.1/test/integration/integration_test_setup.html
[4]: http://devdocs.magento.com/guides/v2.1/install-gde/docker/docker-phpstorm-project.html