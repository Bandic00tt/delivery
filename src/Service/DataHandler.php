<?php
namespace App\Service;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Site;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class DataHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function saveCategorizedData(string $siteCode, array $categoriesData): int
    {
        $site = $this->getSite($siteCode);
        foreach ($categoriesData as $categoriesItem) {
            $category = $this->getCategory($site->getId(), $categoriesItem['name']);
            foreach ($categoriesItem['products'] as $productItem) {
                $product = new Product();
                $product->setCategory($category);
                $product->setName($productItem['name']);
                $product->setDescription($productItem['description']);
                $product->setParams($productItem['params']);
                $product->setImgUrl($productItem['img_url']);
                $product->setPrice($productItem['price']);
                $product->setCreatedAt(time());
                $product->setUpdatedAt(time());

                $this->em->persist($product);
            }
        }

        $this->em->flush();
        $this->em->clear();

        return 0;
    }

    /**
     * @param int $siteId
     * @param string $name
     * @return Category
     */
    private function getCategory(int $siteId, string $name): Category
    {
        $category = $this->findCategory($siteId, $name);
        if ($category) {
            return $category;
        }

        return $this->createAndGetCategory($siteId, $name);
    }

    /**
     * @param int $siteId
     * @param string $name
     * @return Category|null
     */
    private function findCategory(int $siteId, string $name): ?Category
    {
        /** @var Category $category */
        $category = $this->em->getRepository(Category::class)
            ->findOneBy(['site_id' => $siteId, 'name' => $name]);

        return $category;
    }

    /**
     * @param int $siteId
     * @param string $name
     * @return Category
     */
    private function createAndGetCategory(int $siteId, string $name): Category
    {
        $category = new Category();
        $category->setSiteId($siteId);
        $category->setName($name);
        $category->setCreatedAt(time());
        $category->setUpdatedAt(time());

        $this->em->persist($category);
        $this->em->flush();
        $this->em->clear();

        return $category;
    }

    private function getSite(string $siteCode): Site
    {
        /** @var Site|null $site */
        $site = $this->em->getRepository(Site::class)
            ->findOneBy(['code' => $siteCode]);

        if ($site) {
            return $site;
        }

        throw new EntityNotFoundException('Site not found');
    }
}