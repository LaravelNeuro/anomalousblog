<?php
namespace App\Corporations\CreepyPastaMachine\Transitions;

use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkCorporation;
use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkProject;
use LaravelNeuro\LaravelNeuro\Networking\Transition;
use LaravelNeuro\LaravelNeuro\Networking\TuringStrip;

use LaravelNeuro\LaravelNeuro\Enums\TuringMode;
use LaravelNeuro\LaravelNeuro\Enums\TuringMove;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;

use Carbon\Carbon;

use App\Corporations\CreepyPastaMachine\Database\Models\NewsArticle;
use App\Corporations\CreepyPastaMachine\Database\Models\BlogArticle;
use App\Corporations\CreepyPastaMachine\Database\Models\ScpWarning;

Class NewsCrawler extends Transition
{
        /**
    * An Eloquent Collection instance of the active project Model.
    * @var NetworkProject
    */
    protected NetworkProject $project;
    
    /**
    * An Eloquent Collection instance of the active corporation Model.
    * This instance will Eager Load units belonging to this corporation (accessible with the dynamic property 'units').
    * Each unit will also Eager Load agents and dataSetTemplates belonging to it (accessible with the dynamic properties 'agents' and 'dataSetTemplates')
    * Each dataSetTemplate will also Eager Load dataSets belonging to it (accessible with the dynamic property 'dataSets')
    * Example of accessing a dataSet: $this->corporation->units->first()->dataSetTemplates->first()->dataSets->first();
    * You can use Eloquent to traverse the Eager Loaded relations.
    * @var NetworkCorporation
    */
    protected NetworkCorporation $corporation;

    /**
     *  Execute the Transition.
     */
    public function handle() : TuringStrip
    {
        /**
        * Retrieve the current data from your TuringStrip's head
        * @var string
        */
        $data = $this->head->getData();

        $crawlLinks = ["https://gnews.io/api/v4/search?q=technology&lang=en&max=10&apikey=" . env('GNEWS_API_KEY'),
        "https://gnews.io/api/v4/search?q=weather&lang=en&max=10&apikey=" . env('GNEWS_API_KEY'),
        "https://gnews.io/api/v4/search?q=Climate&lang=en&max=10&apikey=" . env('GNEWS_API_KEY')];

        $articles = NewsArticle::where('consumed', false)
                 ->where('created_at', '>=', Carbon::now()->subHours(72)->toDateTimeString())
                 ->get();

        if($articles->count() == 0)
        {
            foreach($crawlLinks as $link)
            {
                $response = Http::get($link);
                $news = json_decode($response->body())->articles;
                foreach($news as $newsArticle)
                {
                    $scrape = Roach::collectSpider('App\\Corporations\\CreepyPastaMachine\\Spiders\\NewsScraper'::class,
                                                        new Overrides(startUrls: [$newsArticle->url]));
                    if(isset($scrape[0]) && !empty($scrape[0]->get('content')))
                    {
                        NewsArticle::create([
                            'title' => $newsArticle->title,
                            'link' => $newsArticle->url,
                            'content' => $scrape[0]->get('content'),
                            'description' => $newsArticle->description
                        ]);
                    }
                }
            }
            NewsArticle::where('content', '')->delete();
        }

        $articles = NewsArticle::where('consumed', false)
                 ->where('created_at', '>=', Carbon::now()->subHours(72)->toDateTimeString())
                 ->get();

        if($articles->count() == 0)
        {
            exit("Exiting: No news articles available for modification.");
        }

        $getArticle = NewsArticle::where('consumed', false)
                        ->where('created_at', '>=', Carbon::now()->subHours(72)->toDateTimeString())
                        ->inRandomOrder()
                        ->first();

        BlogArticle::create([
            "project_id" => $this->project->id,
            "original" => $getArticle->id,
            "published" => false
        ]);

        ScpWarning::create([
            "blog_id" => BlogArticle::where('project_id', $this->project->id)->latest()->first()->id,
            "lang" => "en"
        ]);

        $data = '**' . $getArticle->title . '**' . '\n' . $getArticle->content;

        $getArticle->consumed = true;
        $getArticle->save();

        $this->head->setData($data);

        return $this->head;
    }

}