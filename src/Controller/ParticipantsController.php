<?php

namespace App\Controller;

use App\Entity\Participants;
use App\Entity\Team;
use App\Form\ParticipantsType;
use App\Repository\ParticipantsRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/participants")
 */
class ParticipantsController extends AbstractController
{
    /**
     * @Route("/", name="app_participants_index", methods={"GET"})
     */
    public function index(ParticipantsRepository $participantsRepository): Response
    {
        return $this->render('participants/index.html.twig', [
            'participants' => $participantsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_participants_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ParticipantsRepository $participantsRepository): Response
    {
        $participant = new Participants();
        $form = $this->createForm(ParticipantsType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participantsRepository->add($participant, true);

            return $this->redirectToRoute('app_participants_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participants/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/ajax/inscription", name="ajax_submit_form", methods={"GET", "POST"})
     */
    public function ajax_submit_form(Request $request,TeamRepository $teamRepository, EntityManagerInterface $entityManager, ParticipantsRepository $participantsRepository): Response
    {
        if ($request->isMethod('POST')) {

            $data = $request->request->all();
            $lastTeam = $teamRepository->findOneBy([], ['id' => 'DESC']) ;
            if ($lastTeam) {
                $newTeamId = $lastTeam->getId() + 1;
            } else {
                $newTeamId = 1;
            }

            $team = new Team();
            $team->setNomTeam("Groupe $newTeamId");

            $data['expertise'][] = $data['expertise_other'];
            $team->setCompetences($data['expertise']);
            $team->setComment($data['comment']);
            $entityManager->persist($team);
            // Retrieve form data
            foreach ($data['participant'] as $participantData) {

                if($participantData['nom'] != "") {
                    $participant = new Participants();
                    $participant->setNom($participantData['nom']);
                    $participant->setPrenom($participantData['prenom']);
                    $participant->setEmail($participantData['email']);
                    $participant->setNiveauEtude($participantData['niveauEtude']);
                    $participant->setTeam($team);
                    $entityManager->persist($participant);
                }
            }

            $entityManager->flush();
        }

        return new JsonResponse(['message' => 'Form submitted successfully!'], 200);
    }


    /**
     * @Route("/check-emails", name="check-emails", methods={"GET", "POST"})
     */
    public function checkEmails(Request $request, ParticipantsRepository $participantsRepository): JsonResponse
    {
        // Récupérer les e-mails depuis la requête POST
        $emails = $request->request->get('emails', []);

        if (!is_array($emails) || empty($emails)) {
            return new JsonResponse(['error' => 'Invalid or empty email list'], 400);
        }

        // Vérifiez les e-mails dans la base de données
        $existingEmails = $participantsRepository->createQueryBuilder('p')
            ->select('p.email')
            ->where('p.email IN (:emails)')
            ->setParameter('emails', $emails)
            ->getQuery()
            ->getArrayResult();

        // Transformez les résultats en un tableau simple
        $existingEmails = array_column($existingEmails, 'email');

        return new JsonResponse(['existingEmails' => $existingEmails]);
    }


}
