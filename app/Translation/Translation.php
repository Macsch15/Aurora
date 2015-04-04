<?php
namespace Tricolore\Translation;

use Tricolore\Foundation\Application;
use Tricolore\Config\Config;
use Tricolore\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class Translation
{
    /**
     * Get translator
     * 
     * @param string $resource
     * @return Symfony\Component\Translation\Translator
     */
    public function getTranslator($resource = null)
    {
        $locale = Config::getParameter('trans.locale');

        $translator = new Translator($locale, new MessageSelector());
        $translator->addLoader('xliff', new XliffFileLoader());

        $this->addResource($translator, $resource, $locale);
        $this->addValidatorResource($translator, $locale);

        $translator->setFallbackLocale(['en_EN']);

        return $translator;
    }

    /**
     * Add resource
     * 
     * @param Symfony\Component\Translation\Translator $translator
     * @param string $resource
     * @param string $locale
     * @throws Tricolore\Exception\NotFoundResourceException
     * @return void
     */
    private function addResource(Translator $translator, $resource, $locale)
    {
        if ($resource === null) {
            $translator->addResource('xliff', 
                Application::getInstance()->createPath(
                    sprintf('app:Translation:Resources:%s:messages.xliff', $locale)), 
                $locale);           
        } else {
            if (file_exists($resource) === false) {
                throw new NotFoundResourceException(sprintf('Translation resource "%s" not found.', $resource));
            }

            $translator->addResource('xliff', $resource, $locale);
        }
    }

    /**
     * Add validator resource
     * 
     * @param Symfony\Component\Translation\Translator $translator
     * @param string $locale
     * @return void
     */
    private function addValidatorResource(Translator $translator, $locale)
    {
        $translator->addResource('xliff', 
            Application::getInstance()->createPath(
                sprintf('app:Translation:Resources:%s:validators.xliff', $locale)), 
            $locale);  
    }
}