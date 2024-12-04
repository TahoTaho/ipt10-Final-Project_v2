<?php

namespace App\Controllers;

use App\Traits\Renderable;

class BaseController
{
    use Renderable;

    protected function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function renderPage(string $templateName, array $data): string
    {
        $content = $this->render($templateName, $data);

        $layoutData = array_merge($data, [
            'content' => $content,
        ]);

        return $this->render('layout', $layoutData);
    }

    protected function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }
}