<?php

namespace Controller\Public;

use Model\Page;
use Src\View;

class Home
{
    public function index(): string
    {
        return (string) new View('Public.home');
    }

    public function page(string $slug): string
    {
        $page = Page::published()->where('slug', $slug)->first();

        if (!$page) {
            http_response_code(404);
            return (string) new View('Public.home', [
                'error' => 'Страница не найдена',
            ]);
        }

        return (string) new View('Public.page', [
            'page' => $page,
        ]);
    }
}
