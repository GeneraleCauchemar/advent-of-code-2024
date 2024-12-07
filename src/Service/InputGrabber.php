<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class InputGrabber
{
    private HttpClientInterface $httpClient;

    public function __construct(
        #[Autowire('%aoc_uri%')] string $aocBaseUri,
        #[Autowire('%aoc_session_key%')] string $aocSessionKey,
        #[Autowire('%kernel.project_dir%')] private string $projectDir
    ) {
        $this->httpClient = HttpClient::createForBaseUri($aocBaseUri, [
            'headers' => ['Cookie: session=' . $aocSessionKey],
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function grabInput(int $year, int $day): void
    {
        $input = $this->httpClient->request('GET', \sprintf('%u/day/%u/input', $year, $day))
                                  ->getContent()
        ;

        $dirName = \sprintf(
            '%s/src/Resources/input/Year%s/',
            $this->projectDir,
            $year
        );

        if (!is_dir($dirName)) {
            $this->createDirs($dirName);
        }

        $filename = \sprintf(
            '%s%s.txt',
            $dirName,
            str_pad($day, 2, '0', STR_PAD_LEFT)
        );

        file_put_contents($filename, $input);
    }

    private function createDirs(string $dirName): void
    {
        $dir = $dirName . 'test/';
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $dir));
        }

        // Both folder contents must be ignored
        file_put_contents(
            $dirName . '.gitignore',
            <<<TEXT
*
!test/
!.gitignore
TEXT
        );

        file_put_contents(
            $dir . '.gitignore',
            <<<TEXT
*
!.gitignore
TEXT
        );
    }
}
