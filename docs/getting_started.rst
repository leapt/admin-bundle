Getting started with LeaptAdminBundle
=====================================

LeaptAdminBundle provides a few useful features for your backoffice applications. The goal of this bundle is to provide a
series of tools and helpers to facilitate your work, but also to get out of your way when you want to add totally custom
features.

Translations
------------

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

.. code-block:: yaml

    # app/config/config.yml

    framework:
        translator: ~


For more information about translations, check `Symfony documentation <http://symfony.com/doc/current/book/translation.html>`_.

Installation
============

Installation is a 7 step process:

1. Download LeaptAdminBundle using composer
2. Enable the Bundle and its dependencies
3. Create your admin bundle
4. Enable the admin routing
5. Configure Assetic
6. Configure security
7. Additional configuration steps

Step 1: Download LeaptAdminBundle using composer
------------------------------------------------

Add LeaptAdminBundle in your composer.json:

.. code-block:: javascript

    {
        "require": {
            "leapt/admin-bundle": "~2.0"
        }
    }


Now tell composer to download the bundle by running the command:

.. code-block:: bash

    php composer.phar update leapt/admin-bundle


Composer will install the bundle to your project's `vendor/leapt` directory.

Step 2: Enable the bundle
-------------------------

Enable the bundle in the kernel:

.. code-block:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Leapt\AdminBundle\LeaptAdminBundle(),
            // Required dependencies
            new Leapt\CoreBundle\LeaptCoreBundle(),
            new Leapt\BootstrapBundle\LeaptBootstrapBundle(),
            new Leapt\ImBundle\LeaptImBundle(),
        );
    }


Step 3: Create your Admin Bundle
--------------------------------

In order to be able to use the Leapt Admin Bundle, you need to create your own Admin Bundle in your project.

.. code-block:: bash

    php ./bin/console generate:bundle

Your bundle must extend LeaptAdminBundle in order for it to work.

.. code-block:: php

    <?php
    // src/Acme/AdminBundle/AcmeAdminBundle.php

    public function getParent()
    {
        return 'LeaptAdminBundle';
    }


Step 4: Enable admin routing
----------------------------

.. code-block:: yaml

    # app/config/routing.yml

    leapt_admin:
        resource: "@LeaptAdminBundle/Resources/config/routing.yml"
        prefix: /admin


Step 5: Configure Assetic
-------------------------

LeaptAdminBundle needs your assets to be installed.

.. code-block:: console

    bin/console assets:install --symlink web


Step 6: Configure security
--------------------------

The AdminBundle requires at least an active firewall.

You can use whichever authentication mechanism you like. In order to make your life easier, LeaptAdminBundle provides a base user class, and a few other extras to be used with Doctrine's entity user provider and standard login form authentication.

First, create a user class in your AdminBundle's entity directory:

.. code-block:: php

    <?php
    // src/Acme/AdminBundle/Entity/AdminUser.php

    namespace Acme\AdminBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use Leapt\AdminBundle\Entity\User;

    /**
     * @ORM\Entity
     * @ORM\Table
     */
    class AdminUser extends User
    {

    }

You can then change your security.yml config file:

.. code-block:: yaml

    # app/config/security.yml

    leapt_admin:
        security:
            user_class: Acme\AdminBundle\Entity\AdminUser

    security:
        encoders:
            Leapt\AdminBundle\Entity\User: sha512

        providers:
            admin_users:
                entity: { class: AcmeAdminBundle:AdminUser, property: username }

        firewalls:
            ...

            admin:
                pattern:    ^/admin
                provider: admin_users
                anonymous: ~
                form_login:
                    login_path:  leapt_admin_login
                    check_path:  leapt_admin_login_check
                logout:
                    path: leapt_admin_logout
                remember_me:
                    secret:   '%secret%'
                    lifetime: 604800
                    path:     /

        access_control:
            - { path: ^/admin/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/admin, role: ROLE_ADMIN }


Don't forget to update your database schema, using schema:update or migrations:diff / migrations:migrate:

.. code-block:: bash

    php ./bin/console doctrine:schema:update --force

When this is done, you can create admin users through the command line:

.. code-block:: bash

    php ./bin/console leapt:admin:generate:user

Make sure to give to your user at least one admin role as configured in your security.yml file.

You can now access the administration interface.

Step 7: Additional configuration steps
--------------------------------------

**Enable translations**

LeaptAdminBundle stores its own translation messages under the "LeaptAdminBundle" translation domain. Other interface messages, such as the title in the navbar, form and datalist labels, are specific to your project, and are translated through a distinct translation domain. By default, this translation domain is "admin", but you can change it in your project config:

.. code-block:: yaml

    # app/config/config.yml

    leapt_admin:
        default_translation_domain: backoffice


Your first admin class
----------------------

One of the main features of LeaptAdminBundle is to allow you to create CRUD interfaces that manage entities. We call those CRUD interfaces "Content Admins".

Creating a Content Admin can be done in 2 steps:

1. Create a Content Admin class
2. Register your admin class with the Service Container

Create a Content Admin class
----------------------------

The first step is to create an Admin class that extends the abstract ContentAdmin class. You will have to implement at least four methods:

* _getForm_ must return a Symfony/Component/Form/FormInterface instance
* _getDatalist_ must return a Leapt/AdminBundle/Datalist/DatalistInterface instance
* _getEntityName_ receives an entity as sole argument and must return a textual representation of that entity (its name or its title for instance)
* _getEntityClass_ must return the fully qualified class name of the managed entity

.. code-block:: php

    <?php
    // src/Acme/AdminBundle/Admin/ArtistAdmin.php

    namespace Acme\AdminBundle\Admin;

    use Leapt\AdminBundle\Admin\ContentAdmin

    class ArtistAdmin extends ContentAdmin
    {
        /**
         * Return the main admin form for this content
         *
         * @return \Symfony\Component\Form\Form
         */
        public function getForm()
        {
            return $this->getFormFactory()
                ->createBuilder('form', null, array('data_class' => 'Acme\SiteBundle\Entity\Artist'))
                ->add('firstName', 'text')
                ->add('lastName', 'text')
                ->getForm();
        }

        /**
         * Return the main admin list for this content
         *
         * @return \Leapt\AdminBundle\Datalist\DatalistInterface
         */
        public function getDatalist()
        {
            return $this->getDatalistFactory()
                ->createBuilder('datalist', array('data_class' => 'Acme\SiteBundle\Entity\Artist'))
                ->addField('firstName', 'text')
                ->addField('lastName', 'text')
                ->getDatalist();
        }

        /**
         * @param object $entity
         * @return string
         */
        public function getEntityName($entity)
        {
            return $entity->getName();
        }

        /**
         * @return string
         */
        public function getEntityClass()
        {
            return 'Acme\SiteBundle\Entity\Artist';
        }
    }

Your admin class is ready but we still need to register it as a service.

Register your admin class with the Service Container
----------------------------------------------------

Simply edit your Admin Bundle services.yml file and declare your Admin Class as a service that extends the

.. code-block:: yaml

    # src/Acme/AdminBundle/Resources/config/services.yml

    class: Acme\AdminBundle\Admin\ArtistAdmin
        parent: leapt_admin.admin_content
        tags:
            - { name: leapt_admin.admin, alias: artist, label: Artist|Artists }


That's it, your admin class is ready to use. You can test it at http://yourbaseurl/admin/artist

Next steps
----------

Now that you have a basic CRUD admin, you are ready to dive into more advanced features.
