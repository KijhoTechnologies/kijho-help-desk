<?php

namespace Kijho\HelpDeskBundle\Form\Client;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Kijho\HelpDeskBundle\Entity\Ticket;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Kernel;

class TicketType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->translator = $options['translator'];
        $version = "3.0.0";
        $symfonyVersion = Kernel::VERSION;

        $ticket = new Ticket;
        /**
         * symfonyVersion  < version for symfony 2.8
         */
        if (version_compare($symfonyVersion, $version) == '-1') {
            $priorityOptions = array(
                Ticket::PRIORITY_URGENT => $this->translator->trans($ticket->getTextPriority(Ticket::PRIORITY_URGENT)),
                Ticket::PRIORITY_HIGH => $this->translator->trans($ticket->getTextPriority(Ticket::PRIORITY_HIGH)),
                Ticket::PRIORITY_MEDIUM => $this->translator->trans($ticket->getTextPriority(Ticket::PRIORITY_MEDIUM)),
                Ticket::PRIORITY_LOW => $this->translator->trans($ticket->getTextPriority(Ticket::PRIORITY_LOW)),
            );
        } else {
            $priorityOptions = array(
                $this->translator->trans($ticket->getTextPriority(Ticket::PRIORITY_URGENT)) => Ticket::PRIORITY_URGENT,
                $this->translator->trans($ticket->getTextPriority(Ticket::PRIORITY_HIGH)) => Ticket::PRIORITY_HIGH,
                $this->translator->trans($ticket->getTextPriority(Ticket::PRIORITY_MEDIUM)) => Ticket::PRIORITY_MEDIUM,
                $this->translator->trans($ticket->getTextPriority(Ticket::PRIORITY_LOW)) => Ticket::PRIORITY_LOW,
            );
        }


        $builder
                ->add('subject', Type\TextType::class, array(
                    'required' => true,
                    'label' => $this->translator->trans('help_desk.tickets.subject'),
                    'attr' => array(
                        'maxlength' => 100
                    )
                ))
                ->add('body', Type\TextareaType::class, array(
                    'required' => false,
                    'label' => $this->translator->trans('help_desk.tickets.message')
                ))
                ->add('priority', Type\ChoiceType::class, array(
                    'required' => true,
                    'label' => $this->translator->trans('help_desk.tickets.priority'),
                    'choices' => $priorityOptions,
                ))
                ->add('category', EntityType::class, array(
                    'class' => 'HelpDeskBundle:TicketCategory',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('tc')
                                ->where('tc.isEnabled = TRUE')
                                ->orderBy('tc.name', 'ASC');
                    },
                    'required' => true,
                    'label' => $this->translator->trans('help_desk.tickets.category'),
                    'placeholder' => $this->translator->trans('help_desk.global.select'),
                ))
                ->add('submit', SubmitType::class, array(
                    'label' => $this->translator->trans('help_desk.global.send'),
                ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setRequired('translator');
        $resolver->setDefaults(array(
            'data_class' => 'Kijho\HelpDeskBundle\Entity\Ticket'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'helpdeskbundle_client_ticket_type';
    }

}
