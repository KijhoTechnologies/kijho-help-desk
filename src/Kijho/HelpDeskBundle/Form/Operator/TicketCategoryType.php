<?php

namespace Kijho\HelpDeskBundle\Form\Operator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Kijho\HelpDeskBundle\Entity\TicketCategory;

class TicketCategoryType extends AbstractType {

    private $container;
    private $translator;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->translator = $this->container->get('translator');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if ($data instanceof TicketCategory) {
                $creationDate = $data->getCreationDate();

                if (!$creationDate) {
                    $form->add('submit', SubmitType::class, array(
                        'label' => $this->translator->trans('help_desk.global.create'),
                    ));
                } else {
                    $form->add('submit', SubmitType::class, array(
                        'label' => $this->translator->trans('help_desk.global.save_changes'),
                    ));
                }
            }
        });

        $builder
                ->add('name', Type\TextType::class, array(
                    'required' => true,
                    'label' => $this->translator->trans('help_desk.ticket_category.name'),
                ))
                ->add('email', Type\EmailType::class, array(
                    'required' => true,
                    'label' => $this->translator->trans('help_desk.ticket_category.email'),
                ))
                ->add('isEnabled', Type\CheckboxType::class, array(
                    'required' => false,
                    'label' => $this->translator->trans('help_desk.ticket_category.enabled'),
                ))
                ->add('description', Type\TextareaType::class, array(
                    'required' => false,
                    'label' => $this->translator->trans('help_desk.ticket_category.description'),
                    'attr' => array(
                        'rows' => 4
                    )
                ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Kijho\HelpDeskBundle\Entity\TicketCategory'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'helpdeskbundle_operator_ticket_category_type';
    }

}
