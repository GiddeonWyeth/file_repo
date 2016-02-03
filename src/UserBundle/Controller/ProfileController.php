<?php
namespace UserBundle\Controller;

use AppBundle\Entity\Directory;
use AppBundle\Form\DirectoryType;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class ProfileController extends BaseController
{
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $directory = new Directory();
        $form = $this->createForm(DirectoryType::class, $directory);
        $form->add('submit', SubmitType::class, array('label' => 'Create Directory', 'attr' => array('class' => 'btn btn-default pull-right')));
        $request = $this->container->get('request_stack')->getCurrentRequest();;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $directory->setUserId($user);
            $directory->setUserName($user->getUsername());
            $directory->createDir();
            $em = $this->getDoctrine()->getManager();
            $em->persist($directory);
            $em->flush();
        }
        return $this->render('FOSUserBundle:Profile:show.html.twig', array('user' => $user, 'form' => $form->createView()));
    }


}
