<?php
/**
 * @package    MainController.php
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Controller;

use App\Repository\NewsItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    #[Route('/', name: 'main')]
    public function dashboard(NewsItemRepository $repository): Response
    {
        $news = $repository->findBy([], ['publishedAt' => 'DESC'], 10);

        return $this->render('news/dashboard.html.twig', ['newsList' => $news]);
    }

    #[Route('/search-news', name: 'search_news', methods: ['GET'])]
    public function search(Request $request, NewsItemRepository $repository): JsonResponse
    {
        $query = $request->query->get('q', '');
        $results = $repository->search($query);

        return $this->json([
            'html' => $this->renderView('news/news_cards.html.twig', ['newsList' => $results]),
        ]);
    }
}