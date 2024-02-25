<?php
namespace App\Corporations\CreepyPastaMachine\Transitions;

use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkCorporation;
use LaravelNeuro\LaravelNeuro\Networking\Database\Models\NetworkProject;
use LaravelNeuro\LaravelNeuro\Networking\Transition;
use LaravelNeuro\LaravelNeuro\Networking\TuringStrip;

use LaravelNeuro\LaravelNeuro\Enums\TuringMode;
use LaravelNeuro\LaravelNeuro\Enums\TuringMove;

use App\Corporations\CreepyPastaMachine\Database\Models\BlogArticle;
use App\Corporations\CreepyPastaMachine\Database\Models\ScpWarning;

use Illuminate\Support\Collection;

Class Validator extends Transition
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

        /**
        *  You can freely implement your Transition logic here.
        *  Utilize $this->head->getData and $this->head->setData to manipulate the data transitioning through the state machine here.
        *  Post-Transition behaviour can be manipulated by passing TuringMove::NEXT, TuringMove::OUTPUT, or TuringMove::REPEAT to $this->head->setNext(). The default value is set to TuringMove::NEXT.
        *  Post-Transition behaviour can also be manipulated by passing TuringMode::CONTINUE, TuringMode::STUCK, and TuringMode::COMPLETE to $this->head->setMode().
        *  In both cases, setting either TuringMove::OUTPUT or TuringMode::COMPLETE will result in the state machine moving to the final step and generating a response.
        */

        $isValid = true;

        $blogArticle = BlogArticle::where('project_id', $this->project->id)
            ->latest()
            ->first();

            $blogRequired = [
                'original',
                'articleENraw',
                'articleDEraw',
                'articleEN',
                'articleDE',
                'articleENvo',
                'articleDEvo'
            ];

            foreach ($blogRequired as $column) {
                if (is_null($blogArticle->$column) || $blogArticle->$column === '') {
                    $isValid = false;
                    break;
                }
            }

        $scpWarnings = ScpWarning::where('blog_id', $blogArticle->id)
            ->latest()
            ->get();

            $scpRequired = [
                'containment',
                'clearance',
                'risk',
                'threat',
                'assessment',
                'disruption',
                'vo_file'
            ];

            foreach($scpWarnings as $scpWarning)
            {
                foreach ($scpRequired as $column) {
                    if (is_null($scpWarning->$column) || $scpWarning->$column === '') {
                        $isValid = false;
                        break 2;
                    }
                }
            }
            
            if ($isValid) {
                $blogArticle->published = true;
                $blogArticle->articleEN = str_replace('```', '', str_replace('```html', '', $blogArticle->articleEN));
                $blogArticle->articleDE = str_replace('```', '', str_replace('```html', '', $blogArticle->articleDE));
                $blogArticle->save();
            }

        $this->head->setData($data);

        return $this->head;
    }

}