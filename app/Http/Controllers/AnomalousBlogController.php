<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Corporations\CreepyPastaMachine\Database\Models\BlogArticle;

class AnomalousBlogController extends Controller
{
    public function portal($blogid = false)
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
                    return array(
                        preg_replace('/http.+?storage/', 'storage', $doc->saveHTML($firstImage)), 
                        $modifiedContent);
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

        $topLevelWarning["en"] = [
        'blog_id' => 'origin',
        'containment' => 'Keter',
        'clearance' => (object) ["name" => "Top Secret",
                                 "level" => "Level 5"],
        'risk' => (object) ["name" => "Caution",
                            "level" => 2],
        'threat' => 'Black',
        'disruption' => (object) ["name" => "Amida",
                                 "level" => 5],
        'title' => "SCP Foundation Report: The Anomalous Blog (anomalous.laravelneuro.org)",
        'assessment' => "<div class='threatAssessment'><h3>Threat Assessment</h3>
        <p>The Anomalous Blog represents a significant threat due to its unique ability to alter reality based on the content of its articles. The anomalous manifestations correlated with the blog's readership present a dynamic and unpredictable threat vector, with the potential for widespread disruption on a global scale. Given its Amida level Disruption Class designation, the anomaly poses a considerable challenge to both containment efforts and global normalcy. The unpredictable nature of the manifestations, coupled with the blog's persistent re-emergence on the public internet, necessitates an ongoing, adaptive containment strategy.</p></div>
        <hr/>
        <div class='threatMeasures'><h3>Measures Taken by the SCP Foundation</h3>
        <p><b>Digital Quarantine:</b> The Foundation has seized and implemented a quarantine protocol on the Anomalous Blog's hosting server. Efforts to isolate the server from the broader internet have been partially successful, though the site's ability to breach containment remains a concern.</p>
        <p><b>Content Monitoring and Manipulation:</b> Foundation cybersecurity teams are engaged in constant monitoring of the site for new content. Where possible, they inject threat assessments and warnings into the articles to deter readership, which appears to mitigate the severity of the manifestations.</p>
        <p><b>Investigation of Origin:</b> Research teams are conducting investigations into the source of the blog's anomalous properties, including the method by which new content is generated and its ability to affect reality.</p>
        <p><b>Public Disinformation Campaign:</b> To further reduce the potential readership and impact of the Anomalous Blog, the Foundation has initiated a disinformation campaign aimed at discrediting the site among the general public and the paranormal community.</p></div>",
        'specialWarning' => "<h3>Public Warning:</h3>
        <p>The SCP Foundation advises all individuals to avoid accessing, sharing, or engaging with the content of the Anomalous Blog hosted at anomalous.laravelneuro.org. Interaction with this site has been linked to unpredictable and dangerous alterations to reality. If you encounter this website or are directed to its content, please report the incident to local authorities or directly to the Foundation through secure channels. Do not share links to the site or discuss its content online to prevent further spread and impact. Your cooperation is essential in maintaining global safety and preventing the escalation of these anomalous manifestations.</p>",
        'vo_file' => "storage/LaravelNeuro/CreepyPastaMachine/audio/top_level_warning_en.mp3"
        ];

        $topLevelWarning["de"] = [
            'blog_id' => 'origin',
            'containment' => 'Keter',
            'clearance' => (object) ["name" => "Streng Geheim",
                                     "level" => "Level 5"],
            'risk' => (object) ["name" => "Vorsicht",
                                "level" => 2],
            'threat' => 'Schwarz',
            'disruption' => (object) ["name" => "Amida",
                                     "level" => 5],
            'title' => "SCP Foundation Bericht: Der Anomale Blog (anomalous.laravelneuro.org)",
            'assessment' => "<div class='threatAssessment'><h3>Gefahrenbewertung</h3>
            <p>Der Anomale Blog stellt eine erhebliche Bedrohung dar aufgrund seiner einzigartigen Fähigkeit, die Realität basierend auf dem Inhalt seiner Artikel zu verändern. Die anomalen Manifestationen, die mit der Leserschaft des Blogs korrelieren, präsentieren einen dynamischen und unvorhersehbaren Bedrohungsvektor mit dem Potenzial für weitreichende Störungen auf globaler Ebene. Angesichts seiner Einstufung in die Amida-Störungsklasse stellt die Anomalie eine erhebliche Herausforderung sowohl für die Eindämmungsbemühungen als auch für die globale Normalität dar. Die unvorhersehbare Natur der Manifestationen, zusammen mit der anhaltenden Wiederkehr des Blogs im öffentlichen Internet, erfordert eine fortlaufende, adaptive Eindämmungsstrategie.</p></div>
            <hr/>
            <div class='threatMeasures'><h3>Von der SCP Foundation ergriffene Maßnahmen</h3>
            <p>Digitale Quarantäne: Die Foundation hat den Hosting-Server des Anomalen Blogs beschlagnahmt und ein Quarantäneprotokoll implementiert. Bemühungen, den Server vom breiteren Internet zu isolieren, waren teilweise erfolgreich, obwohl die Fähigkeit des Blogs, die Eindämmung zu durchbrechen, weiterhin ein Anliegen bleibt.</p>
            <p>Inhaltsüberwachung und -manipulation: Cybersicherheitsteams der Foundation sind ständig mit der Überwachung der Website auf neue Inhalte beschäftigt. Wo möglich, injizieren sie Gefahrenbewertungen und Warnungen in die Artikel, um die Leserschaft abzuschrecken, was die Schwere der Manifestationen zu mildern scheint.</p>
            <p>Untersuchung des Ursprungs: Forschungsteams führen Untersuchungen zur Quelle der anomalen Eigenschaften des Blogs durch, einschließlich der Methode, mit der neue Inhalte generiert werden und seine Fähigkeit, die Realität zu beeinflussen.</p>
            <p>Öffentliche Desinformationskampagne: Um die potenzielle Leserschaft und die Auswirkungen des Anomalen Blogs weiter zu reduzieren, hat die Foundation eine Desinformationskampagne eingeleitet, die darauf abzielt, die Website in der Öffentlichkeit und in der paranormalen Gemeinschaft zu diskreditieren.</p></div>",
            'specialWarning' => "<h3>Öffentliche Warnung</h3>
            <p>Die SCP Foundation rät allen Personen, den Zugriff, das Teilen oder das Engagement mit dem Inhalt des Anomalen Blogs, gehostet unter anomalous.laravelneuro.org, zu vermeiden. Die Interaktion mit dieser Website wurde mit unvorhersehbaren und gefährlichen Veränderungen der Realität in Verbindung gebracht. Wenn Sie auf diese Website stoßen oder auf ihren Inhalt hingewiesen werden, melden Sie den Vorfall bitte den lokalen Behörden oder direkt der Foundation über sichere Kanäle. Teilen Sie keine Links zur Website oder diskutieren Sie ihren Inhalt online, um eine weitere Verbreitung und Auswirkung zu verhindern. Ihre Zusammenarbeit ist entscheidend, um die globale Sicherheit zu gewährleisten und eine Eskalation dieser anomalen Manifestationen zu verhindern.</p>",
            'vo_file' => "storage/LaravelNeuro/CreepyPastaMachine/audio/top_level_warning_de.mp3"
            ];

        if($blogid !== false && BlogArticle::where('published', true)->where('id', $blogid)->first() !== null)
            $articles = BlogArticle::with(['scpDE', 'scpEN', 'link'])->where('id', $blogid)->paginate(1);
        else
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
            $allocateEN["createdTmp"] = $article->created_at;
            $allocateDE["createdTmp"] = $article->created_at;

            $allocateDE["article"] = str_replace('```', '', str_replace('```html', '', $articleDE));
            $allocateDE["title"] = $titleDe;
            $allocateDE["img"] = $img;

            $allocateEN["article"] = str_replace('```', '', str_replace('```html', '', $articleEN));
            $allocateEN["title"] = $titleEn;
            $allocateEN["img"] = $img;

            $allocateDE["articleVo"] = 'storage/LaravelNeuro' . explode('LaravelNeuro', $article->articleDEvo)[1];
            $allocateEN["articleVo"] = 'storage/LaravelNeuro' . explode('LaravelNeuro', $article->articleENvo)[1];
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

        if($articles->currentPage() > 1)
        {
            if($articles->count() == 0)
                abort(404);
            return response()->json(["articles" => $articleData]);
        }

        return Inertia::render('AnomalousBlog', [
            "articles" => $articleData,
            "currentPage" => 1,
            "pages" => $articles->lastPage(),
            "topLevelWarning" => $topLevelWarning,
            "application" => [
                "appName" => config('app.name'),
                "assetURL" => asset('')
            ],
        ]);
    }
}