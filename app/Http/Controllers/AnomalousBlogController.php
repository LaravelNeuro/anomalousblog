<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Corporations\CreepyPastaMachine\Database\Models\BlogArticle;

class AnomalousBlogController extends Controller
{
    public function portal()
    {
        $ClearanceLevelsEN = [
            "Level 1" => "Unrestricted",
            "Level 2" => "Restricted",
            "Level 3" => "Confidential",
            "Level 4" => "Secret",
            "Level 5" => "Top Secret",
            "Level 6" => "Cosmic Top Secret"
        ]; 

        $ClearanceLevelsDE = [
            "Level 1" => "Uneingeschränkt",
            "Level 2" => "Eingeschränkt",
            "Level 3" => "Vertraulich",
            "Level 4" => "Geheim",
            "Level 5" => "Streng geheim",
            "Level 6" => "Kosmisch streng geheim"
        ];  

        $DisruptionClasses = [
            "Dark" => 1,
            "Vlam" => 2,
            "Keneq" => 3,
            "Ekhi" => 4,
            "Amida" => 5,
            ];

        $RiskClasses = [
            "Notice" => 1,
            "Caution" => 2,
            "Warning" => 3,
            "Danger" => 4,
            "Critical" => 5,
            ];

        $getImg = function($htmlString) {
                libxml_use_internal_errors(true);
                $doc = new \DOMDocument();
                $doc->loadHTML(mb_convert_encoding($htmlString, 'HTML-ENTITIES', 'UTF-8'));
                libxml_clear_errors();
                $images = $doc->getElementsByTagName('img');
                if ($images->length > 0) {
                    $firstImage = $images->item(0);
                    $firstImage->parentNode->removeChild($firstImage);
                    $modifiedContent = $doc->saveHTML();
                    libxml_use_internal_errors(false);
                    return array($doc->saveHTML($firstImage), $modifiedContent);
                }
                libxml_use_internal_errors(false);
                return array(null, $htmlString);
            };
            
        $getTitle = function($htmlString) {
                libxml_use_internal_errors(true);
                $doc = new \DOMDocument();
                $doc->loadHTML(mb_convert_encoding($htmlString, 'HTML-ENTITIES', 'UTF-8'));
                libxml_clear_errors();
                $h1Elements = $doc->getElementsByTagName('h1');
                if ($h1Elements->length > 0) {
                    $firstH1 = $h1Elements->item(0);
                    $firstH1->parentNode->removeChild($firstH1);
                    $modifiedContent = $doc->saveHTML();
                    libxml_use_internal_errors(false);
                    return array($firstH1->nodeValue, $modifiedContent);
                }
                libxml_use_internal_errors(false);
                return array(null, $htmlString);
            };

        $articles = BlogArticle::with(['scpDE', 'scpEN', 'link'])->where('published', true)->latest()->paginate(10);

        $articleData = [];

        foreach($articles as $article)
        {
            [$img, $articleEN] = $getImg($article->articleEN);
            [$titleEn, $articleEN] = $getTitle($articleEN);

            [$drop, $articleDE] = $getImg($article->articleDE);
            [$titleDe, $articleDE] = $getTitle($articleDE);

            $allocateEN["created"] = $article->created_at;
            $allocateDE["created"] = $article->created_at;

            $allocateDE["article"] = $articleDE;
            $allocateDE["title"] = $titleDe;
            $allocateDE["img"] = $img;

            $allocateEN["article"] = $articleEN;
            $allocateEN["title"] = $titleEn;
            $allocateEN["img"] = $img;

            $allocateDE["articleVo"] = $article->articleDEvo;
            $allocateEN["articleVo"] = $article->articleENvo;
            $allocate["original"] = $article->link->first()->link;
            $allocateDE["SCPdata"] = $article->scpDE;
            $allocateEN["SCPdata"] = $article->scpEN;
            $allocateDE["SCPdata"]->vo_file = 'storage/' . $allocateDE["SCPdata"]->vo_file;
            $allocateEN["SCPdata"]->vo_file = 'storage/' . $allocateEN["SCPdata"]->vo_file;

                $allocateDE["SCPdata"]->clearance = (object) ["level" => $allocateDE["SCPdata"]->clearance,
                "name" => $ClearanceLevelsDE[$allocateDE["SCPdata"]->clearance]];

                $allocateDE["SCPdata"]->disruption = (object) ["name" => $allocateDE["SCPdata"]->disruption,
                            "level" => $DisruptionClasses[$allocateEN["SCPdata"]->disruption]];

                $allocateDE["SCPdata"]->risk = (object) ["name" => $allocateDE["SCPdata"]->risk,
                        "level" => $RiskClasses[$allocateEN["SCPdata"]->risk]];

                $allocateEN["SCPdata"]->clearance = (object) ["level" => $allocateEN["SCPdata"]->clearance,
                "name" => $ClearanceLevelsEN[$allocateEN["SCPdata"]->clearance]];

                $allocateEN["SCPdata"]->disruption = (object) ["name" => $allocateEN["SCPdata"]->disruption,
                            "level" => $DisruptionClasses[$allocateEN["SCPdata"]->disruption]];

                $allocateEN["SCPdata"]->risk = (object) ["name" => $allocateEN["SCPdata"]->risk,
                        "level" => $RiskClasses[$allocateEN["SCPdata"]->risk]];

                $allocateEN["SCPdataEN"] = $allocateEN["SCPdata"];
                $allocateDE["SCPdataEN"] = $allocateEN["SCPdata"];

            $articleData["en"][] = $allocateEN;
            $articleData["de"][] = $allocateDE;
        }

        return Inertia::render('AnomalousBlog', [
            "articles" => $articleData,
            "application" => [
                "appName" => config('app.name'),
                "assetURL" => asset('')
                ]
        ]);
    }
}