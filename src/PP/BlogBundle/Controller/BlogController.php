<?php

namespace PP\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PP\BlogBundle\Entity\Article;
use PP\BlogBundle\Form\ArticleType;
use PP\BlogBundle\Entity\Comment;
use PP\BlogBundle\Form\CommentType;

class BlogController extends Controller
{
    public function homeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articleRepository = $em->getRepository('PPBlogBundle:Article');
        $articles = $articleRepository->getArticles(1, 4);
        $otherArticles = $articleRepository->getArticles(1, 4);
        return $this->render('@PPBlog/Blog/home.html.twig', array(
            'articles' => $articles,
            'otherArticles' => $otherArticles
        ));
    }

    public function articleAction(Article $article, Request $request, $formContent=null)
    {
        $comment = new Comment();
        $commentForm = $this->get('form.factory')->create(CommentType::class, $comment);

        if ($request->isMethod('post') && $commentForm->handleRequest($request)->isValid()) {

            $article->addComment($comment);
            $listComments = $article->getComments();

            $em = $this->getDoctrine()->getManager();
            //$em->persist($advert);
            $em->flush();
            
            return $this->render('@PPBlog/Blog/article.html.twig', array(
                'article' => $article,
                'listComments' => $listComments,
                'commentForm' => $commentForm->createView()
            ));
        }

        $listComments = $article->getComments();

        return $this->render('@PPBlog/Blog/article.html.twig', array(
            'article' => $article,
            'listComments' => $listComments,
            'commentForm' => $commentForm->createView()
        ));
    }

    public function newAction(Request $request)
    {
        $article = new Article();
        $form = $this->get('form.factory')->create(ArticleType::class, $article);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('pp_blog_homepage');
        }

        return $this->render('@PPBlog/Blog/articleForm.html.twig', array(
            'form'=>$form->createView()
        ));
    }
}
