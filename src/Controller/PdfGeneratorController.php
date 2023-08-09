<?php
 
namespace App\Controller;
 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Repository\DocumentRepository;
 
class PdfGeneratorController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/pdf/generator{id}', name: 'app_pdf_generator')]
    public function index(int $id, DocumentRepository $documentRepository): Response
    {
        $token = $this->tokenStorage->getToken();
        if ($token == null)
        {
            return $this->redirectToRoute('app_login');
        }
        $user = $token->getUser();

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
        $totalTVA = $totalTTC-$totalHT;
        $date = $document->getDate()->format('d/m/Y');
        $dateValide = $document->getDate()->modify('+1 month')->format('d/m/Y');

        $lignesBanque = explode("\n",$user->getDetailsPayment());

        $html =  $this->renderView('pdf_generator/index.html.twig', 
        ['document' => $document,
        'user' => $user,
        'designations' => $designations,
        'type' => $typeName,
        'totalHT' => $totalHT,
        'totalTTC' => $totalTTC,
        'totalTVA' => $totalTVA,
        'dateValide' => $dateValide,
        'date' => $date,
        'lignesBanque' => $lignesBanque,
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