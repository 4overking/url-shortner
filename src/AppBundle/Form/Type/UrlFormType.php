<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Url;
use AppBundle\Exception\HostUnavailableException;
use AppBundle\Exception\ShortNameUsedException;
use AppBundle\Service\ShortenerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UrlFormType extends AbstractType
{
    /**
     * @var ShortenerService
     */
    private $shortener;

    /**
     * UrlFormType constructor.
     * @param ShortenerService $shortener
     */
    public function __construct(ShortenerService $shortener)
    {
        $this->shortener = $shortener;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('originalUrl', TextType::class, [
                'label'          => 'Url',
                'constraints'    => [
                    new Assert\NotNull(),
                    new Assert\Url(),
                ],
            ])
            ->add('shortTag', TextType::class, [
                'required' => false,
            ])
        ;
        $builder
            ->get('originalUrl')
            ->addModelTransformer(new CallbackTransformer(
                function ($url) {
                    return $url;
                },
                function ($url) {
                    if (!preg_match('/^(http|https):\/\//', $url)) {
                        $url = 'http://'.$url;
                    }

                    return $url;
                }
            ));
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $url = $form->getData();
            try {
                $this->shortener->generate($url);
            } catch (ShortNameUsedException $e) {
                $form->get('shortTag')->addError(new FormError($e->getMessage()));
            } catch (HostUnavailableException $e) {
                $form->get('originalUrl')->addError(new FormError($e->getMessage()));
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Url::class,
        ]);
    }
}
