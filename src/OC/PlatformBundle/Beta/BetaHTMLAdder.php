<?php

namespace OC\PlatformBundle\Beta;

use Symfony\Component\HttpFoundation\Response;

class BetaHTMLAdder
{
    public function addBeta(Response $response, $remainingDays)
    {
        $content = $response->getContent();

        $html = '<div style="position: absolute; top: 50px; background: orange; width: 100%; text-align: center; padding: 0.5em;">Beta J-' . (int)$remainingDays . ' !</div>';

        $content = str_replace(
           '<nav',
            $html . '<nav',
            $content
        );

        $response->setContent($content);

        return $response;
    }
}