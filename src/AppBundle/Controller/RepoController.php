<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Directory;
use AppBundle\Form\DirectoryType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RepoController extends Controller
{


    private static function getChildDirectories(Directory $directory, ObjectManager $em)
    {
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('d')
            ->from('AppBundle:Directory', 'd')
            ->where('d.directory = ?1')
            ->setParameter(1, $directory->getId());

        $query = $queryBuilder->getQuery();
        unset($queryBuilder);
        return $query->getResult();
    }

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
        $em = $this->getDoctrine()->getManager();
        $current_dir = $em->getRepository('AppBundle:Directory')->findOneBy(['encoded_name' => $dir_name]);
        $root = $em->getRepository('AppBundle:Directory')->getTree();
        var_dump($root);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $directory->setUserId($user);
            $directory->setUserName($user->getUsername());

            if (!empty($dir_name)) {
                $directory->setId(8);
                $directory->setChildNodeOf($current_dir);
                $parent_path = self::getPath($current_dir, $em)['path'];
                $directory->setPath($parent_path . '/' . $directory->getName());
            }

            $em->persist($directory);
            $directory->createDir();
            $em->flush();
        }

        return $this->render('UserBundle:Profile:dir_show.html.twig', array('user' => $user, 'form' => $form->createView()));
    }

    private static function getPath(Directory $id, ObjectManager $em)
    {
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('d.path')
            ->from('AppBundle:Directory', 'd')
            ->where('d.id = ?1')
            ->setParameter(1, $id->getId());

        $query = $queryBuilder->getQuery();
        unset($queryBuilder);
        return $query->getSingleResult();

    }
}
