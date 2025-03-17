<?php

namespace App\Controller\Admin;

use App\Service\GoogleBooksService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GoogleBooksCrudController extends AbstractCrudController
{
    private $googleBooksService;

    public function __construct(GoogleBooksService $googleBooksService)
    {
        $this->googleBooksService = $googleBooksService;
    }

    public static function getEntityFqcn(): string
    {
        return 'GoogleBook'; // Entité virtuelle pour EasyAdmin
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            TextField::new('author', 'Auteur'),
            TextareaField::new('description', 'Description'),
            ImageField::new('image', 'Couverture')
                ->setBasePath('')
                ->hideOnForm()
        ];
    }

    public function index(AdminContext $context)
    {
        $searchQuery = $context->getRequest()->query->get('query', '');
        $books = $this->googleBooksService->searchBooks($searchQuery);

        return $this->render('admin/google_books.html.twig', [
            'books' => $books,
            'searchQuery' => $searchQuery
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Livres depuis Google Books')
            ->setSearchFields(['title', 'author']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE, Action::DETAIL);
    }
}
