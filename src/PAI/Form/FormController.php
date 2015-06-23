<?php

namespace PAI\Form;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FormController
{
    /** @var null|ContainerBuilder */
    private $container = null;

    /**
     * Injects dependencies.
     *
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * Handles form request.
     *
     * @param Request $request
     * @return Response
     */
    public function handleRequest(Request $request)
    {
        /** @var \Symfony\Component\Form\FormFactory $formFactory */
        $formFactory = $this->container->get('form_factory_builder')->getFormFactory();
        $form = $formFactory->create(new FormType);

        $form->handleRequest($request);
        if ($form->isValid()) {
            return new Response($this->container->get('twig')->render('success.html.twig', ['data' => $form->getData()]));
        }

        return new Response($this->container->get('twig')->render('index.html.twig', ['form' => $form->createView()]));
    }
}
