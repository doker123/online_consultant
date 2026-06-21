<?php

namespace Src;

use Exception;

class View
{
    private string $view = '';
    private array $data = [];
    private string $root = '';
    private string $layout = '/layouts/main.php';

    public function __construct(string $view = '', array $data = [])
    {
        $this->root = $this->getRoot();
        $this->view = $view;
        $this->data = $data;
    }

    private function getRoot(): string
    {
        global $app;
        $root = $app->settings->getRootPath();
        $path = $app->settings->getViewsPath();

        return $_SERVER['DOCUMENT_ROOT'] . $root . $path;
    }

    private function getPathToMain(): string
    {
        return $this->root . $this->layout;
    }

    private function getPathToView(string $view = ''): string
    {
        $view = str_replace('.', '/', $view);
        return $this->getRoot() . "/$view.php";
    }

    public function render(string $view = '', array $data = []): string
    {
        $view = $view ?: $this->view;
        $data = $data ?: $this->data;

        $path = $this->getPathToView($view);
        $mainPath = $this->getPathToMain();

        if (!file_exists($mainPath)) {
            throw new Exception('Error render: layout not found - ' . $mainPath);
        }

        if (!file_exists($path)) {
            throw new Exception('Error render: view not found - ' . $path);
        }

        extract($data, EXTR_PREFIX_SAME, '');

        ob_start();
        require $path;
        $content = ob_get_clean();

        return require($this->getPathToMain());
    }

    public function __toString(): string
    {
        return $this->render($this->view, $this->data);
    }

}