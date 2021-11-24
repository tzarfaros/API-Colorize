<?php

namespace App\Controller\ApiV1;

use App\Entity\Palettes;
use App\Repository\ColorsRepository;
use App\Repository\PalettesRepository;
use App\Repository\ThemesRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/palettes", name="api_v1_palettes_")
 */
class PalettesController extends AbstractController

{   
    /**
     * Affiche la liste des palettes
     * @Route("/colors", name="browseWithColors" , methods={"GET"})
     * 
     */
    public function browseWithColors(PalettesRepository $palettesRepository,PaginatorInterface $paginator, Request $request): Response
    {   
        $allPalettes = $palettesRepository->findPaletteIfPublic();

        $nbrPalettes = 0;

        foreach($allPalettes as $currentPalette) {
            $nbrPalettes += 1;
        }

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $allPalettes  
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_browseWithColors']);
    }

    /**
     * 
     * @Route("/features", name="browseFeatures" , methods={"GET"})
     * 
     */
    public function browseFeatures(PalettesRepository $palettesRepository): Response
    {
        $allPalettes = $palettesRepository->findByFeatures(true);

        $nbrPalettes = 0;

        foreach($allPalettes as $currentPalette) {
            $nbrPalettes += 1;
        }

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $allPalettes  
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_browseWithColors']);
    }

    /**
     * Affiche la liste des palettes avec ses dossiers liés
     * @Route("/files", name="browseWithFiles" , methods={"GET"} , requirements={"page"="\d+"})
     * @IsGranted("ROLE_USER")
     * 
     */
    public function browseWithFiles(PalettesRepository $palettesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $allPalettes = $palettesRepository->findAll();
        $allPalettes = $paginator->paginate (
            $allPalettes , /* requête ne résultat */ 
           $request->query->getInt ('page' , 1 ), /* numéro de page */ 
           20  /* limite par page */
       );
        
        return $this->json($allPalettes, Response::HTTP_OK, [], ['groups' => 'api_palettes_browseWithFiles']);
       
        $allPalettes = $palettesRepository->findPaletteIfPublicWithFiles();

        $nbrPalettes = 0;

        foreach($allPalettes as $currentPalette) {
            $nbrPalettes += 1;
        }

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $allPalettes 
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_browseWithFiles']);
    }

    /**
     * Affiche la liste des palettes avec ses utilisateurs liés
     * @Route("/user", name="browseWithUser" , methods={"GET"} , requirements={"page"="\d+"})
     */
    public function browseWithUser(PalettesRepository $palettesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $allPalettes = $palettesRepository->findPaletteIfPublicWithUser();

        $nbrPalettes = 0;

        foreach($allPalettes as $currentPalette) {
            $nbrPalettes += 1;
        }

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $allPalettes  
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_browseWithUser']);
    }

    /**
     * Affiche la liste des palettes avec ses thèmes liés
     * @Route("/themes", name="browseWithThemes" , methods={"GET"} , requirements={"page"="\d+"})
     */
    public function browseWithThemes(PalettesRepository $palettesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $allPalettes = $palettesRepository->findAll();

        $nbrPalettes = 0;

        foreach($allPalettes as $currentPalette) {
            $nbrPalettes += 1;
        }

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $allPalettes  
        ];
        
        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_browseWithThemes']);
    }

    /**
     * Affiche la liste des palettes par sauvegarde, nombre de likes ou date de création par toutes, générés ou crées par pagination 
     * @Route("/searchBySort", name="browseBySortAndFilter" , methods={"GET"} )
     */
    public function browseBySortAndFilter( PalettesRepository $palettesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $filter = $request->query->getInt('filter');
        $sort = $request->query->get('sort');
        
        if ($sort==='new') {
            $paletteBySort = $palettesRepository->findNewByPagesByFilters($filter);

            $nbrPalettes = 0;

            foreach($paletteBySort as $currentPalette) {
                $nbrPalettes += 1;
            }

            $paletteBySort = $paginator->paginate (
                $paletteBySort , /* requête ne résultat */ 
                $request->query->getInt ('page' , 1 ), /* numéro de page */ 
                20  /* limite par page */
            );

            $data = [
                'nbr_palettes' => $nbrPalettes,
                'list' => $paletteBySort
            ];

             return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithColors']);
        }
        if ($sort==='save') {
            $allPalettes = $palettesRepository->findPaletteIfPublicSave($filter);
            $paletteBySort = $palettesRepository->findSaveByPagesByFilters($filter);

            $nbrPalettes = 0;

            foreach($paletteBySort as $currentPalette) {
                $nbrPalettes += 1;
            }

            foreach($allPalettes as $currentPalette) {
                $nbrPalettes += 1;
            }

            $listPalettes = array_merge($paletteBySort, $allPalettes);
            $listPalettes = $paginator->paginate (
                $listPalettes , /* requête ne résultat */ 
                $request->query->getInt ('page' , 1 ), /* numéro de page */ 
                20  /* limite par page */
            );

            $data = [
                'nbr_palettes' => $nbrPalettes,
                'list' => $listPalettes
            ];

            return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithColors']);
        }

        if ($sort==='likes') {
            $paletteBySort = $palettesRepository->findLikesByPagesByFilters($filter);

            $nbrPalettes = 0;

            foreach($paletteBySort as $currentPalette) {
                $nbrPalettes += 1;
            }

            $paletteBySort = $paginator->paginate (
                $paletteBySort , /* requête ne résultat */ 
                $request->query->getInt ('page' , 1 ), /* numéro de page */ 
                20  /* limite par page */
            );

            $data = [
                'nbr_palettes' => $nbrPalettes,
                'list' => $paletteBySort
            ];

            return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithColors']);
        }
    }

    /**
     * Affiche la liste des palettes pour un thème donné par sauvegarde, nombre de likes ou date de création par toutes, générés ou crées par pagination 
     * @Route("/{theme}/searchBySort", name="browseByThemesBySortAndFilter" , methods={"GET"} )
     */
    public function browseByThemesBySortAndFilter(string $theme, PalettesRepository $palettesRepository, ThemesRepository $themesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $filter = $request->query->getInt('filter');
        $sort = $request->query->get('sort');

        if ($sort==='new') {
            $paletteBySort = $palettesRepository->findByThemeBySortByPagesByFilters($theme, $sort, $filter);

            $nbrPalettes = 0;

            foreach($paletteBySort as $currentPalette) {
                $nbrPalettes += 1;
            }

            $paletteBySort = $paginator->paginate (
                $paletteBySort , /* requête ne résultat */ 
                $request->query->getInt ('page' , 1 ), /* numéro de page */ 
                20  /* limite par page */
            );

            $data = [
                'nbr_palettes' => $nbrPalettes,
                'list' => $paletteBySort
            ];

             return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithColors']);
        }

        if ($sort==='save') {
            $allPalettes = $palettesRepository->findPaletteIfPublicSaveThemes($filter, $theme);
            $paletteBySort = $palettesRepository->findByThemeBySortByPagesByFilters($theme, $sort, $filter);

            $nbrPalettes = 0;

            foreach($paletteBySort as $currentPalette) {
                $nbrPalettes += 1;
            }

            foreach($allPalettes as $currentPalette) {
                $nbrPalettes += 1;
            }

            $listPalettes = array_merge($paletteBySort, $allPalettes);
            $listPalettes = $paginator->paginate (
                $listPalettes , /* requête ne résultat */ 
                $request->query->getInt ('page' , 1 ), /* numéro de page */ 
                20  /* limite par page */
            );

            $data = [
                'nbr_palettes' => $nbrPalettes,
                'list' => $listPalettes
            ];

            return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithColors']);
        }

        if ($sort==='likes') {
            $paletteBySort = $palettesRepository->findByThemeBySortByPagesByFilters($theme, $sort, $filter);

            $nbrPalettes = 0;

            foreach($paletteBySort as $currentPalette) {
                $nbrPalettes += 1;
            }

            $paletteBySort = $paginator->paginate (
                $paletteBySort , /* requête ne résultat */ 
                $request->query->getInt ('page' , 1 ), /* numéro de page */ 
                20  /* limite par page */
            );

            $data = [
                'nbr_palettes' => $nbrPalettes,
                'list' => $paletteBySort
            ];

            return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithColors']);
        }
    }
        
    /** 
     * Affiche une palette avec ses couleurs
     * @Route("/{id}/colors", name="readWithColors", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readWithColors (int $id, PalettesRepository $palettesRepository): Response
    {
        $palette = $palettesRepository->find($id);
       
        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($palette, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithColors']);
    }

    /** 
     * Affiche une liste de palette par nom de palette ou par nom d’utilisateur
     * @Route("/paletteoruser/{name}", name="readByNameOrUser", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readByNameOrUser(string $name, PalettesRepository $palettesRepository, UserRepository $userRepository): Response
    {
        $palette = $palettesRepository->findByName($name);        

        if (!$palette) {

            $user = $userRepository->findOneByUsername($name);
            $palettesCreated = $palettesRepository->findByOwner($user);

            $nbrPalettes = 0;

            foreach($palettesCreated as $currentPalette) {
                $nbrPalettes += 1;
            }

            $data = [
                'nbr_palettes' => $nbrPalettes,
                'list' => $palettesCreated  
            ];

            return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_readUserWithPalettes']);

            if (is_null($user)) {
                return $this->getNotFoundResponse();
            }
        }

        $nbrPalettes = 0;

        foreach($palette as $currentPalette) {
            $nbrPalettes += 1;
        }

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $palette  
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithColors']);
    }

    /** 
     * Affiche une palette avec ses couleurs et avec ses dossiers liés
     * @Route("/{id}/files", name="readWithFiles", methods={"GET"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function readWithFiles(int $id, PalettesRepository $palettesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $palette = $palettesRepository->find($id);
        
        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($palette, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithFiles']);
    }

    /** 
     * Affiche une palette avec ses couleurs et avec ses thèmes liés
     * @Route("/{id}/themes", name="readWithThemes", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readWithThemes(int $id, PalettesRepository $palettesRepository): Response
    {
        $palette = $palettesRepository->find($id);

        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($palette, Response::HTTP_OK, [], ['groups' => 'api_palettes_browseWithThemes']);
    }

    /** 
     * ffiche une palette avec ses couleurs et avec ses utilisateurs liés
     * @Route("/{id}/user", name="readWithUser", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readWithUser(int $id, PalettesRepository $palettesRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $palette = $palettesRepository->find($id);
        
        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($palette, Response::HTTP_OK, [], ['groups' => 'api_palettes_readWithUser']);
    }

    /**
     * Édite une palette
     * @Route("/{currentUser}/{id}", name="edit", methods={"PATCH"}, requirements={"currentUser"="\d+", "id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(ValidatorInterface $validator,int $currentUser, int $id, PalettesRepository $palettesRepository,  Request $request, 
    SerializerInterface $serializer, EntityManagerInterface $entityManager, ColorsRepository $colorsRepository, ThemesRepository $themesRepository, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($currentUser);
        $this->denyAccessUnlessGranted('edit', $user);
        $palette = $palettesRepository->find($id);
        
        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($user)) {
            return $this->getNotFoundResponseUser();
        }

        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Palettes::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $palette
        ]);
        
        $palette->setOwner($user);

        $errors = $validator->validate($palette);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $palette->setUpdatedAt(new DateTimeImmutable());

        foreach($palette->getColors() as $color){
            $exists = $colorsRepository->findOneByHex($color->getHex());
            if(!$exists){
                $palette->addColor($color);
                $entityManager->persist($color);
            }
            if($exists){
                $palette->removeColor($color);
                $palette->addColor($exists);
            } 
        }

        foreach($palette->getThemes() as $theme){
            $exists = $themesRepository->findOneByName($theme->getName());

            if($exists){
                $palette->removeTheme($theme);
                $palette->addTheme($exists);
            }
        }
        
        $entityManager->flush();

        $reponseAsArray = [
            'message' => 'Palette mise à jour',
            'id' => $palette->getId(),
            'updated_at' => $palette->getupdatedAt()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED, [], ['groups' => 'api_palettes_edit']);
    }

   
    /**
     * Ajouter feature à une palette par un admin
     * @Route("/{id}/features", name="editFeatures", methods={"PATCH"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editFeatures(ValidatorInterface $validator, int $id, PalettesRepository $palettesRepository,  Request $request, 
    SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $palette = $palettesRepository->find($id);
        
        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        $jsonContent = $request->getContent();
        //dd($jsonContent);
        $serializer->deserialize($jsonContent, Palettes::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $palette
        ]);

        $errors = $validator->validate($palette);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $entityManager->persist($palette);
        $entityManager->flush();
             
           
        $reponseAsArray = [
            'message' => 'Features mise à jour',
            'id' => $palette->getId(),
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Ajouter un like à une palette et relie un utilisateur à une palette
     * @Route("/{id}/{user}/like", name="editLike", methods={"PATCH"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function editLike(ValidatorInterface $validator, int $id, int $user, PalettesRepository $palettesRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $palette = $palettesRepository->find($id);
        $currentUser = $userRepository->find($user);
        $currentUser->addPalettesLike($palette);
        $nbrLikes = $palette->getNbrLikes();

        $this->denyAccessUnlessGranted('edit', $currentUser);

        $newNbrLikes = $nbrLikes + 1;
        $palette->setNbrLikes($newNbrLikes);

        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($currentUser)) {
            return $this->getNotFoundResponseUser();
        }
        
        $errors = $validator->validate($palette);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($palette);
        $entityManager->flush();
             
           
        $reponseAsArray = [
            'message' => 'Palette avec son nombre de like mise à jour',
            'id' => $palette->getId(),
            'updated_at' => $palette->getupdatedAt()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Supprimer un like à une palette et la relation entre un utilisateur à une palette
     * @Route("/{id}/{user}/dislike", name="editDislike", methods={"PATCH"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function editDislike(ValidatorInterface $validator, int $id, int $user, PalettesRepository $palettesRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $palette = $palettesRepository->find($id);
        $currentUser = $userRepository->find($user);
        $currentUser->removePalettesLike($palette);
        $nbrLikes = $palette->getNbrLikes();

        $this->denyAccessUnlessGranted('edit', $currentUser);

        $newNbrLikes = $nbrLikes - 1;
        $palette->setNbrLikes($newNbrLikes);

        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($currentUser)) {
            return $this->getNotFoundResponseUser();
        }
        
        $errors = $validator->validate($palette);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($palette);
        $entityManager->flush();
             
           
        $reponseAsArray = [
            'message' => 'Palette avec son nombre de like mise à jour',
            'id' => $palette->getId(),
            'updated_at' => $palette->getupdatedAt()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Création d’une palette par un utilisateur
     * @Route("/{currentUser}", name="add" , methods={"POST"} , requirements={"page"="\d+"})
     */
    public function add(int $currentUser, ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ColorsRepository $colorsRepository, UserRepository $userRepository, ThemesRepository $themesRepository): Response
    {
        $user = $userRepository->find($currentUser);
        $jsonContent = $request->getContent();
        $palettes = $serializer->deserialize($jsonContent, Palettes::class, 'json');


        if (is_null($user)) {
            return $this->getNotFoundResponseUser();
        }

        $errors = $validator->validate($palettes);

        if (count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $palettes->setOwner($user);

        foreach($palettes->getColors() as $color){
            $exists = $colorsRepository->findOneByHex($color->getHex());

            if(!$exists){
                $palettes->addColor($color);
                $entityManager->persist($color);
            }
            if($exists){
                $palettes->removeColor($color);
                $palettes->addColor($exists);
            } 
        }

        foreach($palettes->getThemes() as $theme){
            $exists = $themesRepository->findOneByName($theme->getName());

            if($exists){
                $palettes->removeTheme($theme);
                $palettes->addTheme($exists);
            }
        }

        $entityManager->persist($palettes);
        $entityManager->flush();
            
        $reponseAsArray = [
            'message' => ' nouvelle palette créé',
            'id' => $palettes->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Supprimer une palette
     * @Route("/{id}/{currentUser}", name="delete", methods={"DELETE"}, requirements={"id"="\d+", "currentUser"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(int $id, int $currentUser, PalettesRepository $palettesRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $palette = $palettesRepository->find($id);
        $user = $userRepository->find($currentUser);
        //dd($palette->getOwner());
        $this->denyAccessUnlessGranted('delete', $user);

        if (is_null($palette)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($user)) {
            return $this->getNotFoundResponseUser();
        }

        if ($palette->getOwner() === $user) {
            $entityManager->remove($palette);
            $entityManager->flush();
        }
        
        $reponseAsArray = [
            'message' => 'Palette supprimÃ©',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }

    private function getNotFoundResponse()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvée',
            'internalMessage' => 'Cette palette n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function getNotFoundResponseUser()
    {
        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvée',
            'internalMessage' => 'Cet utilisateur n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
