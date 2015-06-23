<?php

use PAI\Form\FormController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Validator\Validation;

require __DIR__ . '/../vendor/autoload.php';

$container = new ContainerBuilder();
$container->register('csrf_provider', '\Symfony\Component\Security\Csrf\CsrfTokenManager');
$container
    ->register('csrf_form_extension', '\Symfony\Component\Form\Extension\Csrf\CsrfExtension')
    ->addArgument((new Reference('csrf_provider')));

$container->register('http_foundation_form_extension', '\Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension');

$container->set('validator', Validation::createValidator());
$container
    ->register('validator_form_extension', '\Symfony\Component\Form\Extension\Validator\ValidatorExtension')
    ->addArgument((new Reference('validator')));

$container->register('core_form_extension', '\Symfony\Component\Form\Extension\Core\CoreExtension');
$container
    ->register('form_factory_builder', '\Symfony\Component\Form\FormFactoryBuilder')
    ->addMethodCall('addExtension', [new Reference('core_form_extension')])
    ->addMethodCall('addExtension', [new Reference('http_foundation_form_extension')])
    ->addMethodCall('addExtension', [new Reference('csrf_form_extension')])
    ->addMethodCall('addExtension', [new Reference('validator_form_extension')]);

$container->register('xliff_file_loader', '\Symfony\Component\Translation\Loader\XliffFileLoader');
$container
    ->register('translator', '\Symfony\Component\Translation\Translator')
    ->addArgument('pl')
    ->addMethodCall('addLoader', ['xlf', new Reference('xliff_file_loader')])
    ->addMethodCall('addResource', ['xlf', realpath(__DIR__ . '/../src/PAI/Resources/translation/messages.pl.xlf'), 'pl'])
    ->addMethodCall('addResource',
        [
            'xlf',
            realpath(__DIR__ . '/../vendor/symfony/form/Resources/translations/validators.pl.xlf'),
            'pl',
            'validators'
        ]
    )->addMethodCall('addResource',
        [
            'xlf',
            realpath(__DIR__ . '/../vendor/symfony/validator/Resources/translations/validators.pl.xlf'),
            'pl',
            'validators'
        ]
    );
$container->register('translator_form_extension', '\Symfony\Bridge\Twig\Extension\TranslationExtension')
    ->addArgument(new Reference('translator'));

$container->register('twig_loader_filesystem', '\Twig_Loader_Filesystem')
    ->addArgument(
        [
            realpath(__DIR__ . '/../src/PAI/Resources/views'),
            realpath(__DIR__ . '/../vendor/symfony/twig-bridge/Resources/views/Form')
        ]
    );

$container
    ->register('twig', '\Twig_Environment')
    ->addArgument(new Reference('twig_loader_filesystem'))
    ->addMethodCall('addExtension', [new Reference('twig_form_extension')])
    ->addMethodCall('addExtension', [new Reference('translator_form_extension')]);

$container
    ->register('twig_form_renderer_engine', '\Symfony\Bridge\Twig\Form\TwigRendererEngine')
    ->addArgument(['bootstrap_3_layout.html.twig'])
    ->addMethodCall('setEnvironment', [new Reference('twig')]);

$container
    ->register('twig_form_renderer', '\Symfony\Bridge\Twig\Form\TwigRenderer')
    ->addArgument(new Reference('twig_form_renderer_engine'))
    ->addArgument(new Reference('csrf_provider'));

$container
    ->register('twig_form_extension', '\Symfony\Bridge\Twig\Extension\FormExtension')
    ->addArgument(new Reference('twig_form_renderer'));

$response = (new FormController($container))->handleRequest(Request::createFromGlobals());
$response->send();
