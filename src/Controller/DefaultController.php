<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Car;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        $repository = $this->getDoctrine()->getRepository(Car::class);
        $cars = $repository->findAll();

        return $this->render('base.html.twig', [
            'cars' => $cars,
        ]);        
    }

    /**
     * @Route("/tyxo", name="tyxo")
     */
    public function tyxoAction()
    {
        return $this->redirect('https://tyxo.com/tracker/daily/442432384565');
    }


    /**
     * @Route("/menu", name="menu")
     */
    public function menuAction()
    {
        return $this->render('menu.html.twig');
    }

    /**
     * @Route("/sendmail", name="sendmail")
     */
    public function sendmailAction()
    {
        $request = Request::createFromGlobals();

        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $message = $request->request->get('message');
        
        if (empty($name) || empty($email) || empty($message)) {
            return new Response('Missing data', Response::HTTP_BAD_REQUEST);
        }

        $from    = 'spas@spasov-auto.com';
        $to      = 'spas@spasov-auto.com';
        $subject = 'Запитване от сайта';
        $message = 'От ' . $name . PHP_EOL . PHP_EOL . $message;
        $headers = 'From: ' . $from . "\r\n" .
            'Reply-To: ' . $email . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $is_sent = mail($to, $subject, $message, $headers);

        $result = $is_sent ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;
        return new Response('Response', $result);
    }


    /**
     * @Route("/import")
     */
    public function importAction()
    {
        $curl = new \Curl\Curl();
        $curl->get('https://spasovauto2016.mobile.bg/');

        $images = [];
        $links = [];

        // get images
        preg_match_all('/img src="\/\/(.+?)" width=120 height=90/', $curl->response, $matches);
        if (isset($matches[1])) {
            foreach ($matches[1] as $value) {
                $images[] = 'http://' . str_replace('/med/', '/big/', $value);
            }
        }
        $matches[1] = [];


        // get links
        preg_match_all('/<td class="algcent valgmid"><a href="\/\/(www.mobile.bg.*?=details)" onclick/', $curl->response, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $value) {
                $links[] = 'http://' . $value;
            }
        }

        
        // remove old and insert into db
        if (count($links) == count($images) && count($links) > 0) {
            // delete old
            $entity_manager = $this->getDoctrine()->getManager();

            $repository = $this->getDoctrine()->getRepository(Car::class);
            $cars = $repository->findAll();

            foreach ($cars as $car) {
                $entity_manager->remove($car);
            }
            $entity_manager->flush();

            // insert new
            for ($i=0; $i < count($links); $i++) { 
                $car = new Car();
                $car->setImage($images[$i]);
                $car->setLink($links[$i]);

                $entity_manager->persist($car);
                $entity_manager->flush();
            }
        } else { // problem extracting data
            //  notify for problem
        }

        return $this->redirectToRoute('home');
    }

}