<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilesType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $action_path = (!empty($options['action'])) ? "/myRepoFunctions/filesSubmit/{$options['action']}" : "/myRepoFunctions/filesSubmit";
        $builder->add('files', FileType::class, ['multiple' => true])
            ->setAction($action_path);;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'AppBundle\Entity\Files'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_bundle_files_type';
    }
}
