<?php

namespace OC\PlatformBundle\Antispam;


class OCAntispam
{
    private $mailer;
    private $locale;
    private $minLength;

    public function __construct(\Swift_Mailer $mailer) {
        $this->mailer       = $mailer;
    }

    public function setLocale(string $locale) {
        $this->locale = $locale;
    }

    public function setMinLength(int $minLength) {
        $this->minLength = $minLength;
    }

    /**
     * Vérifie si le texte est un spam ou non
     *
     * @param String $text la chaîne à vérifier
     * @return bool
     */
    public function isSpam($text) {
        return strlen($text) < $this->minLength;
    }
}