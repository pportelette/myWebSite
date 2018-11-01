<?php

namespace PP\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PP\CoreBundle\Entity\Professional;
use PP\CoreBundle\Form\ProfessionalType;
use PP\CoreBundle\Entity\Education;
use PP\CoreBundle\Form\EducationType;
use PP\CoreBundle\Entity\Internship;
use PP\CoreBundle\Form\InternshipType;
use PP\CoreBundle\Entity\Message;
use PP\CoreBundle\Form\MessageType;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class CoreController extends Controller
{
    public function indexAction()
    {
        return $this->render('@PPCore/Core/index.html.twig');
    }

    public function resumeAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $userRepository = $em->getRepository('PPUserBundle:User');
        $user = $userRepository->find(3);

        $professionalRepository = $em->getRepository('PPCoreBundle:Professional');
        $professionals = $professionalRepository->findBy(array(), array('startDate' => 'desc'));
 
        $items['professionals']=$professionals;
        
        $internshipRepository = $em->getRepository('PPCoreBundle:Internship');
        $internships = $internshipRepository->findBy(array(), array('startDate' => 'desc'));
        $items['internships']=$internships;

        $educationRepository = $em->getRepository('PPCoreBundle:Education');
        $educations = $educationRepository->findBy(array(), array('startDate' => 'desc'));
        $items['educations']=$educations;

        if($request->isMethod('POST')) {
            $html = $this->renderView('@PPCore/Core/printResume.html.twig', array(
                'user'=>$user,
                'items'=>$items
            ));

            /*$this->get('knp_snappy.pdf')->generateFromHtml(
                $html, 
                "C:/wamp64/www/project/web/snappy/file.pdf", 
                array(
                    
                ),
                true
            );
            return $this->render('@PPCore/Core/resume.html.twig', array(
                'user'=>$user,
                'items'=>$items
            ));*/
            return new PdfResponse(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 'CV-Pierre Portelette.pdf'
            );
        }
        return $this->render('@PPCore/Core/resume.html.twig', array(
            'user'=>$user,
            'items'=>$items
        ));
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addItemAction(Request $request, $itemType) {
        $className = "PP\\CoreBundle\\Entity\\".$itemType; 
        $item = new $className;
        $itemForm = $this->get('form.factory')->create("PP\\CoreBundle\\Form\\".$itemType.'Type'::class, $item);
        if ($request->isMethod('POST') && $itemForm->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
        }
        return $this->render('@PPCore/Core/newItem.html.twig', array(
            'form'=>$itemForm->createView()
        ));
    }

    public function contactAction(Request $request) {
        $message = new Message();
        $form= $this->get('form.factory')->create(MessageType::class, $message);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            //$em->flush();

            $transport = (new \Swift_SmtpTransport('smtp.free.fr', 2525))
                ->setUsername('pierre.portelett@free.fr')
                ->setPassword('lisisi.85')
            ;
            $mail = \Swift_Message::newInstance()
                ->setFrom(array($message->getEmail() => "myWebSite"))
                ->setTo('pierre.portelette@free.fr')
                ->setCharset('utf-8')
                ->setContentType('text/html')
                ->setBody($message->getBody());
            
            $mailer = new \Swift_Mailer($transport);
            $mailer->send($mail);
            
            return $this->redirectToRoute('pp_core_homepage');
        }
        return $this->render('@PPCore/Core/contact.html.twig', array(
          'form' => $form->createView(),
        ));
    }
}
