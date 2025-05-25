<?php

/**
 * @copyright  2025 Zhalayletdinov Vyacheslav evil_tut@mail.ru
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace App\Service;

use App\DTO\NewsItemDTO;
use App\Entity\NewsItem;
use App\Mercure\NewsLoadedPublisher;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NewsService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validatorDto,
        private HttpClientInterface $client,
        private ParameterBagInterface $parameterBag,
        private NewsLoadedPublisher $publisher,
        private LoggerInterface $logger,
    ) {
    }

    public function stub(): string
    {
        return '{
    "totalArticles": 6573766,
    "articles": [
        {
            "title": "Новая глава для Optiq: Cadillac запускает второе поколение и добавляет V-версию",
            "description": "Названа дата старта производства нового Cadillac Optiq 2026",
            "content": "Компания Cadillac готовится запустить серийное производство второго поколения электрического кроссовера Optiq. По данным GM Authority, выпуск модели 2026 года стартует 25 августа 2025 года. Прием предзаказов откроется уже с 26 июня, а первые заказы н... [2330 chars]",
            "url": "https://www.32cars.ru/posts/id-10711-novaja-glava-dlja-optiq-cadillac-zapuskaet-vtoroe-pokolenie-i-dobavljaet-v-versiju",
            "image": "https://www.32cars.ru/uploads/materials/10711/inner/zTZyBmspAophCQGVRmUK.jpg",
            "publishedAt": "2025-05-25T06:58:21Z",
            "source": {
                "name": "Автомобильный портал 32CARS.RU",
                "url": "https://www.32cars.ru"
            }
        },
        {
            "title": "В Steam можно бесплатно забрать экшн-головоломку The Forest Prison",
            "description": "Неожиданное обновление в Steam: игра The Forest Prison, ранее продававшаяся за 360 рублей, стала полностью бесплатной. Проект находится в раннем доступе, однако последнее обновление от разработчиков вышло более двух лет назад - с тех пор...",
            "content": "Неожиданное обновление в Steam: игра The Forest Prison, ранее продававшаяся за 360 рублей, стала полностью бесплатной. Проект находится в раннем доступе, однако последнее обновление от разработчиков вышло более двух лет назад — с тех пор развитие игр... [951 chars]",
            "url": "https://www.playground.ru/forest_prison/news/v_steam_mozhno_besplatno_zabrat_ekshn_golovolomku_the_forest_prison-1772037",
            "image": "https://i.playground.ru/e/aaZZ_GPzBQ6JsNRs34tTSw.jpeg",
            "publishedAt": "2025-05-25T06:38:14Z",
            "source": {
                "name": "PlayGround.ru",
                "url": "https://www.playground.ru"
            }
        },
        {
            "title": "В Госдуме готовят законодательную инициативу по защите религиозных символов",
            "description": "По словам Володина, норма будет охранять права верующих",
            "content": "21 мая ряд российских СМИ сообщили, что администрация президента РФ изменила дизайн государственного герба. Ему вернули кресты на державу и короны двуглавого орла. До этого на гербе вместо крестов были изображения ромбов. Этот вариант подвергся крити... [331 chars]",
            "url": "https://www.vedomosti.ru/politics/news/2025/05/25/1112456-v-gosdume-gotovyat",
            "image": "https://sharing.vedomosti.ru/1748158050/vedomosti.ru/politics/news/2025/05/25/1112456-v-gosdume-gotovyat.jpg",
            "publishedAt": "2025-05-25T06:23:56Z",
            "source": {
                "name": "Ведомости",
                "url": "https://www.vedomosti.ru"
            }
        }
    ]
}';
    }

    public function loadNews(): array
    {
        //        $data = $this->stub();
        $apiKey = $this->parameterBag->get('app.news.apikey');
        $url = sprintf('https://gnews.io/api/v4/top-headlines?country=ru&category=general&apikey=%s', $apiKey);

        try {
            $answer = $this->client->request('GET', $url);
            $data = $answer->getContent();
        } catch (\Throwable $e) {
            throw new \RuntimeException('Ошибка при отправке запроса в АПИ новостей: ' . $e->getMessage());
        }
        $result = $this->createFromData($data);

        try {
            $this->publisher->publish($result['totalLoaded']);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }

        return $result;
    }

    public function createFromData(string $data): array
    {
        $newsData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        $news = $newsData['articles'];
        $errorsList = [];
        foreach ($news as $item) {
            try {
                $instance = NewsItemDTO::fromArray($item);
                $errors = $this->validatorDto->validate($instance);

                if ($errors->count() > 0) {
                    throw new \RuntimeException(implode("\n", array_map(static function (ConstraintViolationInterface $item) { return $item->getMessage(); }, (array) $errors)));
                }

                $blacklist = ['.ua', 'meduza.io', 'svoboda.org']; // сюда добавляешь всё, что нужно исключить

                foreach ($blacklist as $blocked) {
                    if (str_contains($instance->getUrl(), $blocked)) {
                        throw new \RuntimeException(sprintf('Skip blacklisted domain: %s', $blocked));
                    }
                }

                $this->createFromDto($instance, flush: false);
            } catch (\Throwable $e) {
                $errorsList[] = $e->getMessage();
            }
        }

        $this->entityManager->flush();

        return [
            'totalNews' => count($news),
            'errorsCount' => count($errorsList),
            'totalLoaded' => count($news) - count($errorsList),
            'errors' => $errorsList,
        ];
    }

    public function createFromDto(NewsItemDTO $dto, bool $flush = true): NewsItem
    {
        $item = new NewsItem();

        $exist = $this->entityManager->getRepository(NewsItem::class)->findOneBy(['title' => $dto->getTitle()]);
        if ($exist) {
            throw new \RuntimeException(sprintf('Новость "%s" уже загружена', $dto->getTitle()));
        }
        $item->setTitle($dto->getTitle());
        $item->setDescription($dto->getDescription());
        $item->setContent($dto->getContent());
        $item->setUrl($dto->getUrl());
        $item->setImage($dto->getImage());
        $item->setPublishedAt($dto->getPublishedAt());
        $item->setSourceName($dto->getSourceName());
        $item->setSourceUrl($dto->getSourceUrl());

        $this->entityManager->persist($item);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $item;
    }
}
