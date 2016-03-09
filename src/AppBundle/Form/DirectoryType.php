<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirectoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $action_path = (!empty($options['action'])) ? "/myRepoFunctions/directorySubmit/{$options['action']}" : "/myRepoFunctions/directorySubmit";
        $builder->add('name')
            ->add('isPrivate', CheckboxType::class, ['required' => false])
            ->setAction($action_path);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'AppBundle\Entity\Directory'));
    }
}
