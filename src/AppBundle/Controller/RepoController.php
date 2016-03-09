<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Directory;
use AppBundle\Entity\File;
use AppBundle\Entity\Files;
use AppBundle\Form\DirectoryType;
use AppBundle\Form\FilesType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

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
        $directoryForm = $this->createForm(DirectoryType::class, $directory, ['action' => $dir_name]);
        $directoryForm->add('submit', SubmitType::class, array('label' => 'Create Directory', 'attr' => array('class' => 'btn btn-default pull-right')));

        $files = new Files();
        $filesForm = $this->createForm(FilesType::class, $files, ['action' => $dir_name]);
        $filesForm->add('submit', SubmitType::class, array('label' => 'Upload Files', 'attr' => array('class' => 'btn btn-default pull-right')));

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $current_dir = $dir_name ? $em->getRepository('AppBundle:Directory')->findOneBy(['encodedName' => $dir_name]) : null;
        $sub_directories = $dir_name ? $current_dir->getDirectories()->toArray() : self::getRootDirectories($user, $em);
        $sub_files = $dir_name ? $current_dir->getFiles()->toArray() : self::getRootFiles($em);

        return $this->render('UserBundle:Profile:dir_show.html.twig', [
            'user' => $user,
            'directoryForm' => $directoryForm->createView(),
            'filesForm' => $filesForm->createView(),
            'sub_directories' => $sub_directories,
            'sub_files' => $sub_files,
            'current_directory' => $current_dir
        ]);
    }
    //TODO: Перенести кастомные запросы к бд в репозитории

    /**
     * @param User $user
     * @param ObjectManager $em
     * @return mixed
     */
    private static function getRootDirectories(User $user, ObjectManager $em)
    {
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('d')
            ->from('AppBundle:Directory', 'd')
            ->where('d.user = ?1 AND d.directory IS NULL')
            ->setParameter(1, $user->getId());

        $query = $queryBuilder->getQuery();
        unset($queryBuilder);
        return $query->getResult();
    }

    private static function getRootFiles(ObjectManager $em)
    {
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->select('f')
            ->from('AppBundle:File', 'f')
            ->where('f.directory IS NULL');

        $query = $queryBuilder->getQuery();
        unset($queryBuilder);
        return $query->getResult();
    }

    /**
     * @param Request $request
     * @param $parent_dir
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/myRepoFunctions/directorySubmit/{parent_dir}", name="directorySubmit", defaults={"parent_dir" = null})
     */
    public function directorySubmitAction($parent_dir, Request $request)
    {


        $directory = new Directory();
        $directoryForm = $this->createForm(DirectoryType::class, $directory);
        $directoryForm->add('submit', SubmitType::class, array('label' => 'Create Directory', 'attr' => array('class' => 'btn btn-default pull-right')));


        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();


        $current_dir = $parent_dir ? $em->getRepository('AppBundle:Directory')->findOneBy(['encodedName' => $parent_dir]) : null;
        $directoryForm->handleRequest($request);
        if ($directoryForm->isValid()) {

            $directory->setUserId($user);
            $directory->setUserName($user->getUsername());

            if (!empty($parent_dir)) {
                $directory->setDirectory($current_dir);
                $parent_path = self::getPath($current_dir, $em)['path'];
                $directory->setPath($parent_path . '/' . $directory->getName());
            }

            $em->persist($directory);
            $directory->createDir();

            $em->flush();
            return $this->redirectToRoute('dir_show', ['dir_name' => $parent_dir]);
        }

        return false;
    }

    /**
     * @param Directory $id
     * @param ObjectManager $em
     * @return mixed
     */
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

    /**
     * @Route("/myRepoFunctions/filesSubmit/{parent_dir}", name="filesSubmit", defaults={"parent_dir" = null})
     */
    public function filesSubmitAction($parent_dir, Request $request)
    {
        $files = new Files();
        $filesForm = $this->createForm(FilesType::class, $files, ['action' => $parent_dir]);
        $filesForm->add('submit', SubmitType::class, array('label' => 'Upload Files', 'attr' => array('class' => 'btn btn-default pull-right')));

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $current_dir = $parent_dir ? $em->getRepository('AppBundle:Directory')->findOneBy(['encodedName' => $parent_dir]) : null;
        $filesForm->handleRequest($request);
        if ($filesForm->isValid()) {
            $filesArray = $files->getFiles();
            foreach ($filesArray as $item) {
                $file = new File();
                $file->setDirectory($current_dir);
                $file->setName($item->getClientOriginalName());
                $em->persist($file);
                $file->uploadFile($item, $user, $current_dir);
            }
            $em->flush();
            return $this->redirectToRoute('dir_show', ['dir_name' => $parent_dir]);
        }
        return false;
    }

}
