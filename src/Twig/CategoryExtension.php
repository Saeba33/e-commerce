<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class CategoryExtension extends AbstractExtension implements GlobalsInterface
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getGlobals(): array
    {
        return [
            'categories' => $this->categoryRepository->findAll(),
        ];
    }
}
