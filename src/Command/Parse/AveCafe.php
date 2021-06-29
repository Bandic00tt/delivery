<?php
namespace App\Command\Parse;

use App\Service\DataHandler;
use DiDom\Document;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AveCafe extends Command
{
    public const SITE_CODE = 'avecafe';
    private const URL = 'https://avecafe.ru';

    protected static $defaultName = 'parse:avecafe';
    private DataHandler $dataHandler;

    public function __construct(DataHandler $dataHandler)
    {
        parent::__construct();
        $this->dataHandler = $dataHandler;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $document = new Document(self::URL, true);
        $menu = $document->find('#menu')[0];
        $categories = [];

        foreach ($menu->children() as $catalog) {
            if ($catalog->has('.ttls') === false) {
                continue;
            }

            $category['name'] = trim($catalog->find('h2.ttls')[0]->text());
            $category['products'] = [];

            foreach ($catalog->find('.catalogconteiner') as $productBlock) {
                $product['img_url'] = self::URL .'/'. $productBlock->find('img')[0]->attr('url');
                $product['name'] = $productBlock->find('.nms')[0]->text();
                $product['description'] = $productBlock->find('.font12')[0]->text();
                $product['params'] = $productBlock->find('.fleft')[0]->text();
                if ($productBlock->has('.fright')) {
                    $product['price'] = (int) $productBlock->find('.fright')[0]->text();
                } else {
                    $product['price'] = -1;
                }

                $category['products'][] = $product;
            }

            $categories[] = $category;
            echo count($category['products']) . PHP_EOL;
        }

        $this->dataHandler->saveCategorizedData(self::SITE_CODE, $categories);

        return 0;
    }
}