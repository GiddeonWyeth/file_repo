<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Directory;
use AppBundle\Form\DirectoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RepoController extends Controller
{


    /**
     * @param $dir_name
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/myRepo/{dir_name}", name="dir_show", defaults={"dir_name" = null})
     */
    public function indexAction($dir_name)
    {
        $directory = new Directory();
        $form = $this->createForm(DirectoryType::class, $directory);
        $form->add('submit', SubmitType::class, array('label' => 'Create Directory', 'attr' => array('class' => 'btn btn-default pull-right')));
        $request = $this->container->get('request_stack')->getCurrentRequest();;
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $directory->setUserId($user);
            $directory->setUserName($user->getUsername());
            $em = $this->getDoctrine()->getManager();
            if (!empty($dir_name)) {
                $parent_dir = $em->getRepository('AppBundle:Directory')->findOneBy(['encoded_name' => $dir_name]);
                $directory->setDirectoryId($parent_dir);
                $parent_path = $this->getPathAction($parent_dir->getId(), $em->createQueryBuilder())['path'];
                $directory->setPath($parent_path . '/' . $directory->getName());
            }
            $em->persist($directory);
            $directory->createDir();
            $em->flush();

        }
        return $this->render('UserBundle:Profile:dir_show.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    private static function getPathAction($id, $queryBuilder)
    {
        $qb = $queryBuilder;
        $qb->select('d.path')
            ->from('AppBundle:Directory', 'd')
            ->where('d.id = ?1')
            ->setParameter(1, $id);

        $query = $qb->getQuery();
        return $query->getSingleResult();

    }
}
