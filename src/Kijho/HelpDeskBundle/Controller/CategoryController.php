<?php

namespace Kijho\HelpDeskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kijho\HelpDeskBundle\Entity as Entity;
use Kijho\HelpDeskBundle\Form\Operator\TicketCommentType;
use Kijho\HelpDeskBundle\Form\Operator\TicketCategoryType;
use Symfony\Component\HttpFoundation\Request;
use Kijho\HelpDeskBundle\Util\Util;

class CategoryController extends Controller {

    public function indexAction() {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createNotFoundException('Access Denied. You must be logged in');
        }

        $em = $this->getDoctrine()->getManager();

        $search = array();
        $order = array('name' => 'ASC');
        $categories = $em->getRepository('HelpDeskBundle:TicketCategory')->findBy($search, $order);


        return $this->render('HelpDeskBundle:Operator/Category:index.html.twig', array(
                    'categories' => $categories,
        ));
    }

    /**
     * Permite crear una nueva categoria
     * @author Cesar Giraldo <cnaranjo@kijho.com> May 4, 2016
     * @param Request $request datos de la solicitud
     * @param string $id identificador de la categoria
     * @return type
     */
    public function newAction(Request $request) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createNotFoundException('Access Denied. You must be logged in');
        }

        $em = $this->getDoctrine()->getManager();
        $category = new Entity\TicketCategory();

        //creamos el formulario para la creacion de comentarios del cliente
        $formCategory = $this->createForm(TicketCategoryType::class, $category);
        $formCategory->handleRequest($request);
        
        if ($formCategory->isSubmitted() && $formCategory->isValid()) {
            //fomateamos el slug
            $slug = strtolower(str_replace(' ', '_', $category->getName()));
            $category->setSlug($slug);
            $em->persist($category);
            $em->flush();

            $this->get('session')->getFlashBag()->add('operator_success_message', $this->get('translator')->trans('help_desk.ticket_category.succesfully_created'));
            return $this->redirectToRoute('help_desk_operator_categories');
        }

        return $this->render('HelpDeskBundle:Operator/Category:new.html.twig', array(
                    'category' => $category,
                    'form_category' => $formCategory->createView(),
        ));
    }

    /**
     * Permite editar la informacion de una categoria
     * @author Cesar Giraldo <cnaranjo@kijho.com> May 4, 2016
     * @param Request $request datos de la solicitud
     * @param string $id identificador de la categoria
     * @return type
     */
    public function editAction(Request $request, $id) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createNotFoundException('Access Denied. You must be logged in');
        }

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('HelpDeskBundle:TicketCategory')->find($id);

        if ($category instanceof Entity\TicketCategory) {

            //creamos el formulario para la creacion de comentarios del cliente
            $formCategory = $this->createForm(TicketCategoryType::class, $category);
            $formCategory->handleRequest($request);
            if ($formCategory->isSubmitted() && $formCategory->isValid()) {

                //fomateamos el slug
                $slug = strtolower(str_replace(' ', '_', $category->getName()));
                $category->setSlug($slug);

                $em->persist($category);
                $em->flush();

                $this->get('session')->getFlashBag()->add('operator_success_message', $this->get('translator')->trans('help_desk.ticket_category.succesfully_updated'));
                return $this->redirectToRoute('help_desk_operator_categories');
            }

            return $this->render('HelpDeskBundle:Operator/Category:edit.html.twig', array(
                        'category' => $category,
                        'form_category' => $formCategory->createView(),
            ));
        } else {
            $this->get('session')->getFlashBag()->add('operator_error_message', $this->get('translator')->trans('help_desk.ticket_category.not_found_message'));
            return $this->redirectToRoute('help_desk_operator_categories');
        }
    }

}
