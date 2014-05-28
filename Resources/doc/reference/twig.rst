.. index::
single: Extensions Twig

Extensions Twig
===============

We provide a Twig extension to help rendering Drupal parts into your Symfony Twig templates.

ToolbarExtension
----------------

You can render the administration Drupal toolbar parts by adding the following HTML to your Twig template:

.. code-block:: html

    {% if is_granted('PERMISSION_DRUPAL_ACCESS_TOOLBAR') %}
        <div id="toolbar" class="toolbar overlay-displace-top clearfix toolbar-processed">
            <div class="toolbar-menu clearfix">
                {{ ekino_drupal_toolbar_render_item('toolbar_home') }}
                {{ ekino_drupal_toolbar_render_item('toolbar_user') }}
                {{ ekino_drupal_toolbar_render_item('toolbar_menu') }}
                {{ ekino_drupal_toolbar_render_item('toolbar_toggle') }}
            </div>
        </div>
    {% endif %}

Please ensure that your user has the correct role to "access toolbar" in Drupal administration.

As an additional note, you will also need to load the following stylesheets:

.. code-block:: html

    <link rel="stylesheet" type="text/css" href="{{ asset('modules/system/system.base.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('modules/toolbar/toolbar.css') }}" />
