<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Services\Mailer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;



class  ResetPassMailController extends AbstractController
{
    /**
     * @Route("/requete", name="request_resetting")
     */
    public function request(Request $request, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        // création d'un formulaire "à la volée", afin que l'internaute puisse renseigner son mail
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'attr'=>['placeholder'=>'Email'],
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // voir l'épisode 2 de cette série pour retrouver la méthode loadUserByUsername:
            $user = $em->getRepository(User::class)->loadUserByUsername($form->getData()['email']);

            // aucun email associé à ce compte.
            if (!$user) {
                $request->getSession()->getFlashBag()->add('warning', "Cet email n'existe pas.");
                return $this->redirectToRoute("request_resetting");
            }

            // création du token
            $user->setToken($tokenGenerator->generateToken());
            // enregistrement de la date de création du token
            $user->setPasswordRequestedAt(new \Datetime());
            $em->flush();

            // on utilise le service Mailer créé précédemment
            $bodyMail = $mailer->createBodyMail('email/reset.html.twig', [
                'user' => $user
            ]);
            $mailer->sendMessage('from@email.com', $user->getEmail(), 'renouvellement du mot de passe', $bodyMail);
            $request->getSession()->getFlashBag()->add('info', "Un mail va vous être envoyé afin que vous puissiez renouveller votre mot de passe. Le lien que vous recevrez sera valide 24h.");

            return $this->redirectToRoute("app_login");
        }

        return $this->render('reset_pass_mail/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // si supérieur à 10min, retourne false
    // sinon retourne false
    private function isRequestInTime(\Datetime $passwordRequestedAt = null)
    {
        if ($passwordRequestedAt === null)
        {
            return false;
        }

        $now = new \DateTime();
        $interval = $now->getTimestamp() - $passwordRequestedAt->getTimestamp();

        $daySeconds = 60 * 10;
        $response = $interval > $daySeconds ? false : $reponse = true;
        return $response;
    }

    /**
     * @Route("/{id}/{token}", name="resetting")
     */
    public function resetting(User $user, $token, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // interdit l'accès à la page si:
        // le token associé au membre est null
        // le token enregistré en base et le token présent dans l'url ne sont pas égaux
        // le token date de plus de 10 minutes
        if ($user->getToken() === null || $token !== $user->getToken() || !$this->isRequestInTime($user->getPasswordRequestedAt()))
        {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // réinitialisation du token à null pour qu'il ne soit plus réutilisable
            $user->setToken(null);
            $user->setPasswordRequestedAt(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "Votre mot de passe a été renouvelé.");

            return $this->redirectToRoute('app_login');

        }

        return $this->render('reset_pass_mail/reset.html.twig', [
            'form' => $form->createView()
        ]);

    }
}

