<?php

namespace Kijho\HelpDeskBundle\Form\Operator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as Type;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TicketCommentType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $this->translator = $options['translator'];

        $builder
                ->add('comment', Type\TextareaType::class, array(
                    'required' => true,
                    'label' => $this->translator->trans('help_desk.ticket_comment.type_message'),
                    'attr' => array(
                        'rows' => 4
                    )
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
            'data_class' => 'Kijho\HelpDeskBundle\Entity\TicketComment'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'helpdeskbundle_operator_ticket_comment_type';
    }

}
