<?php

namespace App\Controller\ApiV1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class mainController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(): Response
    
    {
        return $this->render('api_v1/main_controller/homepage.html.twig');
    }

    /**
     * @Route("/users", name="usersPage")
     */
    public function usersPage(): Response
    {
        return $this->render('api_v1/main_controller/users.html.twig');
    }

    /**
     * @Route("/palettes", name="palettesPage")
     */
    public function palettesPage(): Response
    {
        return $this->render('api_v1/main_controller/palettes.html.twig');
    }

    /**
     * @Route("/themes", name="themesPage")
     */
    public function themesPage(): Response
    {
        return $this->render('api_v1/main_controller/themes.html.twig');
    }

    /**
     * @Route("/files", name="filesPage")
     */
    public function filesPage(): Response
    {
        return $this->render('api_v1/main_controller/files.html.twig');
    }

    /**
     * @Route("/colors", name="colorsPage")
     */
    public function colorsPage(): Response
    {
        return $this->render('api_v1/main_controller/colors.html.twig');
    }
}
