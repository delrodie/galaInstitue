<?php

namespace App\Controller;

use App\Repository\PaysRepository;
use App\Utilities\GestionParticipation;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/participation')]
class ParticipationController extends AbstractController
{
	private $gestionParticipation;
	
	public function __construct(GestionParticipation $gestionParticipation)
	{
		$this->gestionParticipation = $gestionParticipation;
	}
	
    #[Route('/', name: 'app_participation', methods:['GET','POST'])]
    public function index(PaysRepository $paysRepository): Response
    {
        return $this->render('participation/index.html.twig', [
            'pays' => $paysRepository->findBy([],['nomFrFr' => "ASC"]),
        ]);
    }
	
	#[Route('/new', name: 'app_participation_new', methods:['GET','POST'])]
	public function new(Request $request)
	{
		// Initialisation
		$encoders = [new XmlEncoder(), new JsonEncoder()];
		$normalizers = [new ObjectNormalizer()];
		$serializer = new Serializer($normalizers, $encoders); //dd($request);
		
		$message = $this->gestionParticipation->formulaire($request);
		
		
		return $this->json($message);
	}
}
