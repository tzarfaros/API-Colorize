<?php

namespace App\Controller\ApiV1;

use App\Entity\Files;
use App\Repository\FilesRepository;
use App\Repository\PalettesRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
    * @Route("/api/v1/files", name="api_v1_files_")
    * @IsGranted("ROLE_USER")
*/
class FilesController extends AbstractController
{
    /**
     * Affiche les dossiers d’un utilisateur
     * @Route("/{currentUser}", name="browse", methods={"GET"}), requirements={"currentUser"="\d+"})
     */
    public function browse(FilesRepository $filesRepository, int $currentUser, UserRepository $userRepository): Response
    {
        $allFiles = $filesRepository->findAll();
        $basicUser = $userRepository->find($currentUser);
        $this->denyAccessUnlessGranted('view', $basicUser);

        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }

        return $this->json($allFiles, Response::HTTP_OK, [], ['groups' => 'api_files_browse']);
    }

    /**
     * Affiche les dossiers d’un utilisateur avec les palettes
     * @Route("/palettes/{currentUser}", name="browseWithPalettes", methods={"GET"}), requirements={"currentUser"="\d+"})
     */
    public function browseWithPalettes(FilesRepository $filesRepository, int $currentUser, UserRepository $userRepository): Response
    {
        $allFiles = $filesRepository->findAll();
        $basicUser = $userRepository->find($currentUser);
        $this->denyAccessUnlessGranted('view', $basicUser);

        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }

        return $this->json($allFiles, Response::HTTP_OK, [], ['groups' => 'api_files_browseWithPalettes']);
    }

    /**
     * Affiche un dossier d’un utilisateur
     * @Route("/{id}/{currentUser}", name="read", methods={"GET"}, requirements={"id"="\d+", "currentUser"="\d+"})
     */
    public function read(int $id, int $currentUser, UserRepository $userRepository, FilesRepository $filesRepository): Response
    {
        $file = $filesRepository->find($id);
        $basicUser = $userRepository->find($currentUser);
        $this->denyAccessUnlessGranted('view', $basicUser);

        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }

        return $this->json($file, Response::HTTP_OK, [], ['groups' => 'api_files_browse']);
    }

    /**
     * Affiche un dossier d’un utilisateur avec les palettes
     * @Route("/{id}/palettes/{currentUser}", name="readWithPalettes", methods={"GET"}, requirements={"id"="\d+", "currentUser"="\d+"})
     */
    public function readWithPalettes(int $id, int $currentUser, UserRepository $userRepository, FilesRepository $filesRepository): Response
    {
        $file = $filesRepository->find($id);
        $basicUser = $userRepository->find($currentUser);
        $this->denyAccessUnlessGranted('view', $basicUser);

        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }

        return $this->json($file, Response::HTTP_OK, [], ['groups' => 'api_files_browseWithPalettes']);
    }

    /**
     * Modifier un dossier
     * @Route("/{id}/{currentUser}", name="edit", methods={"PATCH"}, requirements={"id"="\d+", "currentUser"="\d+"})
     */
    public function edit(ValidatorInterface $validator, int $id, int $currentUser, UserRepository $userRepository, FilesRepository $filesRepository, PalettesRepository $palettesRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $file = $filesRepository->find($id);
        $basicUser = $userRepository->find($currentUser);
        $this->denyAccessUnlessGranted('view', $basicUser);
        
        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }

        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Files::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $file
        ]);

        $errors = $validator->validate($file);

        if(count($errors) > 0)
        {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
  
        $entityManager->persist($file);
        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Dossier mis à jour',
            'id' => $file->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Modifier un dossier et ajoute une palette 
     * @Route("/{id}/{currentPalette}/{currentUser}", name="editAddPalette", methods={"PATCH"}, requirements={"id"="\d+", "currentPalette"="\d+", "currentUser"="\d+"})
     */
    public function editAddPalette(ValidatorInterface $validator, int $id, int $currentPalette, int $currentUser, UserRepository $userRepository, FilesRepository $filesRepository, PalettesRepository $palettesRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $palette = $palettesRepository->find($currentPalette);
        $file = $filesRepository->find($id);
        $basicUser = $userRepository->find($currentUser);
        
        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($palette)) {
            return $this->getNotFoundResponsePalette();
        }

        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }

        $jsonContent = $request->getContent();

        $serializer->deserialize($jsonContent, Files::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $file
        ]);

        $file->addPalette($palette);

        $errors = $validator->validate($file);

        if(count($errors) > 0)
        {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
  
        $entityManager->persist($file);
        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Dossier mis à jour, palette ajoutée',
            'id' => $file->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Ajout d’un dossier pour un utilisateur
     * @Route("/user/{id}", name="addFilesForUser", methods={"POST"}), requirements={"id"="\d+"})
     */
    public function addFilesForUser(int $id,ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $basicUser = $userRepository->find($id);
        $this->denyAccessUnlessGranted('edit', $basicUser);

        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }

        $jsonContent = $request->getContent();
        $file = $serializer->deserialize($jsonContent, Files::class, 'json');
        $basicUser->addFilesPersonnel($file);

        $errors = $validator->validate($file);

        if(count($errors) > 0)
        {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($file);
        $entityManager->persist($basicUser);
        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Dossier créé',
            'id' => $file->getId()
        ];

        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    /**
     * Supprimer un dossier
     * @Route("/{id}/{currentUser}", name="delete", methods={"DELETE"}, requirements={"id"="\d+", "currentUser"="\d+"})
     */
    public function delete(int $id, int $currentUser, FilesRepository $filesRepository, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $file = $filesRepository->find($id);
        $basicUser = $userRepository->find($currentUser);
        $this->denyAccessUnlessGranted('delete', $basicUser);

        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }
        
        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }
  
        $entityManager->remove($file);
        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Dossier supprimé',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }

    /**
     * Supprime une palette dans un dossier
     * @Route("/{id}/palettes/{palette}/{currentUser}", name="deletePaletteInFiles", methods={"DELETE"}, requirements={"id"="\d+", "palette"="\d+"})
     */
    public function deletePaletteInFiles(int $id, int $palette, int $currentUser,  FilesRepository $filesRepository, UserRepository $userRepository, EntityManagerInterface $entityManager, PalettesRepository $palettesRepository): Response
    {
        $currentPalette = $palettesRepository->find($palette);
        $file = $filesRepository->find($id);
        $basicUser = $userRepository->find($currentUser);
        $this->denyAccessUnlessGranted('delete', $basicUser);

        if (is_null($file)) {
            return $this->getNotFoundResponse();
        }

        if (is_null($palette)) {
            return $this->getNotFoundResponsePalette();
        }

        if (is_null($basicUser)) {
            return $this->getNotFoundResponseUser();
        }

        $file->removePalette($currentPalette);
        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'Palette dans le dossier supprimée',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    }

    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Ce dossier n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function getNotFoundResponsePalette() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cette palette n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function getNotFoundResponseUser() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cet utilisateur n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

}
