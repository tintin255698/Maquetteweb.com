<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Image;
use App\Entity\Livraison;
use App\Entity\Menu;
use App\Entity\Repas;
use App\Entity\Reservation;
use App\Form\ContactType;
use App\Form\LivraisonType;
use App\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("", name="index")
     */
    public function index(Request $request, \Swift_Mailer $mailer)
    {
        // Formulaire de contact

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $message = (new \Swift_Message('Nouveau Contact'))
                ->setFrom('maquetterestaurant@gmail.com')
                ->setTo('maquetterestaurant@gmail.com')
                ->setBody(
                    $this->renderView(
                        'email/contact2.html.twig', compact('contact')
                    ),
                    'text/html'
                );
            $mailer->send($message);

            $message = (new \Swift_Message("Confirmation d'email"))
                ->setFrom('maquetterestaurant@gmail.com')
                ->setTo($contact['Email'])
                ->setBody(
                    $this->renderView(
                        'email/contact3.html.twig', compact('contact')
                    ),
                    'text/html'
                );
            $mailer->send($message);



        }

        // Formulaire de reservation

        $reservation = new Reservation();

        $form1 = $this->createForm(ReservationType::class, $reservation);

        $form1->handleRequest($request);

        if ($form1->isSubmitted() && $form1->isValid()) {
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($reservation);
            $doctrine->flush();
            $contact = $form1["email"]->getData();

            $message = (new \Swift_Message('Réservation confirmée '))
                ->setFrom('betisesetvolupthe@gmail.com')
                ->setTo($contact)
                ->setBody(
                    $this->renderView(
                        'email/reservation2.html.twig', compact('reservation')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

            $message = (new \Swift_Message('Nouvelle Reservation'))
                ->setFrom('maquetterestaurant@gmail.com')
                ->setTo('maquetterestaurant@gmail.com')
                ->setBody(
                    $this->renderView(
                        'email/reservation3.html.twig', compact('reservation')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

        }

        // Commentaires

        $repo = $this->getDoctrine()->getRepository(Commentaire::class)->findByExampleField2();

        $repos = $this->getDoctrine()->getRepository(Menu::class);
        $menu = $repos->findall();

        $image = $this->getDoctrine()->getRepository(Image::class)->findByExampleField2();

            return $this->render('index/index.html.twig', [
                'pla'=>$image,
                'menu'=>$menu,
                'reservationForm' => $form1->createView(),
                'contactForm' =>$form->createView(),
                'repo'=>$repo,

            ]);
        }
}
