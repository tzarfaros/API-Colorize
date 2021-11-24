<?php

namespace App\Controller\ApiV1;

use App\Entity\Files;
use App\Entity\User;
use App\Repository\FilesRepository;
use App\Repository\PalettesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
* @Route("/api/v1/user", name="api_v1_user_")
*/
class UserController extends AbstractController
{

    /**
     * Affiche la liste des utilisateurs
     * @Route("/", name="browse", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function browse(UserRepository $userRepository): Response
    {
        $allUser = $userRepository->findAll();

        return $this->json($allUser, Response::HTTP_OK, [], ['groups' => 'api_user_browse']);
    }

    /**
     * Affiche les détails d’un utilisateur
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, UserRepository $userRepository): Response
    {
        $basicUser = $userRepository->find($id);
        $this->denyAccessUnlessGranted('view', $basicUser);

        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($basicUser, Response::HTTP_OK, [], ['groups' => 'api_user_browse']);
    }

    /**
     * Affiche toutes les palettes liées aux dossiers d’un utilisateur donné
     * @Route("/{id}/palettes/favorites", name="readWithPalettesFavorites", methods={"GET"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function readWithPalettesFavorites(int $id, UserRepository $userRepository, PalettesRepository $palettesRepository): Response
    {
        $basicUser = $userRepository->find($id);
        $nbrPalettes = count($basicUser->getPalettesFavorites());
        $this->denyAccessUnlessGranted('view', $basicUser);

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $basicUser
        ];
        
        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_user_readWithPalettesFavorites']);
    }
    
    /**
     * Afficher toutes les palettes créées d’un utilisateur donné
     * @Route("/{id}/palettes/created", name="readWithPalettescreated", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readWithPalettescreated(int $id, UserRepository $userRepository, PalettesRepository $palettesRepository): Response
    {
        $basicUser = $userRepository->find($id);
        $palettesCreated = $palettesRepository->findByOwner($basicUser);
        
        $nbrPalettes = 0;

        foreach($palettesCreated as $currentPalette) {
            $nbrPalettes += 1;
        }

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $basicUser  
        ];

        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_user_readWithPalettescreated']);
    }

    /**
     * Afficher toutes les palettes likées d’un utilisateur donné
     * @Route("/{id}/palettes/likes", name="readWithPalettesLikes", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readWithPalettesLikes(int $id, UserRepository $userRepository): Response
    {
        $basicUser = $userRepository->find($id);
        $nbrPalettes = count($basicUser->getPalettesLikes());
        $this->denyAccessUnlessGranted('view', $basicUser);

        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $basicUser  
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_user_readWithPalettesLikes']);
    }

    /**
     * Afficher toutes les palettes likées d’un utilisateur donné par pagination
     * @Route("/{id}/palettes/like", name="readWithPalettesLikesProd", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function readWithPalettesLikesProd(int $id, UserRepository $userRepository,PalettesRepository $palettesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $basicUser = $userRepository->find($id);
        $listPalettes = $palettesRepository->findPaletteLikes($id);
        $nbrPalettes = count($basicUser->getPalettesLikes());
        $this->denyAccessUnlessGranted('view', $basicUser);

        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        $listPalettes = $paginator->paginate (
            $listPalettes , /* requête ne résultat */ 
            $request->query->getInt ('page' , 1 ), /* numéro de page */ 
            20  /* limite par page */
        ); 

        $data = [
            'nbr_palettes' => $nbrPalettes,
            'list' => $listPalettes  
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'api_user_readWithPalettesLikes']);
    }

    /**
     * Affiche un utilisateur avec ses dossiers
     * @Route("/{id}/files", name="readWithFiles", methods={"GET"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function readWithFiles(int $id, UserRepository $userRepository): Response
    {
        $basicUser = $userRepository->find($id);
        $this->denyAccessUnlessGranted('view', $basicUser);

        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($basicUser, Response::HTTP_OK, [], ['groups' => 'api_user_readWithFiles']);
    }

    /**
     * Affiche un utilisateur avec un dossier donné
     * @Route("/{id}/files/{idFiles}", name="readWithFile", methods={"GET"}, requirements={"id"="\d+", })
     * @IsGranted("ROLE_USER")
     */
    public function readWithFile(int $id, int $idFiles, UserRepository $userRepository, FilesRepository $filesRepository): Response
    {
        $basicUser = $userRepository->find($id);
        $file = $filesRepository->find($idFiles);

        $this->denyAccessUnlessGranted('view', $basicUser);
            
        $dataArray = [
            'user' => $basicUser,
            'file' => $file,
        ];

        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($file)) {
            return $this->getNotFoundFilesResponse();
        }

        return $this->json($dataArray, Response::HTTP_OK, [], ['groups' => 'api_user_readWithFiles']);
    }

    /**
     * Modifier un utilisateur
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(ValidatorInterface $validator, int $id, UserRepository $userRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $basicUser = $userRepository->find($id);
        $this->denyAccessUnlessGranted('edit', $basicUser);

        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        $jsonContent = $request->getContent();
        
        $jsonArray = json_decode($request->getContent(), true);
            $needsHash = false;
            if (isset($jsonArray['password'])) {
            $needsHash = true;
        };

        $serializer->deserialize($jsonContent, User::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $basicUser
        ]);
        
        
        if ($needsHash)
        {
            $clearPassword = $basicUser->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($basicUser, $clearPassword);
            $basicUser->setPassword($hashedPassword);
        }

        $errors = $validator->validate($basicUser);

        if(count($errors) > 0)
        {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'utilisateur mis à jour',
            'id' => $basicUser->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Ajout d’un utilisateur avec un dossier par défaut
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $jsonContent = $request->getContent();
        $basicUser = $serializer->deserialize($jsonContent, User::class, 'json');

        $errors = $validator->validate($basicUser);

        if(count($errors) > 0)
        {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $clearPassword = $basicUser->getPassword();
        
        if (!empty($clearPassword))
        {
            $hashedPassword = $passwordHasher->hashPassword($basicUser, $clearPassword);
            $basicUser->setPassword($hashedPassword);
        }
        $entityManager->persist($basicUser);
        $entityManager->flush();
        
        $file = new Files;
        $entityManager->persist($file);
    
        $file->setName('Default');
        $file->setUser($basicUser);
    
        $entityManager->flush();

        $reponseAsArray = [
            'message' => 'utilisateur créé',
            'id' => $basicUser->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    
    /**
     * Supprimer un utilisateur et ses dossiers
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager, FilesRepository $filesRepository): Response
    {
        $basicUser = $userRepository->find($id);
        $listFiles = $filesRepository->findAll();

        $this->denyAccessUnlessGranted('delete', $basicUser);

        foreach ($listFiles as $currentFiles) {
            if ($currentFiles->getUser() === $basicUser) {
                $entityManager->remove($currentFiles);
            }
        }

        if (is_null($basicUser)) {
            return $this->getNotFoundResponse();
        }

        $entityManager->remove($basicUser);
        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'utilisateur supprimé',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }
    
    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cet utilisateur n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function getNotFoundFilesResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Ce dossier n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

