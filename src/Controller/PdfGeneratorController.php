<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use App\Repository\DocumentRepository;
 
class PdfGeneratorController extends AbstractController
{
    #[Route('/pdf/generator{id}', name: 'app_pdf_generator')]
    public function index(int $id, DocumentRepository $documentRepository): Response
    {
        // return $this->render('pdf_generator/index.html.twig', [
        //     'controller_name' => 'PdfGeneratorController',
        // ]);
        $document = $documentRepository->find($id);
        $type = $document->getType();
        if (strpos($type, "devis") !== false) {
            $typeName = "Devis";
        }
        if (strpos($type, "facture") !== false) {
            $typeName = "Facture";
        }

        $designations = $document->getDesignations()->toArray();
        $totalHT = 0;
        for ($i = 0; $i < count($designations); $i++) {
            $totalHT = $totalHT + $designations[$i]->getPrixHorsTax();
        }
        $totalTTC = 0;
        for ($i = 0; $i < count($designations); $i++) {
            $totalTTC = $totalTTC + $designations[$i]->getPrixTotal();
        }

        $html =  $this->renderView('pdf_generator/index.html.twig', 
        ['document' => $document,
        'designations' => $designations,
        'type' => $typeName,
        'totalHT' => $totalHT,
        'totalTTC' => $totalTTC,
        ]);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        return new Response (
            $dompdf->stream('document', ["Attachment" => false]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }
}