<?php

namespace App\Controller\ApiV1;

use App\Entity\Colors;
use App\Repository\ColorsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
    * @Route("/api/v1/colors", name="api_v1_colors_")
*/
class ColorsController extends AbstractController
{
    /**
     * Affiche la liste des couleurs
     * @Route("/", name="browse", methods={"GET"})
     */
    public function browse(ColorsRepository $colorsRepository): Response
    {
        $allColors = $colorsRepository->findAll();

        return $this->json($allColors, Response::HTTP_OK, [], ['groups' => 'api_colors_browse']);
    }

    /**
     * Affiche les détails d’une couleur
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, ColorsRepository $colorsRepository): Response
    {
        $color = $colorsRepository->find($id);

        if (is_null($color)) {
            return $this->getNotFoundResponse();
        }

        return $this->json($color, Response::HTTP_OK, [], ['groups' => 'api_colors_browse']);
    }

    /**
     * Ajout d’une couleur
     * @Route("/", name="add", methods={"POST"})
     */
    public function add(ValidatorInterface $validator, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ColorsRepository $colorsRepository): Response
    {
        $jsonContent = $request->getContent();
        $color = $serializer->deserialize($jsonContent, Colors::class, 'json');

        $errors = $validator->validate($color);

        if(count($errors) > 0)
        {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];

            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $colorsList = $colorsRepository->findAll();

        foreach($colorsList as $currentColor) {
            if ($color->getHex() === $currentColor->getHex()) {
                $entityManager->remove($color);
                $reponseAsArray = [
                    'message' => 'Couleur non créée car déjà existante',
                    'id' => $color->getId()
                ];
            } else {
                $entityManager->persist($color);
                $reponseAsArray = [
                    'message' => 'Couleur créée',
                    'id' => $color->getId()
                ];
            }
        }

        $entityManager->flush();
        return $this->json($reponseAsArray, Response::HTTP_CREATED);
    }

    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cette couleur n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

