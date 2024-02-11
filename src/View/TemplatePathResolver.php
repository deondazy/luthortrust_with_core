<?php

declare(strict_types=1);

namespace Denosys\Core\View;

readonly class TemplatePathResolver
{
    /**
     * Resolves the full path of a given template.
     *
     * @param string $template The template name.
     *
     * @return string The resolved template file path.
     */
    public function resolve(string $template): string
    {
        if (str_contains($template, '.')) {
            $templateName = str_replace('.', DIRECTORY_SEPARATOR, $template);
        } else {
            $templateName = $template;
        }

        foreach (config('views.twig.extensions', []) as $extension) {
            $templateFile = $templateName . $extension;

            if (file_exists(config('paths.views_dir') . DIRECTORY_SEPARATOR . $templateFile)) {
                return $templateFile;
            }
        }

        return $templateName;
    }
}
