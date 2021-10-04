<?php

namespace App\Controller;

use App\Entity\Documents;
use App\Form\DocumentsType;
use App\Repository\DocumentsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\FileUploader;



#[Route('/documents')]
class DocumentsController extends AbstractController
{
    #[Route('/', name: 'documents_index', methods: ['GET'])]
    public function index(DocumentsRepository $documentsRepository): Response
    {
        return $this->render('documents/index.html.twig', [
            'documents' => $documentsRepository->findAll(),
        ]);
    }
    



    #[Route('/new', name: 'documents_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $documents = new Documents();
        $form = $this->createForm(DocumentsType::class, $documents);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $documents */
            $content = $form->get('content')->getData();
            if ($content) {
                $docFilename = $fileUploader->upload($content);
                $documents->setdocFilename($docFilename);
            }
    
            // ...
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($documents);
            $entityManager->flush();

            return $this->redirectToRoute('documents_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('documents/new.html.twig', [
            'document' => $documents,
            'form' => $form,
        ]);
    }

//     #[Route('/new', name: 'documents_new', methods: ['GET', 'POST'])]
// public function new(Request $request, SluggerInterface $slugger)
//     {
//         $documents = new Documents();
//         $form = $this->createForm(DocumentsType::class, $documents);
//         $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {
//             /** @var UploadedFile $documentsFile */
//             $documentsFile = $form->get('documents')->getViewData();

//             // this condition is needed because the 'brochure' field is not required
//             // so the PDF file must be processed only when a file is uploaded
//             if ($documentsFile) {
//                 $originalFilename = pathinfo($documentsFile->getClientOriginalName(), PATHINFO_FILENAME);
//                 // this is needed to safely include the file name as part of the URL
//                 $safeFilename = $slugger->slug($originalFilename);
//                 $newFilename = $safeFilename.'-'.uniqid().'.'.$documentsFile->guessExtension();

//                 // Move the file to the directory where brochures are stored
//                 try {
//                     $documentsFile->move(
//                         $this->getParameter('documents_directory'),
//                         $newFilename
//                     );
//                 } catch (FileException $e) {
//                     // ... handle exception if something happens during file upload
//                 }

//                 // updates the 'documentsFile' property to store the PDF file name
//                 // instead of its contents
//                 $documents->setDocFilename($newFilename);
//             }

//             // ... persist the $product variable or any other work

//             return $this->redirectToRoute('document_index');
//         }

//         return $this->renderForm('documents/new.html.twig', [
//             'form' => $form,
//         ]);
//     }










    #[Route('/{id}', name: 'documents_show', methods: ['GET'])]
    public function show(Documents $document): Response
    {
        return $this->render('documents/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/{id}/edit', name: 'documents_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Documents $document): Response
    {
        $form = $this->createForm(DocumentsType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('documents_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('documents/edit.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'documents_delete', methods: ['POST'])]
    public function delete(Request $request, Documents $document): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($document);
            $entityManager->flush();
        }

        return $this->redirectToRoute('documents_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
