<?php

/*
 * This file is part of the Ekino Drupal package.
 *
 * (c) 2011 Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Bundle\DrupalBundle\Twig;

/**
 * Twig toolbar extension class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class ToolbarExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    protected $toolbar;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ekino_drupal_toolbar_render_item', array($this, 'renderToolbarItem'), array(
                'is_safe' => array('html')
            )),
        );
    }

    /**
     * Renders HTML for a Drupal administration toolbar item
     *
     * @param $item
     *
     * @return string
     */
    public function renderToolbarItem($item)
    {
        if (null === $this->toolbar) {
            $this->toolbar = toolbar_view();
        }

        return render($this->toolbar[$item]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekino_drupal_toolbar_extension';
    }
}
