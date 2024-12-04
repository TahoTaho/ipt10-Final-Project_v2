<?php

namespace App\Traits;

trait Renderable
{
    public function render($template, $data = []): string
    {
        global $mustache;

        try {
            $tpl = $mustache->loadTemplate($template);
            return $tpl->render($data);
        } catch (\Exception $e) {
            throw new \RuntimeException("Error rendering template: {$e->getMessage()}");
        }
    }
}
