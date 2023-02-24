<?php

namespace tests\functional;

use GigaDB\services\URLsService;
use GuzzleHttp\Client;

class UpdateFileSizeCest
{
    private const TEST_URLS = [
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/Diagram-ALL-FIELDS-Check-annotation.jpg",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/readme.txt",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/SRAmetadb.zip",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142",
        "https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100142/"
    ];

    public function tryFetchFileSizeFromFilesUrl(\FunctionalTester $I): void
    {
        $expectedLengthList = [
            55547,
            2351,
            383892184,
            0,
            0,
        ];

        $u = new URLsService(self::TEST_URLS);
        $I->assertTrue(is_a($u, "GigaDB\\services\\URLsService"));

        $zeroOutRedirectsAndDirectories = function ($response, $url) {
            if (301 === $response->getStatusCode() || str_ends_with($url, "/")) {
                return 0;
            }
            return null;
        };
        $webClient = new Client([ 'allow_redirects' => false ]);
        $contentLengthList = $u->fetchResponseHeader("Content-Length", $webClient, $zeroOutRedirectsAndDirectories);

        foreach ($expectedLengthList as $index => $expectedLength) {
            $I->assertEquals(
                $expectedLength,
                array_values($contentLengthList)[$index],
                array_keys($contentLengthList)[$index]
            );
        }
    }

    #[Codeception\Attribute\Skip]
    public function tryUpdateFileSizeWhenContentLengthInBytes(\FunctionalTester $I): void
    {
    }
}
