<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {
    /**
     * Fake Drupal toolbar_view() function
     *
     * @return array
     */
    function toolbar_view()
    {
        return array('test' => 'value');
    }

    /**
     * Fake Drupal render() function
     *
     * @param string $item
     *
     * @return string
     */
    function render($item)
    {
        return sprintf('<h1>%s</h1>', $item);
    }
}

namespace Ekino\Bundle\DrupalBundle\Tests\Twig {
    use Ekino\Bundle\DrupalBundle\Twig\ToolbarExtension;

    /**
     * Class ToolbarExtensionTest
     *
     * @author Vincent Composieux <vincent.composieux@gmail.com>
     */
    class ToolbarExtensionTest extends \PHPUnit_Framework_TestCase
    {
        /**
         * @var ToolbarExtension
         */
        protected $extension;

        /**
         * Sets up Twig toolbar extension
         */
        public function setUp()
        {
            $this->extension = new ToolbarExtension();
        }

        /**
         * Tests the getFunctions() method
         */
        public function testGetFunctions()
        {
            $functions = $this->extension->getFunctions();

            $this->assertTrue(is_array($functions), 'Should return an array of functions');

            foreach ($functions as $function) {
                $this->assertInstanceOf('\Twig_SimpleFunction', $function);
            }
        }

        /**
         * Tests the renderToolbarItem() method
         */
        public function testRenderToolbarItem()
        {
            // When
            $result = $this->extension->renderToolbarItem('test');

            // Then
            $this->assertEquals('<h1>value</h1>', $result, 'Should return render() function');
        }

        /**
         * Tests the getName() method
         */
        public function testGetName()
        {
            // When
            $name = $this->extension->getName();

            // Then
            $this->assertEquals('ekino_drupal_toolbar_extension', $name, 'Should return correct Twig extension name');
        }
    }
}