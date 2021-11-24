<?php

namespace App\Controller\ApiV1;

use App\Entity\Themes;
use App\Repository\ThemesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
* @Route("/api/v1/themes", name="api_v1_themes_")
*/
class ThemesController extends AbstractController

{   
    /**
     * Affiche la liste des thèmes
     * @Route("/", name="list" , methods={"GET"})
     */
    public function browse(ThemesRepository $themesRepository): Response
    {   
        $allThemes = $themesRepository->findAll();

        return $this->json($allThemes, Response::HTTP_OK, [], ['groups' => 'api_themes_browse']);
    }

    /**
     * Affiche la liste des thèmes avec leurs palettes
     * @Route("/palettes", name="browseWithPalettes" , methods={"GET"})
     */
    public function browseWithPalettes(ThemesRepository $themesRepository): Response
    {   
        $allThemes=$themesRepository->findAll();

        return $this->json($allThemes, Response::HTTP_OK, [], ['groups' => 'api_themes_readWithPalettes']);
    }

    /** 
     * Affiche le détail d’un thème
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, ThemesRepository $themesRepository): Response
    {
        $theme = $themesRepository->find($id);

        if (is_null($theme)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($theme, Response::HTTP_OK, [], ['groups' => 'api_themes_browse']);
    }

    /** 
     * Liste des palettes selon un thème spécifique.
     * @Route("/{id}/palettes", name="readWithPalettes", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readWithPalettes(int $id, ThemesRepository $themesRepository, Request $request, PaginatorInterface $paginator): Response
    {  
        $theme = $themesRepository->find($id);
        $nbrPalettes = count($theme->getPalettes());
        
        $theme = $paginator->paginate (
            $theme->getPalettes() , /* requête ne résultat */ 
           $request->query->getInt ('page' , 1 ), /* numéro de page */ 
           20  /* limite par page */
       );        
        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $theme
        ];

        if (is_null($theme)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_themes_readWithPalettes']);
    }

    /**
     * Ajout d’un thème par un admin
     * @Route("/", name="add", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $jsonContent = $request->getContent();
        $theme = $serializer->deserialize($jsonContent, Themes::class, 'json');

        $errors = $validator->validate($theme);

        if(count($errors) > 0)
        {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($theme);
        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Thème créé',
            'id' => $theme->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Supprime un thème par un admin
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(int $id, ThemesRepository $themesRepository, EntityManagerInterface $entityManager): Response
    {
        // we get the palette in the database
        $theme = $themesRepository->find($id);

        if (is_null($theme)) {
            return $this->getNotFoundResponse();
        }


        // launching le flush
        $entityManager->remove($theme);
        $entityManager->flush();

        $reponseAsArray = [
            'message' => 'Thème supprimé',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }

    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvÃ©',
            'internalMessage' => 'Ce theme n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}